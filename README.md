# VidSlide

* Design: [figma-wireframe](https://figma.com/file/jAorHmVSyFGXCQxHzR57w6/VidSlide?node-id=0%3A1&t=hr9c5Co75ePuGCz3-1)

## Setup Database

`docker-compose up --build -d` 
<span style="color:green"><br>// The command uses the `"docker-compose"` file and builds it with the RUN-commands in the `"Docker"` file to install mysqli. 
The compose file includes 2 containers: The **mySQL database** and the **Apache PHP server** which functions as an mysqli-API to the database. The frontend GETs or POSTs to the backend and receives the index.php which is filled with the response at the backend API as a JSON file.</span>
