<main>
    <h2>Авторизація</h2>

    <?php if (!empty($errors)): ?>
        <div class="form-errors">
            <p>Невірний логін або пароль.</p>
        </div>
    <?php endif; ?>

    <form action="index.php?action=login" method="post" class="registration-form">
        <div class="form-row">
            <label for="login">Логін:</label>
            <input
                type="text"
                id="login"
                name="login"
                value="<?php echo htmlspecialchars($old['login'] ?? ''); ?>"
                required
            >
        </div>

        <div class="form-row">
            <label for="password">Пароль:</label>
            <input
                type="password"
                id="password"
                name="password"
                required
            >
        </div>

        <div class="form-row">
            <button type="submit">Увійти</button>
        </div>
    </form>
</main>