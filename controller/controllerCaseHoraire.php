<?php
RequirePage::model('CRUD');
RequirePage::model('Film');
RequirePage::model('Programmation');
RequirePage::model('Serie');
RequirePage::model('Emission');
RequirePage::model('Pub');
RequirePage::model('Intermede');
RequirePage::model('Televiseur');
RequirePage::model('CaseHoraire');
RequirePage::model('PlageHoraire');
RequirePage::model('PlageHoraireMedia');



class ControllerCaseHoraire extends Controller {

    private function calculerHeureFin($heureDebut, $dureeFilm)
    {
        $heureDebutDateTime = new DateTime($heureDebut);
        $interval = new DateInterval('PT' . $dureeFilm . 'M');
        $heureDebutDateTime->add($interval);

        return $heureDebutDateTime->format('H:i:s');
    }

    public function index()
    {
        $caseHoraireModel = new CaseHoraire(); // Utilise le modèle CaseHoraire
        $casesHoraires = $caseHoraireModel->select(); // Méthode pour récupérer toutes les cases horaires

        if (empty($casesHoraires)) {
            return Twig::render('error.twig', ['message' => 'Aucune case horaire disponible.']);
        }

        return Twig::render('caseHoraire/index.twig', ['casesHoraires' => $casesHoraires]);
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
    

        return Twig::render('caseHoraire/create.twig', [
        'programmes' => $programmes,
        'films' => $films,
        'series' => $series,
        'emissions' => $emissions,
        'publicites' => $publicites,
        'intermedes' => $intermedes
    ]);
        
    }

    public function edit($date)
    {
        $plageHoraireModel = new PlageHoraire();
        $plagesHoraires = $plageHoraireModel->getByDate($date);

        $plageHoraireMedia = new PlageHoraireMedia();
        $plagesHorairesMedia = $plageHoraireMedia->getByDate($date);

        $plagesHoraires = array_merge($plagesHoraires, $plagesHorairesMedia);


        if (!$plagesHoraires) {
            return Twig::render('error.twig', ['message' => 'Aucune plage horaire trouvée pour cette date.']);
        }

        return Twig::render('caseHoraire/edit.twig', [
            'plagesHoraires' => $plagesHoraires,
            'date' => $date
        ]);
    }



    public function store()
    {
       
        
    }




    public function show($id)
    {












    }
}







































