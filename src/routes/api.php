<?php

/*
* This file contains the main routes for the REST api
*/

// a group  makes the url /api/* for the nested methods
$app->group('/api', function () use ($app) {

    if( stripos($app->request->getPathInfo(),"/api")){
        $app->response->headers->set('Content-Type', 'application/json');
    }

    $app->get('/', function() use ($app){
        $json = '{"apis":[
                        {"name":"Peers","description":"The people in your team",
                        "url":"' 
                            .  $app->request->getUrl()  .  $app->urlFor('api.peers')
                        . '"},
                        '/* {"name":"Cheers","description":"cheer history",
                         "url":"' 
                            .  $app->request->getUrl()   .  $app->urlFor('api.cheers')
                        . '},
                        */. '
                        {"name":"Goodies","description":"Listing of available goodies",
                        "url":"'
                            .  $app->request->getUrl()   .  $app->urlFor('api.goodys')
                        . '"},
                        {"name":"Redeem","description":"Trade your cheers for swaq, gift cards and more!",
                        "url":"' 
                            .  $app->request->getUrl()   .  $app->urlFor('api.trades')
                        . '"}
                ]}';
                echo $json;    
    })->name('api');

    $app->group('/peers', function () use ($app) {

        $peerRepo = new RedBeanPeerRepository();

        // get listing of all peer
        $app->get('/', function() use ($app, $peerRepo){
             $peers = $peerRepo->findAll();
             $json = $peerRepo->asJson($peers);
                          echo '{"peers":'.$json.'}';
        })->name('api.peers');

        
        $app->get('/:id', function($id) use ($app, $peerRepo){
            $peer = $peerRepo->find($id);
            if($peer->id){
                $json = $peerRepo->asJson($peer);
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
                          echo $json;


        });


    }); // end teams group



    $app->group('/goodys', function () use ($app) {

        $goodyRepo = new RedBeanGoodyRepository();

        // get listing of all peer
        $app->get('/', function() use ($app, $goodyRepo){
             $goodys = $goodyRepo->findAll();
             $json = $goodyRepo->asJson($goodys);
                          echo '{"goodys":'.$json.'}';
        })->name('api.goodys');

        
        $app->get('/:id', function($id) use ($app, $goodyRepo){
            $goody = $goodyRepo->find($id);
            if($goody->id){
                $json = $goodyRepo->asJson($goody);
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
                                echo $json;           
            }else{
                $app->halt(404);
                echo '{"message":"invalid ID or bad path"}';
            }
        });


                // add a new gooy
        $app->post('/', function() use ($app, $goodyRepo){

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



    $app->group('/trades', function () use ($app) {

        $tradeService = new TradeService($app);

        // get listing of all peer
        $app->get('/', function() use ($app,$tradeService){
             $trades = $tradeService->findAll();
             $json = $tradeService->asJson($trades);
                          echo '{"trades":'.$json.'}';
        })->name('api.trades');

        
        $app->get('/:id', function($id) use ($app,$tradeService){
            $trade = $tradeService->find($id);
            if($trade->id){
                $json = $tradeService->asJson($trade);
                                echo $json;
            }else{
                $app->halt(404);
                echo '{"message":"invalid ID or bad path"}';
            }
        })->name('api.trade');

        $app->put('/:id', function($id) use ($app,$tradeService){
            $trade = $tradeService->find($id);
            if($trade->id){
                $json = $app->request->getBody();
                $data = json_decode($json, true);        
                $record = $tradeService->update($trade,$data);
                $json = $tradeService->asJson($record);
                                echo $json;           
            }else{
                $app->halt(404);
                echo '{"message":"invalid ID or bad path"}';
            }
        });

                // add a new gooy
        $app->post('/', function() use ($app,$tradeService){

            $json = $app->request->getBody();
            $data = json_decode($json, true);        
            if( empty($data['user_id']) || empty($data['goody_id'])  ){
                $app->halt(400,'{"message":"Must provide at least `user_id` and `goody_id`"}');
            }
            $record = $tradeService->add($data['user_id'],$data['goody_id']);
            if( ! empty($record->id)){
                $json = $tradeService->asJson($record);
                echo $json;
            }else{
                $app->halt(500,$record);
            }
        });


    }); // end teams group



}); //end api group





function slackError($app,$message){
    $response='{"text":  "Whoops: ' . $message . '"}';
    echo $response;
    die();
}
