<?php
RequirePage::model('CRUD');
RequirePage::model('Film');
RequirePage::model('Programmation');
RequirePage::model('Serie');
RequirePage::model('Emission');
RequirePage::model('Pub');
RequirePage::model('Intermede');
RequirePage::model('Televiseur');
RequirePage::model('PlageHoraire');
RequirePage::model('PlageHoraireMedia');


class ControllerPlageHoraire extends Controller {

    private function calculerHeureFin($heureDebut, $dureeFilm)
    {
        $heureDebutDateTime = new DateTime($heureDebut);
        $interval = new DateInterval('PT' . $dureeFilm . 'M');
        $heureDebutDateTime->add($interval);

        return $heureDebutDateTime->format('H:i:s');
    }

    public function index()
    {
        $plageHoraireModel = new PlageHoraire(); // Utilise le modèle PlageHoraire
        $plagesHoraires = $plageHoraireModel->select(); // Méthode pour récupérer toutes les plages horaires

        if (empty($plagesHoraires)) {
            return Twig::render('error.twig', ['message' => 'Aucune plage horaire disponible.']);
        }

        return Twig::render('plageHoraire/index.twig', ['plagesHoraires' => $plagesHoraires]);
    }



    public function create() {
        $programmeModel = new Programmation();
        $programmes = $programmeModel->select();

          $filmModel = new Film();
    $films = $filmModel->select();

    $serieModel = new Serie();
    $series = $serieModel->select();

    $emissionModel = new Emission();
    $emissions = $emissionModel->select();

    $publiciteModel = new Pub();
    $publicites = $publiciteModel->select();

    $intermedeModel = new Intermede();
    $intermedes = $intermedeModel->select();
    

        return Twig::render('plageHoraire/create.twig', [
        'programmes' => $programmes,
        'films' => $films,
        'series' => $series,
        'emissions' => $emissions,
        'publicites' => $publicites,
        'intermedes' => $intermedes
    ]);
        
    }



    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Récupération des données du formulaire
            $idProgramme = $_POST['id_programme'] ?? null;
            $heureDebut = $_POST['heure_debut'] ?? null;
            $heureFin = $_POST['heure_fin'] ?? null;

            // Valide les données ici

            // Crée une instance de ton modèle de plage horaire
            $plageHoraireModel = new PlageHoraire();

            // Prépare les données pour l'insertion
            $nouvellePlageHoraire = [
                'id_programme' => $idProgramme,
                'heure_debut' => $heureDebut,
                'heure_fin' => $heureFin,
            ];

            // Insère la plage horaire dans la base de données
            if ($plageHoraireModel->insert($nouvellePlageHoraire)) {
                $plageHoraireId = $plageHoraireModel->lastInsertId();
                // Récupère les informations du programme
                $programmationModel = new Programmation();
                $programme = $programmationModel->selectId($idProgramme);
                $dateDebut = $programme['date_debut'];
                $dateFin = $programme['date_fin'];
                $plageHoraire = $plageHoraireModel->selectId($plageHoraireId);
                // Génère des plages horaires dynamiques pour les films
                $filmModel = new Film();
                $categories = ['long-metrage'];
                while ($dateDebut <= $dateFin) {
                    $categorie = $categories[array_rand($categories)];
                    $criteres = ['categorie' => $categorie];

                    $filmDuJour = $filmModel->mediaSelectionneAuHasard($criteres, $idProgramme, $plageHoraireModel);

               

                    if ($filmDuJour) {
                        $plageHoraireMediaModel = new PlageHoraireMedia();
                        $plageHoraireMediaModel->insert([
                            'plage_horaire_id' => $plageHoraireId,
                            'media_id' => $filmDuJour['id'],
                            'media_type' => 'film',
                            'date' => $dateDebut // Ajout de la date ici
                        ]);
                    }

                    // Incrémenter la date de début pour le jour suivant
                    $dateDebut = date('Y-m-d', strtotime($dateDebut . ' +1 day'));
                }

                return Twig::render('plageHoraire/show.twig', ['plageHoraire' => $plageHoraire]);
            } else {
                // Gestion des erreurs
            }
        }
    }




    public function show($id)
    {
       

        $plageHoraireModel = new PlageHoraire();
        $filmModel = new Film(); // Assure-toi d'avoir une instance de Film
        $plageHoraireMediaModel = new PlageHoraireMedia(); // Instance du modèle PlageHoraireMedia
        $plageHoraire = $plageHoraireModel->selectId($id);

        if (!$plageHoraire) {
            return Twig::render('error.twig', ['message' => 'Plage horaire non trouvée.']);
        }

        // Récupère les informations des médias pour cette plage horaire
        $medias = $plageHoraireMediaModel->getMediasByPlageHoraireId($id);

        // Récupère la date de la plage horaire
        $date = $plageHoraireMediaModel->getMediaForDate('date');

        // Récupère les informations des films pour ces médias
        $films = [];

        foreach ($medias as $key => $media) {
            if (isset($media['heure_debut']) && isset($media['duree_film'])) {
                $heureDebut = $media['heure_debut'];
                $dureeFilm = $media['duree_film'];

                // Calcule l'heure de fin
                $heureFin = $this->calculerHeureFin($heureDebut, $dureeFilm);
              

                // Ajoute l'heure de fin au tableau media
                $medias[$key]['heure_fin'] = $heureFin;

                // Récupère les informations du film correspondant
                $film = $filmModel->selectId($media['media_id']);
            }
            
        }
      
    

        return Twig::render('plageHoraire/show.twig', [
            'plageHoraire' => $plageHoraire,
            'medias' => $medias,
            'films' => $films
        ]);
    }









}







































