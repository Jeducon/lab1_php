<main>
    <h2>Обрані ігри</h2>

    <?php if (empty($favoriteGames)): ?>
        <p>У вас поки немає обраних ігор.</p>
    <?php else: ?>
        <ul class="games-list">
            <?php foreach ($favoriteGames as $game): ?>
                <li>
                    <strong><?php echo htmlspecialchars($game['title']); ?></strong>
                    (<?php echo htmlspecialchars($game['platform']); ?>, 
                     <?php echo htmlspecialchars($game['genre']); ?>,
                     <?php echo (int)$game['release_year']; ?>)
                    — <?php echo number_format($game['price'], 2); ?> грн
                    <a href="index.php?action=view_game&id=<?php echo (int)$game['id']; ?>">
                        Перегляд
                    </a>
                    <a href="index.php?action=toggle_favorite&id=<?php echo (int)$game['id']; ?>">
                        Прибрати з обраних
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</main>