<?php
session_start();

$allowedViews = [
    'main',
    'about',
    'registration',
    'registration_successful',
    'login',
    'create_game',
    'games',
    'update_game',
    'view_game',
    'delete_game',
    'favorites',
    'cart'
];

$action = $_GET['action'] ?? 'main';

if (!in_array($action, $allowedViews, true)
    && !in_array($action, ['logout', 'toggle_favorite', 'add_to_cart'], true)) {
    $action = 'main';
}

$errors = [];
$old = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'registration') {
    $login = trim($_POST['login'] ?? '');
    $password = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');

    $old = [
        'login' => $login,
        'email' => $email,
        'phone' => $phone,
    ];

    if (!preg_match('/^[A-Za-zА-Яа-яІіЇїЄє0-9_-]{4,}$/u', $login)) {
        $errors['login'] = 'Логін має містити щонайменше 4 символи: літери, цифри, _ або -.';
    }

    if (
        strlen($password) < 7 ||
        !preg_match('/[A-ZА-ЯІЇЄ]/u', $password) ||
        !preg_match('/[a-zа-яіїє]/u', $password) ||
        !preg_match('/[0-9]/', $password)
    ) {
        $errors['password'] = 'Пароль має бути не менше 7 символів і містити великі, малі літери та цифри.';
    }

    if ($password2 !== $password) {
        $errors['password2'] = 'Повтор пароля не співпадає з паролем.';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Введіть коректну email-адресу.';
    }

    if ($phone !== '') {
        if (mb_strlen($phone) > 30) {
            $errors['phone'] = 'Телефон не повинен перевищувати 30 символів.';
        } elseif (!preg_match('/^[0-9()\s+\-]+$/', $phone)) {
            $errors['phone'] = 'Телефон може містити лише цифри, дужки, пробіли, дефіс та +.';
        }
    }

    if (empty($errors)) {
        require_once 'config/db.php';

        $passwordHash = password_hash($password, PASSWORD_BCRYPT);

        $stmt = $mysqli->prepare(
            "INSERT INTO users (login, password_hash, email, phone, admin) VALUES (?, ?, ?, ?, 0)"
        );

        if ($stmt === false) {
            die('Помилка підготовки запиту: ' . $mysqli->error);
        }

        $stmt->bind_param('ssss', $login, $passwordHash, $email, $phone);

        if ($stmt->execute()) {
            $action = 'registration_successful';
        } else {
            $errors['db'] = 'Не вдалося зберегти користувача в базі даних.';
            $action = 'registration';
        }

        $stmt->close();
        $mysqli->close();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'login') {
    $login = trim($_POST['login'] ?? '');
    $password = $_POST['password'] ?? '';

    $old = ['login' => $login];

    require_once 'config/db.php';

    $stmt = $mysqli->prepare(
        "SELECT id, login, password_hash, admin FROM users WHERE login = ? LIMIT 1"
    );

    if ($stmt === false) {
        die('Помилка підготовки запиту: ' . $mysqli->error);
    }

    $stmt->bind_param('s', $login);
    $stmt->execute();

    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['login'] = $user['login'];
        $_SESSION['admin'] = $user['admin'];

        $stmt->close();
        $mysqli->close();

        header('Location: index.php?action=main');
        exit;
    } else {
        $errors['login'] = 'Невірний логін або пароль.';
    }

    $stmt->close();
    $mysqli->close();
}

if ($action === 'logout') {
    $_SESSION = [];
    session_destroy();

    header('Location: index.php?action=main');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'create_game') {

    if (empty($_SESSION['user_id'])) {
        header('Location: index.php?action=login');
        exit;
    }

    $title        = trim($_POST['title'] ?? '');
    $platform     = trim($_POST['platform'] ?? '');
    $genre        = trim($_POST['genre'] ?? '');
    $release_year = (int)($_POST['release_year'] ?? 0);
    $price        = (float)($_POST['price'] ?? 0);

    $old = [
        'title'        => $title,
        'platform'     => $platform,
        'genre'        => $genre,
        'release_year' => $release_year,
        'price'        => $price,
    ];

    $errors = [];

    if ($title === '') {
        $errors['title'] = 'Вкажіть назву гри.';
    }
    if ($platform === '') {
        $errors['platform'] = 'Вкажіть платформу.';
    }
    if ($genre === '') {
        $errors['genre'] = 'Вкажіть жанр.';
    }
    $currentYear = (int)date('Y') + 1;
    if ($release_year < 1970 || $release_year > $currentYear) {
        $errors['release_year'] = 'Вкажіть коректний рік виходу.';
    }
    if ($price <= 0) {
        $errors['price'] = 'Вкажіть додатну ціну.';
    }

    if (empty($errors)) {
        require_once 'config/db.php';

        $visible  = !empty($_SESSION['admin']) ? 1 : 0;
        $authorId = (int)$_SESSION['user_id'];

        $stmt = $mysqli->prepare(
            "INSERT INTO games (title, platform, genre, release_year, price, visible, author_id)
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        if ($stmt === false) {
            die('Помилка підготовки запиту: ' . $mysqli->error);
        }

        $stmt->bind_param(
            'sssiiii',
            $title,
            $platform,
            $genre,
            $release_year,
            $price,
            $visible,
            $authorId
        );

        if ($stmt->execute()) {
            header('Location: index.php?action=games');
            exit;
        } else {
            $errors['db'] = 'Не вдалося додати гру.';
        }

        $stmt->close();
        $mysqli->close();
    }
}

if ($action === 'games') {
    require_once 'config/db.php';

    $where = [];
    $params = [];
    $types  = '';

    $isAdmin = !empty($_SESSION['admin']);

    if (!$isAdmin) {
        $where[] = 'visible = 1';
    }

    $platform = trim($_GET['platform'] ?? '');
    if ($platform !== '') {
        $where[] = 'platform LIKE ?';
        $params[] = '%' . $platform . '%';
        $types .= 's';
    }

    $genre = trim($_GET['genre'] ?? '');
    if ($genre !== '') {
        $where[] = 'genre LIKE ?';
        $params[] = '%' . $genre . '%';
        $types .= 's';
    }

    $priceMin = $_GET['price_min'] ?? '';
    $priceMax = $_GET['price_max'] ?? '';
    if ($priceMin !== '' && is_numeric($priceMin)) {
        $where[] = 'price >= ?';
        $params[] = (float)$priceMin;
        $types .= 'd';
    }
    if ($priceMax !== '' && is_numeric($priceMax)) {
        $where[] = 'price <= ?';
        $params[] = (float)$priceMax;
        $types .= 'd';
    }

    $sql = 'SELECT * FROM games';
    if (!empty($where)) {
        $sql .= ' WHERE ' . implode(' AND ', $where);
    }
    $sql .= ' ORDER BY date DESC';

    $stmt = $mysqli->prepare($sql);
    if ($stmt === false) {
        die('Помилка підготовки запиту: ' . $mysqli->error);
    }

    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $games = $result->fetch_all(MYSQLI_ASSOC);

    $stmt->close();
    $mysqli->close();
}

if ($action === 'view_game') {
    $id = (int)($_GET['id'] ?? 0);

    require_once 'config/db.php';

    if (!empty($_SESSION['admin'])) {
        $sql = "SELECT * FROM games WHERE id = ? LIMIT 1";
    } else {
        $sql = "SELECT * FROM games WHERE id = ? AND visible = 1 LIMIT 1";
    }

    $stmt = $mysqli->prepare($sql);
    if ($stmt === false) {
        die('Помилка підготовки запиту: ' . $mysqli->error);
    }

    $stmt->bind_param('i', $id);
    $stmt->execute();

    $result = $stmt->get_result();
    $game = $result->fetch_assoc();

    $stmt->close();
    $mysqli->close();
}

if ($action === 'update_game' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    if (empty($_SESSION['admin'])) {
        header('Location: index.php?action=login');
        exit;
    }

    $id = (int)($_GET['id'] ?? 0);

    require_once 'config/db.php';
    $stmt = $mysqli->prepare("SELECT * FROM games WHERE id = ? LIMIT 1");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $game = $result->fetch_assoc();
    $stmt->close();
    $mysqli->close();
}

if ($action === 'update_game' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_SESSION['admin'])) {
        header('Location: index.php?action=login');
        exit;
    }

    $id = (int)($_GET['id'] ?? 0);

    $title        = trim($_POST['title'] ?? '');
    $platform     = trim($_POST['platform'] ?? '');
    $genre        = trim($_POST['genre'] ?? '');
    $release_year = (int)($_POST['release_year'] ?? 0);
    $price        = (float)($_POST['price'] ?? 0);
    $visible      = isset($_POST['visible']) ? 1 : 0;

    $old = [
        'title'        => $title,
        'platform'     => $platform,
        'genre'        => $genre,
        'release_year' => $release_year,
        'price'        => $price,
        'visible'      => $visible,
    ];

    $errors = [];

    if (empty($errors)) {
        require_once 'config/db.php';

        $stmt = $mysqli->prepare(
            "UPDATE games
             SET title = ?, platform = ?, genre = ?, release_year = ?, price = ?, visible = ?
             WHERE id = ?"
        );
        if ($stmt === false) {
            die('Помилка підготовки запиту: ' . $mysqli->error);
        }

        $stmt->bind_param(
            'sssiiii',
            $title,
            $platform,
            $genre,
            $release_year,
            $price,
            $visible,
            $id
        );

        if ($stmt->execute()) {
            header('Location: index.php?action=games');
            exit;
        } else {
            $errors['db'] = 'Не вдалося оновити гру.';
        }

        $stmt->close();
        $mysqli->close();
    } else {
        require_once 'config/db.php';
        $stmt = $mysqli->prepare("SELECT * FROM games WHERE id = ? LIMIT 1");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $game = $result->fetch_assoc();
        $stmt->close();
        $mysqli->close();
    }
}

if ($action === 'delete_game') {
    if (empty($_SESSION['admin'])) {
        header('Location: index.php?action=login');
        exit;
    }

    $id = (int)($_GET['id'] ?? 0);

    require_once 'config/db.php';

    $stmt = $mysqli->prepare("SELECT id FROM games WHERE id = ? LIMIT 1");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $exists = $result->fetch_assoc();
    $stmt->close();

    if ($exists) {
        $stmt = $mysqli->prepare("DELETE FROM games WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->close();
    }

    $mysqli->close();

    header('Location: index.php?action=games');
    exit;
}

if ($action === 'toggle_favorite') {
    if (empty($_SESSION['user_id'])) {
        header('Location: index.php?action=login');
        exit;
    }

    $userId = (int)$_SESSION['user_id'];
    $gameId = (int)($_GET['id'] ?? 0);

    if ($gameId > 0) {
        require_once 'config/db.php';

        $stmt = $mysqli->prepare(
            "SELECT 1 FROM favorites WHERE user_id = ? AND game_id = ? LIMIT 1"
        );
        $stmt->bind_param('ii', $userId, $gameId);
        $stmt->execute();
        $exists = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($exists) {
            $stmt = $mysqli->prepare(
                "DELETE FROM favorites WHERE user_id = ? AND game_id = ?"
            );
            $stmt->bind_param('ii', $userId, $gameId);
            $stmt->execute();
            $stmt->close();
        } else {
            $stmt = $mysqli->prepare(
                "INSERT INTO favorites (user_id, game_id) VALUES (?, ?)"
            );
            $stmt->bind_param('ii', $userId, $gameId);
            $stmt->execute();
            $stmt->close();
        }

        $mysqli->close();
    }

    header('Location: index.php?action=games');
    exit;
}

if ($action === 'favorites') {
    if (empty($_SESSION['user_id'])) {
        header('Location: index.php?action=login');
        exit;
    }

    $userId = (int)$_SESSION['user_id'];

    require_once 'config/db.php';

    $stmt = $mysqli->prepare(
        "SELECT g.*
         FROM games g
         JOIN favorites f ON f.game_id = g.id
         WHERE f.user_id = ?
         ORDER BY g.date DESC"
    );
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $favoriteGames = $result->fetch_all(MYSQLI_ASSOC);

    $stmt->close();
    $mysqli->close();
}

if ($action === 'add_to_cart') {
    $gameId = (int)($_GET['id'] ?? 0);

    if ($gameId > 0) {
        if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        if (!in_array($gameId, $_SESSION['cart'], true)) {
            $_SESSION['cart'][] = $gameId;
        }
    }

    header('Location: index.php?action=games');
    exit;
}

if ($action === 'cart') {
    $cartIds = $_SESSION['cart'] ?? [];

    if (!empty($cartIds)) {
        $placeholders = implode(',', array_fill(0, count($cartIds), '?'));
        $types = str_repeat('i', count($cartIds));

        require_once 'config/db.php';

        $sql = "SELECT * FROM games WHERE id IN ($placeholders)";
        $stmt = $mysqli->prepare($sql);
        if ($stmt === false) {
            die('Помилка підготовки запиту: ' . $mysqli->error);
        }

        $stmt->bind_param($types, ...$cartIds);
        $stmt->execute();
        $result = $stmt->get_result();
        $cartGames = $result->fetch_all(MYSQLI_ASSOC);

        $stmt->close();
        $mysqli->close();

        $cartTotal = 0;
        foreach ($cartGames as $g) {
            $cartTotal += (float)$g['price'];
        }
    } else {
        $cartGames = [];
        $cartTotal = 0;
    }
}

require_once 'layout/header.php';
require_once 'layout/left_menu.php';
require_once 'views/' . $action . '.php';
require_once 'layout/footer.php';