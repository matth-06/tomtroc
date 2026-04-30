<?php
    require_once 'Models/DBManager.php';

    $dbManager = DBManager::getInstance();
    // Si l'URL ne contient pas d'id, on redirige sur la page d'accueil
    if (empty($_GET['id'])) {
        header('Location: index.php');
    }
    $query = $dbManager->query("SELECT * FROM livre WHERE id = :id", ['id' => $_GET['id']]);
    $result = $query->fetch();
    if (!$result) {
        header('Location: index.php');
    }
?>

<article class="book-detail">
    <div class="image-detail">
        <img src="<?= $result['image'] ?>" alt="<?= $result['titre'] ?>">
    </div>
    <div class="content-detail">
        <h1><?= $result['titre'] ?></h1>
        <p class="book-author">par <?= $result['auteur'] ?></p>
        <p class="book-description">description : <br />
             <?= $result['description'] ?>
        </p>
        <p class="book-seller">Propriétaire <br /> <?= $result['propriétaire'] ?></p>
         <a class="btn btn-primary">Envoyer un message</a>
    </div>
</article>
