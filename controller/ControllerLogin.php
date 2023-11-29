<?php

RequirePage::model('CRUD');
RequirePage::model('User');
RequirePage::library('Validation');
RequirePage::model('Log');

class ControllerLogin extends controller
{

    public function index()
    {
  
        if (isset($_SESSION['user_id'])) {
    
            RequirePage::url('home');
            exit();
        }

        Twig::render('auth/index.php');
    }

    public function auth()
    {
        $validation = new Validation;
        extract($_POST);
        $validation->name('username')->value($username)->max(50)->required()->pattern('alphanum');
        $validation->name('password')->value($password)->max(20)->min(6);

        if (!$validation->isSuccess()) {
            $errors = $validation->displayErrors();
            return Twig::render('auth/index.php', ['errors' => $errors, 'user' => $_POST]);
        }

        $user = new User;
        $sql = "SELECT * FROM users WHERE username = ?";
        $stmt = $user->prepare($sql);
        $stmt->execute(array($_POST['username']));
        $count = $stmt->rowCount();

        if ($count === 1) {
            $salt = "biO09Nh3";
            $saltPassword = $_POST['password'] . $salt;
            $info_user = $stmt->fetch();

            if (password_verify($saltPassword, $info_user['password'])) {
                session_regenerate_id();
                $_SESSION['user_id'] = $info_user['id'];
                $_SESSION['username'] = $info_user['username'];
                $_SESSION['privilege'] = $info_user['privilege_id'];
                $_SESSION['fingerPrint'] = md5($_SERVER['HTTP_USER_AGENT'] . $_SERVER['REMOTE_ADDR']);
                $_SESSION['profile_photo'] = $info_user['profile_photo'];

                // Enregistrement de l'action de connexion dans le journal de bord
                $log = new Log();
                $log->recordAction("Connexion", "L'utilisateur " . $_SESSION['username'] . " s'est connecté.", $_SESSION['user_id']);

                RequirePage::url('home');
            } else {
                $errors = "<ul><li>Verifier le mot de passe</li></ul>";
                return Twig::render('auth/index.php', ['errors' => $errors, 'user' => $_POST]);
            }
        } else {
            $errors = "<ul><li>Verifier le username</li></ul>";
            return Twig::render('auth/index.php', ['errors' => $errors, 'user' => $_POST]);
        }
    }



    public function logout()
    {
        $log = new Log();
        $log->recordAction("Déconnexion", "L'utilisateur " . $_SESSION['username'] . " s'est déconnecté.", $_SESSION['user_id']);

        session_destroy();
        RequirePage::url('login');
    }

}