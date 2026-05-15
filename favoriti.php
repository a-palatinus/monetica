<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';

zahtijevajPrijavu();

$naslovStranice = 'Moji favoriti';

// dohvacanje favorita
$stmt = $pdo->prepare("SELECT * FROM favoriti WHERE korisnik_id = ? ORDER BY datum_dodavanja DESC");
$stmt->execute([$_SESSION['korisnik_id']]);
$favoriti = $stmt->fetchAll();

// razdvajanje favorita
$favXML = array_filter($favoriti, fn($f) => $f['izvor'] === 'xml');
$favAPI = array_filter($favoriti, fn($f) => $f['izvor'] === 'api');

// za XML favorite, dohvati sve XML podatke i filtriraj po ID-u
$svaDjelaXML = dohvatiDjelaXML();
$favDjelaXML = [];
foreach ($svaDjelaXML as $djelo) {
    foreach ($favXML as $fav) {
        if ($djelo['id'] === $fav['djelo_id']) {
            $favDjelaXML[] = $djelo;
        }
    }
}

include 'includes/header.php';
?>

<div class="profil-hero">
    <div class="kontejner">
        <h1 class="profil-ime" style="font-size: 2rem;">
            <i class="fa fa-heart" style="color: #e74c3c;"></i> Moji favoriti
        </h1>
        <p style="color: rgba(255,255,255,0.7); margin-top: 10px;">
            <?= count($favoriti) ?> sačuvanih <?= count($favoriti) === 1 ? 'djelo' : (count($favoriti) < 5 ? 'djela' : 'djela') ?>
        </p>
    </div>
</div>

<section class="sekcija">
    <div class="kontejner">

        <?php if (empty($favoriti)): ?>
            <div style="text-align: center; padding: 60px 20px;">
                <i class="fa fa-heart-broken" style="font-size: 4rem; color: var(--boja-siva-sv); display: block; margin-bottom: 20px;"></i>
                <h2>Nemaš još nijednog favorita</h2>
                <p style="color: var(--boja-siva); margin-bottom: 30px;">Posjeti galeriju i klikni na srce na djelu koje ti se sviđa.</p>
                <a href="<?= BASE_URL ?>/galerija.php" class="btn btn-primarni">
                    <i class="fa fa-images"></i> Otvori galeriju
                </a>
            </div>

        <?php else: ?>

            <?php if (!empty($favDjelaXML)): ?>
            <h2 class="naslov-sekcija lijevo">
                <i class="fa fa-palette" style="color: var(--boja-akcent);"></i> Radovi Monetice
            </h2>
            <div class="galerija-mreza" style="margin-bottom: 60px;">
                <?php foreach ($favDjelaXML as $djelo): ?>
                <div class="galerija-kartica"
                     data-naslov="<?= ocisti($djelo['naslov']) ?>"
                     data-autor="<?= ocisti($djelo['autor']) ?>"
                     data-kategorija="<?= ocisti($djelo['kategorija']) ?>">
                    <div class="slika-omotac">
                        <span class="kategorija-oznaka"><?= ocisti($djelo['kategorija']) ?></span>
                        <img src="<?= ocisti($djelo['slika']) ?>"
                             alt="<?= ocisti($djelo['naslov']) ?>"
                             loading="lazy">
                        <div class="kartica-overlay">
                            <h4><?= ocisti($djelo['naslov']) ?></h4>
                            <p><?= ocisti($djelo['autor']) ?> · <?= ocisti($djelo['godina']) ?></p>
                            <p><em><?= ocisti($djelo['tehnika']) ?></em></p>
                        </div>
                        <button class="btn-favorit aktivan"
                                data-djelo-id="<?= ocisti($djelo['id']) ?>"
                                data-izvor="xml"
                                title="Ukloni iz favorita">
                            <i class="fa fa-heart"></i>
                        </button>
                    </div>
                    <div class="info">
                        <h4><?= ocisti($djelo['naslov']) ?></h4>
                        <span><?= ocisti($djelo['autor']) ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <?php if (!empty($favAPI)): ?>
            <h2 class="naslov-sekcija lijevo">
                <i class="fa fa-globe" style="color: var(--boja-akcent);"></i> Svjetska remek-djela
            </h2>
            <div class="galerija-mreza">
                <?php foreach ($favAPI as $fav): ?>
                <?php
                $apiUrl  = "https://api.artic.edu/api/v1/artworks/{$fav['djelo_id']}?fields=id,title,artist_display,date_display,image_id";
                $ctx     = stream_context_create(['http' => ['timeout' => 4]]);
                $apiOdg  = @file_get_contents($apiUrl, false, $ctx);
                $apiData = $apiOdg ? json_decode($apiOdg, true)['data'] ?? null : null;
                if (!$apiData || !$apiData['image_id']) continue;
                $slikaUrl = "https://www.artic.edu/iiif/2/{$apiData['image_id']}/full/400,/0/default.jpg";
                ?>
                <div class="galerija-kartica">
                    <div class="slika-omotac">
                        <img src="<?= ocisti($slikaUrl) ?>"
                             alt="<?= ocisti($apiData['title'] ?? '') ?>"
                             loading="lazy">
                        <div class="kartica-overlay">
                            <h4><?= ocisti($apiData['title'] ?? 'Djelo') ?></h4>
                            <p style="font-size:0.8rem;"><?= skratiTekst(ocisti($apiData['artist_display'] ?? ''), 60) ?></p>
                            <p><?= ocisti($apiData['date_display'] ?? '') ?></p>
                        </div>
                        <button class="btn-favorit aktivan"
                                data-djelo-id="<?= ocisti($fav['djelo_id']) ?>"
                                data-izvor="api"
                                title="Ukloni iz favorita">
                            <i class="fa fa-heart"></i>
                        </button>
                    </div>
                    <div class="info">
                        <h4 style="font-size: 0.9rem;"><?= skratiTekst(ocisti($apiData['title'] ?? ''), 35) ?></h4>
                        <span><?= ocisti($apiData['date_display'] ?? '') ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

        <?php endif; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
