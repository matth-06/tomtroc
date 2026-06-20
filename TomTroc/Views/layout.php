<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'TomTroc'; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <header>

        <div class="header-logo">
            <img src="assets/layout/logo.svg" alt="TomTroc Logo" class="logo">
        </div>
        <nav class="header-center">
            <a href="index.php?action=accueil">Accueil</a>
            <a href="index.php?action=livreEx">Nos livres à l'échange</a>
        </nav>
        <div class="header-right">
            <a href="index.php?action=messagerie">Messagerie</a>
            <a href="index.php?action=monCompte">Mon compte</a>
            <?php if (!empty($_SESSION['user_id'])): ?>
                <a href="index.php?action=logout">Déconnexion</a>
            <?php else: ?>
                <a href="index.php?action=login">Connexion</a>
            <?php endif; ?>
        </div>

    </header>
    <main>
        <?= $content ?>
    </main>
    <footer>
        <div class="footer-links">
            <p>Politique de confidentialité</p>
            <p>Mentions légales</p>
            <p>Tom Troc&copy;</p>
        </div>
        <div class="footer-logo">
            <img src="assets/layout/logof.svg" alt="TomTroc Logo" class="logo">
        </div>
    </footer>
</body>

</html>