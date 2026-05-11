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


    </div>
</section>

<?php include '../includes/footer.php'; ?>
