<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

zahtijevajAdmina();

$naslovStranice = 'Dodaj djelo';
$poruka = '';
$greska = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $naslov    = trim($_POST['naslov']    ?? '');
    $autor     = trim($_POST['autor']     ?? '');
    $tehnika   = trim($_POST['tehnika']   ?? '');
    $godina    = trim($_POST['godina']    ?? '');
    $dimenzije = trim($_POST['dimenzije'] ?? '');
    $kategorija= trim($_POST['kategorija'] ?? '');
    $opis      = trim($_POST['opis']      ?? '');

    if (empty($naslov) || empty($autor) || empty($kategorija)) {
        $greska = 'Naslov, autor i kategorija su obavezni.';
    } else {
        $slikaPath = '';

        if (isset($_FILES['slika']) && $_FILES['slika']['error'] === UPLOAD_ERR_OK) {
            $dozvoljeni = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $tip = mime_content_type($_FILES['slika']['tmp_name']);
            if (!in_array($tip, $dozvoljeni)) {
                $greska = 'Dozvoljeni formati: JPG, PNG, GIF, WebP.';
            } elseif ($_FILES['slika']['size'] > 5 * 1024 * 1024) {
                $greska = 'Slika ne smije biti veća od 5 MB.';
            } else {
                $ext       = pathinfo($_FILES['slika']['name'], PATHINFO_EXTENSION);
                $imeDat    = 'djelo_' . time() . '_' . rand(100, 999) . '.' . $ext;
                move_uploaded_file($_FILES['slika']['tmp_name'], ROOT_DIR . '/images/djela/' . $imeDat);
                $slikaPath = 'images/djela/' . $imeDat;
            }
        } elseif (!empty($_POST['slika_url'])) {
            $slikaPath = trim($_POST['slika_url']);
        }

        if (!$greska) {
            $novo = [
                'naslov'    => $naslov,
                'autor'     => $autor,
                'tehnika'   => $tehnika,
                'godina'    => $godina,
                'dimenzije' => $dimenzije,
                'kategorija'=> $kategorija,
                'slika'     => $slikaPath,
                'opis'      => $opis,
            ];
            spremiDjeloXML($novo);
            $poruka = "Djelo \"$naslov\" uspješno je dodano u galeriju!";
        }
    }
}

include '../includes/header.php';
?>

<section class="sekcija" style="padding-top: 40px;">
    <div class="kontejner">

        <div class="admin-nav">
            <a href="<?= BASE_URL ?>/admin/index.php"><i class="fa fa-tachometer-alt"></i> Nadzorna ploča</a>
            <a href="<?= BASE_URL ?>/admin/vijesti.php"><i class="fa fa-newspaper"></i> Vijesti</a>
            <a href="<?= BASE_URL ?>/admin/dodaj-djelo.php" class="aktivan"><i class="fa fa-plus-circle"></i> Dodaj djelo</a>
            <a href="<?= BASE_URL ?>/admin/uredi-djelo.php"><i class="fa fa-edit"></i> Uredi galeriju</a>
            <a href="<?= BASE_URL ?>/admin/korisnici.php"><i class="fa fa-users"></i> Korisnici</a>
            <a href="<?= BASE_URL ?>/admin/poruke.php"><i class="fa fa-envelope"></i> Poruke</a>
        </div>

        <div class="forma-omotac siroka" style="max-width: 700px; margin: 0 auto;">
            <h2 style="margin-bottom: 5px;">Dodaj novo djelo</h2>
            <p class="podnaslov-forme">Dodavanje u XML galeriju Društva Monetica</p>

            <?php if ($poruka): ?>
                <div class="poruka-uspjeh"><i class="fa fa-check-circle"></i> <?= ocisti($poruka) ?></div>
            <?php endif; ?>
            <?php if ($greska): ?>
                <div class="poruka-greska"><i class="fa fa-exclamation-circle"></i> <?= ocisti($greska) ?></div>
            <?php endif; ?>

            <form action="<?= BASE_URL ?>/admin/dodaj-djelo.php" method="POST" enctype="multipart/form-data">

                <div class="forma-2kol">
                    <div class="forma-grupa">
                        <label for="naslov">Naslov *</label>
                        <input type="text" id="naslov" name="naslov" value="<?= ocisti($_POST['naslov'] ?? '') ?>" placeholder="Naslov djela" required>
                    </div>
                    <div class="forma-grupa">
                        <label for="autor">Autor *</label>
                        <input type="text" id="autor" name="autor" value="<?= ocisti($_POST['autor'] ?? '') ?>" placeholder="Ime i prezime autora" required>
                    </div>
                </div>

                <div class="forma-2kol">
                    <div class="forma-grupa">
                        <label for="tehnika">Tehnika *</label>
                        <input type="text" id="tehnika" name="tehnika" value="<?= ocisti($_POST['tehnika'] ?? '') ?>" placeholder="npr. Ulje na platnu" required>
                    </div>
                    <div class="forma-grupa">
                        <label for="kategorija">Kategorija *</label>
                        <select id="kategorija" name="kategorija" required>
                            <option value="">-- odaberi --</option>
                            <option value="Slikarstvo"         <?= ($_POST['kategorija'] ?? '') === 'Slikarstvo' ? 'selected' : '' ?>>Slikarstvo</option>
                            <option value="Crtež"            <?= ($_POST['kategorija'] ?? '') === 'Crtež' ? 'selected' : '' ?>>Crtež</option>
                            <option value="Fotografija"            <?= ($_POST['kategorija'] ?? '') === 'Fotografija' ? 'selected' : '' ?>>Fotografija</option>
                            <option value="Ostalo"         <?= ($_POST['kategorija'] ?? '') === 'Ostalo' ? 'selected' : '' ?>>Ostalo</option>
                        </select>
                    </div>
                </div>

                <div class="forma-2kol">
                    <div class="forma-grupa">
                        <label for="godina">Godina nastanka</label>
                        <input type="number" id="godina" name="godina" value="<?= ocisti($_POST['godina'] ?? date('Y')) ?>" min="1900" max="<?= date('Y') ?>">
                    </div>
                    <div class="forma-grupa">
                        <label for="dimenzije">Dimenzije</label>
                        <input type="text" id="dimenzije" name="dimenzije" value="<?= ocisti($_POST['dimenzije'] ?? '') ?>" placeholder="npr. 80 × 60 cm">
                    </div>
                </div>

                <div class="forma-grupa">
                    <label for="opis">Opis djela</label>
                    <textarea id="opis" name="opis" placeholder="Kratki opis ili priča iza djela..."><?= ocisti($_POST['opis'] ?? '') ?></textarea>
                </div>

                <div class="forma-grupa">
                    <label for="slikaUpload">Slika <small style="color:var(--boja-siva)">(upload s računala, max 5MB)</small></label>
                    <input type="file" id="slikaUpload" name="slika" accept="image/*">
                    <img id="pregledSlike" src="" alt="Pregled" style="display:none; margin-top:10px; max-height:150px; border-radius:4px;">
                </div>

                <div class="forma-grupa">
                    <label for="slika_url">ili URL slike <small style="color:var(--boja-siva)">(ako je slika na internetu)</small></label>
                    <input type="url" id="slika_url" name="slika_url" value="<?= ocisti($_POST['slika_url'] ?? '') ?>" placeholder="https://...">
                </div>

                <div style="display: flex; gap: 15px;">
                    <button type="submit" class="btn btn-primarni">
                        <i class="fa fa-plus"></i> Dodaj djelo
                    </button>
                    <a href="<?= BASE_URL ?>/admin/uredi-djelo.php" class="btn btn-akcent" style="text-decoration:none;">
                        <i class="fa fa-list"></i> Prikaz galerije
                    </a>
                </div>
            </form>
        </div>

    </div>
</section>

<?php include '../includes/footer.php'; ?>
