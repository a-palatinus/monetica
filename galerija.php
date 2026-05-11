<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';

$naslovStranice = 'Galerija';

$svaDjela    = dohvatiDjelaXML();
$kategorije  = dohvatiKategorijeXML();

include 'includes/header.php';
?>
<div class="o-nama-hero">
    <div class="kontejner">
        <h1>Galerija</h1>
        <p>Pregledajte radove naših članova i remek-djela iz svjetskih zbirki</p>
    </div>
</div>

<section class="sekcija" style="padding-bottom: 0;">
    <div class="kontejner">
        <div class="pretraga-okvir">
            <div class="pretraga-red">
                <div class="pretraga-unos" style="flex: 2;">
                    <label for="pretragaUnos">
                        <i class="fa fa-search"></i> Pretraži
                    </label>
                    <input type="text"
                           id="pretragaUnos"
                           class="unos-polje"
                           placeholder="Pretraži po naslovu, autoru ili kategoriji...">
                </div>

                <div class="pretraga-unos">
                    <label><i class="fa fa-filter"></i> Kategorija</label>
                    <div style="display: flex; gap: 8px; flex-wrap: wrap; padding-top: 5px;">
                        <button class="filter-gumb btn btn-primarni aktivan"
                                data-kategorija="sve"
                                style="padding: 8px 15px; font-size: 0.8rem;">
                            Sve
                        </button>
                        <?php foreach ($kategorije as $kat): ?>
                        <button class="filter-gumb btn btn-akcent"
                                data-kategorija="<?= ocisti($kat) ?>"
                                style="padding: 8px 15px; font-size: 0.8rem; background: transparent; color: var(--boja-primarni); border-color: var(--boja-siva-sv);">
                            <?= ocisti($kat) ?>
                        </button>
                        <?php endforeach; ?>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

<section class="sekcija" style="padding-top: 30px;">
    <div class="kontejner">
        <h2 class="naslov-sekcija lijevo">
            Radovi Društva Monetica
            <small style="font-size: 0.5em; font-weight: 400; color: var(--boja-siva); font-family: var(--font-tekst);">
                <i class="fa fa-file-code"></i> Izvor: lokalna zbirka
            </small>
        </h2>

        <p id="bezRezultata" style="display:none;" class="poruka-info">
            Nema pronađenih radova za uneseni pojam.
        </p>

        <div class="galerija-mreza">
            <?php foreach ($svaDjela as $djelo): ?>
            <div class="galerija-kartica"
                 data-naslov="<?= ocisti($djelo['naslov']) ?>"
                 data-autor="<?= ocisti($djelo['autor']) ?>"
                 data-kategorija="<?= ocisti($djelo['kategorija']) ?>">

                <div class="slika-omotac">
                    <span class="kategorija-oznaka"><?= ocisti($djelo['kategorija']) ?></span>

                    <img src="<?= ocisti($djelo['slika']) ?>"
                         alt="<?= ocisti($djelo['naslov']) ?>"
                         loading="lazy"
                         class="lokalna-slika-klik"
                         data-naslov="<?= ocisti($djelo['naslov']) ?>"
                         data-autor="<?= ocisti($djelo['autor']) ?>"
                         data-godina="<?= ocisti($djelo['godina']) ?>"
                         data-tehnika="<?= ocisti($djelo['tehnika']) ?>"
                         data-dimenzije="<?= ocisti($djelo['dimenzije'] ?? '') ?>"
                         data-opis="<?= ocisti($djelo['opis'] ?? '') ?>"
                         style="cursor: zoom-in;"
                         onerror="this.src='https://via.placeholder.com/400x300?text=Nema+slike'">

                    <div class="kartica-overlay">
                        <h4><?= ocisti($djelo['naslov']) ?></h4>
                        <p><?= ocisti($djelo['autor']) ?> · <?= ocisti($djelo['godina']) ?></p>
                        <p><em><?= ocisti($djelo['tehnika']) ?></em></p>
                        <?php if ($djelo['dimenzije']): ?>
                        <p><?= ocisti($djelo['dimenzije']) ?></p>
                        <?php endif; ?>
                        <?php if ($djelo['opis']): ?>
                        <p style="font-size:0.8rem; margin-top:8px;"><?= skratiTekst(ocisti($djelo['opis']), 100) ?></p>
                        <?php endif; ?>
                    </div>

                    <?php if (jePrijavljen()): ?>
                        <?php $jeOmiljeno = jeUFavoritima($pdo, $_SESSION['korisnik_id'], $djelo['id'], 'xml'); ?>
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
            <?php endforeach; ?>
        </div>
    </div>
</section>

<div id="lightboxModal" class="lightbox-pozadina" style="display:none;" role="dialog" aria-modal="true" aria-label="Uvećana slika djela">
    <div class="lightbox-sadrzaj">
        <button class="lightbox-zatvori" id="lightboxZatvori" aria-label="Zatvori">&times;</button>
        <div class="lightbox-unutarnji">
            <div class="lightbox-slika-omotac">
                <img id="lightboxSlika" src="" alt="" class="lightbox-slika">
            </div>
            <div class="lightbox-info">
                <h3 id="lightboxNaslov"></h3>
                <p id="lightboxAutor" class="lightbox-autor"></p>
                <p id="lightboxGodina" class="lightbox-meta"></p>
                <p id="lightboxTehnika" class="lightbox-meta lightbox-italic"></p>
                <p id="lightboxDimenzije" class="lightbox-meta"></p>
                <p id="lightboxOpis" class="lightbox-opis"></p>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
