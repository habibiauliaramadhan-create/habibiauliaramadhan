<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: dashboard.php');
    exit;
}

$id = (int)($_POST['id'] ?? 0);

if ($id > 0) {
    $stmt = $pdo->prepare('SELECT title FROM events WHERE id = ?');
    $stmt->execute([$id]);
    $event = $stmt->fetch();

    if ($event) {
        $del = $pdo->prepare('DELETE FROM events WHERE id = ?');
        $del->execute([$id]);
        $_SESSION['flash'] = 'Event "' . $event['title'] . '" berhasil dihapus.';
    } else {
        $_SESSION['flash'] = 'Event tidak ditemukan.';
    }
}

header('Location: dashboard.php');
exit;
