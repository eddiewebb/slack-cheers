<?php

class GoodyService{

	private $app;
	
	function __construct ($app, $goodyRepo){
		$this->app = $app;
		$this->goodyRepo = $goodyRepo;

	}

	function listGoodies(){
		
	}


	function add($name, $description, $cost, $imgUrl="/images/generic.png"){
			$record = $this->goodyRepo->dispense();

            $record->name=$name;
            $record->description=$description;
            $record->cost=$cost;
            $record->imgUrl=$imgUrl;

       		$record = $this->goodyRepo->store($existingGoody);

    }

    function update($existingGoody, $requestData){
        if( ! empty($requestData['name']) ){
            $existingGoody->name = $requestData['name'];
        }
        if( ! empty($requestData['description']) ){
            $existingGoody->description = $requestData['description'];
        }
        if( ! empty($requestData['cost']) ){
            $existingGoody->cost = $requestData['cost'];
        }
        if( ! empty($requestData['imgUrl']) ){
            $existingGoody->imgUrl = $requestData['imgUrl'];
        }
        $record = $this->goodyRepo->store($existingGoody);
        return $record;
    }


}


