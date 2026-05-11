<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

zahtijevajAdmina();

$naslovStranice = 'Admin panel';

// statistike za dashboard
$brKorisnika = $pdo->query("SELECT COUNT(*) FROM korisnici")->fetchColumn();
$brVijesti   = $pdo->query("SELECT COUNT(*) FROM vijesti")->fetchColumn();
$brDjela     = count(dohvatiDjelaXML());

// zadnjih 5 korisnika
$zadnjiKorisnici = $pdo->query("SELECT * FROM korisnici ORDER BY datum_registracije DESC LIMIT 5")->fetchAll();

include '../includes/header.php';
?>

<section class="sekcija" style="padding-top: 40px;">
    <div class="kontejner">

        <div class="admin-nav">
            <a href="<?= BASE_URL ?>/admin/index.php" class="aktivan">
                <i class="fa fa-tachometer-alt"></i> Nadzorna ploča
            </a>
            <a href="<?= BASE_URL ?>/admin/vijesti.php">
                <i class="fa fa-newspaper"></i> Vijesti
            </a>
            <a href="<?= BASE_URL ?>/admin/dodaj-djelo.php">
                <i class="fa fa-plus-circle"></i> Dodaj djelo
            </a>
            <a href="<?= BASE_URL ?>/admin/uredi-djelo.php">
                <i class="fa fa-edit"></i> Uredi galeriju
            </a>
            <a href="<?= BASE_URL ?>/admin/korisnici.php">
                <i class="fa fa-users"></i> Korisnici
            </a>
            <a href="<?= BASE_URL ?>/admin/poruke.php">
                <i class="fa fa-envelope"></i> Poruke
            </a>
        </div>

        <h2 class="naslov-sekcija lijevo">Nadzorna ploča</h2>

        <div class="dash-mreza">
            <div class="dash-kartica">
                <i class="fa fa-users"></i>
                <div class="broj"><?= $brKorisnika ?></div>
                <div class="naziv">Registriranih korisnika</div>
            </div>
            <div class="dash-kartica">
                <i class="fa fa-images"></i>
                <div class="broj"><?= $brDjela ?></div>
                <div class="naziv">Djela u galeriji</div>
            </div>
            <div class="dash-kartica">
                <i class="fa fa-newspaper"></i>
                <div class="broj"><?= $brVijesti ?></div>
                <div class="naziv">Vijesti / blog objave</div>
            </div>
        </div>

        <div>
            <h3 style="margin-bottom: 15px; font-size: 1.2rem;">
                <i class="fa fa-user-plus" style="color: var(--boja-akcent);"></i> Registrirani korisnici
            </h3>
            <table class="admin-tablica">
                <thead>
                    <tr>
                        <th>Ime</th>
                        <th>Uloga</th>
                        <th>Datum registracije</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($zadnjiKorisnici as $k): ?>
                    <tr>
                        <td><?= ocisti($k['ime'] . ' ' . $k['prezime']) ?></td>
                        <td>
                            <span class="oznaka oznaka-<?= $k['uloga'] ?>">
                                <?= $k['uloga'] === 'admin' ? 'Admin' : 'Korisnik' ?>
                            </span>
                        </td>
                        <td><?= date('d.m.Y', strtotime($k['datum_registracije'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <a href="<?= BASE_URL ?>/admin/korisnici.php" class="btn btn-primarni" style="margin-top: 12px; padding: 8px 18px; font-size: 0.85rem;">Svi korisnici</a>
        </div>

    </div>
</section>

<?php include '../includes/footer.php'; ?>
