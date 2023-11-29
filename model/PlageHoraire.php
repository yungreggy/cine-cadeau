<?php


class PlageHoraire extends CRUD {
    protected $table = 'plages_horaires'; // La table principale de cette classe
     protected $primaryKey = 'id';

    protected $fillable = ['id_programme', 'heure_debut', 'heure_fin', 'id_emission', 'id_film', 'id_publicite', 'id_intermede', 'id_film'];


    public function estMediaDejaProgramme($mediaType, $mediaId, $dateDebut, $dateFin)
    {
        $sql = "SELECT COUNT(*) FROM plages_horaires_media 
        JOIN plages_horaires ON plages_horaires_media.plage_horaire_id = plages_horaires.id
        JOIN programmes ON plages_horaires.id_programme = programmes.id
        WHERE media_id = :mediaId 
        AND media_type = :mediaType 
        AND programmes.date_debut <= :dateFin 
        AND programmes.date_fin >= :dateDebut";


        $stmt = $this->prepare($sql);
        $stmt->bindParam(':mediaId', $mediaId, PDO::PARAM_INT);
        $stmt->bindParam(':mediaType', $mediaType, PDO::PARAM_STR);
        $stmt->bindParam(':dateDebut', $dateDebut);
        $stmt->bindParam(':dateFin', $dateFin);

        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }
   


    public function getByProgrammeId($programmeId) {
        $sql = "SELECT * FROM plages_horaires WHERE id_programme = :programmeId";
        $stmt = $this->prepare($sql);
        $stmt->bindParam(':programmeId', $programmeId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }



    public function getByDate($date)
    {
        $sql = "SELECT * FROM plages_horaires WHERE DATE(heure_debut) = :date";
        $stmt = $this->prepare($sql);
        $stmt->bindParam(':date', $date, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }





    
}
