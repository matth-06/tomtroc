<?php
    /**
     * Affichage de la page de monitoring admin : liste des articles avec nombre de vues, nombre de commentaires et date de publication.
     */

    // Fonction helper pour générer les liens de tri
    function getSortLink($column, $currentSort, $currentOrder, $label) {
        $newOrder = ($currentSort === $column && $currentOrder === 'asc') ? 'desc' : 'asc';
        $arrow = ' ↑↓'; 
        if ($currentSort === $column) {
            $arrow = $currentOrder === 'asc' ? ' ↑' : ' ↓';
        }
        return '<a href="index.php?action=monitoring&sort=' . $column . '&order=' . $newOrder . '" class="sort-link">' . $label . $arrow . '</a>';
    }
?>

<h2>Monitoring des articles</h2>

<a class="submit" href="index.php?action=admin">Retour à l'administration</a>

<div class="adminArticle">
    <div class="articleLine headerLine">
        <div class="title"><?php echo getSortLink('title', $sortColumn ?? 'title', $sortOrder ?? 'asc', 'Titre'); ?></div>
        <div class="content"><?php echo getSortLink('views', $sortColumn ?? 'title', $sortOrder ?? 'asc', 'Nombre de vues'); ?></div>
        <div class="content"><?php echo getSortLink('comments', $sortColumn ?? 'title', $sortOrder ?? 'asc', 'Nombre de commentaires'); ?></div>
        <div class="content"><?php echo getSortLink('date', $sortColumn ?? 'title', $sortOrder ?? 'asc', 'Date de publication'); ?></div>
        <div class="content">Actions</div>
    </div>
    <?php foreach ($articlesWithStats as $stat) { ?>
        <div class="articleLine monitoringLine">
            <div class="title"><?= $stat['article']->getTitle() ?></div>
            <div class="content"><?= $stat['article']->getViewCount() ?></div>
            <div class="content"><?= $stat['commentCount'] ?></div>
            <div class="content"><?= $stat['article']->getDateCreation()->format('d/m/Y H:i') ?></div>
            <div class="content"><a class="submit" href="index.php?action=adminComments&id=<?= $stat['article']->getId() ?>">Commentaires</a></div>
        </div>
    <?php } ?>
</div>

<a class="submit" href="index.php?action=admin">Retour à l'administration</a>