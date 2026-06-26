<?php
$anciennete = $_SESSION['anciennete'] ?? '—';
?>
<link rel="stylesheet" href="assets/css/account.css">

<section class="account-section">
    <h1>Mon compte</h1>

    <div class="account-container">
        <div class="account-info">
            <img
                src="<?= !empty($_SESSION['avatar']) ? htmlspecialchars($_SESSION['avatar']) : 'assets/users/default-avatar.png' ?>"
                alt="Photo de profil"
                class="profile-pic">
            <div class="form-group">
                <label for="avatar">modifier</label>
                <input type="file" id="avatar" name="avatar" accept="image/png, image/jpeg" form="edit-form" />
            </div>
            <div class="separator"></div>
            <span class="username"><?= htmlspecialchars($user ? $user->getNickname() : ($_SESSION['pseudo'] ?? 'Utilisateur')) ?></span>
            <span class="member-since"> Membre depuis <?= htmlspecialchars($anciennete) ?> </span>
            <div class="library-block">
                <div class="lib-label">Bibliothèque</div>
                <div class="lib-count">📚 <?= htmlspecialchars($bookCount ?? 0) ?> livres</div>
            </div>
        </div>

        <div class="account-actions">
            <?php if (!empty($successMessage)): ?>
                <div class="alert alert-success"><?= htmlspecialchars($successMessage) ?></div>
            <?php endif; ?>
            <?php if (!empty($errorMessage)): ?>
                <div class="alert alert-error"><?= htmlspecialchars($errorMessage) ?></div>
            <?php endif; ?>
            <h2>Vos informations personnelles</h2>
            <form method="POST" action="index.php?action=updateProfile" enctype="multipart/form-data" id="edit-form">
                <div class="form-group">
                    <label for="email">Adresse email</label>
                    <input type="email" name="email" id="email" value="<?= htmlspecialchars($user ? $user->getMail() : ($_SESSION['email'] ?? '')) ?>" required>
                </div>

                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <input type="password" name="password" id="password" placeholder="••••••••">
                </div>

                <div class="form-group">
                    <label for="pseudo">Pseudo</label>
                    <input type="text" name="pseudo" id="pseudo" value="<?= htmlspecialchars($user ? $user->getNickname() : ($_SESSION['pseudo'] ?? '')) ?>" required>
                </div>

                <button class="btn btn-outline" type="submit">Enregistrer</button>
            </form>

        </div>
    </div>

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
            <?php if (!empty($books)): ?>
                <?php foreach ($books as $book): ?>
                    <tr>
                        <td>
                            <img src="assets/book/<?= htmlspecialchars($book->getImage()) ?>" alt="<?= htmlspecialchars($book->getTitre()) ?>" class="book-thumb">
                        </td>
                        <td><?= htmlspecialchars($book->getTitre()) ?></td>
                        <td><?= htmlspecialchars($book->getAuteur()) ?></td>
                        <td><?= htmlspecialchars(substr($book->getDescription(), 0, 100)) ?>...</td>
                        <td>
                            <?php if ($book->getDisponibilite() === 'disponible'): ?>
                                <span class="badge-dispo"><?= htmlspecialchars($book->getDisponibilite()) ?></span>
                            <?php else: ?>
                                <span class="badge-indispo"><?= htmlspecialchars($book->getDisponibilite()) ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="index.php?action=edit&id=<?= (int)$book->getId() ?>" class="btn-edit">Éditer</a>
                            <a href="index.php?action=delete&id=<?= (int)$book->getId() ?>" class="btn-delete" onclick="return confirm('Supprimer ce livre ?')">Supprimer</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" style="text-align:center; color:#aaa; padding: 2rem;">Aucun livre dans votre bibliothèque.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <a href="index.php?action=ajoutLivre" class="btn btn-primary addBook">Ajouter un livre</a>
</section>