<?php


class Film extends CRUD
{

    protected $table = 'films';

    protected $primaryKey = 'id';
    protected $fillable = ['titre', 'description', 'genre', 'realisateur', 'acteurs', 'duree', 'lien_image', 'lien_video', 'anne_originale'];

    public function getSortedFilms($sortOption)
    {
        // Détermine le critère de tri en fonction de l'option
        switch ($sortOption) {
            case 'annee_asc':
                $orderBy = 'annee_originale ASC';
                break;
            case 'annee_desc':
                $orderBy = 'annee_originale DESC';
                break;
            case 'id_asc':
                $orderBy = 'id ASC';
                break;
            case 'id_desc':
                $orderBy = 'id DESC';
                break;
            case 'titre_asc':
                $orderBy = 'titre ASC';
                break;
            case 'titre_desc':
                $orderBy = 'titre DESC';
                break;
            case 'genre_asc':
                $orderBy = 'genre ASC';
                break;
            case 'genre_desc':
                $orderBy = 'genre DESC';
                break;
            default:
                $orderBy = 'titre ASC'; // Tri par défaut
        }

        // Construit la requête SQL
        $sql = "SELECT * FROM films ORDER BY $orderBy";

        // Préparation et exécution de la requête
        $stmt = $this->prepare($sql);
        $stmt->execute();

        // Retourne les résultats
        return $stmt->fetchAll();
    }




    public function searchFilms($searchQuery)
    {
        // Préparer la requête SQL avec un opérateur LIKE pour la recherche dans le titre et la collection
        $sql = "SELECT * FROM films WHERE titre LIKE :searchQuery OR collection LIKE :searchQuery";

        // Ajoute des caractères joker (%) de chaque côté du terme de recherche
        $searchTerm = '%' . $searchQuery . '%';

        // Préparation de la requête
        $stmt = $this->prepare($sql);

        // Liaison du terme de recherche à la requête pour les deux critères
        $stmt->bindParam(':searchQuery', $searchTerm);

        // Exécution de la requête
        $stmt->execute();

        // Retourner les résultats
        return $stmt->fetchAll();
    }

    public function getByCriteres($criteres)
    {
        $sql = "SELECT * FROM films WHERE 1=1";

        if (!empty($criteres['categorie'])) {
            $sql .= " AND categorie = :categorie";
        }
        // Ajoute d'autres conditions en fonction des critères

        $stmt = $this->prepare($sql);

        if (!empty($criteres['categorie'])) {
            $stmt->bindParam(':categorie', $criteres['categorie'], PDO::PARAM_STR);
        }
        // Lie d'autres paramètres si nécessaire

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function mediaSelectionneAuHasard($criteres, $idProgramme, $plageHoraireModel)
    {
        // Récupérez les dates de programme
        $programmationModel = new Programmation();
        $datesProgramme = $programmationModel->selectProgrammeDates($idProgramme);

        // Assurez-vous que les dates de début et de fin sont disponibles
        if ($datesProgramme && isset($datesProgramme['date_debut'], $datesProgramme['date_fin'])) {
            $dateDebut = $datesProgramme['date_debut'];
            $dateFin = $datesProgramme['date_fin'];

            $sql = "SELECT * FROM films WHERE 1=1";
            // Ajoute des conditions basées sur les critères
            if (!empty($criteres['categorie'])) {
                $sql .= " AND categorie = :categorie";
            }
            $stmt = $this->prepare($sql);
            if (!empty($criteres['categorie'])) {
                $stmt->bindParam(':categorie', $criteres['categorie'], PDO::PARAM_STR);
            }
            $stmt->execute();
            $films = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Filtrer les films déjà programmés
            $filmsDisponibles = array_filter($films, function ($film) use ($plageHoraireModel, $dateDebut, $dateFin) {
                return !$plageHoraireModel->estMediaDejaProgramme('film', $film['id'], $dateDebut, $dateFin);
            });

            if (!empty($filmsDisponibles)) {
                return $filmsDisponibles[array_rand($filmsDisponibles)];
            }
        }

        return null;
    }

    public function estMediaDejaProgramme($mediaType, $mediaId, $dateDebut, $dateFin)
    {
        $sql = "SELECT COUNT(*) FROM plages_horaires_media 
                JOIN plages_horaires ON plages_horaires_media.plage_horaire_id = plages_horaires.id
                WHERE media_id = :mediaId 
                AND media_type = :mediaType 
                AND date_debut <= :dateFin 
                AND date_fin >= :dateDebut";

        $stmt = $this->prepare($sql);
        $stmt->bindParam(':mediaId', $mediaId, PDO::PARAM_INT);
        $stmt->bindParam(':mediaType', $mediaType, PDO::PARAM_STR);
        $stmt->bindParam(':dateDebut', $dateDebut);
        $stmt->bindParam(':dateFin', $dateFin);

        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    public function getDureeFilm($filmId)
    {
        $sql = "SELECT duree FROM films WHERE id = :filmId";
        $stmt = $this->prepare($sql);
        $stmt->bindParam(':filmId', $filmId, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['duree'] : null;
    }



}



    

