<?php
RequirePage::model('CRUD');
RequirePage::library('CheckSession');
RequirePage::model('Log');
RequirePage::model('Privilege');


class ControllerLog extends controller
{

    public function __construct()
    {
        // Assurez-vous que seul l'administrateur peut accéder à cette page
        if ($_SESSION['privilege'] != 1) {
            RequirePage::url('login');
            exit();
        }
    }

    public function index()
    {
        // Vérification si l'utilisateur est un administrateur
        if ($_SESSION['privilege'] != 1) {
            RequirePage::url('home');
            exit();
        }

        $log = new Log();
        $logs = $log->selectAllByRecentDate();

        return Twig::render('log/index.php', ['logs' => $logs]);
    }
}

