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
             if(strpos($text,"@") === false){
                slackError($app,"No peers specified, see help");
             }else{
                $arguments = parseArguments($text);
             }


             $response = new stdClass();
             $response->response_type = "in_channel";

             foreach($arguments->peers as $mention){
                if(strpos($mention, '@') === false){
                    //TODO: Use lookup with slack to validate user and get name.
                    continue;
                }
                $peerName = str_replace('@', '', $mention);
                if( $peerName == $fromHandle ){
                    slackError($app,"Way to recognize yourself. No points awarded.");
                }

                $peer = R::findOne('peer','handle = ?', [$peerName]);
                if (empty($peer)){
                    //first nomination, create
                    $peer=R::dispense('peer');
                    $peer->handle = $peerName;
                    $peer->user_id = findIdForHandle($peerName);
                }
                $cheers = R::dispense('cheer');
                $cheers->reason = $arguments->reason;
                $cheers->from = $fromId;
                $peer->ownCheersList[] = $cheers; 
                R::store($peer);


                 $detail = new stdClass();
                 $detail->text = "Way to go " . $mention . " for: " . $arguments->reason;
                 $response->attachments[] = $detail;
                 $response->text = "See " . $app->request->getUrl() . $app->urlFor('report') . " report for full details.";
             }




             $json = json_encode($response);
             $app->response->headers->set('Content-Type', 'application/json');
             echo $json;


        });


    }); // end teams group
}); //end api group


function parseArguments($text){
    $arguments = new stdClass();
    $tokens = explode('|', $text);
    if(sizeof($tokens) > 1){
        $arguments->reason = $tokens[1];
    }else{
        $arguments->reason = "Being Awesome";
    }
    $arguments->peers = explode(" ", trim($tokens[0]));
    return $arguments;
}


function findIdForHandle($handle){
    return 'UNKNOWN';
}

function slackError($app,$message){
    $response='{"text":  "Whoops: ' . $message . '"}';
    $app->response->headers->set('Content-Type', 'application/json');
    echo $response;
    die();
}
