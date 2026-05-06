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
        $stmt = $pdo->prepare("SELECT id, password FROM user WHERE mail = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password'])) {
            return "Email ou mot de passe incorrect";
        }

        // Démarrer la session et stocker l'ID de l'utilisateur
        session_start();
        $_SESSION['user_id'] = $user['id'];

        return true;
    }
}
?>