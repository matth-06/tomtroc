<section class="edit-book-section">
    <a href="index.php" class="back-link">retour</a>
    <h1>Modifier les informations</h1>

    <section class="edit-book">
        <div class="photo-section">
            <span class="photo-label">Photo</span>
            <?php if (!empty($book['image'])): ?>
                <img src="assets/book/<?= htmlspecialchars($book['image']) ?>" alt="<?= htmlspecialchars($book['titre']) ?>" class="book-thumb">
            <?php endif; ?>
            <div class="form-group">
                <label for="image" class="change-photo-link">Modifier la photo</label>
                <input type="file" id="image" name="image" accept="image/*" form="edit-form" />
            </div>
        </div>

        <form class="form-fields" method="POST" enctype="multipart/form-data" id="edit-form" action="index.php?action=updateBook&id=<?= $book['id'] ?>">
            <div class="form-group">
                <label for="title">Titre</label>
                <input type="text" id="title" name="title" value="<?= htmlspecialchars($book['titre']) ?>" required>
            </div>
            <div class="form-group">
                <label for="author">Auteur</label>
                <input type="text" id="author" name="author" value="<?= htmlspecialchars($book['auteur']) ?>" required>
            </div>
            <div class="form-group">
                <label for="description">Commentaire</label>
                <textarea id="description" name="description" required><?= htmlspecialchars($book['description']) ?></textarea>
            </div>
            <div class="form-group">
                <label for="availability">Disponibilité</label>
                <div class="select-wrapper">
                    <select id="availability" name="availability" required>
                        <option value="disponible" <?= $book['disponibilité'] === 'disponible' ? 'selected' : '' ?>>disponible</option>
                        <option value="indisponible" <?= $book['disponibilité'] === 'indisponible' ? 'selected' : '' ?>>indisponible</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Valider</button>
        </form>
    </section>
</section>