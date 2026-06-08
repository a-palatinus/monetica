<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

zahtijevajAdmina();

$naslovStranice = 'Upravljanje korisnicima';
$poruka = '';

if (isset($_GET['uloga']) && isset($_GET['id'])) {
    $novaUloga = $_GET['uloga'] === 'admin' ? 'admin' : 'korisnik';
    $korisnikId = (int)$_GET['id'];

    if ($korisnikId !== $_SESSION['korisnik_id']) {
        $pdo->prepare("UPDATE korisnici SET uloga = ? WHERE id = ?")->execute([$novaUloga, $korisnikId]);
        $poruka = 'Uloga korisnika je promijenjena.';
    }
}

if (isset($_GET['brisi'])) {
    $korisnikId = (int)$_GET['brisi'];
    if ($korisnikId !== $_SESSION['korisnik_id']) {
        $pdo->prepare("DELETE FROM korisnici WHERE id = ?")->execute([$korisnikId]);
        $poruka = 'Korisnik je obrisan.';
    }
}

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

        <?php if ($poruka): ?>
            <div class="poruka-uspjeh"><i class="fa fa-check-circle"></i> <?= ocisti($poruka) ?></div>
        <?php endif; ?>

        <table class="admin-tablica">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Ime i prezime</th>
                    <th>E-mail</th>
                    <th>Uloga</th>
                    <th>Registriran</th>
                    <th>Radnje</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($korisnici as $k): ?>
                <tr>
                    <td data-label="#"><?= $k['id'] ?></td>
                    <td data-label="Ime">
                        <?= ocisti($k['ime'] . ' ' . $k['prezime']) ?>
                        <?php if ($k['id'] === $_SESSION['korisnik_id']): ?>
                            <small style="color: var(--boja-siva);">(ti)</small>
                        <?php endif; ?>
                    </td>
                    <td data-label="E-mail"><?= ocisti($k['email']) ?></td>
                    <td data-label="Uloga">
                        <span class="oznaka oznaka-<?= $k['uloga'] ?>">
                            <?= $k['uloga'] === 'admin' ? 'Admin' : 'Korisnik' ?>
                        </span>
                    </td>
                    <td data-label="Registriran"><?= date('d.m.Y', strtotime($k['datum_registracije'])) ?></td>
                    <td data-label="">
                        <?php if ($k['id'] !== $_SESSION['korisnik_id']): ?>
                            <?php if ($k['uloga'] === 'korisnik'): ?>
                                <a href="?uloga=admin&id=<?= $k['id'] ?>"
                                   style="color: #f39c12; margin-right: 10px;"
                                   title="Postavi za admina">
                                    <i class="fa fa-crown"></i> Admin
                                </a>
                            <?php else: ?>
                                <a href="?uloga=korisnik&id=<?= $k['id'] ?>"
                                   style="color: var(--boja-siva); margin-right: 10px;"
                                   title="Smanji na korisnika">
                                    <i class="fa fa-user-minus"></i> Korisnik
                                </a>
                            <?php endif; ?>

                            <a href="?brisi=<?= $k['id'] ?>"
                               class="btn-brisi"
                               data-naziv="<?= ocisti($k['ime'] . ' ' . $k['prezime']) ?>"
                               style="color: #e74c3c; text-decoration: none;">
                                <i class="fa fa-trash"></i> Briši
                            </a>
                        <?php else: ?>
                            <span style="color: var(--boja-siva); font-size: 0.85rem;">—</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    </div>
</section>

<?php include '../includes/footer.php'; ?>
