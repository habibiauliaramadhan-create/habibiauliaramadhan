// Klik foto (yang punya class "zoomable") -> foto membesar di tengah layar,
// background jadi gelap. Klik lagi di mana saja (atau tombol X / Esc) untuk menutup.
(function () {
  function getOverlay() {
    var ov = document.getElementById('photoLightbox');
    if (!ov) {
      ov = document.createElement('div');
      ov.id = 'photoLightbox';
      ov.className = 'photo-lightbox';
      ov.innerHTML = '<button type="button" class="photo-lightbox-close" aria-label="Tutup">&times;</button><img alt="">';
      document.body.appendChild(ov);

      ov.addEventListener('click', function (e) {
        closeLightbox();
      });
    }
    return ov;
  }

  function openLightbox(src, alt) {
    var ov = getOverlay();
    var img = ov.querySelector('img');
    img.src = src;
    img.alt = alt || '';
    ov.classList.add('is-open');
    document.body.style.overflow = 'hidden';
  }

  function closeLightbox() {
    var ov = document.getElementById('photoLightbox');
    if (ov) ov.classList.remove('is-open');
    document.body.style.overflow = '';
  }

  document.addEventListener('click', function (e) {
    var trigger = e.target.closest('.zoomable');
    if (!trigger) return;

    var img = trigger.tagName === 'IMG' ? trigger : trigger.querySelector('img');
    if (!img || !img.src) return;

    e.preventDefault();
    openLightbox(img.src, img.alt);
  });

  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') closeLightbox();
  });
})();
