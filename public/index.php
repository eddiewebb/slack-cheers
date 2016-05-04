<?php

/*
*  This file (index.php) should remain pretty light.
*  It does not include any business logic, just the basic configuration of frameworks.
*/


require dirname(__FILE__).'/../src/DatabaseUrlParser.php';
require dirname(__FILE__).'/../vendor/autoload.php';
require dirname(__FILE__).'/../lib/rb.php';

//let's grab a DB through ORM tool redbean
if (array_key_exists('DATABASE_URL', $_ENV)) {
    $databaseUrl = $_ENV['DATABASE_URL'];$parser = new DatabaseUrlParser();
    $parsedUrl = $parser->toRedBean($databaseUrl);
    R::setup($parsedUrl['connection'], $parsedUrl['user'], $parsedUrl['pass']);
} else {
   error_log("NO DATABASE_URL defined by environment - running in DEVMODE with local DB");	
    R::setup();
}



//initialize slim and configure to use Twig for rendering
$app = new \Slim\Slim(array(
    'view' => new \Slim\Views\Twig(),
    'templates.path' => '../src/templates'
));
$view = $app->view();
$view->parserOptions = array(
    'debug' => true,
    'cache' =>  '/tmp/cache'
);
$view->parserExtensions = array(
    new \Slim\Views\TwigExtension()
);

//system settings needed for session and date() use
session_start();
date_default_timezone_set("America/New_York");



//
//great, now let's define some app routes in our included files
//

require dirname(__FILE__).'/../src/routes/main.php';

require dirname(__FILE__).'/../src/routes/api.php';



//catch any undefeind URLs
$app->notFound(function () use ($app) {
    $app->render('blocks/404.twig');
});

$app->run();

?>
