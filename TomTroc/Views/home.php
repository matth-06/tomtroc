<?php
require_once 'Models/DBManager.php';

$dbManager = DBManager::getInstance();
$query = $dbManager->query("SELECT * FROM livre ORDER BY id DESC LIMIT 4;");
$results = $query->fetchAll();
?>

<section class="hero">
    <div class="hero-copy">
        <h1><?php echo 'Rejoignez nos <br />
        lecteurs passionnés'; ?></h1>
        <p class="hero-text">Donnez une nouvelle vie à vos livres en les échangeant avec d'autres amoureux de la lecture. Nous croyons en la magie du partage de connaissances et d'histoires à travers les livres.</p>
        <a href="index.php?action=livreEx" class="btn btn-primary">Découvrir</a>
    </div>
    <div class="hero-media">
        <img src="assets/home/hamza.svg" alt="Hamza" class="hero-image">
        <p class="hero-credit">Hamza</p>
    </div>
</section>

<section class="books-section">
    <h2>Les derniers livres ajoutés</h2>
    <div class="books-grid">
        <?php foreach ($results as $result): ?>
            <article class="book-card">
                <a href="index.php?action=showBook&id=<?= $result['id'] ?>">
                    <img src="<?= $result['image'] ?>" alt="<?= $result['titre'] ?>">
                    <div class="book-content">
                        <h3><?= $result['titre'] ?></h3>
                        <p class="book-author"><?= $result['auteur'] ?></p>
                        <p class="book-seller">Vendu par : <?= $result['propriétaire'] ?></p>
                    </div>
                </a>
            </article>
        <?php endforeach; ?>
    </div>
    <div class="books-actions">
        <a href="index.php?action=livreEx" class="btn btn-primary">Voir tous les livres</a>
    </div>
</section>

<section class="steps-section">
    <h2>Comment ça marche ?</h2>
    <p class="section-intro">Échanger des livres avec TomTroc c'est simple et <br />
        amusant ! Suivez ces étapes pour commencer :</p>
    <div class="steps-grid">
        <div class="step-card">Inscrivez-vous gratuitement sur notre plateforme.</div>
        <div class="step-card">Ajoutez les livres que vous souhaitez échanger à votre profil.</div>
        <div class="step-card">Parcourez les livres disponibles chez d'autres membres.</div>
        <div class="step-card">Proposez un échange et discutez avec d'autres passionnés de lecture.</div>
    </div>
    <div class="books-actions">
        <a href="index.php?action=livreEx" class="btn btn-outline">Voir tous les livres</a>
    </div>
</section>
<img src="assets/home/band.svg" alt="" class="band-graphic">
<section class="values-section">

    <div class="values-copy">
        <h2>Nos valeurs</h2>
        <p>Chez Tom Troc, nous mettons l'accent sur le partage, la découverte et la communauté. Nos valeurs sont ancrées dans notre passion pour les livres et notre désir de créer des liens entre les lecteurs. Nous croyons en la puissance des histoires pour rassembler les gens et inspirer des conversations enrichissantes.</p>
        <p>Notre association a été fondée avec une conviction profonde : chaque livre mérite d'être lu et partagé.</p>
        <p>Nous sommes passionnés par la création d'une plateforme conviviale qui permet aux lecteurs de se connecter, de partager leurs découvertes littéraires et d'échanger des livres qui attendent patiemment sur les étagères.</p>
        <p class="signature">L’équipe Tom Troc </p>
        <div class="values-graphic">
            <img src="assets/home/heart.svg" alt="" class="values-illustration">
        </div>
    </div>
</section>