<?php

require_once '{{path}}model/Film.php'; // Assure-toi d'inclure les bons chemins

$filmModel = new Film();
$criteres = ['categorie' => 'long-metrage']; // Exemple de critère
$filmAuHasard = $filmModel->mediaSelectionneAuHasard('film', $criteres);



   