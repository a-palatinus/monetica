<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

zahtijevajAdmina();

$naslovStranice = 'Admin panel';

$brKorisnika = $pdo->query("SELECT COUNT(*) FROM korisnici")->fetchColumn();
$brPoruka    = $pdo->query("SELECT COUNT(*) FROM kontakt_poruke WHERE procitano = 0")->fetchColumn();
$brVijesti   = $pdo->query("SELECT COUNT(*) FROM vijesti")->fetchColumn();
$brDjela     = count(dohvatiDjelaXML());
$brNewsletter = $pdo->query("SELECT COUNT(*) FROM newsletter WHERE aktivan = 1")->fetchColumn();

$zadnjePoruke = $pdo->query("SELECT * FROM kontakt_poruke ORDER BY datum_slanja DESC LIMIT 5")->fetchAll();

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
                <?php if ($brPoruka > 0): ?>
                    <span style="background: #e74c3c; color: white; border-radius: 50%; padding: 1px 6px; font-size: 0.7rem; margin-left: 4px;"><?= $brPoruka ?></span>
                <?php endif; ?>
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
            <div class="dash-kartica" style="border-color: <?= $brPoruka > 0 ? '#e74c3c' : 'var(--boja-akcent)' ?>;">
                <i class="fa fa-envelope" style="color: <?= $brPoruka > 0 ? '#e74c3c' : 'var(--boja-akcent)' ?>;"></i>
                <div class="broj"><?= $brPoruka ?></div>
                <div class="naziv">Nepročitanih poruka</div>
            </div>
        </div>

        <div class="admin-2kol">

            <div>
                <h3 style="margin-bottom: 15px; font-size: 1.2rem;">
                    <i class="fa fa-envelope" style="color: var(--boja-akcent);"></i> Najnovije poruke
                </h3>
                <table class="admin-tablica">
                    <thead>
                        <tr>
                            <th>Od</th>
                            <th>Datum</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($zadnjePoruke as $poruka): ?>
                        <tr>
                            <td data-label="Od">
                                <?= ocisti($poruka['ime']) ?>
                                <?php if (!$poruka['procitano']): ?>
                                    <span class="oznaka oznaka-admin" style="font-size: 0.65rem; padding: 1px 6px;">NOVO</span>
                                <?php endif; ?>
                            </td>
                            <td data-label="Datum"><?= date('d.m.Y', strtotime($poruka['datum_slanja'])) ?></td>
                            <td data-label="">
                                <a href="<?= BASE_URL ?>/admin/poruke.php?id=<?= $poruka['id'] ?>" style="color: var(--boja-akcent); font-size: 0.85rem;">Čitaj</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($zadnjePoruke)): ?>
                            <tr><td colspan="3" style="text-align: center; color: var(--boja-siva);">Nema poruka</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                <a href="<?= BASE_URL ?>/admin/poruke.php" class="btn btn-primarni" style="margin-top: 12px; padding: 8px 18px; font-size: 0.85rem;">Sve poruke</a>
            </div>

            <div>
                <h3 style="margin-bottom: 15px; font-size: 1.2rem;">
                    <i class="fa fa-user-plus" style="color: var(--boja-akcent);"></i> Novi korisnici
                </h3>
                <table class="admin-tablica">
                    <thead>
                        <tr>
                            <th>Ime</th>
                            <th>Uloga</th>
                            <th>Datum</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($zadnjiKorisnici as $k): ?>
                        <tr>
                            <td data-label="Ime"><?= ocisti($k['ime'] . ' ' . $k['prezime']) ?></td>
                            <td data-label="Uloga">
                                <span class="oznaka oznaka-<?= $k['uloga'] ?>">
                                    <?= $k['uloga'] === 'admin' ? 'Admin' : 'Korisnik' ?>
                                </span>
                            </td>
                            <td data-label="Datum"><?= date('d.m.Y', strtotime($k['datum_registracije'])) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <a href="<?= BASE_URL ?>/admin/korisnici.php" class="btn btn-primarni" style="margin-top: 12px; padding: 8px 18px; font-size: 0.85rem;">Svi korisnici</a>
            </div>

        </div>

    </div>
</section>

<?php include '../includes/footer.php'; ?>
