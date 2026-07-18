<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../config/db.php';

$categories = ['Seminar', 'Workshop', 'Lomba', 'Pelatihan'];
$errors = [];
$form = ['title' => '', 'category' => '', 'description' => '', 'event_date' => '', 'event_time' => '', 'location' => '', 'quota' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form['title']       = trim($_POST['title'] ?? '');
    $form['category']    = $_POST['category'] ?? '';
    $form['description'] = trim($_POST['description'] ?? '');
    $form['event_date']  = $_POST['event_date'] ?? '';
    $form['event_time']  = $_POST['event_time'] ?? '';
    $form['location']    = trim($_POST['location'] ?? '');
    $form['quota']       = $_POST['quota'] ?? '';

    if ($form['title'] === '') $errors[] = 'Judul event wajib diisi.';
    if (!in_array($form['category'], $categories, true)) $errors[] = 'Kategori tidak valid.';
    if ($form['description'] === '') $errors[] = 'Deskripsi wajib diisi.';
    if ($form['event_date'] === '') $errors[] = 'Tanggal wajib diisi.';
    if ($form['event_time'] === '') $errors[] = 'Waktu wajib diisi.';
    if ($form['location'] === '') $errors[] = 'Lokasi wajib diisi.';
    if ($form['quota'] === '' || !ctype_digit((string)$form['quota']) || (int)$form['quota'] < 1) {
        $errors[] = 'Kuota harus berupa angka lebih besar dari 0.';
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare('INSERT INTO events (title, category, description, event_date, event_time, location, quota) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([
            $form['title'], $form['category'], $form['description'],
            $form['event_date'], $form['event_time'], $form['location'], (int)$form['quota'],
        ]);

        $_SESSION['flash'] = 'Event "' . $form['title'] . '" berhasil ditambahkan.';
        header('Location: dashboard.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Tambah Event — Smart Event Campus</title>
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="admin-shell">
  <div class="topbar">
    <div class="container topbar-inner">
      <a href="dashboard.php" class="brand">
        <span class="brand-mark zoomable"><img src="../assets/img/logo-kampus.jpg" alt="Logo Smart Event Campus"></span>
        <span class="brand-text">
          <strong>Smart Event Campus</strong>
          <span>Panel Administrator</span>
        </span>
      </a>
      <nav>
        <a href="../index.php" target="_blank">Lihat Katalog Publik</a>
        <a href="../logout.php" class="btn-ghost-light">Keluar</a>
      </nav>
    </div>
  </div>

  <div class="admin-header">
    <div class="container admin-header-inner">
      <div>
        <h1>Tambah Event Baru</h1>
        <p>Lengkapi detail kegiatan kampus di bawah ini.</p>
      </div>
      <a href="dashboard.php" class="btn btn-outline">&larr; Kembali</a>
    </div>
  </div>

  <main class="admin-main">
    <div class="container" style="max-width:640px;">
      <div class="panel" style="padding:26px 24px;">

        <?php if (!empty($errors)): ?>
          <div class="alert alert-error">
            <?php foreach ($errors as $err): ?>
              <?= htmlspecialchars($err) ?><br>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

        <form method="post" action="add_event.php" novalidate>
          <div class="field">
            <label for="title">Judul Event</label>
            <input type="text" id="title" name="title" value="<?= htmlspecialchars($form['title']) ?>" required>
          </div>

          <div class="field">
            <label for="category">Kategori</label>
            <select id="category" name="category" required>
              <option value="">-- Pilih kategori --</option>
              <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat ?>" <?= $form['category'] === $cat ? 'selected' : '' ?>><?= $cat ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="field">
            <label for="description">Deskripsi</label>
            <textarea id="description" name="description" required><?= htmlspecialchars($form['description']) ?></textarea>
          </div>

          <div class="field-row">
            <div class="field">
              <label for="event_date">Tanggal</label>
              <input type="date" id="event_date" name="event_date" value="<?= htmlspecialchars($form['event_date']) ?>" required>
            </div>
            <div class="field">
              <label for="event_time">Waktu</label>
              <input type="time" id="event_time" name="event_time" value="<?= htmlspecialchars($form['event_time']) ?>" required>
            </div>
          </div>

          <div class="field-row">
            <div class="field">
              <label for="location">Lokasi</label>
              <input type="text" id="location" name="location" value="<?= htmlspecialchars($form['location']) ?>" required>
            </div>
            <div class="field">
              <label for="quota">Kuota Peserta</label>
              <input type="number" id="quota" name="quota" min="1" value="<?= htmlspecialchars($form['quota']) ?>" required>
            </div>
          </div>

          <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center; margin-top:8px;">Simpan Event</button>
        </form>

      </div>
    </div>
  </main>
</div>

  <script src="../assets/js/lightbox.js"></script>
</body>
</html>
