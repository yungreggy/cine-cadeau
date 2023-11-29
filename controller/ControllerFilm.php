<?php
RequirePage::model('CRUD');
RequirePage::model('Film');
RequirePage::model('Programmation');


class ControllerFilm extends Controller


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
        $filmModel = new Film;
        $films = $filmModel->select();

        // Vérifie si la liste des films est vide
        if (empty($films)) {
            // Affiche un message d'erreur si aucun film n'est trouvé
            return Twig::render('error.twig', ['message' => 'Aucun film disponible.']);
        }

        // Affiche la liste des films avec l'URL de l'image (lien_image) pour chaque film
        return Twig::render('film/index.twig', ['films' => $films]);
    }

    public function show($id)
    {

        $filmModel = new Film;
     


        $film = $filmModel->selectId($id);
        if (!$film) {
            RequirePage::url('error');
            return;
        }

       

        return Twig::render('film/show.twig', ['film' => $film]);
    }
    public function create()
    {
        // Afficher le formulaire de création de film
        return Twig::render('film/create.twig');
    }
    public function store()
    {
      

        $filmModel = new Film();
        $data = $_POST;

        // Validation et nettoyage des données ici

        // Insère le film dans la base de données
        $newFilmId = $filmModel->insert($data);

        if ($newFilmId) {
            // Rediriger vers la page de détails du film nouvellement ajouté
            RequirePage::url('film/show/' . $newFilmId);
        } else {
            // Gérer l'erreur d'insertion
            // Rediriger vers une page d'erreur ou afficher un message
        }
    }




    public function edit($id)
    {
        $filmModel = new Film;
        $film = $filmModel->selectId($id);

        if (!$film) {
            // Gérer le cas où le film n'est pas trouvé
        }

        // Afficher le formulaire d'édition avec les données du film
        return Twig::render('film/edit.twig', ['film' => $film]);
    }

    public function update($id)
    {
        $filmModel = new Film;
        $data = $_POST; // Récupère les données du formulaire

        // Validation et nettoyage des données ici
        // Assure-toi que les données sont sûres avant de continuer

        // Ajoute l'ID à l'ensemble des données
        $data['id'] = $id;

        // Mise à jour du film dans la base de données
        $result = $filmModel->update($data);

        if ($result) {
            // Rediriger vers la page de détails du film mis à jour
            RequirePage::url('film/index');
        } else {
            // Gérer l'erreur de mise à jour
            // Tu peux rediriger vers une page d'erreur ou afficher un message
        }
    }
    public function list()
    {
        $filmModel = new Film;

        // Récupère l'option de tri de la requête GET, avec une valeur par défaut
        $sortOption = $_GET['sort_by'] ?? 'titre_asc';

        // Obtient les films triés
        $films = $filmModel->getSortedFilms($sortOption);

        // Affiche la vue 'film/index' avec les films triés
        return Twig::render('film/index.twig', ['films' => $films]);
    }
    public function search()
    {
        $filmModel = new Film;
        $searchQuery = $_GET['search_query'] ?? '';

        // Effectue la recherche
        $films = $filmModel->searchFilms($searchQuery);

        // Affiche la vue 'film/index' avec les résultats de la recherche
        return Twig::render('film/index.twig', ['films' => $films, 'isSearch' => true]);
    }








}