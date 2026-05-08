<?php 
require_once 'Models/DBManager.php';

class UserController {
    public function registerUser($pseudo, $email, $password) {
        $dbManager = DBManager::getInstance();
        $pdo = $dbManager->getPDO();

        // Vérifier si utilisateur existe
        $stmt = $pdo->prepare("SELECT id FROM user WHERE mail = ? OR nickname = ?");
        $stmt->execute([$email, $pseudo]);

        if ($stmt->fetch()) {
            return "Utilisateur déjà existant";
        }

        // Hash du mot de passe
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insertion
        $stmt = $pdo->prepare("INSERT INTO user (nickname, mail, password) VALUES (?, ?, ?)");
        $stmt->execute([$pseudo, $email, $hashedPassword]);

        return true;
    }
    
    public function connectUser($email, $password) {
        $dbManager = DBManager::getInstance();
        $pdo = $dbManager->getPDO();

        // Récupérer l'utilisateur
        $stmt = $pdo->prepare("SELECT id, nickname, mail, password FROM user WHERE mail = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password'])) {
            return "Email ou mot de passe incorrect";
        }

        // Démarrer la session et stocker l'ID de l'utilisateur
        session_start();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['pseudo'] = $user['nickname'];
        $_SESSION['email'] = $user['mail'];
        $_SESSION['password'] = $password;

        return true;
    }

    public function getBookById($userId) {
        $dbManager = DBManager::getInstance();
        $pdo = $dbManager->getPDO();

        $stmt = $pdo->prepare("SELECT * FROM livre WHERE propriétaireid = (SELECT id FROM user WHERE id = ?)");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

     public function getBookCountByUserId($userId) {
        $dbManager = DBManager::getInstance();
        $pdo = $dbManager->getPDO();
 
        $stmt = $pdo->prepare("SELECT COUNT(*) AS total FROM livre WHERE propriétaireid = ?");
        $stmt->execute([$userId]);
        $result = $stmt->fetch();
        return (int) $result['total'];
    }

    public function updateUser($userId, $newEmail, $newPseudo, $newPassword) {
        $dbManager = DBManager::getInstance();
        $pdo = $dbManager->getPDO();
 
        // Vérifier que l'email ou le pseudo ne sont pas déjà pris par un autre utilisateur
        $stmt = $pdo->prepare("SELECT id FROM user WHERE (mail = ? OR nickname = ?) AND id != ?");
        $stmt->execute([$newEmail, $newPseudo, $userId]);
        if ($stmt->fetch()) {
            return "Cet email ou pseudo est déjà utilisé par un autre compte.";
        }
 
        // Mise à jour email + pseudo
        $stmt = $pdo->prepare("UPDATE user SET mail = ?, nickname = ? WHERE id = ?");
        $stmt->execute([$newEmail, $newPseudo, $userId]);
        $stmt = $pdo->prepare("UPDATE livre SET propriétaire = ? WHERE propriétaireid = ?");
        $stmt->execute([$newPseudo, $userId]);
 
        // Mise à jour du mot de passe uniquement si un nouveau est fourni
        if (!empty($newPassword)) {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE user SET password = ? WHERE id = ?");
            $stmt->execute([$hashedPassword, $userId]);
        }
 
        // Mettre à jour la session avec les nouvelles valeurs
        $_SESSION['email']  = $newEmail;
        $_SESSION['pseudo'] = $newPseudo;
 
        return true;
    }
}
?>