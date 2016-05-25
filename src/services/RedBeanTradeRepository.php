<?php



class RedBeanTradeRepository implements GoodyRepositoryInterface
{
   

    public function find($id)
    {
        return R::load('trade',$id);
    }

    public function findAll()
    {
        $trades = R::find('trade');
        return $trades;
    }

    public function store($record){
        R::store($record);   
        return $record;     
    }

    public function delete( $trade){
        die('nt implemented');
    }

    public function dispense(){
                $record = R::dispense('trade');
                return $record;
    }


    public function asJson($rbObject){
        return json_encode(R::exportAll($rbObject));
    }

}