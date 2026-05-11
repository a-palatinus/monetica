<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';

$naslovStranice = 'Aktualno';

$interneVijesti = $pdo->query("SELECT * FROM vijesti ORDER BY datum DESC")->fetchAll();

include 'includes/header.php';
?>

<div class="o-nama-hero">
    <div class="kontejner">
        <h1>Aktualno</h1>
        <p>Vijesti iz društva i aktualnosti iz svijeta likovnih umjetnosti</p>
    </div>
</div>

<section class="sekcija">
    <div class="kontejner">
        <h2 class="naslov-sekcija lijevo">Vijesti Društva Monetica</h2>

        <?php if (empty($interneVijesti)): ?>
            <p class="poruka-info">Trenutno nema objavljenih vijesti.</p>
        <?php else: ?>
            <div class="vijesti-mreza">
                <?php foreach ($interneVijesti as $vijest): ?>
                <article class="vijest-kartica">
                    <?php if ($vijest['slika']): ?>
                    <div class="vijest-slika">
                        <img src="<?= ocisti($vijest['slika']) ?>"
                             alt="<?= ocisti($vijest['naslov']) ?>"
                             loading="lazy">
                    </div>
                    <?php endif; ?>

                    <div class="vijest-tekst">
                        <div class="vijest-datum"><?= formatirajDatum($vijest['datum']) ?></div>
                        <h2 class="vijest-naslov"><?= ocisti($vijest['naslov']) ?></h2>
                        <p class="vijest-opis"><?= skratiTekst(ocisti($vijest['tekst']), 160) ?></p>
                        <a href="<?= BASE_URL ?>/vijest.php?id=<?= (int)$vijest['id'] ?>" class="btn btn-primarni" style="padding:8px 20px; font-size:0.85rem;">
                            Saznaj više →
                        </a>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
