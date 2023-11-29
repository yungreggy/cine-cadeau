<?php


class Programmation extends CRUD {
    protected $table = 'programmes'; // La table principale de cette classe
     protected $primaryKey = 'id';

    protected $fillable = ['nom', 'type', 'description', 'date_debut', 'date_fin'];



    // Dans votre modèle de Programme (Programmation.php par exemple)
    public function selectProgrammeDates($idProgramme)
    {
        $sql = "SELECT date_debut, date_fin FROM programmes WHERE id = :idProgramme";

        $stmt = $this->prepare($sql);
        $stmt->bindParam(':idProgramme', $idProgramme, PDO::PARAM_INT);

        if ($stmt->execute()) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            return false; // Gestion des erreurs si la requête échoue
        }
    }











}
