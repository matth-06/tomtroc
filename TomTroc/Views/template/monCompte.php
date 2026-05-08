<?php
session_start();
require_once __DIR__ . '/../../Models/DBManager.php';
require_once __DIR__ . '/../../Controllers/UserController.php';

$userController = new UserController();
$successMessage = null;
$errorMessage   = null;

// ──────────────────────────────────────────
// Traitement du formulaire (POST)
// ──────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {

    $newEmail    = trim($_POST['email']    ?? '');
    $newPseudo   = trim($_POST['pseudo']   ?? '');
    $newPassword = trim($_POST['password'] ?? '');

    // Validation basique
    if (empty($newEmail) || empty($newPseudo)) {
        $errorMessage = "L'email et le pseudo sont obligatoires.";
    } elseif (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
        $errorMessage = "L'adresse email n'est pas valide.";
    } else {
        $result = $userController->updateUser(
            $_SESSION['user_id'],
            $newEmail,
            $newPseudo,
            $newPassword
        );

        if ($result === true) {
            $successMessage = "Vos informations ont bien été mises à jour.";
        } else {
            $errorMessage = $result; // message d'erreur retourné par le controller
        }
    }
}
?>

<link rel="stylesheet" href="assets/css/account.css">

<section class="account-section">
    <h1>Mon compte</h1>

    <div class="account-container">

        <!-- Bloc gauche : info profil -->
        <div class="account-info">
            <img
                src="<?= !empty($_SESSION['avatar']) ? htmlspecialchars($_SESSION['avatar']) : 'assets/layout/default-avatar.png' ?>"
                alt="Photo de profil"
                class="profile-pic">
            <a href="#">modifier</a>

            <div class="separator"></div>

            <span class="username"><?= htmlspecialchars($_SESSION['pseudo'] ?? 'Utilisateur') ?></span>
            <span class="member-since">Membre depuis <?= htmlspecialchars($_SESSION['member_since'] ?? '—') ?></span>

            <div class="library-block">
                <div class="lib-label">Bibliothèque</div>
                <div class="lib-count">
                    📚 <?= $userController->getBookCountByUserId($_SESSION['user_id']) ?> livres
                </div>
            </div>
        </div>

        <!-- Bloc droite : formulaire -->
        <div class="account-actions">
            <h2>Vos informations personnelles</h2>
            <!-- Messages de retour -->
            <?php if ($successMessage): ?>
                <div class="alert alert-success"><?= htmlspecialchars($successMessage) ?></div>
            <?php endif; ?>
            <?php if ($errorMessage): ?>
                <div class="alert alert-error"><?= htmlspecialchars($errorMessage) ?></div>
            <?php endif; ?>

            <form method="POST" action="">

                <div class="form-group">
                    <label for="email">Adresse email</label>
                    <input type="email" name="email" id="email" value="<?= htmlspecialchars($_SESSION['email'] ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <input type="password" name="password" id="password" value="<?= htmlspecialchars($_SESSION['password'] ?? '') ?>" placeholder="••••••••" required>
                </div>

                <div class="form-group">
                    <label for="pseudo">Pseudo</label>
                    <input type="text" name="pseudo" id="pseudo" value="<?= htmlspecialchars($_SESSION['pseudo'] ?? '') ?>" required>
                </div>

                <button class="btn btn-outline" type="submit" name="update_profile">Enregistrer</button>
        </div>

    </div>

    <!-- Table bibliothèque -->
    <table class="account-table">
        <thead>
            <tr>
                <th>PHOTO</th>
                <th>TITRE</th>
                <th>AUTEUR</th>
                <th>DESCRIPTION</th>
                <th>DISPONIBILITÉ</th>
                <th>ACTION</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $userBooks = [];
            $userId = $_SESSION['user_id'] ?? 0;
            if ($userId > 0) {
                $userController = new UserController();
                if (method_exists($userController, 'getBookById')) {
                    $userBooks = $userController->getBookById($userId);
                }
            }
            if (!empty($userBooks)):
                foreach ($userBooks as $book): ?>
                    <tr>
                        <td>
                            <img
                                src="<?= htmlspecialchars($book['image'] ?? 'assets/layout/default-book.png') ?>"
                                alt="<?= htmlspecialchars($book['titre']) ?>"
                                class="book-thumb">
                        </td>
                        <td><?= htmlspecialchars($book['titre']) ?></td>
                        <td><?= htmlspecialchars($book['auteur']) ?></td>
                        <td><?= htmlspecialchars($book['description']) ?></td>
                        <td>
                            <span class="badge-dispo">
                                <?= htmlspecialchars($book['disponibilite'] ?? 'disponible') ?>
                            </span>
                        </td>
                        <td>
                            <a href="?action=edit&id=<?= (int)$book['id'] ?>" class="btn-edit">Éditer</a>
                            <a href="?action=delete&id=<?= (int)$book['id'] ?>" class="btn-delete"
                                onclick="return confirm('Supprimer ce livre ?')">Supprimer</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" style="text-align:center; color:#aaa; padding: 2rem;">
                        Aucun livre dans votre bibliothèque.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

</section>