<?php

class CheerService{

	private $fromId;
	private $fromHandle;
	private $arguments;
	private $app;
	
	function __construct ($fromId, $fromHandle, $text, $app){
		$this->fromId = $fromId;
		$this->fromHandle = $fromHandle;
		$this->text = $text;
		$this->app = $app;

		if(strpos($text,"@") === false){
			$this->errorMessage = "No peers specified, see help";
			return;
		}else{
			$this->arguments = $this->parseArguments($text);
		}
	}

	function createCheerResponseFromSlackCommand() {
		$response = new stdClass();
		$response->response_type = "in_channel";

		foreach($this->arguments->peers as $mention){
			if(strpos($mention, '@') === false){
			    //TODO: Use lookup with slack to validate user and get name.
			    continue;
			}
			$peerName = str_replace('@', '', $mention);
			if( $peerName == $this->fromHandle || $peerName == "me" ){
			    slackError($this->app,"Way to recognize yourself. No points awarded.");
			}

			$peer = R::findOne('peer','handle = ?', [$peerName]);
			if (empty($peer)){
			    //first nomination, create
			    $peer=R::dispense('peer');
			    $peer->handle = $peerName;
			    $peer->user_id = findIdForHandle($peerName);
			}
			$cheers = R::dispense('cheer');
			$cheers->reason = $this->arguments->reason;
			$cheers->from = $this->fromId;
			$peer->ownCheersList[] = $cheers; 
			R::store($peer);


			 $detail = new stdClass();
			 $detail->text = "Way to go " . $mention . " for: " . $this->arguments->reason;
			 $response->attachments[] = $detail;
			 $response->text = "See " . $this->app->request->getUrl() . $this->app->urlFor('report') . " report for full details.";
		}

        return $response;
	}



function parseArguments($text){
    $arguments = new stdClass();
    $pattern = '/^((?:@\S+\s)+)(.*)$/';
    $valid = preg_match($pattern, $text, $matches);

    if($valid){
        if(sizeof($matches[1]) > 0){
	       	$arguments->peers = explode(" ", trim($matches[1]));
	    }else{
	    	$this->errorMessage = "User mention pattern is not valid.";
	    }

	    if( ! sizeof($matches[2]) > 1 ){
	        $arguments->reason = "Being Awesome";
	    }else{
	    	$arguments->reason = $matches[2];
	    }
    }else{
    	$this->errorMessage = "Please use the pattern '@username [additional users..] Reason for cheers'";
    }

    
    return $arguments;
}

}


