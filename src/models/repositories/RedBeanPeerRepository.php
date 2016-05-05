<?php


require dirname(__FILE__).'/PeerRepositoryInterface.php';

class RedBeanPeerRepository implements PeerRepositoryInterface
{
   

    public function find($id)
    {
        return R::findOne('peer',$id);
    }

    public function findAll()
    {
        $peers = R::find('peer');
        return $peers;
    }

    public function store(Peer $user){
        die('nt implemented');
    }


    public function asJson($rbObject){
        return json_encode(R::exportAll($rbObject));
    }

}