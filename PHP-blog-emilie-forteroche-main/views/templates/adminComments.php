<?php
    /**
     * Affichage de la page de gestion des commentaires d'un article en admin.
     */
?>

<h2>Commentaires de l'article : <?= htmlspecialchars($article->getTitle()) ?></h2>

<a class="submit" href="index.php?action=monitoring">Retour au monitoring</a>

<div class="adminArticle">
    <?php if (count($comments) === 0): ?>
        <p>Aucun commentaire pour cet article.</p>
    <?php else: ?>
        <?php foreach ($comments as $comment): ?>
            <div class="articleLine monitoringLine">
                <div class="title"><?= htmlspecialchars($comment->getPseudo()) ?></div>
                <div class="content"><?= nl2br(htmlspecialchars($comment->getContent())) ?></div>
                <div class="content"><?= $comment->getDateCreation()->format('d/m/Y H:i') ?></div>
                <div class="content">
                    <a class="submit" href="index.php?action=deleteComment&id=<?= $comment->getId() ?>" <?= Utils::askConfirmation('Supprimer ce commentaire ?') ?>>Supprimer</a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<a class="submit" href="index.php?action=monitoring">Retour au monitoring</a>