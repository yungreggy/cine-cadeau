<?php


class Serie extends CRUD
{

    protected $table = 'series';

    protected $primaryKey = 'id';
    protected $fillable = ['titre', 'description', 'lien_image', 'lien_video', 'anne_originale'];

    public function getByCriteres($criteres) {
        


}


}