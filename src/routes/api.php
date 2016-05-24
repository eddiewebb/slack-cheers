<?php

/*
* This file contains the main routes for the REST api
*/

// a group  makes the url /api/* for the nested methods
$app->group('/api', function () use ($app, $peerRepo, $goodyRepo) {


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



    $app->group('/goodys', function () use ($app, $goodyRepo) {

        // get listing of all peer
        $app->get('/', function() use ($app, $goodyRepo){
             $goodys = $goodyRepo->findAll();
             $json = $goodyRepo->asJson($goodys);
             $app->response->headers->set('Content-Type', 'application/json');
             echo '{"goodys":'.$json.'}';
        });

        
        $app->get('/:id', function($id) use ($app, $goodyRepo){
            $goody = $goodyRepo->find($id);
            if($goody->id){
                $json = $goodyRepo->asJson($goody);
                $app->response->headers->set('Content-Type', 'application/json');
                echo $json;
            }else{
                $app->halt(404);
                echo '{"message":"invalid ID or bad path"}';
            }
        })->name('api.goody');

        $app->put('/:id', function($id) use ($app, $goodyRepo){
            $goody = $goodyRepo->find($id);
            if($goody->id){
                $json = $app->request->getBody();
                $data = json_decode($json, true);        
                $goodyService = new GoodyService($app, $goodyRepo);
                $record = $goodyService->update($goody,$data);
                $json = $goodyRepo->asJson($record);
                $app->response->headers->set('Content-Type', 'application/json');
                echo $json;           
            }else{
                $app->halt(404);
                echo '{"message":"invalid ID or bad path"}';
            }
        });


                // add a new gooy
        $app->post('/', function() use ($app, $goodyRepo){

            $app->response->headers->set('Content-Type', 'application/json');
            $json = $app->request->getBody();
            $data = json_decode($json, true);        
            if( empty($data['name']) || empty($data['description']) || empty($data['cost']) ){
                $app->halt(400,'{"message":"Must provide at least `name`, `description`, and `cost`. Optional `imgUrl`."}');
            }
            $goodyService = new GoodyService($app, $goodyRepo);
            $record = $goodyService->add($data['name'],$data['description'],$data['cost'],$data['imgUrl']);
            if( ! empty($record->id)){
                $json = $goodyRepo->asJson($record);
                echo $json;
            }else{
                $app->halt(500,"Error saving record. Check for duplicates.");
            }
        });


    }); // end teams group



}); //end api group




function slackError($app,$message){
    $response='{"text":  "Whoops: ' . $message . '"}';
    $app->response->headers->set('Content-Type', 'application/json');
    echo $response;
    die();
}
