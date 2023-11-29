<?php

class User extends CRUD
{
    protected $table = 'users';
    protected $primaryKey = 'id';

    protected $fillable = ['username', 'password', 'email', 'privilege_id', 'date_registered', 'profile_photo'];

    public function checkUser($username, $password = null)
    {
        $sql = "SELECT * FROM $this->table WHERE username = ?";
        $stmt = $this->prepare($sql);
        $stmt->execute(array($username));
        $count = $stmt->rowCount();

        if ($count === 1) {
            $info_user = $stmt->fetch();

            if ($password != null) {
                $salt = "biO09Nh3"; 
                $saltedPassword = $password . $salt;

                if (password_verify($saltedPassword, $info_user['password'])) {
                    session_regenerate_id();
                    $_SESSION['user_id'] = $info_user['id'];
                    $_SESSION['username'] = $info_user['username'];
                    $_SESSION['privilege'] = $info_user['privilege_id'];
                    $_SESSION['fingerPrint'] = md5($_SERVER['HTTP_USER_AGENT'] . $_SERVER['REMOTE_ADDR']);
                    $_SESSION['profile_photo'] = $info_user['profile_photo']; 

                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    public function getUserInfo($username)
    {
        $sql = "SELECT * FROM $this->table WHERE username = ?";
        $stmt = $this->prepare($sql);
        $stmt->execute(array($username));
        return $stmt->fetch();
    }


    public function updateProfilePhoto($userId, $photoPath)
    {
        try {
            $sql = "UPDATE users SET profile_photo = ? WHERE id = ?";
            $stmt = $this->prepare($sql);
            $stmt->execute([$photoPath, $userId]);

            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Erreur lors de la mise à jour du profil : " . $e->getMessage());
            return false;
        }
    }

    public function getAllUsersSortedById()
    {
        $sql = "SELECT * FROM users ORDER BY id ASC"; 
        $stmt = $this->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    }


    public function updateUserInfo($userId, $userInfo)
    {
        try {
            $sql = "UPDATE users SET username = ?, email = ?, privilege_id = ? WHERE id = ?";
            $stmt = $this->prepare($sql);
            $stmt->execute([
                $userInfo['username'],
                $userInfo['email'],
                $userInfo['privilege_id'],
                $userId
            ]);

            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Erreur lors de la mise à jour de l'utilisateur : " . $e->getMessage());
            return false;
        }
    }



}