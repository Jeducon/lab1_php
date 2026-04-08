<aside class="left-menu">
    <h2>Меню</h2>
    <nav>
        <ul>
            <a href="index.php?action=main">Головна</a></li>
            <a href="index.php?action=about">Про сайт</a></li>
            <a href="index.php?action=registration">Реєстрація</a></li>

            <?php if (empty($_SESSION['user_id'])): ?>
                <a href="index.php?action=login">Увійти</a></li>
            <?php else: ?>
                <a href="index.php?action=logout">Вийти</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</aside>