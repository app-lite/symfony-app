# Symfony 6 - DDD Example

An example for DDD, CQS, Application-Side Joins using Symfony 6 as a framework, Codeception and running with PHP 8.2.

The application has a selection option through LATERAL (PostgreSQL) and an option through a window function, the last "n" posts sorted by the time of the last, in each category in descending order.

Build and start the app:  
`make init`

Open app - http://localhost:8080

Running Tests:  
`make test`

Similar application on Laravel 9 + Phpunit - https://github.com/app-lite/laravel-app
