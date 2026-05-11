<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    header('Location: ' . BASE_URL . '/aktualno.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM vijesti WHERE id = ?");
$stmt->execute([$id]);
$vijest = $stmt->fetch();

if (!$vijest) {
    header('Location: ' . BASE_URL . '/aktualno.php');
    exit;
}

$naslovStranice = ocisti($vijest['naslov']);
include 'includes/header.php';
?>

<div class="o-nama-hero">
    <div class="kontejner">
        <h1><?= ocisti($vijest['naslov']) ?></h1>
        <p><?= formatirajDatum($vijest['datum']) ?></p>
    </div>
</div>

<section class="sekcija">
    <div class="kontejner">
        <div style="max-width: 820px; margin: 0 auto;">

            <a href="<?= BASE_URL ?>/aktualno.php" class="btn btn-obrub" style="margin-bottom: 30px; display: inline-flex; align-items: center; gap: 6px;">
                <i class="fa fa-arrow-left"></i> Povratak na vijesti
            </a>

            <?php if ($vijest['slika']): ?>
            <div style="margin-bottom: 32px; border-radius: 12px; overflow: hidden;">
                <img src="<?= ocisti($vijest['slika']) ?>"
                     alt="<?= ocisti($vijest['naslov']) ?>"
                     style="width: 100%; max-height: 480px; object-fit: cover; display: block;">
            </div>
            <?php endif; ?>

            <div style="font-size: 0.9rem; color: var(--boja-siva); margin-bottom: 24px;">
                <i class="fa fa-calendar-alt"></i> <?= formatirajDatum($vijest['datum']) ?>
            </div>

            <div style="font-size: 1.05rem; line-height: 1.85; color: var(--boja-tekst);">
                <?= nl2br(ocisti($vijest['tekst'])) ?>
            </div>

            <hr style="margin: 40px 0; border: none; border-top: 1px solid var(--boja-rub, #ddd);">

            <a href="<?= BASE_URL ?>/aktualno.php" class="btn btn-primarni">
                <i class="fa fa-arrow-left"></i> Sve vijesti
            </a>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
