<aside class="left-menu">
    <h2>Меню</h2>
    <nav>
        <ul>
            <li><a href="index.php?action=main">Головна</a></li>
            <li><a href="index.php?action=about">Про сайт</a></li>
            <li><a href="index.php?action=games">Ігри</a></li>

            <?php if (!empty($_SESSION['user_id'])): ?>
                <li><a href="index.php?action=favorites">Обрані ігри</a></li>
                <li><a href="index.php?action=cart">Кошик</a></li>
            <?php endif; ?>

            <?php if (!empty($_SESSION['admin'])): ?>
                <li><a href="index.php?action=create_game">Додати гру</a></li>
            <?php endif; ?>

            <?php if (empty($_SESSION['user_id'])): ?>
                <li><a href="index.php?action=registration">Реєстрація</a></li>
                <li><a href="index.php?action=login">Увійти</a></li>
            <?php else: ?>
                <li><a href="index.php?action=logout">Вийти</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</aside>