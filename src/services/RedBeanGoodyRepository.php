<?php



class RedBeanGoodyRepository implements GoodyRepositoryInterface
{
   

    public function find($id)
    {
        return R::load('goody',$id);
    }

    public function findAll()
    {
        $goodys = R::find('goody');
        return $goodys;
    }

    public function store($record){
        R::store($record);   
        return $record;     
    }

    public function delete( $goody){
        die('nt implemented');
    }

    public function dispense(){
                $record = R::dispense('goody');
                return $record;
    }


    public function asJson($rbObject){
        return json_encode(R::exportAll($rbObject));
    }

}