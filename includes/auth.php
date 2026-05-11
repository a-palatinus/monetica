<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!defined('BASE_URL')) {
    define('BASE_URL', rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\'));
}

function jePrijavljen() {
    return isset($_SESSION['korisnik_id']);
}

function jeAdmin() {
    return isset($_SESSION['uloga']) && $_SESSION['uloga'] === 'admin';
}

function zahtijevajPrijavu() {
    if (!jePrijavljen()) {
        header('Location: ' . BASE_URL . '/prijava.php?poruka=morate_se_prijaviti');
        exit;
    }
}

function zahtijevajAdmina() {
    if (!jeAdmin()) {
        header('Location: ' . BASE_URL . '/index.php');
        exit;
    }
}

function postavljiSesiju($korisnik) {
    $_SESSION['korisnik_id']  = $korisnik['id'];
    $_SESSION['ime']          = $korisnik['ime'];
    $_SESSION['prezime']      = $korisnik['prezime'];
    $_SESSION['email']        = $korisnik['email'];
    $_SESSION['uloga']        = $korisnik['uloga'];
}

function punoIme() {
    if (jePrijavljen()) {
        return ($_SESSION['ime'] ?? '') . ' ' . ($_SESSION['prezime'] ?? '');
    }
    return '';
}
