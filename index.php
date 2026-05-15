<?php
// učitavanje potrebnih datoteka
require_once 'includes/db.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';

// naslov stranice
$naslovStranice = 'Dobrodošli';

// dohvat zadnjih 3 vijesti iz baze
$vijesti = $pdo->query("SELECT * FROM vijesti ORDER BY datum DESC LIMIT 3")->fetchAll();

// prvih 6 djela iz xml-a za prikaz na početnoj
$prikazDjela = array_slice(dohvatiDjelaXML(), 0, 3);

// statistike: broj članova i ukupan broj djela
$brClanova = $pdo->query("SELECT COUNT(*) FROM korisnici")->fetchColumn();
$brDjela   = count(dohvatiDjelaXML());

// uključi zajedničko zaglavlje
include 'includes/header.php';
?>
<section class="hero" style="--hero-slika: url('<?= BASE_URL ?>/images/header.jpg')">
    <div class="hero-sadrzaj">
        <h1 class="hero-naslov">
            Dobrodošli u<br>
            <span>Monetica</span>
        </h1>
        <p class="hero-podnaslov">
            Digitalna platforma Umjetničkog društva Monetica —<br>
            gdje se tradicija susreće s kreativnošću od 1987. godine
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

<section class="sekcija animiraj">
    <div class="kontejner" style="max-width:860px;">
        <h2 class="naslov-sekcija tekst-centar">Zašto Monetica?</h2>
        <p style="color:var(--boja-siva); margin-bottom:12px; line-height:1.8; text-align:center;">
            Osnivamo se 1987. s jednim ciljem — stvoriti prostor gdje svaki ljubitelj likovnih
            umjetnosti može učiti, izlagati i rasti. Više od 37 godina organiziramo radionice,
            godišnje izložbe i javne programe koji spajaju generacije i discipline.
        </p>
        <p style="color:var(--boja-siva); margin-bottom:40px; line-height:1.8; text-align:center;">
            Naša digitalna platforma donosi galeriju, vijesti i zajednicu na jedno mjesto —
            dostupno svima, od početnika do profesionalnih umjetnika.
        </p>

        <div class="zasto-ikone" style="display:grid; grid-template-columns:repeat(4,1fr); gap:16px; margin-bottom:36px;">
            <div style="background:var(--boja-pozadina); border-radius:var(--zaobljenje); padding:28px 20px; text-align:center;">
                <i class="fa fa-palette" style="font-size:2rem; color:var(--boja-akcent); margin-bottom:12px; display:block;"></i>
                <strong style="display:block; margin-bottom:6px;">Radionice</strong>
                <span style="color:var(--boja-siva); font-size:0.88rem;">Za sve razine i dobne skupine</span>
            </div>
            <div style="background:var(--boja-pozadina); border-radius:var(--zaobljenje); padding:28px 20px; text-align:center;">
                <i class="fa fa-images" style="font-size:2rem; color:var(--boja-akcent); margin-bottom:12px; display:block;"></i>
                <strong style="display:block; margin-bottom:6px;">Izložbe</strong>
                <span style="color:var(--boja-siva); font-size:0.88rem;">15+ godišnje, lokalno i šire</span>
            </div>
            <div style="background:var(--boja-pozadina); border-radius:var(--zaobljenje); padding:28px 20px; text-align:center;">
                <i class="fa fa-users" style="font-size:2rem; color:var(--boja-akcent); margin-bottom:12px; display:block;"></i>
                <strong style="display:block; margin-bottom:6px;">Zajednica</strong>
                <span style="color:var(--boja-siva); font-size:0.88rem;">Otvorena za sve ljubitelje umjetnosti</span>
            </div>
            <div style="background:var(--boja-pozadina); border-radius:var(--zaobljenje); padding:28px 20px; text-align:center;">
                <i class="fa fa-globe" style="font-size:2rem; color:var(--boja-akcent); margin-bottom:12px; display:block;"></i>
                <strong style="display:block; margin-bottom:6px;">Digitalno</strong>
                <span style="color:var(--boja-siva); font-size:0.88rem;">Galerija i sadržaji dostupni online</span>
            </div>
        </div>

        <div class="tekst-centar">
            <a href="<?= BASE_URL ?>/o-nama.php" class="btn btn-primarni">
                <i class="fa fa-info-circle"></i> Više o društvu
            </a>
        </div>
    </div>
</section>

<section class="sekcija sekcija-siva animiraj">
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

<section class="sekcija animiraj" id="sekcije" aria-labelledby="naslov-istrazuj">
    <div class="kontejner">
        <header class="sekcija-zaglavlje tekst-centar">
            <h2 id="naslov-istrazuj" class="naslov-sekcija">Istraži naš svijet</h2>
            <p style="color:var(--boja-siva); margin-bottom:50px; max-width:600px; margin-left:auto; margin-right:auto;">
                Digitalna galerija, radionice, aktualne vijesti iz svijeta likovnih umjetnosti —
                sve na jednom mjestu, dostupno svima koji vole kulturu i kreativnost.
            </p>
        </header>

        <div class="mreza-3">
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
        </div>
    </div>
</section>

<?php // uključi zajedničko podnožje
include 'includes/footer.php'; ?>
