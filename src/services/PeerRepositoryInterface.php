<?php

interface PeerRepositoryInterface
{
    public function find($id);
    public function store(Peer $user);
}