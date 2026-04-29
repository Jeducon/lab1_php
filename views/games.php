<main>
    <h2>Каталог ігор</h2>
    <form method="get" action="index.php" class="filter-form">
    <input type="hidden" name="action" value="games">

    <label>
        Платформа:
        <input type="text" name="platform"
               value="<?php echo htmlspecialchars($_GET['platform'] ?? ''); ?>">
    </label>

    <label>
        Жанр:
        <input type="text" name="genre"
               value="<?php echo htmlspecialchars($_GET['genre'] ?? ''); ?>">
    </label>

    <label>
        Ціна від:
        <input type="number" step="0.01" name="price_min"
               value="<?php echo htmlspecialchars($_GET['price_min'] ?? ''); ?>">
    </label>

    <label>
        до:
        <input type="number" step="0.01" name="price_max"
               value="<?php echo htmlspecialchars($_GET['price_max'] ?? ''); ?>">
    </label>

    <button type="submit">Фільтрувати</button>
    </form>
    <?php if (empty($games)): ?>
        <p>Поки що жодної гри не додано.</p>
    <?php else: ?>
        <table class="cars-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Назва</th>
                    <th>Платформа</th>
                    <th>Жанр</th>
                    <th>Рік</th>
                    <th>Ціна</th>
                    <th>Опублікована</th>
                    <th>Дії</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($games as $game): ?>
                <tr>
                    <td><?php echo (int)$game['id']; ?></td>
                    <td><?php echo htmlspecialchars($game['title']); ?></td>
                    <td><?php echo htmlspecialchars($game['platform']); ?></td>
                    <td><?php echo htmlspecialchars($game['genre']); ?></td>
                    <td><?php echo (int)$game['release_year']; ?></td>
                    <td><?php echo number_format($game['price'], 2); ?></td>
                    <td><?php echo $game['visible'] ? 'Так' : 'Ні'; ?></td>
                    <td>
                        <a href="index.php?action=view_game&id=<?php echo (int)$game['id']; ?>">Перегляд</a>
                        <?php if (!empty($_SESSION['user_id'])): ?>
                        |<a href="index.php?action=toggle_favorite&id=<?php echo (int)$game['id']; ?>">
                                Додати / прибрати з обраних
                        </a>
                        |<a href="index.php?action=add_to_cart&id=<?php echo (int)$game['id']; ?>">
                                Додати в кошик
                            </a>
                        <?php endif; ?>
                        <?php if (!empty($_SESSION['admin'])): ?>
                            | <a href="index.php?action=update_game&id=<?php echo (int)$game['id']; ?>">Редагувати</a>
                            | <a href="index.php?action=delete_game&id=<?php echo (int)$game['id']; ?>"
                                 onclick="return confirm('Видалити цю гру?');">Видалити</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</main>