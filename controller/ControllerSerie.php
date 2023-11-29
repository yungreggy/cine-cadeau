<?php

RequirePage::model('CRUD');
RequirePage::model('Serie');
RequirePage::model('Emission');
RequirePage::model('Programmation');


class ControllerSerie extends Controller
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
        $serieModel = new Serie;
        $series = $serieModel->select();

        // Vérifie si la liste des series est vide
        if (empty($series)) {
            // Affiche un message d'erreur si aucun serie n'est trouvé
            return Twig::render('error.twig', ['message' => 'Aucune série disponible.']);
        }

        // Affiche la liste des series avec l'URL de l'image (lien_image) pour chaque serie
        return Twig::render('serie/index.twig', ['series' => $series]);
    }
    public function show($id)
    {
        $serieModel = new Serie();
        $serie = $serieModel->selectId($id); // Récupère les détails de la série

        if (!$serie) {
            RequirePage::url('error');
            return;
        }

        $emissionModel = new Emission();
        $emissions = $emissionModel->getEpisodesBySerieId($id);

        // Regrouper les émissions par saison
        $episodesParSaison = [];
        foreach ($emissions as $emission) {
            $episodesParSaison[$emission['numero_saison']][] = $emission;
        }

        return Twig::render('serie/show.twig', ['serie' => $serie, 'episodesParSaison' => $episodesParSaison]);
    }



    public function create()
    {
        // Afficher le formulaire de création de serie
        return Twig::render('serie/create.twig');
    }


    public function store()
    {


        $serieModel = new Serie();
        $data = $_POST;
        // Vérifie si id_emission est vide et le remplace par NULL ou une valeur par défaut
        

        // Validation et nettoyage des données ici

        // Insère le serie dans la base de données
        $newSerieId = $serieModel->insert($data);

        if ($newSerieId) {
            // Rediriger vers la page de détails du serie nouvellement ajouté
            RequirePage::url('serie/show/' . $newSerieId);
        } else {
            // Gérer l'erreur d'insertion
            // Rediriger vers une page d'erreur ou afficher un message
        }
    }

    public function edit($id)
    {
        $serieModel = new Serie;
        $serie = $serieModel->selectId($id);

        if (!$serie) {
            // Gérer le cas où le serie n'est pas trouvé
        }

        // Afficher le formulaire d'édition avec les données du serie
        return Twig::render('serie/edit.twig', ['serie' => $serie]);
    }


    public function update($id)
    {
        $serieModel = new Serie;
        $data = $_POST; // Récupère les données du formulaire
       

        // Validation et nettoyage des données ici
        // Assure-toi que les données sont sûres avant de continuer

        // Ajoute l'ID à l'ensemble des données
        $data['id'] = $id;

        // Mise à jour du serie dans la base de données
        $result = $serieModel->update($data);

        if ($result) {
            // Rediriger vers la page de détails du serie mis à jour
            RequirePage::url('serie/index');
        } else {
            // Gérer l'erreur de mise à jour
            // Tu peux rediriger vers une page d'erreur ou afficher un message
        }
    }









}