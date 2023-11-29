<?php

abstract class Controller
{
    abstract public function index();

    public function error($message)
    {
        return Twig::render('error.twig', ['message' => $message]);
    }

  


}


  



?>