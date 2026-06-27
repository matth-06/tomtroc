<div class="login">
    <form action="index.php?action=registerUser" method="post" class="foldedCorner">
        <div class="login-form">
            <h1>Inscription</h1>
            <?php if (!empty($errorMessage)): ?>
                <div class="alert alert-error"><?= htmlspecialchars($errorMessage) ?></div>
            <?php endif; ?>
            <label for="pseudo">Pseudo</label>
            <input type="text" name="pseudo" id="pseudo" value="<?= htmlspecialchars(isset($oldData) && $oldData instanceof SignupFormData ? $oldData->getPseudo() : '') ?>" required>
            <label for="email">Adresse email</label>
            <input type="email" name="email" id="email" value="<?= htmlspecialchars(isset($oldData) && $oldData instanceof SignupFormData ? $oldData->getEmail() : '') ?>" required>
            <label for="password">Mot de passe</label>
            <input type="password" name="password" id="password" required>
            <button class="btn btn-primary login-btn" type="submit">S'inscrire</button>
            <p>Déjà inscrit ? <a class="login-link" href="index.php?action=login">Connectez-vous</a></p>
        </div>
    </form>
    <div class="login-media">
        <img src="assets/login/logImg.svg" alt="signup" class="signup-image">
    </div>
</div>