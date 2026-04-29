<main>
    <?php if (!$game): ?>
        <h2>Гру не знайдено</h2>
        <p>Такої сторінки не існує.</p>
    <?php else: ?>
        <h2>Редагування гри ID <?php echo (int)$game['id']; ?></h2>

        <?php if (!empty($errors)): ?>
            <div class="form-errors">
                <ul>
                    <?php foreach ($errors as $msg): ?>
                        <li><?php echo htmlspecialchars($msg); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="index.php?action=update_game&id=<?php echo (int)$game['id']; ?>" method="post" class="registration-form">
            <div class="form-row">
                <label for="title">Назва гри:</label>
                <input type="text" id="title" name="title"
                       value="<?php echo htmlspecialchars($old['title'] ?? $game['title']); ?>" required>
            </div>

            <div class="form-row">
                <label for="platform">Платформа:</label>
                <input type="text" id="platform" name="platform"
                       value="<?php echo htmlspecialchars($old['platform'] ?? $game['platform']); ?>" required>
            </div>

            <div class="form-row">
                <label for="genre">Жанр:</label>
                <input type="text" id="genre" name="genre"
                       value="<?php echo htmlspecialchars($old['genre'] ?? $game['genre']); ?>" required>
            </div>

            <div class="form-row">
                <label for="release_year">Рік виходу:</label>
                <input type="number" id="release_year" name="release_year"
                       value="<?php echo htmlspecialchars($old['release_year'] ?? $game['release_year']); ?>" required>
            </div>

            <div class="form-row">
                <label for="price">Ціна (грн):</label>
                <input type="number" step="0.01" id="price" name="price"
                       value="<?php echo htmlspecialchars($old['price'] ?? $game['price']); ?>" required>
            </div>

            <div class="form-row">
                <label>
                    <input type="checkbox" name="visible"
                           <?php echo (!empty($old['visible']) || $game['visible']) ? 'checked' : ''; ?>>
                    Опублікована
                </label>
            </div>

            <div class="form-row">
                <button type="submit">Зберегти зміни</button>
            </div>
        </form>
    <?php endif; ?>
</main>