<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function jePrijavljen() {
    return isset($_SESSION['korisnik_id']);
}

function jeAdmin() {
    return isset($_SESSION['uloga']) && $_SESSION['uloga'] === 'admin';
}

// preusmjeravanje na prijavu ako korisnik nije prijavljen
function zahtijevajPrijavu() {
    if (!jePrijavljen()) {
        header('Location: ' . BASE_URL . '/prijava.php?poruka=morate_se_prijaviti');
        exit;
    }
}

// preusmjeravanje na početnu ako korisnik nije admin
function zahtijevajAdmina() {
    if (!jeAdmin()) {
        header('Location: ' . BASE_URL . '/index.php');
        exit;
    }
}

// pohrana podatke o korisniku u sesiju nakon prijave
function postavljiSesiju($korisnik) {
    $_SESSION['korisnik_id']  = $korisnik['id'];
    $_SESSION['ime']          = $korisnik['ime'];
    $_SESSION['prezime']      = $korisnik['prezime'];
    $_SESSION['email']        = $korisnik['email'];
    $_SESSION['uloga']        = $korisnik['uloga'];
}

// dohvacanje punog imena korisnika
function punoIme() {
    if (jePrijavljen()) {
        return ($_SESSION['ime'] ?? '') . ' ' . ($_SESSION['prezime'] ?? '');
    }
    return '';
}
