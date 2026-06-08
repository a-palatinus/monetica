<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';

$naslovStranice = 'O nama';
include 'includes/header.php';
?>

<section class="o-nama-hero">
    <div class="kontejner">
        <h1>O Umjetničkom društvu Monetica</h1>
        <p>Promičemo likovne umjetnosti i stvaramo prostor za kreativnost od 1987. godine</p>
    </div>
</section>

<section class="sekcija animiraj">
    <div class="kontejner">

        <div class="o-nama-red">
            <div class="o-nama-tekst animiraj slide-lijevo">
                <h2 class="naslov-sekcija lijevo">Tko smo?</h2>
                <p>Umjetničko društvo Monetica osnovano je 1987. godine s ciljem promicanja i razvoja likovnih
                umjetnosti u široj zajednici. Kroz više od tri desetljeća postojanja postali smo jedno od vodećih
                kulturnih udruženja u regiji. Naše članstvo obuhvaća profesionalne slikare, kipare i grafičare,
                ali i amatere koji tek otkrivaju kreativni izraz — uvjereni da je likovnost dostupna svima.</p>
            </div>
            <div class="o-nama-slika animiraj slide-desno odgoda-1">
                <img src="images/grupna_slika.jpg" alt="Grupna slika članova Društva Monetica">
            </div>
        </div>

        <div class="o-nama-red">
            <div class="o-nama-slika animiraj slide-lijevo">
                <img src="images/sto_radimo.jpg" alt="Radionica Društva Monetica">
            </div>
            <div class="o-nama-tekst animiraj slide-desno odgoda-1">
                <h2 class="naslov-sekcija lijevo">Što radimo?</h2>
                <p>Svake godine organiziramo više od 15 izložbi, radionica i tečajeva prilagođenih svim
                dobnim skupinama i razinama znanja — od dječjih ljetnih kampova do majstorskih tečajeva
                s priznatim umjetnicima. Naši programi obuhvaćaju slikarstvo uljem i akvarelom, kiparstvo,
                linogravuru, sitotisak, keramiku, plein air i digitalne medije. Surađujemo s muzejima,
                galerijama i kulturnim institucijama u Hrvatskoj i Europi.</p>
            </div>
        </div>

        <div class="o-nama-red">
            <div class="o-nama-tekst animiraj slide-lijevo">
                <h2 class="naslov-sekcija lijevo">Naša vizija</h2>
                <p>Vjerujemo da je umjetnost temeljni dio ljudskog iskustva i zajedničke kulture. Naša
                digitalna platforma novi je korak u razvoju Društva — prostor gdje tradicija susreće
                suvremenu tehnologiju, a zajednica dobiva alate za međusobno dijeljenje i inspiraciju.
                Želimo biti most između lokalnog i globalnog likovnog svijeta te poticati nove generacije
                da prepoznaju vrijednost kreativnog izražavanja.</p>
            </div>
            <div class="o-nama-slika animiraj slide-desno odgoda-1">
                <img src="images/vizija.jpg" alt="Vizija Društva Monetica">
            </div>
        </div>

    </div>
</section>

<section class="sekcija sekcija-siva animiraj">
    <div class="kontejner">
        <div class="video-omotac" style="max-width:820px; margin:0 auto;">
            <iframe src="https://www.youtube.com/embed/c0htthdj_-g?si=42W3nUdcickacE4e"
                    title="Umjetnost kao disanje"
                    allowfullscreen
                    loading="lazy">
            </iframe>
        </div>
    </div>
</section>


<section class="sekcija sekcija-siva animiraj">
    <div class="kontejner">
        <h2 class="naslov-sekcija tekst-centar">Naše vrijednosti</h2>

        <div class="mreza-3">
            <div style="background: white; border-radius: 8px; padding: 35px; text-align: center; box-shadow: var(--sjena);">
                <i class="fa fa-paint-brush" style="font-size: 2.5rem; color: var(--boja-akcent); margin-bottom: 20px; display: block;"></i>
                <h3 style="margin-bottom: 12px;">Kreativnost</h3>
                <p style="color: var(--boja-siva);">Poticamo slobodno izražavanje i eksperimentiranje s novim tehnikama i stilovima. Nema granica za maštu.</p>
            </div>

            <div style="background: white; border-radius: 8px; padding: 35px; text-align: center; box-shadow: var(--sjena);">
                <i class="fa fa-hands-helping" style="font-size: 2.5rem; color: var(--boja-akcent); margin-bottom: 20px; display: block;"></i>
                <h3 style="margin-bottom: 12px;">Zajedništvo</h3>
                <p style="color: var(--boja-siva);">Gradimo zajednicu u kojoj se međusobno podupiremo, dijelimo znanje i inspiriramo jedni druge.</p>
            </div>

            <div style="background: white; border-radius: 8px; padding: 35px; text-align: center; box-shadow: var(--sjena);">
                <i class="fa fa-star" style="font-size: 2.5rem; color: var(--boja-akcent); margin-bottom: 20px; display: block;"></i>
                <h3 style="margin-bottom: 12px;">Izvrsnost</h3>
                <p style="color: var(--boja-siva);">Stremimo prema visokim standardima u svemu što radimo — od radionice do međunarodnih izložbi.</p>
            </div>
        </div>
    </div>
</section>

<section class="sekcija animiraj" style="text-align: center;">
    <div class="kontejner" style="max-width: 600px;">
        <h2 class="naslov-sekcija tekst-centar">Želi se pridružiti?</h2>
        <p style="color: var(--boja-siva); margin-bottom: 30px; font-size: 1.05rem;">
            Uvijek smo otvoreni za nove članove i suradnje. Javite nam se i postanite dio naše
            kreativne zajednice!
        </p>
        <div style="display:flex; flex-wrap:wrap; gap:14px; justify-content:center;">
            <a href="<?= BASE_URL ?>/kontakt.php" class="btn btn-primarni">
                <i class="fa fa-envelope"></i> Kontaktirajte nas
            </a>
            <a href="<?= BASE_URL ?>/registracija.php" class="btn btn-akcent">
                <i class="fa fa-user-plus"></i> Registrirajte se
            </a>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
