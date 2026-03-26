<?php

$allowedViews = ['main', 'about', 'registration', 'registration_successful'];

$action = $_GET['action'] ?? 'main';
if(!in_array($action, $allowedViews, true)){
    $action = 'main';
}

$errors = [];
$old = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($action === 'registration')){
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

    if(!preg_match('/^[A-Za-zA-Яа-яІіЇїЄє0-9_-]{4,}$/u', $login)){
        $errors['login'] = 'Логін має містити щонайменше 4 символи: літери, цифри, _ або -.';
    }

    if(strlen ($password) < 7 || !preg_match('/[A-ZA-ЯІЇЄ]/u', $password)
        || !preg_match('/[a-za-яіїє]/u', $password)
        || !preg_match('/[0-9]/', $password)
    )
    {
        $errors['password'] = 'Пароль має бути не менше 7 символів і містити великі та малі літери та цифри';
    }
    
    if($password2 !== $password){
        $errors['password2'] = 'Повтор не співпадає з паролем';
    }

    if(!preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/u',$email)){
        $errors['email'] = 'Введіть коректну email-адресу';
    }

    if($phone !== ''){
        if(mb_strlen($phone) > 30){
            $errors['phone'] = 'Телефон не повинен перевищувати 30 символів';
        } elseif (!preg_match('/^[0-9()\s+\-]+$/', $phone)){
            $errors['phone'] = 'Телефон може містити лише цифри, дужки, пробіли, дефіс та +';
        }

    if(empty($errors)){
        $action = 'registration_successfull';
    }
    }
}

require_once 'layout/header.php';
require_once 'layout/left_menu.php';

require_once 'views/'. $action .'.php';

require_once 'layout/footer.php';
?>
