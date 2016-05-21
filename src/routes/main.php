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