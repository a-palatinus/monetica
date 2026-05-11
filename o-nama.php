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

<section class="sekcija">
    <div class="kontejner">

        <div class="sadrzaj-s-videom">
            <div class="video-omotac">
                <iframe src="https://www.youtube.com/embed/9T35H8sVhho?si=09MUa01EbPmPNQgj"
                        title="Važnost likovne umjetnosti"
                        allowfullscreen
                        loading="lazy">
                </iframe>
            </div>
            <div>

                <h2 class="naslov-sekcija lijevo">Naša priča</h2>

                <div class="odlomak">
                    <h3>Tko smo?</h3>
                    <p>
                        Umjetničko društvo Monetica osnovano je 1987. godine s ciljem promicanja i razvoja likovnih
                        umjetnosti u široj zajednici. Kroz više od tri desetljeća postojanja, postali smo jedno od
                        vodećih kulturnih udruženja u regiji, s članstvom koje obuhvaća profesionalne umjetnike,
                        amatere i ljubitelje likovnih aktivnosti.
                    </p>
                </div>

                <div class="odlomak">
                    <h3>Što radimo?</h3>
                    <p>
                        Organiziramo godišnje izložbe, radionice i tečajeve za sve dobne skupne i razine znanja.
                        Naši programi obuhvaćaju slikarstvo, kiparstvo, grafiku, digitalne medije i mnoge druge
                        likovne discipline. Surađujemo s muzejima, galerijama i kulturnim institucijama diljem
                        Hrvatske i Europe.
                    </p>
                </div>

                <div class="odlomak">
                    <h3>Naša vizija</h3>
                    <p>
                        Vjerujemo da je umjetnost temeljni dio ljudskog iskustva i zajedničke kulture. Naša
                        digitalna platforma nova je faza razvoja — prostor gdje tradicija susreće suvremenu
                        tehnologiju, a zajednica dobiva alate za međusobno dijeljenje i inspiraciju. Želimo biti
                        most između lokalnog i globalnog umjetničkog scene.
                    </p>
                </div>
            </div>

        </div>
    </div>
</section>

<section class="sekcija sekcija-siva">
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

<section class="sekcija" style="text-align: center;">
    <div class="kontejner" style="max-width: 600px;">
        <h2 class="naslov-sekcija tekst-centar">Želi se pridružiti?</h2>
        <p style="color: var(--boja-siva); margin-bottom: 30px; font-size: 1.05rem;">
            Uvijek smo otvoreni za nove članove i suradnje. Javite nam se i postanite dio naše
            kreativne zajednice!
        </p>
        <a href="<?= BASE_URL ?>/kontakt.php" class="btn btn-primarni" style="margin-right: 15px;">
            <i class="fa fa-envelope"></i> Kontaktirajte nas
        </a>
        <a href="<?= BASE_URL ?>/registracija.php" class="btn btn-akcent">
            <i class="fa fa-user-plus"></i> Registrirajte se
        </a>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
