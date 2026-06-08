<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';

$naslovStranice = 'Aktualno';

$interneVijesti = $pdo->query("SELECT * FROM vijesti ORDER BY datum DESC")->fetchAll();

$rssUrl   = 'https://www.theguardian.com/artanddesign/rss';
$rssVijesti = dohvatiRSS($rssUrl, 6);

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

<section class="sekcija sekcija-siva">
    <div class="kontejner">
        <h2 class="naslov-sekcija lijevo">
            Iz svijeta umjetnosti
            <small style="font-size: 0.5em; font-weight: 400; color: var(--boja-siva); font-family: var(--font-tekst);">
                <i class="fa fa-rss"></i> The Guardian Arts
            </small>
        </h2>

        <?php if (empty($rssVijesti)): ?>
            <div class="poruka-info">
                <i class="fa fa-exclamation-circle"></i>
                Vanjske vijesti trenutno nisu dostupne. Provjerite internetsku vezu.
            </div>
        <?php else: ?>
            <div class="vijesti-mreza">
                <?php foreach ($rssVijesti as $vijest): ?>
                <article class="vijest-kartica">
                    <?php if (!empty($vijest['slika'])): ?>
                    <div class="vijest-slika">
                        <img src="<?= ocisti($vijest['slika']) ?>"
                             alt="<?= ocisti($vijest['naslov']) ?>"
                             loading="lazy"
                             onerror="this.parentElement.style.background='linear-gradient(135deg,#83681e,#555)'; this.remove();">
                    </div>
                    <?php else: ?>
                    <div class="vijest-slika" style="background: linear-gradient(135deg, #83681e, #555); display: flex; align-items: center; justify-content: center;">
                        <i class="fa fa-newspaper" style="font-size: 3rem; color: rgba(255,255,255,0.79);"></i>
                    </div>
                    <?php endif; ?>

                    <div class="vijest-tekst">
                        <span class="rss-oznaka"><i class="fa fa-rss"></i> Vanjska vijest</span>

                        <div class="vijest-datum">
                            <?php
                            // format RSS datum
                            $ts = strtotime($vijest['datum']);
                            echo $ts ? formatirajDatum(date('Y-m-d H:i:s', $ts)) : ocisti($vijest['datum']);
                            ?>
                        </div>

                        <h2 class="vijest-naslov"><?= ocisti($vijest['naslov']) ?></h2>

                        <?php if ($vijest['opis']): ?>
                        <p class="vijest-opis"><?= skratiTekst(ocisti($vijest['opis']), 200) ?></p>
                        <?php endif; ?>

                        <a href="<?= ocisti($vijest['link']) ?>"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="btn btn-primarni"
                           style="padding: 8px 20px; font-size: 0.85rem;">
                            Čitaj cijeli članak <i class="fa fa-external-link-alt"></i>
                        </a>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<script>
    setTimeout(function() {
        location.reload();
    }, 600000); 
</script>

<?php include 'includes/footer.php'; ?>
