# VidSlide

* **Wireframe:** [figma](https://figma.com/file/jAorHmVSyFGXCQxHzR57w6/VidSlide?node-id=0%3A1&t=hr9c5Co75ePuGCz3-1)
* **Demo:** [vercel](https://svelte-kit-vid-slide.vercel.app)
* Frontend: [github](https://github.com/jonasfroeller/SvelteKit_VidSlide/tree/master/frontend)
* Backend: [github](https://github.com/jonasfroeller/SvelteKit_VidSlide/tree/master/backend)
* Database: [github](https://github.com/jonasfroeller/SvelteKit_VidSlide/tree/master/database)

## Setup Frontend

1. `npm install` <span style="color:green">// install node_modules</span>

2. `npm run all` <span style="color:green">// view development server</span>

3. `npm run build` <span style="color:green">// build development to production</span>

4. `npm run preview` <span style="color:green">// view production server</span>

## Setup Database & Backend API

| `sudo MYSQL_ROOT_PASSWORD=<password> docker-compose up --build -d`  
<span style="color:green">  
// The command uses the "`docker-compose`" file and builds it with the RUN-commands in the "`Docker`" file to install mysqli.
The compose file includes 2 containers: The **mySQL database** and the **Apache PHP server** which functions as an mysqli-API to the database. The frontend GETs or POSTs to the backend and receives the index.php which is filled with the response at the backend API as a JSON file.
</span>
