<?php

require_once __DIR__ . '/auth.php';

$naslovStranice = $naslovStranice ?? 'Monetica';
?>
<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= ocisti($naslovStranice) ?> | Umjetničko društvo Monetica</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
          crossorigin="anonymous">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
</head>
<body>

<header class="zaglavlje-stranice">
    <nav class="nav-glavna" id="navigacija" aria-label="Glavna navigacija">
        <div class="nav-inner">

            <a href="<?= BASE_URL ?>/index.php" class="nav-logo">
                <span class="logo-m">M</span>onetica
            </a>

            <button class="hamburger" id="hamburger" aria-label="Izbornik">
                <span></span>
                <span></span>
                <span></span>
            </button>

            <ul class="nav-lista" id="navLista">
                <li><a href="<?= BASE_URL ?>/index.php"     class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'index.php' ? 'aktivan' : '' ?>">Početna</a></li>
                <li><a href="<?= BASE_URL ?>/o-nama.php"    class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'o-nama.php' ? 'aktivan' : '' ?>">O nama</a></li>
                <li><a href="<?= BASE_URL ?>/aktualno.php"  class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'aktualno.php' ? 'aktivan' : '' ?>">Aktualno</a></li>
                <li><a href="<?= BASE_URL ?>/galerija.php"  class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'galerija.php' ? 'aktivan' : '' ?>">Galerija</a></li>
                <li><a href="<?= BASE_URL ?>/kontakt.php"   class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'kontakt.php' ? 'aktivan' : '' ?>">Kontakt</a></li>

                <?php if (jePrijavljen()): ?>
                    <li class="nav-dropdown">
                        <a href="#" class="nav-link nav-korisnik">
                            <i class="fa fa-user-circle"></i>
                            <?= ocisti($_SESSION['ime'] ?? '') ?>
                            <i class="fa fa-chevron-down fa-xs"></i>
                        </a>
                        <ul class="dropdown-menu-nav">
                            <li><a href="<?= BASE_URL ?>/profil.php"><i class="fa fa-user"></i> Moj profil</a></li>
                            <li><a href="<?= BASE_URL ?>/favoriti.php"><i class="fa fa-heart"></i> Favoriti</a></li>
                            <?php if (jeAdmin()): ?>
                                <li class="dropdown-divider-nav"></li>
                                <li><a href="<?= BASE_URL ?>/admin/index.php"><i class="fa fa-cog"></i> Admin panel</a></li>
                            <?php endif; ?>
                            <li class="dropdown-divider-nav"></li>
                            <li><a href="<?= BASE_URL ?>/odjava.php"><i class="fa fa-sign-out-alt"></i> Odjava</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li><a href="/monetica/prijava.php" class="nav-link nav-btn-prijava">Prijava</a></li>
                    <li><a href="/monetica/registracija.php" class="nav-link nav-btn-reg">Registracija</a></li>
                <?php endif; ?>
            </ul>

        </div>
    </nav>
    <div class="nav-spacer"></div>
</header>

<main id="glavni-sadrzaj">
