<?php

class TradeService{

	private $app,$repo;
	
	function __construct ($app){
		$this->app = $app;
		$this->repo = new RedBeanTradeRepository();

	}

	public function find($id)
    {
        return $this->repo->find($id);
    }

    public function findAll()
    {
        return $this->repo->findAll();
    }




	function add($user_id, $goody_id){
		//use transactions here.
		 R::begin();
		 try{
			$user = R::load('peer',$user_id);
			$goody = R::load('goody',$goody_id);
			if ( empty($user->id)){
				return '{"message":"INvalid user ID"}';
			}
			if ( empty($goody->id)){
				return '{"message":"INvalid goody ID"}';
			}
			if ($goody->cost > $user->unspentCheers){
				return '{"message":"User does not have enough credit"}';
			}else{
				$user->unspentCheers = $user->unspentCheers - $goody->cost;
			}


			$trade = $this->repo->dispense();
            $trade->cost=$goody->cost;
          	$trade->status='ordered';
            $trade->created=R::isoDateTime();
            $trade->user = $user;
            $trade->goody = $goody;
       		R::store($trade);
       		R::commit();
       	}catch (Exception $e){
       		R::rollback();
			return '{"message":"Error saving transaction"}';
       	}

        return $trade;
    }

    function fulfill($id){
    	$trade = $this->repo->find($id);
    	$trade->status='fulfilled';
        $record = $this->repo->store($trade);
        return $record;
    }


    public function asJson($rbObject){
        return $this->repo->asJson($rbObject);
    }


}