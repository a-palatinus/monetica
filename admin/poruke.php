<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

zahtijevajAdmina();

$naslovStranice = 'Kontakt poruke';

if (isset($_GET['brisi'])) {
    $pdo->prepare("DELETE FROM kontakt_poruke WHERE id = ?")->execute([(int)$_GET['brisi']]);
    header('Location: ' . BASE_URL . '/admin/poruke.php');
    exit;
}

if (isset($_GET['id'])) {
    $pdo->prepare("UPDATE kontakt_poruke SET procitano = 1 WHERE id = ?")->execute([(int)$_GET['id']]);
}

$poruke = $pdo->query("SELECT * FROM kontakt_poruke ORDER BY datum_slanja DESC")->fetchAll();

$odabranaPoruka = null;
if (isset($_GET['id'])) {
    foreach ($poruke as $p) {
        if ($p['id'] == $_GET['id']) { $odabranaPoruka = $p; break; }
    }
}

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

        <h2 class="naslov-sekcija lijevo">Kontakt poruke (<?= count($poruke) ?>)</h2>

        <?php if ($odabranaPoruka): ?>
        <div style="background: white; border-radius: 8px; padding: 30px; box-shadow: var(--sjena); margin-bottom: 30px; border-left: 4px solid var(--boja-akcent);">
            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 20px;">
                <div>
                    <h3 style="font-size: 1.2rem;"><?= ocisti($odabranaPoruka['ime']) ?></h3>
                    <a href="mailto:<?= ocisti($odabranaPoruka['email']) ?>" style="color: var(--boja-akcent);">
                        <i class="fa fa-envelope"></i> <?= ocisti($odabranaPoruka['email']) ?>
                    </a>
                </div>
                <div style="text-align: right;">
                    <small style="color: var(--boja-siva);"><?= formatirajDatum($odabranaPoruka['datum_slanja']) ?></small><br>
                    <a href="?brisi=<?= $odabranaPoruka['id'] ?>"
                       class="btn-brisi"
                       data-naziv="poruku od <?= ocisti($odabranaPoruka['ime']) ?>"
                       style="color: #e74c3c; font-size: 0.85rem; text-decoration: none; margin-top: 8px; display: inline-block;">
                        <i class="fa fa-trash"></i> Obriši
                    </a>
                </div>
            </div>
            <p style="background: var(--boja-pozadina); padding: 20px; border-radius: 8px; line-height: 1.8; white-space: pre-wrap;"><?= ocisti($odabranaPoruka['poruka']) ?></p>
            <a href="<?= BASE_URL ?>/admin/poruke.php" style="color: var(--boja-siva); font-size: 0.9rem; margin-top: 15px; display: inline-block;">
                ← Nazad na sve poruke
            </a>
        </div>
        <?php endif; ?>

        <?php if (empty($poruke)): ?>
            <div class="poruka-info">Nema primljenih poruka.</div>
        <?php else: ?>
            <table class="admin-tablica">
                <thead>
                    <tr>
                        <th>Od</th>
                        <th>E-mail</th>
                        <th>Poruka (pregled)</th>
                        <th>Datum</th>
                        <th>Radnje</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($poruke as $p): ?>
                    <tr style="<?= !$p['procitano'] ? 'font-weight: bold; background: #fffdf0;' : '' ?>">
                        <td data-label="Od">
                            <?= ocisti($p['ime']) ?>
                            <?php if (!$p['procitano']): ?>
                                <span class="oznaka oznaka-admin" style="font-size: 0.6rem;">NOVO</span>
                            <?php endif; ?>
                        </td>
                        <td data-label="E-mail"><?= ocisti($p['email']) ?></td>
                        <td data-label="Poruka" style="font-weight: normal;">
                            <?= ocisti(skratiTekst($p['poruka'], 80)) ?>
                        </td>
                        <td data-label="Datum" style="font-weight: normal;"><?= date('d.m.Y', strtotime($p['datum_slanja'])) ?></td>
                        <td data-label="">
                            <a href="?id=<?= $p['id'] ?>" style="color: var(--boja-akcent); margin-right: 10px;">
                                <i class="fa fa-eye"></i> Čitaj
                            </a>
                            <a href="?brisi=<?= $p['id'] ?>"
                               class="btn-brisi"
                               data-naziv="poruku od <?= ocisti($p['ime']) ?>"
                               style="color: #e74c3c; text-decoration: none;">
                                <i class="fa fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

    </div>
</section>

<?php include '../includes/footer.php'; ?>
