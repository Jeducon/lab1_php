<main>
    <h2>Додати гру</h2>

    <?php if (!empty($errors)): ?>
        <div class="form-errors">
            <ul>
                <?php foreach ($errors as $msg): ?>
                    <li><?php echo htmlspecialchars($msg); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="index.php?action=create_game" method="post" class="registration-form">
        <div class="form-row">
            <label for="title">Назва гри:</label>
            <input type="text" id="title" name="title"
                   value="<?php echo htmlspecialchars($old['title'] ?? ''); ?>" required>
        </div>

        <div class="form-row">
            <label for="platform">Платформа:</label>
            <input type="text" id="platform" name="platform"
                   value="<?php echo htmlspecialchars($old['platform'] ?? 'PC'); ?>" required>
        </div>

        <div class="form-row">
            <label for="genre">Жанр:</label>
            <input type="text" id="genre" name="genre"
                   value="<?php echo htmlspecialchars($old['genre'] ?? ''); ?>" required>
        </div>

        <div class="form-row">
            <label for="release_year">Рік виходу:</label>
            <input type="number" id="release_year" name="release_year"
                   value="<?php echo htmlspecialchars($old['release_year'] ?? ''); ?>" required>
        </div>

        <div class="form-row">
            <label for="price">Ціна (грн):</label>
            <input type="number" step="0.01" id="price" name="price"
                   value="<?php echo htmlspecialchars($old['price'] ?? ''); ?>" required>
        </div>

        <div class="form-row">
            <button type="submit">Зберегти гру</button>
        </div>
    </form>
</main>