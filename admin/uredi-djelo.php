<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

zahtijevajAdmina();

$naslovStranice = 'Uredi galeriju';
$poruka = '';
$greska = '';

if (isset($_GET['brisi'])) {
    $id = trim($_GET['brisi']);
    izbrisiDjeloXML($id);
    header('Location: ' . BASE_URL . '/admin/uredi-djelo.php?obrisano=1');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];
    $podaci = [
        'naslov'    => trim($_POST['naslov']    ?? ''),
        'autor'     => trim($_POST['autor']     ?? ''),
        'tehnika'   => trim($_POST['tehnika']   ?? ''),
        'godina'    => trim($_POST['godina']    ?? ''),
        'dimenzije' => trim($_POST['dimenzije'] ?? ''),
        'kategorija'=> trim($_POST['kategorija'] ?? ''),
        'opis'      => trim($_POST['opis']      ?? ''),
        'slika'     => trim($_POST['slika_url'] ?? ''),
    ];

    if (empty($podaci['naslov']) || empty($podaci['autor'])) {
        $greska = 'Naslov i autor su obavezni.';
    } else {
        if (isset($_FILES['slika']) && $_FILES['slika']['error'] === UPLOAD_ERR_OK) {
            $ext    = pathinfo($_FILES['slika']['name'], PATHINFO_EXTENSION);
            $imeDat = 'djelo_' . $id . '_' . time() . '.' . $ext;
            move_uploaded_file($_FILES['slika']['tmp_name'], ROOT_DIR . '/images/djela/' . $imeDat);
            $podaci['slika'] = 'images/djela/' . $imeDat;
        }

        urediDjeloXML($id, $podaci);
        $poruka = 'Djelo je uspješno ažurirano.';
    }
}

$svaDjela = dohvatiDjelaXML();

$uredujemo = null;
if (isset($_GET['uredi'])) {
    foreach ($svaDjela as $d) {
        if ($d['id'] === $_GET['uredi']) {
            $uredujemo = $d;
            break;
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
            <a href="<?= BASE_URL ?>/admin/dodaj-djelo.php"><i class="fa fa-plus-circle"></i> Dodaj djelo</a>
            <a href="<?= BASE_URL ?>/admin/uredi-djelo.php" class="aktivan"><i class="fa fa-edit"></i> Uredi galeriju</a>
            <a href="<?= BASE_URL ?>/admin/korisnici.php"><i class="fa fa-users"></i> Korisnici</a>
            <a href="<?= BASE_URL ?>/admin/poruke.php"><i class="fa fa-envelope"></i> Poruke</a>
        </div>

        <?php if (isset($_GET['obrisano'])): ?>
            <div class="poruka-uspjeh"><i class="fa fa-check-circle"></i> Djelo je uspješno obrisano.</div>
        <?php endif; ?>
        <?php if ($poruka): ?>
            <div class="poruka-uspjeh"><i class="fa fa-check-circle"></i> <?= ocisti($poruka) ?></div>
        <?php endif; ?>
        <?php if ($greska): ?>
            <div class="poruka-greska"><i class="fa fa-exclamation-circle"></i> <?= ocisti($greska) ?></div>
        <?php endif; ?>

        <?php if ($uredujemo): ?>
        <div class="forma-omotac siroka" style="max-width: 700px; margin: 0 0 40px;">
            <h2 style="margin-bottom: 5px;">Uredi djelo</h2>
            <p class="podnaslov-forme" style="text-align:left;"><?= ocisti($uredujemo['naslov']) ?></p>

            <form action="<?= BASE_URL ?>/admin/uredi-djelo.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?= ocisti($uredujemo['id']) ?>">

                <div class="forma-2kol">
                    <div class="forma-grupa">
                        <label>Naslov *</label>
                        <input type="text" name="naslov" value="<?= ocisti($uredujemo['naslov']) ?>" required>
                    </div>
                    <div class="forma-grupa">
                        <label>Autor *</label>
                        <input type="text" name="autor" value="<?= ocisti($uredujemo['autor']) ?>" required>
                    </div>
                </div>
                <div class="forma-2kol">
                    <div class="forma-grupa">
                        <label>Tehnika</label>
                        <input type="text" name="tehnika" value="<?= ocisti($uredujemo['tehnika']) ?>">
                    </div>
                    <div class="forma-grupa">
                        <label>Kategorija</label>
                        <select name="kategorija">
                            <?php foreach (['Crtež','Fotografija','Slikarstvo'] as $kat): ?>
                                <option value="<?= $kat ?>" <?= $uredujemo['kategorija'] === $kat ? 'selected' : '' ?>><?= $kat ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="forma-2kol">
                    <div class="forma-grupa">
                        <label>Godina</label>
                        <input type="number" name="godina" value="<?= ocisti($uredujemo['godina']) ?>" min="1900" max="<?= date('Y') ?>">
                    </div>
                    <div class="forma-grupa">
                        <label>Dimenzije</label>
                        <input type="text" name="dimenzije" value="<?= ocisti($uredujemo['dimenzije']) ?>">
                    </div>
                </div>
                <div class="forma-grupa">
                    <label>Opis</label>
                    <textarea name="opis"><?= ocisti($uredujemo['opis']) ?></textarea>
                </div>
                <div class="forma-grupa">
                    <label>Trenutna slika:</label>
                    <?php if ($uredujemo['slika']): ?>
                        <img src="<?= ocisti(urlSlike($uredujemo['slika'])) ?>" alt="Trenutna slika" style="max-height: 100px; border-radius: 4px; display: block; margin-bottom: 8px;">
                    <?php endif; ?>
                    <input type="file" id="slikaUpload" name="slika" accept="image/*">
                    <img id="pregledSlike" src="" alt="Pregled" style="display:none; margin-top:8px; max-height:80px; border-radius:4px;">
                </div>
                <div class="forma-grupa">
                    <label>Ili URL slike:</label>
                    <input type="url" name="slika_url" value="<?= ocisti($uredujemo['slika']) ?>" placeholder="https://...">
                </div>

                <div style="display: flex; gap: 15px;">
                    <button type="submit" class="btn btn-primarni">
                        <i class="fa fa-save"></i> Spremi izmjene
                    </button>
                    <a href="<?= BASE_URL ?>/admin/uredi-djelo.php" class="btn btn-akcent" style="text-decoration:none;">
                        <i class="fa fa-times"></i> Odustani
                    </a>
                </div>
            </form>
        </div>
        <?php endif; ?>

        <h2 class="naslov-sekcija lijevo">Sva djela u galeriji (<?= count($svaDjela) ?>)</h2>

        <?php if (empty($svaDjela)): ?>
            <div class="poruka-info">Galerija je prazna. <a href="<?= BASE_URL ?>/admin/dodaj-djelo.php">Dodaj prvo djelo</a>.</div>
        <?php else: ?>
            <table class="admin-tablica">
                <thead>
                    <tr>
                        <th>Slika</th>
                        <th>Naslov</th>
                        <th>Autor</th>
                        <th>Kategorija</th>
                        <th>Godina</th>
                        <th>Radnje</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($svaDjela as $djelo): ?>
                    <tr>
                        <td data-label="Slika">
                            <img src="<?= ocisti(urlSlike($djelo['slika'])) ?>"
                                 alt=""
                                 style="width: 60px; height: 45px; object-fit: cover; border-radius: 4px;"
                                 onerror="this.style.display='none'">
                        </td>
                        <td data-label="Naslov"><strong><?= ocisti($djelo['naslov']) ?></strong></td>
                        <td data-label="Autor"><?= ocisti($djelo['autor']) ?></td>
                        <td data-label="Kategorija">
                            <span class="oznaka oznaka-korisnik"><?= ocisti($djelo['kategorija']) ?></span>
                        </td>
                        <td data-label="Godina"><?= ocisti($djelo['godina']) ?></td>
                        <td data-label="">
                            <a href="?uredi=<?= urlencode($djelo['id']) ?>"
                               style="color: var(--boja-akcent); margin-right: 10px;">
                                <i class="fa fa-edit"></i> Uredi
                            </a>
                            <a href="?brisi=<?= urlencode($djelo['id']) ?>"
                               class="btn-brisi"
                               data-naziv="<?= ocisti($djelo['naslov']) ?>"
                               style="color: #e74c3c; text-decoration: none;">
                                <i class="fa fa-trash"></i> Briši
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

    </div>
</section>

<?php include '../includes/footer.php'; ?>
