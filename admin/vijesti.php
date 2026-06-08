<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

zahtijevajAdmina();

$naslovStranice = 'Vijesti';
$poruka = '';
$greska = '';

if (isset($_GET['brisi'])) {
    $id = (int)$_GET['brisi'];
    $pdo->prepare("DELETE FROM vijesti WHERE id = ?")->execute([$id]);
    header('Location: ' . BASE_URL . '/admin/vijesti.php?obrisano=1');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $naslov = trim($_POST['naslov'] ?? '');
    $tekst  = trim($_POST['tekst']  ?? '');
    $slika  = trim($_POST['slika']  ?? '');

    if (empty($naslov) || empty($tekst)) {
        $greska = 'Naslov i tekst su obavezni.';
    } else {
        if (isset($_FILES['slika_upload']) && $_FILES['slika_upload']['error'] === UPLOAD_ERR_OK) {
            $dozvoljeni = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $tip = mime_content_type($_FILES['slika_upload']['tmp_name']);
            if (!in_array($tip, $dozvoljeni)) {
                $greska = 'Dozvoljeni formati: JPG, PNG, GIF, WebP.';
            } elseif ($_FILES['slika_upload']['size'] > 5 * 1024 * 1024) {
                $greska = 'Slika ne smije biti veća od 5 MB.';
            } else {
                $ext    = pathinfo($_FILES['slika_upload']['name'], PATHINFO_EXTENSION);
                $imeDat = 'vijest_' . time() . '_' . rand(100, 999) . '.' . $ext;
                move_uploaded_file($_FILES['slika_upload']['tmp_name'], ROOT_DIR . '/images/vijesti/' . $imeDat);
                $slika = BASE_URL . '/images/vijesti/' . $imeDat;
            }
        }

        if (!$greska) {
            $stmt = $pdo->prepare("INSERT INTO vijesti (naslov, tekst, slika, autor_id) VALUES (?, ?, ?, ?)");
            $stmt->execute([$naslov, $tekst, $slika, $_SESSION['korisnik_id']]);
            $poruka = 'Vijest je uspješno objavljena.';
        }
    }
}

$vijesti = $pdo->query("SELECT v.*, k.ime, k.prezime FROM vijesti v LEFT JOIN korisnici k ON v.autor_id = k.id ORDER BY v.datum DESC")->fetchAll();

include '../includes/header.php';
?>

<section class="sekcija" style="padding-top: 40px;">
    <div class="kontejner">

        <div class="admin-nav">
            <a href="<?= BASE_URL ?>/admin/index.php"><i class="fa fa-tachometer-alt"></i> Nadzorna ploča</a>
            <a href="<?= BASE_URL ?>/admin/vijesti.php" class="aktivan"><i class="fa fa-newspaper"></i> Vijesti</a>
            <a href="<?= BASE_URL ?>/admin/dodaj-djelo.php"><i class="fa fa-plus-circle"></i> Dodaj djelo</a>
            <a href="<?= BASE_URL ?>/admin/uredi-djelo.php"><i class="fa fa-edit"></i> Uredi galeriju</a>
            <a href="<?= BASE_URL ?>/admin/korisnici.php"><i class="fa fa-users"></i> Korisnici</a>
            <a href="<?= BASE_URL ?>/admin/poruke.php"><i class="fa fa-envelope"></i> Poruke</a>
        </div>

        <?php if (isset($_GET['obrisano'])): ?>
            <div class="poruka-uspjeh"><i class="fa fa-check-circle"></i> Vijest je obrisana.</div>
        <?php endif; ?>
        <?php if ($poruka): ?>
            <div class="poruka-uspjeh"><i class="fa fa-check-circle"></i> <?= ocisti($poruka) ?></div>
        <?php endif; ?>
        <?php if ($greska): ?>
            <div class="poruka-greska"><i class="fa fa-exclamation-circle"></i> <?= ocisti($greska) ?></div>
        <?php endif; ?>

        <div class="admin-2kol">

            <div>
                <h2 class="naslov-sekcija lijevo" style="font-size: 1.4rem;">Dodaj novu vijest</h2>

                <form action="<?= BASE_URL ?>/admin/vijesti.php" method="POST" enctype="multipart/form-data">
                    <div class="forma-grupa">
                        <label>Naslov vijesti *</label>
                        <input type="text" name="naslov" value="<?= ocisti($_POST['naslov'] ?? '') ?>" placeholder="Naslov vijesti..." required>
                    </div>
                    <div class="forma-grupa">
                        <label>Slika <small style="color:var(--boja-siva)">(upload s računala, max 5MB)</small></label>
                        <input type="file" name="slika_upload" accept="image/*">
                    </div>
                    <div class="forma-grupa">
                        <label>ili URL slike <small style="color:var(--boja-siva)">(npr. Unsplash direktni link)</small></label>
                        <input type="url" name="slika" value="<?= ocisti($_POST['slika'] ?? '') ?>" placeholder="https://...">
                    </div>
                    <div class="forma-grupa">
                        <label>Tekst vijesti *</label>
                        <textarea name="tekst" style="min-height: 200px;" placeholder="Sadržaj vijesti..." required><?= ocisti($_POST['tekst'] ?? '') ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primarni">
                        <i class="fa fa-plus"></i> Objavi vijest
                    </button>
                </form>
            </div>

            <div>
                <h2 class="naslov-sekcija lijevo" style="font-size: 1.4rem;">Objavljene vijesti (<?= count($vijesti) ?>)</h2>

                <?php if (empty($vijesti)): ?>
                    <p class="poruka-info">Nema vijesti. Dodaj prvu!</p>
                <?php else: ?>
                    <?php foreach ($vijesti as $vijest): ?>
                    <div style="background: white; border-radius: 8px; padding: 18px; margin-bottom: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.06); display: flex; gap: 15px; align-items: center;">
                        <?php if ($vijest['slika']): ?>
                            <img src="<?= ocisti($vijest['slika']) ?>" alt="" style="width: 60px; height: 50px; object-fit: cover; border-radius: 4px; flex-shrink: 0;">
                        <?php else: ?>
                            <div style="width: 60px; height: 50px; background: var(--boja-siva-sv); border-radius: 4px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <i class="fa fa-image" style="color: var(--boja-siva);"></i>
                            </div>
                        <?php endif; ?>
                        <div style="flex: 1; min-width: 0;">
                            <strong style="display: block; font-size: 0.9rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?= ocisti($vijest['naslov']) ?></strong>
                            <small style="color: var(--boja-siva);"><?= formatirajDatum($vijest['datum']) ?></small>
                        </div>
                        <a href="?brisi=<?= $vijest['id'] ?>"
                           class="btn-brisi"
                           data-naziv="<?= ocisti($vijest['naslov']) ?>"
                           style="color: #e74c3c; text-decoration: none; flex-shrink: 0; font-size: 0.85rem;">
                            <i class="fa fa-trash"></i>
                        </a>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>
