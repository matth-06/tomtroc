<section class="edit-book-section">
    <a href="index.php" class="back-link">retour</a>
    <h1>Nouveau livre</h1>

    <section class="edit-book">
        <!-- Colonne gauche : photo -->
        <div class="photo-section">
            <span class="photo-label">Photo</span>
            <div class="form-group">
                <label for="image" class="change-photo-link">ajouter une photo</label>
                <input type="file" id="image" name="image" accept="image/*" form="edit-form" />
            </div>
        </div>

        <!-- Colonne droite : champs -->
        <form class="form-fields" method="POST" enctype="multipart/form-data" id="edit-form" action="index.php?action=addBook">
            <div class="form-group">
                <label for="title">Titre</label>
                <input type="text" id="title" name="title" value="" minlength="5" maxlength="30" required>
            </div>
            <div class="form-group">
                <label for="author">Auteur</label>
                <input type="text" id="author" name="author" value="" minlength="5" maxlength="30" required>
            </div>
            <div class="form-group">
                <label for="description">Commentaire</label>
                <textarea id="description" name="description" minlength="50" maxlength="200" required></textarea>
            </div>
            <div class="form-group">
                <label for="availability">Disponibilité</label>
                <div class="select-wrapper">
                    <select id="availability" name="availability" required>
                        <option value="disponible">disponible</option>
                        <option value="indisponible">indisponible</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Valider</button>
        </form>
    </section>
</section>