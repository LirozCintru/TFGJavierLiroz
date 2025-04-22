<?php
function isLoggedIn() {
    return isset($_SESSION['usuario']);
}

function isAdmin() {
    return isLoggedIn() && $_SESSION['usuario']['rol'] == 1;
}

function redirectIfNotLoggedIn() {
    if (!isLoggedIn()) {
        header('Location: /auth/login'); // O como sea tu URL base
        exit;
    }
}
