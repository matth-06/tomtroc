<?php

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/AuthService.php';
require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Models/UserRepository.php';
require_once __DIR__ . '/../Models/SignupFormData.php';
require_once __DIR__ . '/../Models/BookRepository.php';

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
    public function showSignup(?string $errorMessage = null, ?SignupFormData $oldData = null): void
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
            $this->showSignup("L'adresse email n'est pas valide.", new SignupFormData($pseudo, $email));
            return;
        }
         
        if (!preg_match('/^[a-zA-Z0-9_\-]{2,20}$/', $pseudo)) {
        $this->showSignup("Le pseudo doit contenir 2 à 20 caractères (lettres, chiffres, - ou _).", new SignupFormData($pseudo, $email));
        return;
        }

        $repository = new UserRepository();
        $result = $repository->create(trim($pseudo), $email, trim($password));

        if ($result === true) {
            header('Location: index.php?action=login');
            exit();
        }

        $this->showSignup($result, new SignupFormData($pseudo, $email));
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
        $repository = new UserRepository();
        $user = $repository->findByEmail(trim($email));

        if (!$user || !password_verify($password, $user->getPassword())) {
            $this->showLogin('Email ou mot de passe incorrect');
            return;
        }

        $_SESSION['user_id'] = $user->getId();
        $_SESSION['pseudo'] = $user->getNickname();
        $_SESSION['email'] = $user->getMail();
        $_SESSION['avatar'] = $user->getAvatar();
        $_SESSION['member_since'] = $user->getMemberSince();
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

        $repository = new UserRepository();
        $user = $repository->findById((int)$_SESSION['user_id']);
        $bookRepository = new BookRepository();
        $books = $bookRepository->findByUser((int)$_SESSION['user_id']);
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

        $repository = new UserRepository();
        $result = $repository->update((int)$_SESSION['user_id'], $newEmail, $newPseudo, $newPassword, $newAvatar);

        if ($result === true) {
            // Recharger l'utilisateur pour récupérer les champs mis à jour (avatar notamment)
            $repository = new UserRepository();
            $user = $repository->findById((int)$_SESSION['user_id']);
            $_SESSION['email'] = $user ? $user->getMail() : $newEmail;
            $_SESSION['pseudo'] = $user ? $user->getNickname() : $newPseudo;
            $_SESSION['avatar'] = $user ? $user->getAvatar() : ($_SESSION['avatar'] ?? null);
            $this->account('Vos informations ont bien été mises à jour.', null);
            return;
        }

        $this->account(null, $result);
    }
}
