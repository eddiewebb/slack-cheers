<?php

/*
* This file contains the main routes for the REST api
*/

// a group  makes the url /api/* for the nested methods
$app->group('/api', function () use ($app, $peerRepo) {


    $app->group('/peers', function () use ($app, $peerRepo) {

        // get listing of all peer
        $app->get('/', function() use ($app, $peerRepo){
             $peers = $peerRepo->findAll();
             $json = $peerRepo->asJson($peers);
             $app->response->headers->set('Content-Type', 'application/json');
             echo '{"peers":'.$json.'}';
        });

        
        $app->get('/:id', function($id) use ($app, $peerRepo){
            $peer = $peerRepo->find($id);
            if($peer->id){
                $json = $peerRepo->asJson($peer);
                $app->response->headers->set('Content-Type', 'application/json');
                echo $json;
            }else{
                 $app->notFound();
            }
        })->name('api.user');


    }); // end teams group


    $app->group('/recognize', function () use ($app) {

        // get listing of all peer
        $app->post('/', function() use ($app){
             $fromId = $app->request->post('user_id');
             $fromHandle = $app->request->post('user_name');
             $text = $app->request->post('text');
           

             $cheerService = new CheerService($fromId, $fromHandle, $text, $app);
             if(isset($cheerService->errorMessage)){
                slackError($app,$cheerService->errorMessage);
             }else{
                $response = $cheerService->createCheerResponseFromSlackCommand();
             }

             $json = json_encode($response);
             $app->response->headers->set('Content-Type', 'application/json');
             echo $json;


        });


    }); // end teams group
}); //end api group




function findIdForHandle($handle){
    return 'UNKNOWN';
}

function slackError($app,$message){
    $response='{"text":  "Whoops: ' . $message . '"}';
    $app->response->headers->set('Content-Type', 'application/json');
    echo $response;
    die();
}
