<?php
require_once 'Models/DBManager.php';

$dbManager = DBManager::getInstance();
$search = $_GET['search'] ?? '';
if ($search) {
    $query = $dbManager->query("SELECT * FROM livre WHERE titre LIKE :search OR auteur LIKE :search ORDER BY id DESC;", ['search' => "%$search%"]);
} else {
    $query = $dbManager->query("SELECT * FROM livre");
}
$results = $query->fetchAll();
?>
<div class="search-header">
    <h1>Nos livres à l'échange</h1>
    <form method="GET" class="search-bar">
        <input type="hidden" name="action" value="livreEx">
        <input type="text" name="search" placeholder="Rechercher un livre..." value="<?= htmlspecialchars($search) ?>">
    </form>
</div>
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