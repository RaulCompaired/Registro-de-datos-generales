<?php
session_start();
header('Content-Type: application/json');

if (isset($_SESSION['usuario']) && isset($_SESSION['redirect'])) {
    echo json_encode([
        'logged_in' => true,
        'redirect' => $_SESSION['redirect']
    ]);
} else {
    echo json_encode([
        'logged_in' => false
    ]);
}
exit;
?>
