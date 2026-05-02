<?php
?>

<div class="login">
    <form action="index.php?action=login" method="post" class="foldedCorner">
        <div class="login-form">
            <h2>Inscription</h2>
            <label for="login">Pseudo</label>
            <input type="text" name="login" id="login" required>
            <label for="email">Adresse email</label>
            <input type="email" name="email" id="email" required>
            <label for="password">Mot de passe</label>
            <input type="password" name="password" id="password" required>
            <a class="btn btn-primary login-btn">S'inscrire</a>
            <p>Déjà inscrit ? <a class="login-link" href="index.php?action=login">Connectez-vous</a></p>
            
        </div>
        
    </form>
    <div class="login-media">
            <img src="assets/login/logImg.svg" alt="signup" class="signup-image">
        </div>