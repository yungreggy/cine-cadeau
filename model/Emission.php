<?php



class Emission extends CRUD
{

    protected $table = 'emissions';

    protected $primaryKey = 'id';
    protected $fillable = ['titre', 'description', 'duree', 'genre', 'lien_image', 'lien_video', 'numero_episode', 'numero_saison', 'anne_originale', 'id_serie'];



    public function getBySerieId($serieId)
    {
        $sql = "SELECT * FROM emissions WHERE id_serie = :serieId";
        $stmt = $this->prepare($sql);
        $stmt->bindParam(':serieId', $serieId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insertWithSerieId($data)
    {
        // Assure-toi que la valeur de id_serie est null si elle est vide ou non définie
        $data['id_serie'] = isset($data['id_serie']) && $data['id_serie'] !== '' ? $data['id_serie'] : null;

        // Prépare ta requête d'insertion
        // Utilise la logique déjà présente dans ta méthode CRUD::insert, mais avec la gestion de id_serie
        $sql = "INSERT INTO emissions (titre, description, numero_episode, numero_saison, lien_video, id_serie) VALUES (:titre, :description, :numero_episode, :numero_saison, :lien_video, :id_serie)";

        $stmt = $this->prepare($sql);

        // Lie les valeurs (y compris id_serie) à la requête
        foreach ($data as $key => $value) {
            $stmt->bindValue(":$key", $value, $value === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        }

        // Exécute la requête
        return $stmt->execute();
    }

    public function getEpisodesBySerieId($serieId)
    {
        $sql = "SELECT * FROM emissions WHERE id_serie = :serieId ORDER BY numero_saison ASC, numero_episode ASC";
        $stmt = $this->prepare($sql);
        $stmt->bindParam(':serieId', $serieId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getByCriteres($criteres) {
        


}



}