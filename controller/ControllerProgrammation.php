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


class ControllerProgrammation extends Controller
{


    public function index()
    {
        $programmationModel = new Programmation();
        $programmes = $programmationModel->select();




        if (empty($programmes)) {
            return Twig::render('error.twig', ['message' => 'Aucune programmation disponible.']);
        }

        return Twig::render('programmation/index.twig', ['programmes' => $programmes]);
    }



    public function create()
    {
        $programmeModel = new Programmation();
        $programmes = $programmeModel->select();

        return Twig::render('programmation/create.twig', ['programmes' => $programmes]);
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nomProgramme = $_POST['nom'] ?? '';
            $dateDebut = $_POST['date_debut'] ?? '';
            $dateFin = $_POST['date_fin'] ?? '';

            // Validation des données ici
            // ...

            // Création d'un nouveau programme
            $programmationModel = new Programmation();
            $nouveauProgramme = [
                'nom' => $nomProgramme,
                'date_debut' => $dateDebut,
                'date_fin' => $dateFin,
                // Ajoute d'autres champs nécessaires
            ];

            if ($programmationModel->insert($nouveauProgramme)) {
                // Redirection ou gestion de la réussite
                RequirePage::url('programmation/index');
            } else {
                // Gestion des erreurs
                // ...
            }
        }
    }
    public function show($idProgramme)
    {
        $programmationModel = new Programmation();
        $programme = $programmationModel->selectId($idProgramme);

        if (!$programme) {
            return Twig::render('error.twig', ['message' => 'Programme non trouvé.']);
        }

        $plageHoraireMediaModel = new PlageHoraireMedia();
        $filmsParDate = [];

        $plagesHoraires = $plageHoraireMediaModel->getMediasByProgrammeId($idProgramme);

        foreach ($plagesHoraires as $plage) {
            $date = new DateTime($plage['date']);
            $dateStr = $date->format('Y-m-d');

            if (!isset($filmsParDate[$dateStr])) {
                $filmsParDate[$dateStr] = [];
            }

            $filmsParDate[$dateStr][] = $plage;
        }

        // Tri des films par heure de début pour chaque date
        foreach ($filmsParDate as &$filmsDuJour) {
            usort($filmsDuJour, function ($a, $b) {
                return strcmp($a['heure_debut'], $b['heure_debut']);
            });
        }
        unset($filmsDuJour); // Détruire la référence

        return Twig::render('programmation/show.twig', [
            'programme' => $programme,
            'filmsParDate' => $filmsParDate
        ]);
    }







}





    
