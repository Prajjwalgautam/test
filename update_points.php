<?php
session_start();

if (!isset($_SESSION['points'])) {
    $_SESSION['points'] = 0;
}

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'increment':
        $_SESSION['points'] += 1;
        break;
    case 'decrement':
        if ($_SESSION['points'] > 0) {
            $_SESSION['points'] -= 1;
        }
        break;
    case 'reset':
        $_SESSION['points'] = 0;
        break;
}

echo $_SESSION['points'];
