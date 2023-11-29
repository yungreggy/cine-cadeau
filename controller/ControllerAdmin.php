<?php

RequirePage::model('CRUD');
RequirePage::model('User');
RequirePage::model('Privilege');
RequirePage::library('Validation');
RequirePage::model('Log');

class ControllerAdmin extends controller
{

    public function index()
    {

        if (!isset($_SESSION['user_id']) || $_SESSION['privilege'] != 1) {
            // Si l'utilisateur n'est pas un admin, rediriger vers la page d'accueil ou de profil
            RequirePage::url('home');
            exit();
        }

        $user = new User;
        $select = $user->select('username');
        return Twig::render('user/index.php', ['users' => $select]);
    }



    public function create()
    {
        // Assurez-vous que seul un administrateur peut accéder à cette méthode
        if ($_SESSION['privilege'] != 1) {
            header('Location: ' . PATH_DIR . 'login');
            exit();
        }

        $privilege = new Privilege;
        $privileges = $privilege->select(); // Récupérer tous les niveaux de privilège

        return Twig::render('admin/create.php', ['privileges' => $privileges]);
    }

    public function store()
    {
        // Assurez-vous que seul un administrateur peut exécuter cette méthode
        if ($_SESSION['privilege'] != 1) {
            header('Location: ' . PATH_DIR . 'login');
            exit();
        }

        $validation = new Validation;
        extract($_POST);
        $validation->name('username')->value($username)->max(50)->required()->pattern('alphanum');
        $validation->name('password')->value($password)->max(20)->min(6)->required();
        $validation->name('email')->value($email)->pattern('email')->required();
        $validation->name('privilege_id')->value($privilege_id)->required();

        if (!$validation->isSuccess()) {
            $errors = $validation->displayErrors();
            $privilege = new Privilege;
            $select = $privilege->select();

            return Twig::render('admin/create.php', ['errors' => $errors, 'privileges' => $select]);
        }

        $user = new User;
        $options = ['cost' => 10];
        $passwordSalt = $password . "votreSaltPersonnalisé";
        $hashedPassword = password_hash($passwordSalt, PASSWORD_BCRYPT, $options);

        $userData = [
            'username' => $username,
            'password' => $hashedPassword,
            'email' => $email,
            'privilege_id' => $privilege_id
        ];

        try {
            $insert = $user->insert($userData);
            if ($insert) {
                header('Location: ' . PATH_DIR . 'user/index');
                exit();
            } else {
                return Twig::render('admin/create.php', ['error' => 'Impossible de créer l\'utilisateur.', 'privileges' => $select]);
            }
        } catch (PDOException $e) {
            return Twig::render('admin/create.php', ['error' => 'Erreur de base de données : ' . $e->getMessage(), 'privileges' => $select]);
        }


    }

    public function update($userId)
    {
        $validation = new Validation;
        extract($_POST);
        $validation->name('username')->value($username)->max(50)->required();

        // Initialisez $select ici avant de l'utiliser dans le Twig::render
        $privilege = new Privilege;
        $select = $privilege->select();

        // Vérifier et valider le privilège si l'utilisateur est un administrateur
        if ($_SESSION['privilege'] == 1) {
            $validation->name('privilege_id')->value($privilege_id)->required();
        }

        if (!$validation->isSuccess()) {
            $errors = $validation->displayErrors();
            return Twig::render('admin/edit.php', ['errors' => $errors, 'privileges' => $select, 'user' => $_POST, 'userId' => $userId]);
        }

        $user = new User;

        // Préparation des données pour la mise à jour
        $userData = ['id' => $userId, 'username' => $username];

        // Ajouter le privilège uniquement si l'utilisateur est un admin
        if ($_SESSION['privilege'] == 1 && isset($privilege_id)) {
            $userData['privilege_id'] = $privilege_id;
        }

        try {
            $update = $user->update($userData);
            if ($update) {
                header('Location: ' . PATH_DIR . 'admin/show/' . $userId);
                exit();
            } else {
                return Twig::render('admin/edit.php', ['error' => 'Impossible de mettre à jour l\'utilisateur.', 'privileges' => $select, 'user' => $_POST, 'userId' => $userId]);
            }
        } catch (PDOException $e) {
            return Twig::render('admin/edit.php', ['error' => 'Erreur de base de données : ' . $e->getMessage(), 'privileges' => $select, 'user' => $_POST, 'userId' => $userId]);
        }
    }

    public function edit($userId)
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['privilege'] != 1) {
            // Si l'utilisateur n'est pas un admin, rediriger vers la page d'accueil ou de profil
            RequirePage::url('home');
            exit();
        }
        $user = new User;
        $userInfo = $user->selectId($userId);
        $log = new Log();

        // Enregistrement de l'action dans le journal de bord
        $action = 'Accès à la page de modifications d\'un utilisateur';
        $details = 'Un administrateur utilisateur fait des modifications d\'un autre utilisateur ou lui même.';
        $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null; // Utilise l'ID de l'utilisateur connecté ou null si non connecté
        $log->recordAction($action, $details, $userId);

        if ($userInfo) {
            // Si l'utilisateur connecté est l'utilisateur lui-même ou un admin
            if ($_SESSION['user_id'] == $userInfo['id'] || $_SESSION['privilege'] == 1) {
                $privilege = new Privilege;
                $privileges = $privilege->select();

                return Twig::render('admin/edit.php', ['user' => $userInfo, 'privileges' => $privileges]);
            } else {
                // Redirection si l'utilisateur n'a pas les droits
                RequirePage::url('home');
                exit();
            }
        } else {
            header('Location: ' . PATH_DIR . 'user');
            exit();
        }
    }

    public function show($userId = null)
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['privilege'] != 1) {
            // Si l'utilisateur n'est pas un admin, rediriger vers la page d'accueil ou de profil
            RequirePage::url('home');
            exit();
        }

        $user = new User;
        $userInfo = $user->selectId($userId);

        if ($userInfo) {
            // Si l'utilisateur connecté est l'utilisateur lui-même ou un admin
            if ($_SESSION['user_id'] == $userInfo['id'] || $_SESSION['privilege'] == 1) {
                return Twig::render('admin/show.php', ['user' => $userInfo]);
            } else {
                // Redirection si l'utilisateur n'a pas les droits
                RequirePage::url('home');
                exit();
            }
        } else {
            header('Location: ' . PATH_DIR . 'user');
            exit();
        }
    }

    public function updatePrivilege($userId)
    {
        // Assurez-vous que seul un administrateur puisse accéder à cette fonction
        if ($_SESSION['privilege'] != 1) {
            header('Location: ' . PATH_DIR . 'home');
            exit();
        }

        // Valider l'ID de l'utilisateur et le nouveau privilège
        $validation = new Validation();
        $validation->name('userId')->value($userId)->pattern('int')->required();
        $validation->name('privilege_id')->value($_POST['privilege_id'])->pattern('int')->required();

        if (!$validation->isSuccess()) {
            $errors = $validation->displayErrors();
            // Gérer les erreurs ici
            return Twig::render('user/edit.php', ['errors' => $errors]);
        }

        // Mise à jour du privilège dans la base de données
        $user = new User();
        $updateSuccess = $user->update(['id' => $userId, 'privilege_id' => $_POST['privilege_id']]);

        if ($updateSuccess) {
            // Enregistrer l'action dans le journal
            $log = new Log();
            $log->recordAction('Modification de privilège', 'L\'administrateur a modifié le privilège de l\'utilisateur ID ' . $userId . ' en ' . $_POST['privilege_id'], $_SESSION['user_id']);

            // Redirection vers la liste des utilisateurs après la mise à jour
            header('Location: ' . PATH_DIR . 'user');
            exit();
        } else {
            // Gérer l'erreur de mise à jour
            return Twig::render('admin/edit.php', ['error' => 'Impossible de mettre à jour le privilège.']);
        }
    }
}




