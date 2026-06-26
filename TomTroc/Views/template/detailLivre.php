<link rel="stylesheet" href="assets/css/D.css">
<article class="book-detail">
    <div class="image-detail">
        <img src="assets/book/<?= htmlspecialchars($book->getImage()) ?>" alt="<?= htmlspecialchars($book->getTitre()) ?>">
    </div>
    <div class="content-detail">
        <h1><?= htmlspecialchars($book->getTitre()) ?></h1>
        <p class="book-author">par <?= htmlspecialchars($book->getAuteur()) ?></p>
        <p class="book-description">
            <span class="section-label">Description</span>
            <?= htmlspecialchars($book->getDescription()) ?>
        </p>
        <p class="book-seller">
            <img src="<?= !empty($_SESSION['avatar']) ? htmlspecialchars($_SESSION['avatar']) : 'assets/users/default-avatar.png' ?>"
                alt="Photo de profil"
                class="owner-avatar">
            <?= htmlspecialchars($book->getProprietaire()) ?>
        </p>
        <a href="index.php?action=messagerie&user_id=<?= $book->getProprietaireId() ?>" class="btn btn-primary">Envoyer un message</a>
    </div>
</article>