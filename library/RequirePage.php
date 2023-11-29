<?php

class RequirePage {

    static public function model($model){
        return require_once('model/'.$model.'.php');
    }

    static public function library($library){
        return require_once('library/'.$library.'.php');
    }

    static public function header($title){
        return '
        <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>'.$title.'</title>
                <link rel="stylesheet" href="'.PATH_DIR.'css/style.css">
                <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

            </head>
            <nav>
            <ul>
                <li><a href="'.PATH_DIR.'">Home</a></li>
                <li><a href="'.PATH_DIR.'"></a></li>
                <li><a href="'.PATH_DIR.'/create"> Create</a></li>

            </ul>
        </nav>
        ';
    }

    static public function url($url){
        header('location:'.PATH_DIR.$url);
    }
}


?>