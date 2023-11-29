<?php

RequirePage::model('CRUD');
RequirePage::model('Serie');
RequirePage::model('Film');
RequirePage::model('Emission');
RequirePage::model('Intermede');
RequirePage::model('Pub');
RequirePage::model('Programmation');


class controllerPub extends controller
{

    public function index()
    {
        $pubModel = new Pub(); // Remplace par le nom de ton modèle
        $pubs = $pubModel->select(); // Suppose une méthode pour récupérer toutes les publicités

        return Twig::render('pub/index.twig', ['publicites' => $pubs]);
    }

    public function create()
    {
        // Affiche le formulaire de création d'une nouvelle publicité
        return Twig::render('pub/create.twig');
    }

    public function show($id)
    {
        $publiciteModel = new Pub(); // Remplace par le nom de ton modèle
        $publicite = $publiciteModel->selectId($id); // Suppose une méthode pour récupérer une publicité par son ID

        if (!$publicite) {
            // Gérer le cas où la publicité n'est pas trouvée
            return Twig::render('error.twig', ['message' => 'Publicité non trouvée.']);
        }

        // Afficher les détails de la publicité
        return Twig::render('pub/show.twig', ['publicite' => $publicite]);
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Récupère les données du formulaire
            $titre = $_POST['titre'] ?? '';
            $type = $_POST['type'] ?? '';
            // ... autres champs ...

            $publiciteModel = new Pub();
            $nouvellePublicite = [
                'titre' => $titre,
                'type' => $type,
                // ... autres champs ...
            ];

            if ($publiciteModel->insert($nouvellePublicite)) {
                return Twig::render('pub/index.twig');
          
            } else {
                // Gestion des erreurs
            }
        }
    }


    public function edit($id)
    {
        $publiciteModel = new Pub();
        $publicite = $publiciteModel->selectId($id);

        if (!$publicite) {
            // Gérer l'absence de publicité
            return Twig::render('error.twig', ['message' => 'Publicité non trouvée.']);
        }

        return Twig::render('pub/edit.twig', ['publicite' => $publicite]);
    }


    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Récupère les données mises à jour du formulaire
            $titre = $_POST['titre'] ?? '';
            $type = $_POST['type'] ?? '';
            // ... autres champs ...

            $publiciteModel = new Pub();
            $miseAJourPublicite = [
                'id' => $id,
                'titre' => $titre,
                'type' => $type,
                // ... autres champs ...
            ];

            if ($publiciteModel->update($miseAJourPublicite)) {
              
            
            } else {
                // Gestion des erreurs
            }
        }
    }






}


