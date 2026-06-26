<section class="search-books">
    <?php $search = $search ?? ''; ?>
    <div class="search-header">
        <h1>Nos livres à l'échange</h1>
        <form method="GET" class="search-bar">
            <input type="hidden" name="action" value="livreEx">
            <input type="text" name="search" aria-label="Rechercher un livre" placeholder="Rechercher un livre..." value="<?= htmlspecialchars($search) ?>">
        </form>
    </div>
    <div class="books-grid">
        <?php foreach ($books as $book): ?>
            <article class="book-card">
                <a href="index.php?action=showBook&id=<?= $book->getId() ?>">
                    <img src="assets/book/<?= htmlspecialchars($book->getImage()) ?>" alt="<?= htmlspecialchars($book->getTitre()) ?>">
                    <div class="book-content">
                        <h2><?= htmlspecialchars($book->getTitre()) ?></h2>
                        <p class="book-author"><?= htmlspecialchars($book->getAuteur()) ?></p>
                        <p class="book-seller">Vendu par : <?= htmlspecialchars($book->getProprietaire()) ?></p>
                    </div>
                </a>
            </article>
        <?php endforeach; ?>
    </div>
</section>