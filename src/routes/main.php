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

    $team = R::dispense('peer');
    $team->user_id="U123";
    $team->handle="eddie.webb";
    $cheers = R::dispense('cheer');
    $cheers->reason="Being Awesome";
    $cheers->from="U234";
    $team->ownSudsList[] = $cheers;    
    $id = R::store($team);





    $team = R::dispense('peer');
    $team->user_id="U234";
    $team->handle="ali.ren";
    $cheers = R::dispense('cheer');
    $cheers->reason="Being Awesome";
    $cheers->from="U123";
    $team->ownSudsList[] = $cheers; 
    $cheers = R::dispense('cheer');
    $cheers->reason="Being Awesome";
    $cheers->from="U123";
    $team->ownSudsList[] = $cheers; 
    $id = R::store($team);


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