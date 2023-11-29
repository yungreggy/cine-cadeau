<?php


class Intermede extends CRUD
{

    protected $table = 'intermedes';

    protected $primaryKey = 'id';
    protected $fillable = ['titre', 'type', 'description', 'duree', 'annee_originale', 'lien_video'];



    public function getByCriteres($criteres) {
        


}


}
