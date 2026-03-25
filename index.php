<?php

$allowedViews = ['main', 'about'];

$action = $_GET['action'] ?? 'main';
if(!in_array($action, $allowedViews, true)){
    $action = 'main';
}

require_once 'layout/header.php';
require_once 'layout/left_menu.php';

require_once 'views/'. $action .'.php';

require_once 'layout/footer.php';
?>
