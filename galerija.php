<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';

$naslovStranice = 'Galerija';

$svaDjela = dohvatiDjelaXML();

$radioniceSlike = dohvatiRadioniceXML();

function dohvatiSvjetskaDjelaGalerija($max = 9) {
    $url = "https://api.artic.edu/api/v1/artworks?page=1&limit={$max}&fields=id,title,artist_display,date_display,medium_display,image_id";

    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 12,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_USERAGENT      => 'Mozilla/5.0 (compatible; Monetica/1.0)',
            CURLOPT_HTTPHEADER     => ['Accept: application/json'],
            CURLOPT_ENCODING       => 'gzip, deflate',
        ]);
        $odgovor = curl_exec($ch);
        curl_close($ch);
    } else {
        $ctx = stream_context_create(['http' => ['timeout' => 12, 'user_agent' => 'Mozilla/5.0 (compatible; Monetica/1.0)']]);
        $odgovor = @file_get_contents($url, false, $ctx);
    }

    if (!$odgovor) return [];
    $podaci = json_decode($odgovor, true);
    if (!isset($podaci['data'])) return [];

    $djela = [];
    foreach ($podaci['data'] as $d) {
        if (!$d['image_id']) continue;
        $djela[] = [
            'id'      => (string)$d['id'],
            'naslov'  => $d['title'] ?? 'Nepoznato djelo',
            'autor'   => $d['artist_display'] ?? 'Nepoznati autor',
            'godina'  => $d['date_display'] ?? '',
            'tehnika' => $d['medium_display'] ?? '',
            'slika'   => "https://www.artic.edu/iiif/2/{$d['image_id']}/full/400,/0/default.jpg",
        ];
    }
    return $djela;
}

$svjetskaDjela = dohvatiSvjetskaDjelaGalerija();

include 'includes/header.php';
?>

<div class="o-nama-hero">
    <div class="kontejner">
        <h1>Galerija</h1>
        <p>Radovi naših članova, fotografije s radionica i izložbi te remek-djela iz svjetskih zbirki</p>
    </div>
</div>

<section class="sekcija animiraj">
    <div class="kontejner">

        <h2 class="naslov-sekcija lijevo" style="padding-left:55px;">
            Radovi Društva Monetica</h2>
        <p style="color:var(--boja-siva); margin-bottom:36px; max-width:700px; font-size:0.95rem; line-height:1.8; padding-left:55px;">
            Originalna umjetnička djela članova Društva Monetica — slike, fotografije i crteži
            nastali kroz gotovo četiri desetljeća rada. Kliknite na djelo za uvećani prikaz s detaljima
            o tehnici i autoru.
        </p>

        <?php if (empty($svaDjela)): ?>
            <div class="poruka-info">
                Galerija trenutno nema unesenih radova.
                <?php if (jeAdmin()): ?><a href="<?= BASE_URL ?>/admin/dodaj-djelo.php">Dodaj djelo</a>.<?php endif; ?>
            </div>
        <?php else:
            $kategorije = array_values(array_unique(array_column($svaDjela, 'kategorija')));
            sort($kategorije);
        ?>
        <div id="filtriDjela" class="filter-traka" style="padding-left:55px;">
            <button class="filter-gumb aktivan" data-kategorija="sve">Sve</button>
            <?php foreach ($kategorije as $kat): ?>
            <button class="filter-gumb" data-kategorija="<?= ocisti($kat) ?>"><?= ocisti($kat) ?></button>
            <?php endforeach; ?>
        </div>

        <div id="karozelDjela" class="carousel-omotac" style="padding:20px 55px 32px; position:relative;">
            <button class="carousel-gumb lijevi" aria-label="Prethodni"><i class="fa fa-chevron-left"></i></button>
            <div class="carousel-traka">
                <?php foreach ($svaDjela as $djelo):
                    $jeOmiljeno = jePrijavljen() ? jeUFavoritima($pdo, $_SESSION['korisnik_id'], $djelo['id'], 'xml') : false;
                ?>
                <div class="carousel-item-custom" data-kategorija="<?= ocisti($djelo['kategorija']) ?>">
                    <div class="galerija-kartica"
                         data-naslov="<?= ocisti($djelo['naslov']) ?>"
                         data-autor="<?= ocisti($djelo['autor']) ?>"
                         data-kategorija="<?= ocisti($djelo['kategorija']) ?>">
                        <div class="slika-omotac">
                            <span class="kategorija-oznaka"><?= ocisti($djelo['kategorija']) ?></span>

                            <img src="<?= ocisti(urlSlike($djelo['slika'])) ?>"
                                 alt="<?= ocisti($djelo['naslov']) ?>"
                                 loading="lazy"
                                 onerror="this.src='https://via.placeholder.com/400x300?text=Nema+slike'">

                            <div class="kartica-overlay">
                                <p><em><?= ocisti($djelo['tehnika']) ?></em> · <?= ocisti($djelo['godina']) ?></p>
                                <?php if ($djelo['dimenzije']): ?>
                                <p style="font-size:0.78rem;"><?= ocisti($djelo['dimenzije']) ?></p>
                                <?php endif; ?>
                                <?php if ($djelo['opis']): ?>
                                <p style="font-size:0.78rem; margin-top:4px;"><?= ocisti($djelo['opis']) ?></p>
                                <?php endif; ?>
                            </div>

                            <?php if (jePrijavljen()): ?>
                                <button class="btn-favorit <?= $jeOmiljeno ? 'aktivan' : '' ?>"
                                        data-djelo-id="<?= ocisti($djelo['id']) ?>"
                                        data-izvor="xml"
                                        title="<?= $jeOmiljeno ? 'Ukloni iz favorita' : 'Dodaj u favorite' ?>">
                                    <i class="fa fa-heart"></i>
                                </button>
                            <?php else: ?>
                                <a href="<?= BASE_URL ?>/prijava.php" class="btn-favorit" title="Prijavi se za favorite">
                                    <i class="fa fa-heart"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                        <div class="info">
                            <h4><?= ocisti($djelo['naslov']) ?></h4>
                            <span><?= ocisti($djelo['autor']) ?></span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <button class="carousel-gumb desni" aria-label="Sljedeći"><i class="fa fa-chevron-right"></i></button>
        </div>
        <p style="text-align:right; margin-top:16px; font-size:0.82rem; color:var(--boja-siva);">
            <?= count($svaDjela) ?> <?= count($svaDjela) === 1 ? 'djelo' : 'djela' ?> u zbirci
            <?php if (jePrijavljen()): ?>·
                <a href="<?= BASE_URL ?>/favoriti.php" style="color:var(--boja-akcent);">
                    <i class="fa fa-heart"></i> Moji favoriti
                </a>
            <?php endif; ?>
        </p>
        <?php endif; ?>

    </div>
</section>

<section class="sekcija sekcija-siva animiraj slide-lijevo">
    <div class="kontejner">

        <h2 class="naslov-sekcija lijevo" style="padding-left:55px;">
            Radionice i izložbe
        </h2>
        <p style="color:var(--boja-siva); margin-bottom:36px; max-width:700px; font-size:0.95rem; line-height:1.8; padding-left:55px;">
            Fotografije s radionica, tečajeva i izložbi Društva Monetica. Svake godine organiziramo
            više od 15 programa za sve razine znanja i dobi — od dječjih ljetnih kampova do naprednih
            majstorskih tečajeva s priznatim hrvatskim i međunarodnim umjetnicima.
        </p>

        <div class="carousel-omotac" style="padding:20px 55px 32px; position:relative;">
            <button class="carousel-gumb lijevi" aria-label="Prethodni"><i class="fa fa-chevron-left"></i></button>
            <div class="carousel-traka">
                <?php foreach ($radioniceSlike as $foto): ?>
                <div class="carousel-item-custom">
                    <div class="galerija-kartica">
                        <div class="slika-omotac">
                            <img src="<?= ocisti($foto['slika']) ?>"
                                 alt="<?= ocisti($foto['naslov']) ?>"
                                 loading="lazy"
                                 onerror="this.src='https://images.unsplash.com/photo-1460627390041-532a28402358?w=600&q=80'">

                            <div class="kartica-overlay">
                                <p style="font-size:0.82rem;"><?= ocisti($foto['opis']) ?></p>
                            </div>
                        </div>
                        <div class="info">
                            <h4><?= ocisti($foto['naslov']) ?></h4>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <button class="carousel-gumb desni" aria-label="Sljedeći"><i class="fa fa-chevron-right"></i></button>
        </div>
    </div>
</section>

<section class="sekcija animiraj slide-desno">
    <div class="kontejner">

        <h2 class="naslov-sekcija lijevo" style="padding-left:55px;">
            Svjetska remek-djela
            <small style="font-size:0.5em; font-weight:400; color:var(--boja-siva); font-family:var(--font-tekst);">
                <i class="fa fa-globe"></i> Art Institute of Chicago
            </small>
        </h2>
        <p style="color:var(--boja-siva); margin-bottom:36px; max-width:700px; font-size:0.95rem; line-height:1.8; padding-left:55px;">
            Odabrana remek-djela iz javne zbirke Art Institute of Chicago — jedne od najvećih i
            najuglednijih umjetničkih institucija na svijetu s više od 300.000 radova iz 5.000 godina
            ljudske civilizacije. Zbirka je slobodna za korištenje i istraživanje.
        </p>

        <?php if (empty($svjetskaDjela)): ?>
            <div class="poruka-info">
                <i class="fa fa-exclamation-circle"></i>
                Vanjska zbirka trenutno nije dostupna. Provjerite internetsku vezu.
            </div>
        <?php else: ?>
        <div class="carousel-omotac" style="padding:20px 55px 32px; position:relative;">
            <button class="carousel-gumb lijevi" aria-label="Prethodni"><i class="fa fa-chevron-left"></i></button>
            <div class="carousel-traka">
                <?php foreach ($svjetskaDjela as $djelo):
                    $jeOmiljeno = jePrijavljen() ? jeUFavoritima($pdo, $_SESSION['korisnik_id'], $djelo['id'], 'api') : false;
                ?>
                <div class="carousel-item-custom">
                    <div class="galerija-kartica">
                        <div class="slika-omotac">
                            <img src="<?= ocisti($djelo['slika']) ?>"
                                 alt="<?= ocisti($djelo['naslov']) ?>"
                                 loading="lazy"
                                 onerror="this.src='https://images.unsplash.com/photo-1577083552431-6e5fd01988ec?w=600&q=80'">

                            <div class="kartica-overlay">
                                <h4><?= ocisti($djelo['naslov']) ?></h4>
                                <p style="font-size:0.8rem;"><?= skratiTekst(ocisti($djelo['autor']), 60) ?></p>
                                <?php if ($djelo['godina']): ?>
                                    <p><?= ocisti($djelo['godina']) ?></p>
                                <?php endif; ?>
                                <?php if ($djelo['tehnika']): ?>
                                    <p style="font-size:0.75rem; font-style:italic;"><?= skratiTekst(ocisti($djelo['tehnika']), 50) ?></p>
                                <?php endif; ?>
                            </div>

                            <?php if (jePrijavljen()): ?>
                                <button class="btn-favorit <?= $jeOmiljeno ? 'aktivan' : '' ?>"
                                        data-djelo-id="<?= ocisti($djelo['id']) ?>"
                                        data-izvor="api"
                                        title="<?= $jeOmiljeno ? 'Ukloni iz favorita' : 'Dodaj u favorite' ?>">
                                    <i class="fa fa-heart"></i>
                                </button>
                            <?php endif; ?>
                        </div>
                        <div class="info">
                            <h4 style="font-size:0.9rem;"><?= skratiTekst(ocisti($djelo['naslov']), 40) ?></h4>
                            <span><?= ocisti($djelo['godina']) ?></span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <button class="carousel-gumb desni" aria-label="Sljedeći"><i class="fa fa-chevron-right"></i></button>
        </div>
        <p style="text-align:center; margin-top:20px; font-size:0.8rem; color:var(--boja-siva);">
            Podaci preuzeti iz javne zbirke
            <a href="https://www.artic.edu/open-access/public-api" target="_blank" rel="noopener">
                Art Institute of Chicago
            </a> · Besplatno i otvoreno za javnost.
        </p>
        <?php endif; ?>

    </div>
</section>


<?php include 'includes/footer.php'; ?>
