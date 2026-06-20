<?php

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/AuthService.php';
require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Models/Book.php';

class UserController extends Controller
{
    protected $authService;
    /**
     * UserController constructor.
     * Initializes the AuthService instance.
     */
    public function __construct()
    {
        $this->authService = new AuthService();
    }

    /**
     * Displays the login page.
     *
     * @param string|null $errorMessage Optional error message to display.
     * @return void
     */
    public function showLogin(?string $errorMessage = null): void
    {
        $title = 'Connexion';
        $this->render('template/login.php', compact('title', 'errorMessage'));
    }

    /**
     * Displays the signup page.
     *
     * @param string|null $errorMessage Optional error message to display.
     * @param array $oldData Optional old data to pre-fill the form.
     * @return void
     */
    public function showSignup(?string $errorMessage = null, array $oldData = []): void
    {
        $title = 'Inscription';
        $this->render('template/signup.php', compact('title', 'errorMessage', 'oldData'));
    }

    /**
     * Handles user registration.
     *
     * @param string $pseudo The user's nickname.
     * @param string $email The user's email address.
     * @param string $password The user's password.
     * @return void
     */
    public function register(string $pseudo, string $email, string $password): void
    {
        $email = trim($email);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->showSignup("L'adresse email n'est pas valide.", ['pseudo' => $pseudo, 'email' => $email]);
            return;
        }

        $result = User::create(trim($pseudo), $email, trim($password));

        if ($result === true) {
            header('Location: index.php?action=login');
            exit();
        }

        $this->showSignup($result, ['pseudo' => $pseudo, 'email' => $email]);
    }

    /**
     * Handles user login.
     *
     * @param string $email The user's email address.
     * @param string $password The user's password.
     * @return void
     */
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
        $_SESSION['anciennete'] = $this->calculateAnciennete($_SESSION['member_since']);

        header('Location: index.php?action=monCompte');
        exit();
    }

    /**
     * Calculates the user's account age based on the member_since date.
     *
     * @param string|null $memberSince The date the user joined.
     * @return string The formatted account age.
     */
    private function calculateAnciennete(?string $memberSince): string
    {
        if (!$memberSince) {
            return '—';
        }

        $start = new DateTime($memberSince);
        $today = new DateTime();
        $diff = $start->diff($today);

        return "{$diff->y} an(s) {$diff->m} mois {$diff->d} jour(s)";
    }

    /**
     * Displays the user's account page with their information and books.
     *
     * @param string|null $successMessage Optional success message to display.
     * @param string|null $errorMessage Optional error message to display.
     * @return void
     */
    public function account(?string $successMessage = null, ?string $errorMessage = null): void
    {
        $this->authService->ensureAuthenticated();

        $user = User::findById((int)$_SESSION['user_id']);
        $books = Book::findByUser((int)$_SESSION['user_id']);
        $bookCount = count($books);
        $title = 'Mon compte';

        $this->render('template/monCompte.php', compact('title', 'user', 'books', 'bookCount', 'successMessage', 'errorMessage'));
    }

    /**
     * Logs the user out.
     *
     * @return void
     */
    public function logout(): void
    {
        session_unset();
        session_destroy();
        header('Location: index.php?action=login');
        exit();
    }

    /**
     * Updates the user's profile information.
     *
     * @param array $post The POST data from the form submission.
     * @return void
     */
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
