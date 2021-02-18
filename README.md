# api-using-slim-framework-docker-compose

API is used for converting multiple data inputs (JSON obj of csv file) into one specific data format

`docker-compose up`

the API server will run at `localhost:8000`


GET: http://localhost:8000/ping

will answer with `pong`


POST: http://localhost:8000/data

with json example from `/data` 
or with csv example in same `/data` folder

