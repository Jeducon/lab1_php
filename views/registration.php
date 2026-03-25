<?php
?>

<main>
    <h2> Реєстрація користувача </h2>

    <?php if(!empty($errors)): ?>
        <div class = "form-errors">
            <p>Будь ласка, виправте помилки у формі</p>
            <ul>
                <?php foreach ($errors as $field => $msg): ?>
                    <li><?php echo htmlspecialchars($msg); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

 <form action="index.php?action=registration" method="post" class="registration-form">
        <div class="form-row">
            <label for="login">Логін:</label>
            <input type="text" id="login" name="login"
                   value="<?php echo htmlspecialchars($old['login'] ?? ''); ?>" required>
        </div>

        <div class="form-row">
            <label for="password">Пароль:</label>
            <input type="password" id="password" name="password" required>
        </div>

        <div class="form-row">
            <label for="password2">Повторіть пароль:</label>
            <input type="password" id="password2" name="password2" required>
        </div>

        <div class="form-row">
            <label for="email">Електронна пошта:</label>
            <input type="email" id="email" name="email"
                   value="<?php echo htmlspecialchars($old['email'] ?? ''); ?>" required>
        </div>

        <div class="form-row">
            <label for="phone">Телефон (необов’язково):</label>
            <input type="text" id="phone" name="phone"
                   value="<?php echo htmlspecialchars($old['phone'] ?? ''); ?>">
            <small>Приклади: +380123456789, (012) 3456789, (012) 34-56-789, +38 (012) 34 56 789</small>
        </div>

        <div class="form-row">
            <button type="submit">Зареєструватися</button>
        </div>
    </form>
</main>