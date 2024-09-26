# Open Weather Map - Whatagraph integration

* [Introduction](#introduction)
* [Local installation](#local-installation)
    * [Docker](#docker)
    * [Prerequisites](#prerequisites)
    * [Installation](#installation)
    * [Tests](#tests)


## Introduction

This project pulls data from [One Call API 3.0](https://openweathermap.org/api/one-call-3) weather API for specified locations via a Geocoding API, and then pushes the data back into the [Whatagraph API](https://api.whatagraph.com/public-api/index.html) via a RESTful API.

## Local installation

* Install
    * PHP 8.2+
    * Composer
    * Node.js
    * Docker 2+
    * Docker-Compose
* Create an [Open Weather Map](https://openweathermap.org/) account and subscribe to the "One Call by Call" plan. 
* Create a Whatagraph account, create new "Custom API" source and name it "Weather".

### Project setup

Install the project into the `~/projects/wg-test` directory (your project directory):

1. Download and prepare the project files using the following commands:

        cd ~/projects
        git clone https://github.com/davidgustys/wg-test
        cd wg-test/laravel 
        composer install
        cp .env.example .env
        php artisan key:generate --ansi

2. In the `.env` file, add API keys and Endpints from the Open Weather Map account and Whatagraph custom API definition, respectively:

        WHATAGRAPH_BASE_ENDPOINT=https://api.whatagraph.com/
        WHATAGRAPH_TOKEN=example-1Ph9Lv1r3apHsoceGrwLnjGDErfJppjHiW40yI1gUzxJhmakUveHt

        OPENWEATHERMAP_DATA_ENDPOINT=https://api.openweathermap.org/data/3.0/
        OPENWEATHERMAP_GEO_ENDPOINT=https://api.openweathermap.org/geo/1.0/
        OPENWEATHERMAP_TOKEN=example-f81955614f4bddbea5e3a3e29

3. Run the following commands to seed whatagraph with dimensions and metrics:
        php artisan whatagraph:create-dimension-metric

4. Run the following command to fetch and push weather data, i.e:
        php artisan weather:fetch-and-push "New York" "2024-08-01" "2024-08-29" --batch-size=5  
    
6. Visualize weather data in Whatagraph reports.

### Tests

Run the following command to run the tests:

        php artisan test


## Approach

Initialy, I wanted to create API endpoint which would trigger a job, that would fetch weather data from Open Weather Map API and push it into Whatagraph.
Then after signing-up for Open Weather API and not 401 error (apprently it sometimes takes time to activate the account), I decided to approach the problem differently, that is to use CLI.

I created two commands:
    1. One that sets pre-defined dimensions and metrics
    2. Another one that fetches and pushes weather data.

In the code itself I tried to showcase Though of the "scaling" approach and good code practices. 
Some points to mentions:
   * Used validations for the CLI commands
   * Defined and used DTO's for Whatagraph Service/Adapter (I forgo this for OpenWeatherMap API to save time)
   * Overall tried to use Lavarel framework accordingly. (which even though i used it before, I had to remember)
   * Implemented backoff and retry strategy for the API jobs
   * Batched the API calls and tried to avoid hitting the rate limit


Time Sinks:
   * After I subscribed to the Open Weather API, my API keys became inactive for 1h or so, also I think its a bad practice to ask to subscribe to a paid plan during assignment.
   * Actually figuring out Whatagraph API Schema which is just partially documented, i.e the types of dimension just an example of "date" is given or accumulators on metrics also the same.
   * The UI of adding a report is also not 100% clear, if I understood correctly by default it selects some data, but not all that is passed and it wasnt very clear how to add a custom dimension, i.e `Weather Location` in my case

Considerations: 
   * As mentioned would have preferred of fully finishing with docker, having API, which then triggers a queued job, that would fetch weather data and push it into Whatagraph.
   * Having run-script to lint all php code instead of relying on IDE.
   * Ofcourse adding more tests.
