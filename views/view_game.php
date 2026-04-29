<main>
    <?php if (!$game): ?>
        <h2>Гру не знайдено</h2>
        <p>Такої сторінки не існує.</p>
    <?php else: ?>
        <h2><?php echo htmlspecialchars($game['title']); ?> (ID <?php echo (int)$game['id']; ?>)</h2>
        <p><strong>Платформа:</strong> <?php echo htmlspecialchars($game['platform']); ?></p>
        <p><strong>Жанр:</strong> <?php echo htmlspecialchars($game['genre']); ?></p>
        <p><strong>Рік виходу:</strong> <?php echo (int)$game['release_year']; ?></p>
        <p><strong>Ціна:</strong> <?php echo number_format($game['price'], 2); ?> грн</p>
        <p><strong>Опублікована:</strong> <?php echo $game['visible'] ? 'Так' : 'Ні'; ?></p>
        <p><strong>Автор (ID):</strong> <?php echo (int)$game['author_id']; ?></p>
        <p><strong>Дата додавання:</strong> <?php echo htmlspecialchars($game['date']); ?></p>
    <?php endif; ?>
</main>