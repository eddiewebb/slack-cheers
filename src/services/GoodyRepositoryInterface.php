<?php

interface GoodyRepositoryInterface
{
    public function find($id);
    public function findAll();
    public function store( $user);
    public function delete( $user);
}