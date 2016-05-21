<?php

/*
* This file contains the main routes for the web UI
*/

$app->get('/', function() use ($app) {
    $app->render("blocks/home.twig");
})->name('home');


$app->get('/report', function() use ($app) {
    $peers = R::find('peer');
    $app->render("blocks/report.twig",array("peers"=>$peers));
})->name('report');



$app->get('/nuke', function() use ($app) {
    //add some records
    R::nuke(); // blows up existing DB

    //redbean onsome mysql wants to treat this as an integer, dforce schema and lock it.
    // don't be fooled, there is no way to define schema I can see, so we use the String 'string' to avoid int fields
    $peer = R::dispense('peer');
    $peer->slackid = "string";
    $peer->handle = "string";

    $cheers = R::dispense('cheer');
    $cheers->reason = "string";
    $cheers->from = "string";
    $peer->ownCheersList[] = $cheers; 
    R::store($peer);



    R::store($peer);
    R::wipe( 'cheer' );
    R::wipe( 'peer' );



    R::freeze( TRUE );

    $app->flash('success', "Kaboom!!!! All peers nuked!");
    $app->redirect($app->urlFor('report'));
})->name('nuke');

$app->get('/readme',function() use ($app) {
    //get readme.md
    $myfile = fopen("../readme.md", "r") or die("Unable to open file!");
    $markup = fread($myfile,filesize("../readme.md"));
    fclose($myfile);

    $Parsedown = new Parsedown();
    echo $Parsedown->text($markup);
})->name('readme');