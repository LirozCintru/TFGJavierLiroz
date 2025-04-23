<?php
function isLoggedIn() {
    return isset($_SESSION['usuario']);
}

function isAdmin() {
    return isLoggedIn() && $_SESSION['usuario']['rol'] == 1;
}

function redirectIfNotLoggedIn() {
    if (!isLoggedIn()) {
        header('Location: /auth/login');
        exit;
    }
}
