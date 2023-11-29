<?php

RequirePage::model('CRUD');
RequirePage::model('Intermede');
RequirePage::model('Programmation');


class ControllerIntermede extends Controller


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
        $intermedeModel = new Intermede;
        $intermedes = $intermedeModel->select();

        // Vérifie si la liste des intermedes est vide
        if (empty($intermedes)) {
            // Affiche un message d'erreur si aucun intermede n'est trouvé
            return Twig::render('error.twig', ['message' => 'Aucun intermede disponible.']);
        }

        // Affiche la liste des intermedes avec l'URL de l'image (lien_image) pour chaque intermede
        return Twig::render('intermede/index.twig', ['intermedes' => $intermedes]);
    }
    public function show($id)
    {
        $intermedeModel = new Intermede(); // Remplace par le nom réel de ton modèle

        // Utilise la méthode selectId pour récupérer les détails de l'intermède
        $intermede = $intermedeModel->selectId($id);

        if (!$intermede) {
            // Gérer le cas où l'intermède n'est pas trouvé
            return Twig::render('error.twig', ['message' => 'Intermède non trouvé.']);
        }

        // Affiche la vue 'intermede/show' avec les détails de l'intermède
        return Twig::render('intermede/show.twig', ['intermede' => $intermede]);
    }


    public function edit($id)
    {
        $intermedeModel = new Intermede(); // Remplace par le nom réel de ton modèle
        $intermede = $intermedeModel->selectId($id);

        if (!$intermede) {
            // Gérer le cas où l'intermède n'est pas trouvée
            return Twig::render('error.twig', ['message' => 'Intermède non trouvée.']);
        }

        // Affiche la vue 'intermede/edit' avec les données de l'intermède
        return Twig::render('intermede/edit.twig', ['intermede' => $intermede]);
    }


    public function showVideo($id)
    {
        $intermedeModel = new Intermede(); // Utilise ton modèle pour récupérer les données
        $intermede = $intermedeModel->selectId($id);

        return Twig::render('video/show.twig', ['intermede' => $intermede]);
    }

    public function create()
    {
        $intermedeModel = new Intermede(); // Remplace par le nom réel de ton modèle pour les séries
        $intermedes = $intermedeModel->select(); // Supposons que getAll() récupère toutes les séries

        // Passer la liste des séries au template
        return Twig::render('intermede/create.twig', ['intermedes' => $intermedes]);
    }

    public function store()
    {
        // Vérifie si la requête est une soumission de formulaire
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Récupère les données du formulaire
            $titre = $_POST['titre'] ?? '';
            $type = $_POST['type'] ?? '';
            $description = $_POST['description'] ?? '';
            $duree = $_POST['duree'] ?? '';
            $annee_originale = $_POST['annee_originale'] ?? '';
            $lien_video = $_POST['lien_video'] ?? '';

            // Validation des données (à implémenter selon tes règles de validation)

            // Crée une nouvelle instance du modèle Intermede
            $intermedeModel = new Intermede();

            // Prépare les données pour l'insertion
            $nouvelIntermede = [
                'titre' => $titre,
                'type' => $type,
                'description' => $description,
                'duree' => $duree,
                'annee_originale' => $annee_originale,
                'lien_video' => $lien_video
            ];

            // Insère les données dans la base de données
            if ($intermedeModel->insert($nouvelIntermede)) {
                // Redirection vers la page d'index des intermèdes
                RequirePage::url('intermede/index'); // Remplace 
                exit;
            } else {
                // Gestion des erreurs
                // Par exemple, réafficher le formulaire avec un message d'erreur
            }
        }
    }


}
