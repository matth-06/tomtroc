<?php
?>

<div class="login">
    <form action="index.php?action=login" method="post" class="foldedCorner">
        <div class="login-form">
            <h2>Connexion</h2>
            <label for="login">Adresse email</label>
            <input type="email" name="login" id="login" required>
            <label for="password">Mot de passe</label>
            <input type="password" name="password" id="password" required>
            <a class="btn btn-primary login-btn">S'inscrire</a>
        </div>
        
    </form>
    <div class="login-media">
            <img src="assets/login/logImg.svg" alt="signup" class="signup-image">
        </div>