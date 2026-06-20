<?php

class AuthService
{   
    /**
     * Vérifie si l'utilisateur est authentifié.
     * Si l'utilisateur n'est pas authentifié, redirige vers la page de connexion.
     */
    public function ensureAuthenticated(): void
    {
        if (empty($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit();
        }
    }
}
