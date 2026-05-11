<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';

header('Content-Type: application/json');

if (!jePrijavljen()) {
    echo json_encode(['status' => 'greska_prijava']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'greska']);
    exit;
}

$djeloId = trim($_POST['djelo_id'] ?? '');
$izvor   = trim($_POST['izvor']    ?? '');

if (empty($djeloId) || !in_array($izvor, ['xml', 'api'])) {
    echo json_encode(['status' => 'greska']);
    exit;
}

$korisnikId = $_SESSION['korisnik_id'];

$stmt = $pdo->prepare("SELECT id FROM favoriti WHERE korisnik_id = ? AND djelo_id = ? AND izvor = ?");
$stmt->execute([$korisnikId, $djeloId, $izvor]);
$postojeci = $stmt->fetch();

if ($postojeci) {
    // ukloni iz favorita
    $pdo->prepare("DELETE FROM favoriti WHERE id = ?")->execute([$postojeci['id']]);
    echo json_encode(['status' => 'uklonjen']);
} else {
    // dodaj u favorite
    $pdo->prepare("INSERT INTO favoriti (korisnik_id, djelo_id, izvor) VALUES (?, ?, ?)")
        ->execute([$korisnikId, $djeloId, $izvor]);
    echo json_encode(['status' => 'dodan']);
}
