<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../config/db.php';

$activePage = 'data_event';
$pageTitle  = 'Data Event';
$pageCrumb  = 'Home / Data Event';

$categories = ['Seminar', 'Workshop', 'Lomba', 'Pelatihan'];
$q   = trim($_GET['q'] ?? '');
$cat = $_GET['kategori'] ?? '';

$sql = 'SELECT * FROM events WHERE 1=1';
$params = [];

if ($q !== '') {
    $sql .= ' AND (title LIKE ? OR location LIKE ?)';
    $params[] = "%$q%";
    $params[] = "%$q%";
}
if (in_array($cat, $categories, true)) {
    $sql .= ' AND category = ?';
    $params[] = $cat;
}
$sql .= ' ORDER BY event_date ASC, event_time ASC';

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$events = $stmt->fetchAll();

$tagClasses = ['Seminar' => 'tag-seminar', 'Workshop' => 'tag-workshop', 'Lomba' => 'tag-lomba', 'Pelatihan' => 'tag-pelatihan'];

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Data Event — Smart Event Campus</title>
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="shell">
  <?php include __DIR__ . '/includes/sidebar.php'; ?>

  <div class="content">
    <?php include __DIR__ . '/includes/topbar.php'; ?>

    <main class="content-main">

      <?php if ($flash): ?>
        <div class="alert alert-success"><?= htmlspecialchars($flash) ?></div>
      <?php endif; ?>

      <div class="toolbar">
        <form method="get" action="events.php" style="display:flex; gap:8px; flex-wrap:wrap;">
          <input type="text" name="q" class="search-input" placeholder="Cari event..." value="<?= htmlspecialchars($q) ?>">
          <select name="kategori" class="search-input" style="min-width:160px;" onchange="this.form.submit()">
            <option value="">Semua Kategori</option>
            <?php foreach ($categories as $c): ?>
              <option value="<?= $c ?>" <?= $cat === $c ? 'selected' : '' ?>><?= $c ?></option>
            <?php endforeach; ?>
          </select>
          <button type="submit" class="btn btn-outline btn-sm">Filter</button>
          <?php if ($q !== '' || $cat !== ''): ?>
            <a href="events.php" class="btn btn-outline btn-sm">Reset</a>
          <?php endif; ?>
        </form>
        <a href="add_event.php" class="btn btn-primary">+ Tambah Event</a>
      </div>

      <div class="panel">
        <div class="panel-head">
          <h2>Daftar Kegiatan (<?= count($events) ?>)</h2>
        </div>

        <?php if (empty($events)): ?>
          <div class="empty-state">
            <h3>Tidak ada data</h3>
            <p>Coba ubah kata kunci pencarian atau filter kategori.</p>
          </div>
        <?php else: ?>
        <div style="overflow-x:auto;">
          <table class="data-table">
            <thead>
              <tr>
                <th>Judul</th>
                <th>Kategori</th>
                <th>Tanggal</th>
                <th>Lokasi</th>
                <th>Kuota</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($events as $ev): ?>
                <tr>
                  <td><strong><?= htmlspecialchars($ev['title']) ?></strong></td>
                  <td><span class="badge <?= $tagClasses[$ev['category']] ?? '' ?>"><?= htmlspecialchars($ev['category']) ?></span></td>
                  <td class="mono"><?= htmlspecialchars($ev['event_date']) ?><br><?= substr($ev['event_time'],0,5) ?></td>
                  <td><?= htmlspecialchars($ev['location']) ?></td>
                  <td><?= (int)$ev['quota'] ?></td>
                  <td>
                    <div class="row-actions">
                      <a href="edit_event.php?id=<?= $ev['id'] ?>" class="btn btn-outline btn-sm">Edit</a>
                      <form class="inline" method="post" action="delete_event.php" onsubmit="return confirm('Hapus event \'<?= htmlspecialchars(addslashes($ev['title'])) ?>\'? Tindakan ini tidak bisa dibatalkan.');">
                        <input type="hidden" name="id" value="<?= $ev['id'] ?>">
                        <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                      </form>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <?php endif; ?>
      </div>

    </main>
  </div>
</div>

  <script src="../assets/js/lightbox.js"></script>
</body>
</html>
