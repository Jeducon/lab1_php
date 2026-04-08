<?php
session_start();

$allowedViews = [
    'main',
    'about',
    'registration',
    'registration_successful',
    'login'
];

$action = $_GET['action'] ?? 'main';
if (!in_array($action, $allowedViews, true) && $action !== 'logout') {
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

require_once 'layout/header.php';
require_once 'layout/left_menu.php';
require_once 'views/' . $action . '.php';
require_once 'layout/footer.php';