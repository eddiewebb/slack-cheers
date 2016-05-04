<?php

/*
* This file contains the main routes for the web UI
*/

$app->get('/', function() use ($app) {
    $teams = R::find('team');
    $app->render("blocks/home.twig",array("teams"=>$teams));
})->name('home');


$app->get('/report', function() use ($app) {
    $teams = R::find('peer');
    $app->render("blocks/report.twig",array("teams"=>$teams));
})->name('report');



$app->post('/submit', function() use ($app) {
    if ( isset( $_POST[ 'team_id' ] )
            && isset( $_POST[ 'prez' ] ) 
            && isset( $_POST[ 'soldes' ] ) 
            && isset( $_POST[ 'lync' ] ) 
            && isset( $_POST[ 'upload' ] ) 
            && isset( $_POST[ 'svn' ] ) ){
            $id = strip_tags( trim( $_POST[ 'team_id' ] ));
            $team = R::load('team',$id);
            $team->status = "CheckedIn";
            $team->checkinTime = date(DATE_ATOM);
            R::store($team);

            $app->flash('success',"Team $team->name is checked in!");
            $app->redirect($app->urlFor('report'));
    }else{
        $app->flash('error', "You must confirm all requirements before checking in!");
        $app->redirect($app->urlFor('home'));
    }//end rules check
                 
})->name('submit');


$app->get('/nuke', function() use ($app) {
    //add some records
    R::nuke(); // blows up existing DB

    $team = R::dispense('peer');
    $team->name="eddie.webb";
    $suds = R::dispense('sud');
    $suds->reason="Being Awesome";
    $team->ownSudsList[] = $suds;    
    $id = R::store($team);





    $team = R::dispense('peer');
    $team->name="ali.ren";
    $suds = R::dispense('sud');
    $suds->reason="Being Awesome";
    $team->ownSudsList[] = $suds; 
    $suds = R::dispense('sud');
    $suds->reason="Being Awesome";
    $team->ownSudsList[] = $suds; 
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