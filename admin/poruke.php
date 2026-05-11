<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

zahtijevajAdmina();

$naslovStranice = 'Kontakt poruke';

include '../includes/header.php';
?>

<section class="sekcija" style="padding-top: 40px;">
    <div class="kontejner">

        <div class="admin-nav">
            <a href="<?= BASE_URL ?>/admin/index.php"><i class="fa fa-tachometer-alt"></i> Nadzorna ploča</a>
            <a href="<?= BASE_URL ?>/admin/vijesti.php"><i class="fa fa-newspaper"></i> Vijesti</a>
            <a href="<?= BASE_URL ?>/admin/dodaj-djelo.php"><i class="fa fa-plus-circle"></i> Dodaj djelo</a>
            <a href="<?= BASE_URL ?>/admin/uredi-djelo.php"><i class="fa fa-edit"></i> Uredi galeriju</a>
            <a href="<?= BASE_URL ?>/admin/korisnici.php"><i class="fa fa-users"></i> Korisnici</a>
            <a href="<?= BASE_URL ?>/admin/poruke.php" class="aktivan"><i class="fa fa-envelope"></i> Poruke</a>
        </div>

        <h2 class="naslov-sekcija lijevo">Kontakt poruke</h2>

        <div class="poruka-info" style="text-align: center; padding: 60px 20px;">
            <i class="fa fa-tools" style="font-size: 3rem; color: var(--boja-akcent); display: block; margin-bottom: 20px;"></i>
            <h3 style="margin-bottom: 10px;">Ova funkcionalnost je u razvoju</h3>
            <p style="color: var(--boja-siva); max-width: 500px; margin: 0 auto;">
                Prikaz i upravljanje kontakt porukama bit će implementirano u sljedećoj fazi razvoja.
                Poruke se trenutno uredno pohranjuju u bazu podataka putem kontakt forme.
            </p>
        </div>

    </div>
</section>

<?php include '../includes/footer.php'; ?>
