<?php

$router = route();

$router->get('/', 'WelcomeController@welcome');
$router->get('/hello/{name}', 'HelloController@hello')->withAddMiddleware('man');
$router->get('/view', 'ViewController@hello');

// cache
// model
// middleware
// logger
// auth
// exception
// config
// response