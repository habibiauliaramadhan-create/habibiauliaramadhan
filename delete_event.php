<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../config/db.php';

$activePage = 'dashboard';
$pageTitle  = 'Dashboard';
$pageCrumb  = 'Home / Dashboard';

$events = $pdo->query('SELECT * FROM events ORDER BY event_date ASC, event_time ASC')->fetchAll();

$total = count($events);
$byCategory = ['Seminar' => 0, 'Workshop' => 0, 'Lomba' => 0, 'Pelatihan' => 0];
foreach ($events as $e) {
    if (isset($byCategory[$e['category']])) $byCategory[$e['category']]++;
}
$maxCat = max(1, max($byCategory));

$chipClass = ['Seminar' => 'chip-teal', 'Workshop' => 'chip-brass', 'Lomba' => 'chip-brick', 'Pelatihan' => 'chip-slate'];
$barColor  = ['Seminar' => 'var(--teal)', 'Workshop' => 'var(--brass-dark)', 'Lomba' => 'var(--brick)', 'Pelatihan' => 'var(--ink)'];
$tagClasses = ['Seminar' => 'tag-seminar', 'Workshop' => 'tag-workshop', 'Lomba' => 'tag-lomba', 'Pelatihan' => 'tag-pelatihan'];

// Foto kegiatan per kategori (ditaruh di kartu statistik dashboard).
$chipImage = [
    'Seminar'   => '../assets/img/kegiatan/seminar.jpg',
    'Workshop'  => '../assets/img/kegiatan/workshop.jpg',
    'Lomba'     => '../assets/img/kegiatan/lomba.jpg',
    'Pelatihan' => '../assets/img/kegiatan/pelatihan.jpg',
];

$today = date('Y-m-d');
$todayEvents = array_values(array_filter($events, fn($e) => $e['event_date'] === $today));
$upcomingEvents = array_values(array_filter($events, fn($e) => $e['event_date'] > $today));
$pastEvents = array_values(array_filter($events, fn($e) => $e['event_date'] < $today));
usort($pastEvents, fn($a, $b) => strcmp($b['event_date'], $a['event_date'])); // yang paling baru lewat ditampilkan duluan
$recent = array_slice($events, 0, 5);

function fmtDate(string $date): string {
    $bulan = ['01'=>'Jan','02'=>'Feb','03'=>'Mar','04'=>'Apr','05'=>'Mei','06'=>'Jun','07'=>'Jul','08'=>'Agu','09'=>'Sep','10'=>'Okt','11'=>'Nov','12'=>'Des'];
    [$y,$m,$d] = explode('-', $date);
    return ((int)$d) . ' ' . $bulan[$m] . ' ' . $y;
}

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard — Smart Event Campus</title>
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

      <div class="stat-strip">
        <div class="stat-chip chip-ink">
          <div class="total-collage">
            <?php foreach ($chipImage as $imgSrc): ?>
              <img src="<?= $imgSrc ?>" alt="">
            <?php endforeach; ?>
          </div>
          <div class="stat-chip-body">
            <div class="num"><?= $total ?></div>
            <div class="label">Total Event &middot; Semua Kegiatan</div>
          </div>
        </div>
        <?php foreach ($byCategory as $cat => $count): ?>
          <div class="stat-chip <?= $chipClass[$cat] ?>">
            <?php if (isset($chipImage[$cat])): ?>
              <img class="stat-chip-photo" src="<?= $chipImage[$cat] ?>" alt="<?= $cat ?>">
            <?php endif; ?>
            <div class="stat-chip-body">
              <div class="num"><?= $count ?></div>
              <div class="label"><?= $cat ?> &middot; Event</div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <div class="grid-2">
        <div class="panel" style="padding:20px 22px; display:flex; flex-direction:column; justify-content:center;">
          <h2 style="font-size:16px; margin-bottom:16px;">Statistik Event berdasarkan Kategori</h2>
          <div class="bars">
            <?php foreach ($byCategory as $cat => $count): ?>
              <div class="bar-row">
                <span><?= $cat ?></span>
                <div class="bar-track">
                  <div class="bar-fill" style="width:<?= ($count / $maxCat) * 100 ?>%; background:<?= $barColor[$cat] ?>;"></div>
                </div>
                <span class="mono"><?= $count ?></span>
              </div>
            <?php endforeach; ?>
          </div>
        </div>

        <div style="display:flex; flex-direction:column; gap:20px;">
        <div class="panel" style="padding:20px 22px;">
          <h2 style="font-size:16px; margin-bottom:14px;">Agenda Hari Ini</h2>
          <?php if (empty($todayEvents)): ?>
            <p style="font-size:13.5px; color:var(--ink-soft);">Tidak ada.</p>
          <?php else: ?>
            <div class="agenda-list">
              <?php foreach ($todayEvents as $ev): ?>
                <div class="agenda-item">
                  <div class="agenda-time"><?= substr($ev['event_time'],0,5) ?></div>
                  <div>
                    <h4><?= htmlspecialchars($ev['title']) ?></h4>
                    <p><?= htmlspecialchars($ev['location']) ?></p>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>

        <div class="panel" style="padding:20px 22px;">
          <h2 style="font-size:16px; margin-bottom:14px;">Agenda yang Akan Datang</h2>
          <?php if (empty($upcomingEvents)): ?>
            <p style="font-size:13.5px; color:var(--ink-soft);">Tidak ada.</p>
          <?php else: ?>
            <div class="agenda-list">
              <?php foreach ($upcomingEvents as $ev): ?>
                <div class="agenda-item">
                  <div class="agenda-time"><?= fmtDate($ev['event_date']) ?></div>
                  <div>
                    <h4><?= htmlspecialchars($ev['title']) ?></h4>
                    <p><?= htmlspecialchars($ev['location']) ?></p>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>

        <div class="panel" style="padding:20px 22px;">
          <h2 style="font-size:16px; margin-bottom:14px;">Agenda yang Telah Lewat</h2>
          <?php if (empty($pastEvents)): ?>
            <p style="font-size:13.5px; color:var(--ink-soft);">Tidak ada.</p>
          <?php else: ?>
            <div class="agenda-list">
              <?php foreach ($pastEvents as $ev): ?>
                <div class="agenda-item">
                  <div class="agenda-time"><?= fmtDate($ev['event_date']) ?></div>
                  <div>
                    <h4><?= htmlspecialchars($ev['title']) ?></h4>
                    <p><?= htmlspecialchars($ev['location']) ?></p>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>
        </div>
      </div>

      <div class="panel" style="margin-top:20px;">
        <div class="panel-head">
          <h2>Event Terbaru</h2>
          <a href="events.php" class="btn btn-outline btn-sm">Lihat Semua Event</a>
        </div>
        <?php if (empty($recent)): ?>
          <div class="empty-state">
            <h3>Belum ada data event</h3>
            <p>Klik "Tambah Event" di sidebar untuk mulai mencatat kegiatan kampus.</p>
          </div>
        <?php else: ?>
        <div style="overflow-x:auto;">
          <table class="data-table">
            <thead>
              <tr>
                <th>Nama Event</th>
                <th>Kategori</th>
                <th>Tanggal</th>
                <th>Lokasi</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($recent as $ev): ?>
                <tr>
                  <td><strong><?= htmlspecialchars($ev['title']) ?></strong></td>
                  <td><span class="badge <?= $tagClasses[$ev['category']] ?? '' ?>"><?= htmlspecialchars($ev['category']) ?></span></td>
                  <td class="mono"><?= fmtDate($ev['event_date']) ?></td>
                  <td><?= htmlspecialchars($ev['location']) ?></td>
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
