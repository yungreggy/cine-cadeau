<?php

class Pub extends CRUD
{

    protected $table = 'publicites';

    protected $primaryKey = 'id';
    protected $fillable = ['titre', 'type', 'description', 'duree', 'lien_video', 'anne_originale'];



    public function getByCriteres($criteres) {
        


}

}

