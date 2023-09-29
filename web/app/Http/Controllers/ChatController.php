<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\ChatRequest;
use Illuminate\Http\Request;
use App\Models\Histories;
use App\Jobs\RequestChat;
use App\Models\Chats;
use App\Models\LLMs;
use App\Models\User;
use DB;

class ChatController extends Controller
{
    public function home(Request $request)
    {
        $result = DB::table(function ($query) {
            $query
                ->select(DB::raw('substring(name, 7) as model_id'), 'perm_id')
                ->from('group_permissions')
                ->join('permissions', 'perm_id', '=', 'permissions.id')
                ->where('group_id', Auth()->user()->group_id)
                ->where('name', 'like', 'model_%')
                ->get();
        }, 'tmp')
            ->join('llms', 'llms.id', '=', DB::raw('CAST(tmp.model_id AS BIGINT)'))
            ->select('tmp.*', 'llms.*')
            ->where('llms.enabled', true)
            ->orderby('llms.order')
            ->orderby('llms.created_at')
            ->first();
        if ($result) {
            return redirect()->route('chat.new', $result->id);
        } else {
            return view('chat');
        }
    }

    public function main(Request $request)
    {
        $chat = Chats::findOrFail($request->route('chat_id'));
        if ($chat->user_id != Auth::user()->id) {
            return redirect()->route('chat.home');
        } elseif (LLMs::findOrFail($chat->llm_id)->enabled == true) {
            return view('chat');
        }
        return redirect()->route('archive.chat', $request->route('chat_id'));
    }

    public function create(ChatRequest $request): RedirectResponse
    {
        $input = $request->input('input');
        if ($input) {
            $chat = new Chats();
            $chat->fill(['name' => $input, 'llm_id' => $request->input('llm_id'), 'user_id' => $request->user()->id]);
            $chat->save();
            $history = new Histories();
            $history->fill(['msg' => $input, 'chat_id' => $chat->id, 'isbot' => false]);
            $history->save();
            $tmp = Histories::where("chat_id","=",$chat->id)->select('msg','isbot')->get()->toJson();
            $history = new Histories();
            $history->fill(['msg' => '* ...thinking... *', 'chat_id' => $chat->id, 'isbot' => true, 'created_at' => date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . ' +1 second'))]);
            $history->save();
            $llm = LLMs::findOrFail($request->input('llm_id'));
            Redis::rpush('usertask_' . Auth::user()->id, $history->id);
            RequestChat::dispatch($tmp, $llm->access_code, Auth::user()->id, $history->id, Auth::user()->openai_token);
        }
        return Redirect::route('chat.chat', $chat->id);
    }

    public function request(Request $request): RedirectResponse
    {
        $chatId = $request->input('chat_id');
        $input = $request->input('input');
        if ($chatId && $input) {
            $history = new Histories();
            $history->fill(['msg' => $input, 'chat_id' => $chatId, 'isbot' => false]);
            $history->save();
            $tmp = Histories::where("chat_id","=",$chatId)->select('msg','isbot')->orderby('created_at')->orderby('id', "desc")->get()->toJson();
            $history = new Histories();
            $history->fill(['msg' => '* ...thinking... *', 'chat_id' => $chatId, 'isbot' => true, 'created_at' => date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . ' +1 second'))]);
            $history->save();
            $access_code = LLMs::findOrFail(Chats::findOrFail($chatId)->llm_id)->access_code;
            Redis::rpush('usertask_' . Auth::user()->id, $history->id);
            RequestChat::dispatch($tmp, $access_code, Auth::user()->id, $history->id, Auth::user()->openai_token);
        }
        return Redirect::route('chat.chat', $chatId);
    }

    public function delete(Request $request): RedirectResponse
    {
        try {
            $chat = Chats::findOrFail($request->input('id'));
        } catch (ModelNotFoundException $e) {
            // Handle the exception here, for example:
            return Redirect::route('chat.home');
        }

        $chat->delete();
        return Redirect::route('chat.home');
    }

    public function edit(Request $request): RedirectResponse
    {
        try {
            $chat = Chats::findOrFail($request->input('id'));
        } catch (ModelNotFoundException $e) {
            // Handle the exception here, for example:
            return response()->json(['error' => 'Chat not found'], 404);
        }
        $chat->fill(['name' => $request->input('new_name')]);
        $chat->save();
        return Redirect::route('chat.chat', $request->input('id'));
    }

    public function SSE(Request $request)
    {
        $response = new StreamedResponse();
        $response->headers->set('Content-Type', 'text/event-stream');
        $response->headers->set('Cache-Control', 'no-cache');
        $response->headers->set('X-Accel-Buffering', 'no');
        $response->headers->set('charset', 'utf-8');
        $response->headers->set('Connection', 'close');

        $response->setCallback(function () use ($response, $request) {
            $channel = $request->input('channel');
            if ($channel != null) {
                if (strpos($channel, 'aielection_') === 0) {
                    $client = Redis::connection();
                    $client->subscribe($channel, function ($message, $raw_history_id) use ($client, $response) {
                        global $listening;
                        [$type, $msg] = explode(' ', $message, 2);
                        $history_id = substr($raw_history_id, strrpos($raw_history_id, '_') + 1);
                        if ($type == 'Ended') {
                            $key = array_search($history_id, $listening);
                            if ($key !== false) {
                                unset($listening[$key]);
                            }
                            if (count($listening) == 0) {
                                echo "event: close\n\n";
                                ob_flush();
                                flush();
                                $client->disconnect();
                            }
                        } elseif ($type == 'New') {
                            echo 'data: ' . json_encode(["history_id"=>$history_id, "msg"=>json_decode($msg)->msg])  . "\n\n";
                            ob_flush();
                            flush();
                        }
                    });
                    return;
                }
            }
            global $listening;
            $listening = Redis::lrange('usertask_' . Auth::user()->id, 0, -1);
            if (count($listening) > 0) {
                $client = Redis::connection();
                $client->subscribe($listening, function ($message, $raw_history_id) use ($client, $response) {
                    global $listening;
                    [$type, $msg] = explode(' ', $message, 2);
                    $history_id = substr($raw_history_id, strrpos($raw_history_id, '_') + 1);
                    if ($type == 'Ended') {
                        $key = array_search($history_id, $listening);
                        if ($key !== false) {
                            unset($listening[$key]);
                        }
                        if (count($listening) == 0) {
                            echo "event: close\n\n";
                            ob_flush();
                            flush();
                            $client->disconnect();
                        }
                    } elseif ($type == 'New') {
                        echo 'data: ' . json_encode(["history_id"=>$history_id, "msg"=>json_decode($msg)->msg])  . "\n\n";
                        ob_flush();
                        flush();
                    }
                });
            }
        });

        return $response;
    }
}
