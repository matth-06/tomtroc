<section class="edit-book-section">
    <a href="index.php" class="back-link">retour</a>
    <h1>Modifier les informations</h1>

    <section class="edit-book">
        <div class="photo-section">
            <span class="photo-label">Photo</span>
            <div id="image-preview">
                <img src="assets/book/<?= htmlspecialchars($book->getImage()) ?>" alt="<?= htmlspecialchars($book->getTitre()) ?>" class="book-thumb">
            </div>
            <div class="form-group">
                <label for="image" class="change-photo-link">Modifier la photo</label>
                <input type="file" id="image" name="image" accept="image/*" form="edit-form" />
            </div>
        </div>

        <form class="form-fields" method="POST" enctype="multipart/form-data" id="edit-form" action="index.php?action=updateBook&id=<?= $book->getId() ?>">
            <div class="form-group">
                <label for="title">Titre</label>
                <input type="text" id="title" name="title" value="<?= htmlspecialchars($book->getTitre()) ?>" required>
            </div>
            <div class="form-group">
                <label for="author">Auteur</label>
                <input type="text" id="author" name="author" value="<?= htmlspecialchars($book->getAuteur()) ?>" required>
            </div>
            <div class="form-group">
                <label for="description">Commentaire</label>
                <textarea id="description" name="description" required><?= htmlspecialchars($book->getDescription()) ?></textarea>
            </div>
            <div class="form-group">
                <label for="availability">Disponibilité</label>
                <div class="select-wrapper">
                    <select id="availability" name="availability" required>
                        <option value="disponible" <?= $book->getDisponibilite() === 'disponible' ? 'selected' : '' ?>>disponible</option>
                        <option value="indisponible" <?= $book->getDisponibilite() === 'indisponible' ? 'selected' : '' ?>>indisponible</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Valider</button>
        </form>
    </section>
</section>
<script>
    document.getElementById('image').addEventListener('change', function(event) {
        const file = event.target.files[0];
        const preview = document.getElementById('image-preview');

        if (file) {
            const reader = new FileReader();

            reader.onload = function(e) {
                preview.innerHTML = '<img src="' + e.target.result + '" style="width: 100%; height: 100%; object-fit: cover;" />';
            };

            reader.readAsDataURL(file);
        } else {
            preview.innerHTML = '<img src="assets/book/<?= htmlspecialchars($book->getImage()) ?>" alt="<?= htmlspecialchars($book->getTitre()) ?>" class="book-thumb">';
        }
    });
</script>