<?php
RequirePage::model('CRUD');
RequirePage::model('Film');
RequirePage::model('Programmation');
RequirePage::model('Serie');
RequirePage::model('Emission');


class ControllerEmission extends Controller
{


    public function index()
    {
        $emissionModel = new Emission;
        $emissions = $emissionModel->select();

        // Vérifie si la liste des series est vide
        if (empty($emissions)) {
            // Affiche un message d'erreur si aucun serie n'est trouvé
            return Twig::render('error.twig', ['message' => 'Aucun épisdode  disponible.']);
        }
        $serieModel = new Serie;
        $series = $serieModel->select();

        // Vérifie si la liste des series est vide
        if (empty($series)) {
            // Affiche un message d'erreur si aucun serie n'est trouvé
            return Twig::render('error.twig', ['message' => 'Aucune série disponible.']);
        }

        // Affiche la liste des series avec l'URL de l'image (lien_image) pour chaque serie
        return Twig::render('emission/index.twig', ['series' => $emissions]);
    }
    public function show($id)
    {

        $emissionModel = new Emission;



        $emissions = $emissionModel->selectId($id);
        if (!$emissions) {
            RequirePage::url('error');
            return;
        }

        return Twig::render('emission/show.twig', ['emission' => $emissions]);
    }
  public function create() {
    // Récupérer l'ID de la série à partir de la requête (query string)
    $serieId = $_GET['serie_id'] ?? null;

    $serieModel = new Serie();
    $series = $serieModel->select(); // Récupère toutes les séries

    // Passer la liste des séries et l'ID de la série sélectionnée au template
    return Twig::render('emission/create.twig', ['series' => $series, 'selectedSerieId' => $serieId]);
}


    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Récupère les données du formulaire
            $titre = $_POST['titre'] ?? '';
            $description = $_POST['description'] ?? '';
            $numero_episode = $_POST['numero_episode'] ?? '';
            $numero_saison = $_POST['numero_saison'] ?? '';
            $lien_video = $_POST['lien_video'] ?? '';
            $id_serie = $_POST['id_serie'] ?? '';

            // Validation des données (à implémenter)

            // Crée une nouvelle instance du modèle Emission
            $emissionModel = new Emission();

            // Prépare les données pour l'insertion
            $nouvelleEmission = [
                'titre' => $titre,
                'description' => $description,
                'numero_episode' => $numero_episode,
                'numero_saison' => $numero_saison,
                'lien_video' => $lien_video,
                'id_serie' => $id_serie  // Assure-toi que cette colonne existe dans ta table emissions
            ];

            // Insère les données dans la base de données
             if ($emissionModel->insertWithSerieId($nouvelleEmission)) {
            // Redirection ou gestion de la réussite
        } else {
            // Gestion des erreurs
        }
                // Redirection vers la page de la série
                RequirePage::url('serie/show/' . $id_serie);
              
            } else {
                // Gestion des erreurs
            }
        }
  

    public function edit($id)
    {
        $emissionModel = new Emission(); // Remplace par le nom réel de ton modèle
        $emission = $emissionModel->selectId($id);

        if (!$emission) {
            // Gérer le cas où l'émission n'est pas trouvée
            return Twig::render('error.twig', ['message' => 'Émission non trouvée.']);
        }

        // Récupère également la liste des séries pour le menu déroulant
        $serieModel = new Serie();
        $series = $serieModel->select();

        return Twig::render('emission/edit.twig', ['emission' => $emission, 'series' => $series]);
    }

    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Récupère les données du formulaire
            $titre = $_POST['titre'] ?? '';
            $description = $_POST['description'] ?? '';
            $numero_episode = $_POST['numero_episode'] ?? '';
            $lien_video = $_POST['lien_video'] ?? '';
            $id_serie = $_POST['id_serie'] ?? '';

            // Validation des données (à implémenter)

            // Mise à jour des données de l'émission
            $emissionModel = new Emission();
            $miseAJourEmission = [
                'id' => $id, // Assure-toi d'inclure l'ID pour la mise à jour
                'titre' => $titre,
                'description' => $description,
                'numero_episode' => $numero_episode,
                'lien_video' => $lien_video,
                'id_serie' => $id_serie
            ];

            if ($emissionModel->update($miseAJourEmission)) {
                // Redirection ou affichage d'un message de succès
            } else {
                // Gestion des erreurs
            }
        }
    }





















}



