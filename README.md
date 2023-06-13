# VidSlide

[![Frontend-Hosting](https://therealsujitk-vercel-badge.vercel.app/?app=svelte-kit-vid-slide)](https://svelte-kit-vid-slide.vercel.app)
[![Docs](https://github.com/jonasfroeller/SvelteKit_VidSlide/actions/workflows/pages/pages-build-deployment/badge.svg)](https://jonasfroeller.github.io/SvelteKit_VidSlide/)
[![commitizen friendly](https://img.shields.io/badge/commitizen-friendly-brightgreen.svg)](http://commitizen.github.io/cz-cli/)

## Info  

VidSlide is a TikTok, Instagram Reels and YouTube Reels inspired app. Post, edit and delete posts. Built with [SvelteKit](https://kit.svelte.dev/), [ViteJS](https://vitejs.dev/), [TailwindCSS](https://tailwindcss.com/), [SkeletonUI](https://www.skeleton.dev/), [FloatingUI](https://floating-ui.com/), [Iconify](https://iconify.design/), [Typesave-i18n](https://github.com/ivanhofer/typesafe-i18n), [Zod](https://zod.dev/), [MySQL](https://hub.docker.com/layers/library/mysql/8.0.32/images/sha256-c86dfd69b3d1437e5d192447f0bdc57407d48bd379b97a453e11d72b97962e3b?context=explore), [Apache](https://hub.docker.com/layers/library/php/8.1.17-apache/images/sha256-21925d36ebc3a183aec51ffa417d31c89f97aedca8017ab9ddfd1e2bc80102a6?context=explore), [JS](https://www.w3schools.com/js/js_versions.asp) & [TS](https://www.typescriptlang.org/), [PHP](https://www.php.net/manual/de/intro-whatis.php), [PHP-JWT](https://github.com/firebase/php-jwt), [PHP-DotEnv](https://github.com/vlucas/phpdotenv), [PHP-FFMpeg](https://github.com/PHP-FFMpeg/PHP-FFMpeg), [Binaries-FFMpeg](https://ffmpeg.org/), [PHP-GD](https://www.php.net/manual/de/book.image.php), [PHP-MySQLi](https://www.php.net/manual/de/book.mysqli.php), [CSS](https://www.w3schools.com/css/), [HTML](https://www.w3schools.in/html/history), [Docker](https://www.docker.com/), [Vercel (frontend NodeJS hosting)](https://vercel.com/), [OracleCloud (backend/database Docker container hosting)](https://www.oracle.com/de/cloud/).

* **Wireframe:** [figma](https://figma.com/file/jAorHmVSyFGXCQxHzR57w6/VidSlide?node-id=0%3A1&t=hr9c5Co75ePuGCz3-1)
* **Demo:** [vercel](https://svelte-kit-vid-slide.vercel.app)
* Frontend: [github](https://github.com/jonasfroeller/SvelteKit_VidSlide/tree/master/frontend)
* Backend: [github](https://github.com/jonasfroeller/SvelteKit_VidSlide/tree/master/backend)
* Database: [github](https://github.com/jonasfroeller/SvelteKit_VidSlide/tree/master/database)

## Technologies/Languages

![Svelte](https://img.shields.io/badge/svelte-%23f1413d.svg?style=for-the-badge&logo=svelte&logoColor=white)
![NPM](https://img.shields.io/badge/NPM-%23CB3837.svg?style=for-the-badge&logo=npm&logoColor=white)
![Vite](https://img.shields.io/badge/vite-%23646CFF.svg?style=for-the-badge&logo=vite&logoColor=white)
![ESLint](https://img.shields.io/badge/ESLint-4B3263?style=for-the-badge&logo=eslint&logoColor=white)
![JWT](https://img.shields.io/badge/JWT-black?style=for-the-badge&logo=JSON%20web%20tokens)
![TailwindCSS](https://img.shields.io/badge/tailwindcss-%2338B2AC.svg?style=for-the-badge&logo=tailwind-css&logoColor=white)

![HTML5](https://img.shields.io/badge/html5-%23E34F26.svg?style=for-the-badge&logo=html5&logoColor=white)
![CSS3](https://img.shields.io/badge/css3-%231572B6.svg?style=for-the-badge&logo=css3&logoColor=white)
![JavaScript](https://img.shields.io/badge/javascript-%23323330.svg?style=for-the-badge&logo=javascript&logoColor=%23F7DF1E)
![NodeJS](https://img.shields.io/badge/node.js-6DA55F?style=for-the-badge&logo=node.js&logoColor=white)
![TypeScript](https://img.shields.io/badge/typescript-%23007ACC.svg?style=for-the-badge&logo=typescript&logoColor=white)
![PHP](https://img.shields.io/badge/php-%23777BB4.svg?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/mysql-%2300f.svg?style=for-the-badge&logo=mysql&logoColor=white)
![Markdown](https://img.shields.io/badge/markdown-%23000000.svg?style=for-the-badge&logo=markdown&logoColor=white)

![Docker](https://img.shields.io/badge/docker-%230db7ed.svg?style=for-the-badge&logo=docker&logoColor=white)
![Apache](https://img.shields.io/badge/apache-%23D42029.svg?style=for-the-badge&logo=apache&logoColor=white)
![Vercel](https://img.shields.io/badge/vercel-%23000000.svg?style=for-the-badge&logo=vercel&logoColor=white)
![Oracle](https://img.shields.io/badge/Oracle-F80000?style=for-the-badge&logo=oracle&logoColor=white)

![Visual Studio Code](https://img.shields.io/badge/Visual%20Studio%20Code-0078d7.svg?style=for-the-badge&logo=visual-studio-code&logoColor=white)

## Setup

### Setup Frontend

1. `npm install` <span style="color:green">// install node_modules (dependencies)</span>

2. `npm run all` <span style="color:green">// view development server</span>

3. `npm run build` <span style="color:green">// build development to production</span>

4. `npm run preview` <span style="color:green">// view production server</span>

#### Update Frontend Dependencies

`npm outdated` <span style="color:green">// displays current, wanted and latest version of dep  
`npm update` <span style="color:green">// updates deps to wanted version (never major versions (could break things))  

### Setup Database & Backend API

| `sudo docker-compose up -d --build`  
<span style="color:green">
// The command uses the "<mark>docker-compose.yaml</mark>" file and builds it with the RUN-commands in the "<mark>Docker</mark>" file to install mysqli.
The compose file includes 2 containers: The **mySQL database** and the **Apache PHP server** which functions as an mysqli-API to the database. The frontend fetches data from the backend index.php file which carries the JSON response.
</span>

![docker-compose](./docs/img/docker-compose-censored.png?raw=true "docker-compose")

<strong>Stop And Delete Containers</strong>

* stop and delete: `docker-compose down`  
* stop: `docker-compose stop`  
* start after stopped: `docker-compose start`

#### [Apache-PHP-Backend](https://hub.docker.com/r/jonasfroeller/vidslide-backend)

##### Composer dependencies  

<em>(generated from Docker file on docker-compose)</em>

composer.json:
{
    "require": {
        "firebase/php-jwt": "^6.4",
        "vlucas/phpdotenv": "^5.5"
    }
}

##### Generate JWT keys for authentication  

(Linux | Windows: installed on WSL2, <https://github.com/openssl/openssl/blob/master/NOTES-WINDOWS.md>)

`openssl genrsa -out private_key.pem 2048` <span style="color:green">// generates private key</span>

`openssl rsa -in private_key.pem -outform PEM -pubout -out public_key.pem` <span style="color:green">// generates public key from private key</span>  

**Example in backend/.env:**  
PRIVATE_KEY="
-----BEGIN PRIVATE KEY-----
&#60;RS256 private key&#62;
-----END PRIVATE KEY-----"

PUBLIC_KEY="
-----BEGIN PUBLIC KEY-----
&#60;RS256 public key&#62;
-----END PUBLIC KEY-----"

MYSQL_ROOT_PASSWORD=&#60;password&#62;  

#### MySQL-Database

**Password example in /.env:**  
MYSQL_ROOT_PASSWORD=&#60;password&#62;

**Setup tables with dummy data:**
[http://localhost:8196/db_api.php?setup_db=true](http://localhost:8196/db_api.php?setup_db=true)
