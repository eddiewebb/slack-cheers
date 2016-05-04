<?php

/*
* This file contains the main routes for the REST api
*/

// a group  makes the url /api/* for the nested methods
$app->group('/api', function () use ($app) {

    $app->group('/peers', function () use ($app) {

        // get listing of all peer
        $app->get('/', function() use ($app){
             $peers = R::find('peer');
             $json = json_encode(R::exportAll($peers));
             echo '{"peers":'.$json.'}';
        });


    }); // end teams group
}); //end api group