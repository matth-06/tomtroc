<link rel="stylesheet" href="assets/css/D.css">
<article class="book-detail">
    <div class="image-detail">
        <img src="assets/book/<?= htmlspecialchars($book['image']) ?>" alt="<?= htmlspecialchars($book['titre']) ?>">
    </div>
    <div class="content-detail">
        <h1><?= htmlspecialchars($book['titre']) ?></h1>
        <p class="book-author">par <?= htmlspecialchars($book['auteur']) ?></p>
        <p class="book-description">
            <span class="section-label">Description</span>
            <?= htmlspecialchars($book['description']) ?>
        </p>
        <p class="book-seller">
            <img src="<?= !empty($_SESSION['avatar']) ? htmlspecialchars($_SESSION['avatar']) : 'assets/users/default-avatar.png' ?>"
                alt="Photo de profil"
                class="owner-avatar">
            <?= htmlspecialchars($book['propriétaire']) ?>
        </p>
        <a href="index.php?action=messagerie&user_id=<?= $book['propriétaireId'] ?>" class="btn btn-primary">Envoyer un message</a>
    </div>
</article>