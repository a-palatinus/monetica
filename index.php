<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';

$naslovStranice = 'Dobrodošli';

// dohvat zadnjih 3 vijesti iz baze
$vijesti = $pdo->query("SELECT * FROM vijesti ORDER BY datum DESC LIMIT 3")->fetchAll();

// prvih 6 djela iz xml-a
$prikazDjela = array_slice(dohvatiDjelaXML(), 0, 6);

// statistike
$brClanova = $pdo->query("SELECT COUNT(*) FROM korisnici")->fetchColumn();
$brDjela   = count(dohvatiDjelaXML());

include 'includes/header.php';
?>
<section class="hero">
    <div class="hero-sadrzaj">
        <h1 class="hero-naslov">
            Dobrodošli u<br>
            <span>Monetica</span>
        </h1>
        <p class="hero-podnaslov">
            Digitalna platforma Umjetničkog društva Monetica — gdje se tradicija susreće s kreativnošću
        </p>
        <div class="hero-gumbi">
            <a href="<?= BASE_URL ?>/galerija.php" class="btn btn-akcent">
                <i class="fa fa-images"></i> Pregledaj galeriju
            </a>
            <a href="<?= BASE_URL ?>/o-nama.php" class="btn btn-obrub">
                Saznaj više o nama
            </a>
        </div>
    </div>
    <a href="#sekcije" class="hero-strelica">
        <i class="fa fa-chevron-down"></i>
    </a>
</section>

<section class="sekcija" id="sekcije" aria-labelledby="naslov-istrazuj">
    <div class="kontejner">
        <header class="sekcija-zaglavlje tekst-centar">
            <h2 id="naslov-istrazuj" class="naslov-sekcija">Istraži naš svijet</h2>
            <p style="color:var(--boja-siva); margin-bottom:50px; max-width:600px; margin-left:auto; margin-right:auto;">
                Galerija, aktualne vijesti iz sveta umjetnosti i mogućnost interakcije s društvom
            </p>
        </header>

        <div class="mreza-3">
            <a href="<?= BASE_URL ?>/galerija.php" style="text-decoration:none; color:inherit;">
                <div class="sekcija-kartica">
                    <div class="slika-omotac">
                        <i class="fa fa-palette"></i>
                    </div>
                    <div class="tekst-omotac">
                        <h3>Galerija</h3>
                        <p>Pregledajte radove naših članova i svjetska remek-djela iz javnih zbirki. Filtrirajte po kategorijama i dodajte u favorite.</p>
                        <span class="btn btn-primarni">Otvori galeriju →</span>
                    </div>
                </div>
            </a>

            <a href="<?= BASE_URL ?>/aktualno.php" style="text-decoration:none; color:inherit;">
                <div class="sekcija-kartica">
                    <div class="slika-omotac">
                        <i class="fa fa-newspaper"></i>
                    </div>
                    <div class="tekst-omotac">
                        <h3>Aktualno</h3>
                        <p>Budite u toku s aktivnostima društva i vijestima iz svijeta likovnih umjetnosti. Novosti se automatski osvježavaju.</p>
                        <span class="btn btn-primarni">Čitaj vijesti →</span>
                    </div>
                </div>
            </a>

            <a href="<?= BASE_URL ?>/o-nama.php" style="text-decoration:none; color:inherit;">
                <div class="sekcija-kartica">
                    <div class="slika-omotac">
                        <i class="fa fa-users"></i>
                    </div>
                    <div class="tekst-omotac">
                        <h3>O nama</h3>
                        <p>Upoznajte se s poviješću i misijom Društva Monetica. Zajedno gradimo zajednicu ljubitelja likovnih umjetnosti.</p>
                        <span class="btn btn-primarni">Upoznaj nas →</span>
                    </div>
                </div>
            </a>
        </div>
    </div>
</section>

<section class="sekcija sekcija-siva">
    <div class="kontejner">
        <div class="statistike">
            <div>
                <span class="stat-broj"><?= $brClanova ?>+</span>
                <span class="stat-opis">Registriranih članova</span>
            </div>
            <div>
                <span class="stat-broj"><?= $brDjela ?>+</span>
                <span class="stat-opis">Izloženih radova</span>
            </div>
            <div>
                <span class="stat-broj">37</span>
                <span class="stat-opis">Godina tradicije</span>
            </div>
            <div>
                <span class="stat-broj">15+</span>
                <span class="stat-opis">Godišnjih izložbi</span>
            </div>
        </div>
    </div>
</section>

<section class="sekcija" aria-labelledby="naslov-radovi">
    <div class="kontejner">
        <header class="sekcija-zaglavlje tekst-centar">
            <h2 id="naslov-radovi" class="naslov-sekcija">Radovi naših članova</h2>
        </header>

        <div class="galerija-mreza">
            <?php foreach ($prikazDjela as $djelo): ?>
            <div class="galerija-kartica"
                 data-naslov="<?= ocisti($djelo['naslov']) ?>"
                 data-autor="<?= ocisti($djelo['autor']) ?>"
                 data-kategorija="<?= ocisti($djelo['kategorija']) ?>">

                <div class="slika-omotac">
                    <span class="kategorija-oznaka"><?= ocisti($djelo['kategorija']) ?></span>

                    <img src="<?= ocisti($djelo['slika']) ?>"
                         alt="<?= ocisti($djelo['naslov']) ?>"
                         loading="lazy"
                         onerror="this.src='<?= BASE_URL ?>/images/placeholder.jpg'">

                    <div class="kartica-overlay">
                        <h4><?= ocisti($djelo['naslov']) ?></h4>
                        <p><?= ocisti($djelo['autor']) ?> · <?= ocisti($djelo['godina']) ?></p>
                        <p><em><?= ocisti($djelo['tehnika']) ?></em></p>
                    </div>

                    <?php if (jePrijavljen()): ?>
                        <?php
                        $jeOmiljeno = jeUFavoritima($pdo, $_SESSION['korisnik_id'], $djelo['id'], 'xml'); ?>
                        <button class="btn-favorit <?= $jeOmiljeno ? 'aktivan' : '' ?>"
                                data-djelo-id="<?= ocisti($djelo['id']) ?>"
                                data-izvor="xml"
                                title="<?= $jeOmiljeno ? 'Ukloni iz favorita' : 'Dodaj u favorite' ?>">
                            <i class="fa fa-heart"></i>
                        </button>
                    <?php endif; ?>
                </div>

                <div class="info">
                    <h4><?= ocisti($djelo['naslov']) ?></h4>
                    <span><?= ocisti($djelo['autor']) ?></span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="tekst-centar" style="margin-top:40px;">
            <a href="<?= BASE_URL ?>/galerija.php" class="btn btn-primarni">
                Pogledaj cijelu galeriju →
            </a>
        </div>
    </div>
</section>

<?php if (!empty($vijesti)): ?>
<section class="sekcija sekcija-siva" aria-labelledby="naslov-vijesti">
    <div class="kontejner">
        <header class="sekcija-zaglavlje tekst-centar">
            <h2 id="naslov-vijesti" class="naslov-sekcija">Najnovije vijesti</h2>
        </header>

        <div class="vijesti-mreza">
            <?php foreach ($vijesti as $vijest): ?>
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
                    <h3 class="vijest-naslov"><?= ocisti($vijest['naslov']) ?></h3>
                    <p class="vijest-opis"><?= skratiTekst(ocisti($vijest['tekst']), 120) ?></p>
                    <a href="<?= BASE_URL ?>/vijest.php?id=<?= (int)$vijest['id'] ?>" class="btn btn-primarni" style="padding:8px 20px; font-size:0.85rem;">
                        Saznaj više →
                    </a>
                </div>
            </article>
            <?php endforeach; ?>
        </div>

        <div class="tekst-centar" style="margin-top:40px;">
            <a href="<?= BASE_URL ?>/aktualno.php" class="btn btn-akcent">Sve vijesti →</a>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if (!jePrijavljen()): ?>
<section class="sekcija" aria-labelledby="naslov-zajednica" style="background: linear-gradient(135deg, var(--boja-primarni), #5a3d2b); color: white; text-align: center;">
    <div class="kontejner" style="padding: 60px 20px;">
        <header class="sekcija-zaglavlje">
            <h2 id="naslov-zajednica" style="color: var(--boja-akcent); font-size: 2rem; margin-bottom: 15px;">
                Postani dio naše zajednice
            </h2>
        </header>
        <p style="color: rgba(255,255,255,0.8); font-size: 1.1rem; max-width: 550px; margin: 0 auto 30px;">
            Registracijom dobivate pristup svim funkcijama — dodajte radove u favorite,
            pratite aktivnosti i ostanite povezani s umjetničkim zajednicom.
        </p>
        <a href="<?= BASE_URL ?>/registracija.php" class="btn btn-akcent" style="font-size: 1rem; padding: 15px 40px;">
            Registriraj se besplatno
        </a>
    </div>
</section>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
