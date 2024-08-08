[正體中文 README_TW.md](./README_TW.md)

<h1 align="center">
  <br>
  <a href="https://kuwaai.org/en-US/">
  <img src="./src/multi-chat/public/images/kuwa.png" alt="Kuwa GenAI OS" width="200"></a>
  <br>
  Kuwa GenAI OS
  <br>
</h1>

<h4 align="center">An open, free, secure, and privacy-focused Generative-AI Orchestrating System.</h4>

<p align="center">
  <a href="http://makeapullrequest.com">
    <img src="https://img.shields.io/badge/PRs-welcome-brightgreen.svg">
  </a>
  <a href="#">
    <img src="https://img.shields.io/badge/all_contributors-2-orange.svg?style=flat-square">
  </a>
  <a href="https://laravel.com/docs/10.x/releases">
    <img src="https://img.shields.io/badge/maintained%20with-Laravel-cc00ff.svg">
  </a>
</p>

<p align="center">
  <a href="#key-features">Key Features</a> •
  <a href="#architecture">Architecture</a> •
  <a href="#installation-guide">Installation Guide</a> •
  <a href="#community">Community</a> •
  <a href="#acknowledgements">Acknowledgements</a> •
  <a href="#license">License</a>
</p>

## Key Features
* Multi-lingual turnkey solution for GenAI use, development and deployment on Windows, Linux and MacOS
* Concurrent multi-chat, quoting, full prompt-list import/export/share, and more for users
* Supporting multimodal models, popular RAG/agent tools, traditional applications, and local bot store  
* Flexible orchestration of prompts x RAGs x multi-modal models x tools x bots x hardware/GPUs
* Heterogeneous support from raspberry Pi, laptops, PCs, edge servers, and virtual hosts to cloud
* Open-sourced, allowing developers to contribute and customize the system according to their needs

![screenshot](./src/multi-chat/public/images/demo.gif)

## Architecture
> **Warning**: This a preliminary draft and may be subject to further changes.

[![screenshot](./src/multi-chat/public/images/architecture.svg)](https://kuwaai.org/os/Intro)

## Installation Guide
### Quick Installation
Download the script or the executable file, run it, and follow its steps to have your own Kuwa!
* **Windows**

  Download and run the pre-built Windows executable from [Kuwa's latest releases](https://github.com/kuwaai/genai-os/releases)

* **Linux/Docker**

  Download and run sudo [build.sh](./docker/build.sh) , or invoke the following command to automatically install Docker, Cuda, and Kuwa
  ```
  curl -fsSL https://github.com/kuwaai/genai-os/tree/main/docker/build.sh | sh
  ```
###  Step-by-step Installation
You can build your own customized Kuwa by following the step-by-step documents.
* [Portable Windows version](./windows/README.md)
* [Linux/Docker version](./docker/README.md)
### More Models and Applications
With executors, Kuwa can orchestrate diverse multimodal models, remote services, applications, databases, bots, etc. You can check [Executor's README](./src/executor/README.md) for further customization and configuration.

## Download

You can [download](https://github.com/kuwaai/genai-os/releases) the latest Kuwa GenAI OS version that supports Windows and Linux.

## Community

[Discord](https://discord.gg/4HxYAkvdu5) - Kuwa AI Discord community server

[Facebook](https://www.facebook.com/groups/g.kuwaai.org) - Kuwa AI Community

[Facebook](https://www.facebook.com/groups/g.kuwaai.tw) - Kuwa AI Taiwan community

[Google Group](https://groups.google.com/g/kuwa-dev) - kuwa-dev

## Announcement

[Facebook](https://www.facebook.com/kuwaai) - Kuwa AI

[Google Group](https://groups.google.com/g/kuwa-announce) - kuwa-announce

## Support

Our team currently has only two people. If you are interested in this project, you can help us develop it together and improve this open-source project. Please do not hesitate to contact us anytime if you are willing to help!

## Packages & Applications

The following packages and applications are used in this project:

- [PHP & PHP-FPM](https://www.php.net/)
- [Laravel 10](https://laravel.com/)
- [Python 3](https://www.python.org/)
- [Node.js](https://nodejs.org/)
- [Docker](https://www.docker.com/)
- [Redis](https://redis.io/)
- [Marked](https://github.com/chjj/marked)
- [highlight.js](https://highlightjs.org/)
- [NVIDIA CUDA](https://developer.nvidia.com/cuda-toolkit)

## Acknowledgements
We want to acknowledge NSTC's TAIDE project and the Taiwan AI Academy for their assistance in the early development of this project.
<a href="https://www.nuk.edu.tw/"><img src="./src/multi-chat/public/images/logo_NUK.jpg" height="100px"></a>
<a href="https://taide.tw/"><img src="./src/multi-chat/public/images/logo_taide.jpg" height="100px"></a>
<a href="https://www.nstc.gov.tw/"><img src="./src/multi-chat/public/images/logo_NSTCpng.jpg" height="100px"></a>
<a href="https://www.narlabs.org.tw/"><img src="./src/multi-chat/public/images/logo_NARlabs.jpg" height="100px"></a>
<a href="https://aiacademy.tw/"><img src="./src/multi-chat/public/images/logo_AIA.jpg" height="100px"></a>


## License
[MIT](./LICENSE)
