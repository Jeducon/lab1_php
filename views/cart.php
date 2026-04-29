<main>
    <h2>Кошик</h2>

    <?php if (empty($cartGames)): ?>
        <p>Кошик порожній.</p>
    <?php else: ?>
        <table class="cars-table">
            <thead>
                <tr>
                    <th>Назва</th>
                    <th>Платформа</th>
                    <th>Жанр</th>
                    <th>Рік</th>
                    <th>Ціна</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($cartGames as $game): ?>
                <tr>
                    <td><?php echo htmlspecialchars($game['title']); ?></td>
                    <td><?php echo htmlspecialchars($game['platform']); ?></td>
                    <td><?php echo htmlspecialchars($game['genre']); ?></td>
                    <td><?php echo (int)$game['release_year']; ?></td>
                    <td><?php echo number_format($game['price'], 2); ?> грн</td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <p><strong>Разом:</strong> <?php echo number_format($cartTotal, 2); ?> грн</p>
    <?php endif; ?>
</main>