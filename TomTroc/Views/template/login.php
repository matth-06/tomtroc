<?php
?>

<div class="signup-form">
    <form action="index.php?action=registerUser" method="post" class="foldedCorner">
        <h2>Inscription</h2>
        <div class="formGrid">
            <label for="login">Login</label>
            <input type="text" name="login" id="login" required>
            <label for="password">Mot de passe</label>
            <input type="password" name="password" id="password" required>
            <button class="submit">S'inscrire</button>
        </div>
    </form>