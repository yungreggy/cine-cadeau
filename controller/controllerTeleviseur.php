<?php

RequirePage::model('CRUD');


class ControllerTeleviseur
{
    public function __construct()
    {
        CheckSession::sessionAuth();
        if ($_SESSION['privilege'] != 1 && $_SESSION['privilege'] != 2) {
            RequirePage::url('login');
            exit();
        } else {
            try {



            } catch (\Exception $e) {
                RequirePage::url('erreur.twig');
                exit();
            }


        }




    }

    public function index()
    {
       

        // Affiche la liste des series avec l'URL de l'image (lien_image) pour chaque serie
        return Twig::render('televiseur.twig');
    }

    public function televiseur()
    {
        $videoUrl = $_GET['video'] ?? ''; // Récupère l'URL de la vidéo
        return Twig::render('televiseur.twig', ['video' => $videoUrl]);
    }


}