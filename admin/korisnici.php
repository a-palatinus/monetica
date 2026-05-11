<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

zahtijevajAdmina();

$naslovStranice = 'Korisnici';

$korisnici = $pdo->query("SELECT * FROM korisnici ORDER BY datum_registracije DESC")->fetchAll();

include '../includes/header.php';
?>

<section class="sekcija" style="padding-top: 40px;">
    <div class="kontejner">

        <div class="admin-nav">
            <a href="<?= BASE_URL ?>/admin/index.php"><i class="fa fa-tachometer-alt"></i> Nadzorna ploča</a>
            <a href="<?= BASE_URL ?>/admin/vijesti.php"><i class="fa fa-newspaper"></i> Vijesti</a>
            <a href="<?= BASE_URL ?>/admin/dodaj-djelo.php"><i class="fa fa-plus-circle"></i> Dodaj djelo</a>
            <a href="<?= BASE_URL ?>/admin/uredi-djelo.php"><i class="fa fa-edit"></i> Uredi galeriju</a>
            <a href="<?= BASE_URL ?>/admin/korisnici.php" class="aktivan"><i class="fa fa-users"></i> Korisnici</a>
            <a href="<?= BASE_URL ?>/admin/poruke.php"><i class="fa fa-envelope"></i> Poruke</a>
        </div>

        <h2 class="naslov-sekcija lijevo">Korisnici (<?= count($korisnici) ?>)</h2>

        <div class="poruka-info" style="margin-bottom: 20px;">
            <i class="fa fa-info-circle"></i>
            Upravljanje ulogama i brisanje korisnika bit će dodano u idućoj fazi.
        </div>

        <table class="admin-tablica">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Ime i prezime</th>
                    <th>E-mail</th>
                    <th>Uloga</th>
                    <th>Registriran</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($korisnici as $k): ?>
                <tr>
                    <td><?= $k['id'] ?></td>
                    <td>
                        <?= ocisti($k['ime'] . ' ' . $k['prezime']) ?>
                        <?php if ($k['id'] === $_SESSION['korisnik_id']): ?>
                            <small style="color: var(--boja-siva);">(ti)</small>
                        <?php endif; ?>
                    </td>
                    <td><?= ocisti($k['email']) ?></td>
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

    </div>
</section>

<?php include '../includes/footer.php'; ?>
