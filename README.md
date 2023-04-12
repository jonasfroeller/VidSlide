# VidSlide

* **Wireframe:** [figma](https://figma.com/file/jAorHmVSyFGXCQxHzR57w6/VidSlide?node-id=0%3A1&t=hr9c5Co75ePuGCz3-1)
* **Demo:** [vercel](https://svelte-kit-vid-slide.vercel.app)
* Frontend: [github](https://github.com/jonasfroeller/SvelteKit_VidSlide/tree/master/frontend)
* Backend: [github](https://github.com/jonasfroeller/SvelteKit_VidSlide/tree/master/backend)
* Database: [github](https://github.com/jonasfroeller/SvelteKit_VidSlide/tree/master/database)

## Setup Frontend

1. `npm install` <span style="color:green">// install node_modules (dependencies)</span>

2. `npm run all` <span style="color:green">// view development server</span>

3. `npm run build` <span style="color:green">// build development to production</span>

4. `npm run preview` <span style="color:green">// view production server</span>

## Setup Database & Backend API

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

### Apache-PHP-Backend

#### Composer dependencies  

<em>(generated from Docker file on docker-compose)</em>

composer.json:
{
    "require": {
        "firebase/php-jwt": "^6.4",
        "vlucas/phpdotenv": "^5.5"
    }
}

#### Generate JWT keys for authentication  

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

### MySQL-Database

**Password example in /.env:**  
MYSQL_ROOT_PASSWORD=&#60;password&#62;  
