<?php

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Models/Book.php';

class UserController extends Controller
{
    public function showLogin(?string $errorMessage = null): void
    {
        $title = 'Connexion';
        $this->render('template/login.php', compact('title', 'errorMessage'));
    }

    public function showSignup(?string $errorMessage = null, array $oldData = []): void
    {
        $title = 'Inscription';
        $this->render('template/signup.php', compact('title', 'errorMessage', 'oldData'));
    }

    public function register(string $pseudo, string $email, string $password): void
    {
        $result = User::create(trim($pseudo), trim($email), trim($password));

        if ($result === true) {
            header('Location: index.php?action=login');
            exit();
        }

        $this->showSignup($result, ['pseudo' => $pseudo, 'email' => $email]);
    }

    public function connect(string $email, string $password): void
    {
        $user = User::findByEmail(trim($email));

        if (!$user || !password_verify($password, $user['password'])) {
            $this->showLogin('Email ou mot de passe incorrect');
            return;
        }

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['pseudo'] = $user['nickname'];
        $_SESSION['email'] = $user['mail'];
        $_SESSION['avatar'] = $user['avatar'] ?? null;
        $_SESSION['member_since'] = $user['member_since'] ?? null;

        header('Location: index.php?action=monCompte');
        exit();
    }

    public function account(?string $successMessage = null, ?string $errorMessage = null): void
    {
        if (empty($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit();
        }

        $user = User::findById((int)$_SESSION['user_id']);
        $books = Book::findByUser((int)$_SESSION['user_id']);
        $bookCount = count($books);
        $title = 'Mon compte';

        $this->render('template/monCompte.php', compact('title', 'user', 'books', 'bookCount', 'successMessage', 'errorMessage'));
    }

    public function updateProfile(array $post): void
    {
        if (empty($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit();
        }

        $newEmail = trim($post['email'] ?? '');
        $newPseudo = trim($post['pseudo'] ?? '');
        $newPassword = trim($post['password'] ?? '');
        $newAvatar = $_FILES['avatar'] ?? null;

        if (empty($newEmail) || empty($newPseudo)) {
            $this->account(null, "L'email et le pseudo sont obligatoires.");
            return;
        }

        if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
            $this->account(null, "L'adresse email n'est pas valide.");
            return;
        }

        $result = User::update((int)$_SESSION['user_id'], $newEmail, $newPseudo, $newPassword, $newAvatar);

        if ($result === true) {
            // Recharger l'utilisateur pour récupérer les champs mis à jour (avatar notamment)
            $user = User::findById((int)$_SESSION['user_id']);
            $_SESSION['email'] = $user['mail'] ?? $newEmail;
            $_SESSION['pseudo'] = $user['nickname'] ?? $newPseudo;
            $_SESSION['avatar'] = $user['avatar'] ?? ($_SESSION['avatar'] ?? null);
            $this->account('Vos informations ont bien été mises à jour.', null);
            return;
        }

        $this->account(null, $result);
    }
}
