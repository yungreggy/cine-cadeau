<?php


class PlageHoraireMedia extends CRUD {
    protected $table = 'plages_horaires_media'; // La table principale de cette classe
     protected $primaryKey = 'id';

    protected $fillable = ['plage_horaire_id', 'media_id', 'media_type','date'];



    public function getMediasByPlageHoraireId($plageHoraireId)
    {
        $sql = "SELECT phm.media_id, phm.media_type, p.date_debut, p.date_fin, f.titre 
FROM plages_horaires_media phm
JOIN plages_horaires ph ON phm.plage_horaire_id = ph.id
JOIN programmes p ON ph.id_programme = p.id
LEFT JOIN films f ON phm.media_id = f.id AND phm.media_type = 'film'
WHERE phm.plage_horaire_id = :plageHoraireId;"
;
        $stmt = $this->prepare($sql);
        $stmt->bindParam(':plageHoraireId', $plageHoraireId, PDO::PARAM_INT);
        $stmt->execute();
        $resultat = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $resultat;
    }

    public function getMediaForDate($date)
    {
        $sql = "SELECT phm.*, f.titre, ph.heure_debut
            FROM plages_horaires_media phm
            JOIN films f ON phm.media_id = f.id AND phm.media_type = 'film'
            JOIN plages_horaires ph ON phm.plage_horaire_id = ph.id
            WHERE phm.date = :date";

        $stmt = $this->prepare($sql);
        $stmt->bindParam(':date', $date, PDO::PARAM_STR);
        $stmt->execute();
        $resultat = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $resultat;

    }

    public function getByProgrammeId($programmeId)
    {
        $sql = "SELECT * FROM plages_horaires WHERE id_programme = :programmeId";
        $stmt = $this->prepare($sql);
        $stmt->bindParam(':programmeId', $programmeId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function getMediasByProgrammeId($idProgramme)
    {
        $sql = "SELECT phm.*, f.titre, ph.heure_debut
                FROM plages_horaires_media phm
                JOIN plages_horaires ph ON phm.plage_horaire_id = ph.id
                JOIN programmes p ON ph.id_programme = p.id
                LEFT JOIN films f ON phm.media_id = f.id AND phm.media_type = 'film'
                WHERE p.id = :idProgramme";

        $stmt = $this->prepare($sql);
        $stmt->bindParam(':idProgramme', $idProgramme, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


  
    function trierMediasParHeureDebut(array $medias, $plageHoraireModel)
    {
        // Récupérer les heures de début pour chaque plage horaire
        foreach ($medias as &$media) {
            $plageHoraire = $plageHoraireModel->selectId($media['plage_horaire_id']);
            if ($plageHoraire) {
                $media['heure_debut_plage'] = $plageHoraire['heure_debut'];
            } else {
                $media['heure_debut_plage'] = '00:00:00'; // Heure par défaut si non trouvée
            }
        }
        unset($media); // Détruire la référence

        // Trier les médias par l'heure de début
        usort($medias, function ($a, $b) {
            return strcmp($a['heure_debut_plage'], $b['heure_debut_plage']);
        });

        return $medias;
    }

    public function getByDate($date)
    {
        $sql = 
        "SELECT phm.*, f.titre, ph.heure_debut
        FROM plages_horaires_media phm
        JOIN plages_horaires ph ON phm.plage_horaire_id = ph.id
        JOIN programmes p ON ph.id_programme = p.id
     LEFT JOIN films f ON phm.media_id = f.id AND phm.media_type = 'film'

        WHERE DATE = :date";
        $stmt = $this->prepare($sql);
        $stmt->bindParam(':date', $date, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


}