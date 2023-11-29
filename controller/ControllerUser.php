<?php
RequirePage::model('CRUD');
RequirePage::model('User');
RequirePage::model('Privilege');
RequirePage::library('Validation');
RequirePage::model('Log');

class ControllerUser extends controller

{
    

    public function index()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['privilege'] != 1) {
            RequirePage::url('home');
            exit();
        }

        $user = new User;
        $users = $user->getAllUsersSortedById(); // Utilise la nouvelle fonction pour obtenir les utilisateurs triés par ID
        return Twig::render('user/index.php', ['users' => $users]);
    }

public function create()
{
    $log = new Log();

    // Enregistrement de l'action dans le journal de bord
    $action = 'Accès à la page de création d\'utilisateur';
    $details = 'Un utilisateur accède à la page de création d\'utilisateur.';
    $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null; // Utilise l'ID de l'utilisateur connecté ou null si non connecté
    $log->recordAction($action, $details, $userId);

    // Affichage de la page de création d'utilisateur
    $privilege = new Privilege;
    $privileges = $privilege->select(); // Récupérer tous les niveaux de privilège
    $defaultPrivilegeId = 2;
    $privilege_id = null; // Define the $privilege_id variable with a default value
    return Twig::render('user/create.php', ['privileges' => $privileges, 'privilege_id' => $privilege_id]);
}


    public function store()
    {
        $validation = new Validation;
        extract($_POST);
        $validation->name('username')->value($username)->max(50)->required()->pattern('alphanum');

        $validation->name('password')->value($password)->max(20)->min(6)->required();
        $validation->name('email')->value($email)->pattern('email')->required();
        $validation->name('privilege_id')->value($privilege_id ?? 2)->required();
        $log = new Log();

        // Enregistrement de l'action dans le journal de bord
        $action = 'Accès à la page de création d\'utilisateur';
        $details = 'Un utilisateur accède à la page de création d\'utilisateur et effectue des modifications.';
        $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
        $log->recordAction($action, $details, $userId);

        // Déplacer la définition de $select en dehors du bloc if pour l'utiliser dans toute la méthode
        $privilege = new Privilege;
        $select = $privilege->select();

        if (!$validation->isSuccess()) {
            $errors = $validation->displayErrors();
            return Twig::render('user/create.php', ['errors' => $errors, 'privileges' => $select, 'user' => $_POST]);
        }

        $user = new User;

        $options = ['cost' => 10];
        $passwordSalt = $password . "biO09Nh3";
        $hashedPassword = password_hash($passwordSalt, PASSWORD_BCRYPT, $options);

        // Set the default value of $privilege_id to 2 if it is not set
        if (!isset($privilege_id)) {
            $privilege_id = 2;
        }

        // Préparation des données pour l'insertion
        $userData = [
            'username' => $username,
            'password' => $hashedPassword,
            'email' => $email,
            'privilege_id' => $privilege_id
        ];

        try {
            $insert = $user->insert($userData);
            if ($insert) {
                header('Location: ' . PATH_DIR . 'user');
                exit();
            } else {
                return Twig::render('user/create.php', ['error' => 'Impossible de créer l\'utilisateur.', 'privileges' => $select, 'user' => $_POST]);
            }
        } catch (PDOException $e) {
            return Twig::render('user/create.php', ['error' => 'Erreur de base de données : ' . $e->getMessage(), 'privileges' => $select, 'user' => $_POST]);
        }
    }
    public function show($userId = null)
    {
        if (!$userId) {
            header('Location: ' . PATH_DIR . 'user');
            exit();
        }

        $user = new User;
        $userInfo = $user->selectId($userId);

        if ($userInfo) {
            // Si l'utilisateur connecté est l'utilisateur lui-même ou un admin
            if ($_SESSION['user_id'] == $userInfo['id'] || $_SESSION['privilege'] == 1) {
                return Twig::render('user/show.php', ['user' => $userInfo]);
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


    public function edit($userId)
    {
        $user = new User;
        $userInfo = $user->selectId($userId);
        $log = new Log();

        // Enregistrement de l'action dans le journal de bord
        $action = 'Accès à la page de modifications d\'un utilisateur';
        $details = 'Un utilisateur fait des modifications d\'un autre utilisateur ou lui même.';
        $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null; 
        $log->recordAction($action, $details, $userId);

        if ($userInfo) {
            // Si l'utilisateur connecté est l'utilisateur lui-même ou un admin
            if ($_SESSION['user_id'] == $userInfo['id'] || $_SESSION['privilege'] == 1) {
                $privilege = new Privilege;
                $privileges = $privilege->select();

                return Twig::render('user/edit.php', ['user' => $userInfo, 'privileges' => $privileges]);
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

    public function update($userId)
    {
        // Assurez-vous que l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . PATH_DIR . 'login');
            exit();
        }

        // Récupérer les informations de l'utilisateur à modifier
        $user = new User;
        $userInfo = $user->selectId($userId);

        // Rediriger si l'utilisateur n'existe pas
        if (!$userInfo) {
            header('Location: ' . PATH_DIR . 'user');
            exit();
        }

        $validation = new Validation;
        extract($_POST);
        $validation->name('username')->value($username)->max(50)->required();

        if ($_SESSION['privilege'] == 1) {
            // Administrateur : valider le privilège
            $validation->name('privilege_id')->value($privilege_id)->required();
        } else {
            // Utilisateur régulier : empêcher la modification du privilège
            if ($userId != $_SESSION['user_id']) {
                // Rediriger ou afficher un message d'erreur si l'utilisateur tente de modifier un autre profil
                header('Location: ' . PATH_DIR . 'user/show/' . $_SESSION['user_id']);
                exit();
            }
        }

        if (!$validation->isSuccess()) {
            $errors = $validation->displayErrors();
            $privilege = new Privilege;
            $select = $privilege->select();

            return Twig::render('user/edit.php', ['errors' => $errors, 'privileges' => $select, 'user' => $_POST, 'userId' => $userId]);
        }

        // Préparation des données pour la mise à jour
        $userData = [
            'id' => $userId,
            'username' => $username
        ];

        if ($_SESSION['privilege'] == 1 && isset($privilege_id)) {
            $userData['privilege_id'] = $privilege_id;
        }

        try {
            $update = $user->update($userData);
            if ($update) {
                header('Location: ' . PATH_DIR . 'user/show/' . $userId);
                exit();
            } else {
                return Twig::render('user/edit.php', ['error' => 'Impossible de mettre à jour l\'utilisateur.', 'privileges' => $select, 'user' => $_POST, 'userId' => $userId]);
            }
        } catch (PDOException $e) {
            return Twig::render('user/edit.php', ['error' => 'Erreur de base de données : ' . $e->getMessage(), 'privileges' => $select, 'user' => $_POST, 'userId' => $userId]);
        }
    }


 public function uploadProfilePhoto()
 {
     // Assurez-vous que l'utilisateur est connecté
     if (!isset($_SESSION['user_id'])) {
         RequirePage::url('login');
         exit();
     }
 
     $log = new Log();
 
     $action = 'Accès à la page de modifications d\'utilisateur';
     $details = 'Un utilisateur met à jour la photo de profile.';
     $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null; 
     $log->recordAction($action, $details, $userId);
 
     if (isset($_FILES['profile-photo']) && $_FILES['profile-photo']['error'] == 0) {
         // Effectuer la validation du fichier ici...
 
         $uploadDir = 'assets/uploads/';
         if (!file_exists($uploadDir)) {
             mkdir($uploadDir, 0777, true);
         }
         $fileName = uniqid() . '.' . pathinfo($_FILES['profile-photo']['name'], PATHINFO_EXTENSION);
 
         if (move_uploaded_file($_FILES['profile-photo']['tmp_name'], $uploadDir . $fileName)) {
             $user = new User();
             $userId = $_SESSION['user_id'];
             $user->updateProfilePhoto($userId, $uploadDir . $fileName);
 
             // Affecter la valeur à la variable de session
             $_SESSION['profile_photo'] = $uploadDir . $fileName;
             error_log("Chemin de la photo dans la session : " . $_SESSION['profile_photo']);
 
             RequirePage::url('user/show/' . $userId);
             exit();
         } else {
             echo "Une erreur s'est produite lors de l'upload du fichier.";
         }
     } else {
         echo "Aucun fichier n'a été sélectionné ou une erreur s'est produite lors de l'upload.";
     }
 }


    public function deleteProfilePhoto()
    {
        // Obtenir l'ID de l'utilisateur depuis la session
        $userId = $_SESSION['user_id'];
        $log = new Log();

        // Enregistrement de l'action dans le journal de bord
        $action = 'Accès à la page de modifications d\'utilisateur';
        $details = 'Un utilisateur supprime une photo de profile.';
        $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null; 
        $log->recordAction($action, $details, $userId);

        if (!isset($userId)) {
            // Rediriger vers la page de connexion si l'utilisateur n'est pas connecté
            RequirePage::url('login');
            exit();
        }

        $user = new User();
        $userInfo = $user->selectId($userId);

        if (!$userInfo || empty($userInfo['profile_photo'])) {
        
            RequirePage::url('home');
            exit();
        }

        $photoPath = $userInfo['profile_photo'];

        // Supprimer le fichier de la photo de profil du serveur
        if (file_exists($photoPath)) {
            unlink($photoPath);
        }

        $user->updateProfilePhoto($userId, '');

        // Supprimer l'information de la photo de profil de la session
        unset($_SESSION['profile_photo']);

        RequirePage::url('user/show/' . $userId);
    }



    public function destroy($userId)
    {
        // Vérifier si l'utilisateur connecté est un administrateur
        if ($_SESSION['privilege'] != 1) {
            // Si non-administrateur, rediriger vers une page d'erreur ou de connexion
            RequirePage::url('login');
            exit();
        }

        $user = new User();
        $log = new Log();

        // Procéder à la suppression de l'utilisateur
        if ($user->delete($userId)) {
            $log->recordAction('Suppression utilisateur', 'L\'utilisateur ID ' . $userId . ' a été supprimé', $_SESSION['user_id']);

            // Redirection vers la liste des utilisateurs avec un message de succès
            RequirePage::url('user/index');
        } else {
            // Gérer l'erreur de suppression
            // Afficher un message d'erreur ou rediriger vers une autre page
        }
    }



}