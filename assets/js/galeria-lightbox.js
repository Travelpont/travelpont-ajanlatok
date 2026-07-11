/**
 * Travelpont Ajánlatok – Galéria lightbox
 *
 * Függőség-mentes, vanilla JS. Az ajánlat-aloldal galéria-képeit egy
 * teljes képernyős rétegben nyitja meg (nem új böngészőfülön), előző/következő
 * lapozással, felirattal, billentyűzet- és mobil-swipe-vezérléssel.
 *
 * JS nélkül a galéria-elemek sima linkek maradnak a teljes méretű képre
 * (progresszív fejlesztés).
 */
(function () {
    'use strict';

    var galeria = document.querySelector('.tpa-galeria');
    if (!galeria) return;

    var linkek = Array.prototype.slice.call(galeria.querySelectorAll('.tpa-galeria-elem'));
    if (!linkek.length) return;

    var kepek = linkek.map(function (a) {
        return { url: a.getAttribute('href'), caption: a.getAttribute('data-caption') || '' };
    });

    var aktIndex = 0;
    var overlay, imgEl, captionEl, prevBtn, nextBtn, closeBtn;

    function build() {
        overlay = document.createElement('div');
        overlay.className = 'tpa-lightbox';
        overlay.setAttribute('role', 'dialog');
        overlay.setAttribute('aria-modal', 'true');
        overlay.innerHTML =
            '<button class="tpa-lightbox-close" type="button" aria-label="Bezárás"></button>' +
            '<button class="tpa-lightbox-nav tpa-lightbox-prev" type="button" aria-label="Előző kép"></button>' +
            '<figure class="tpa-lightbox-tartalom">' +
                '<img class="tpa-lightbox-kep" alt="">' +
                '<figcaption class="tpa-lightbox-felirat"></figcaption>' +
            '</figure>' +
            '<button class="tpa-lightbox-nav tpa-lightbox-next" type="button" aria-label="Következő kép"></button>';
        document.body.appendChild(overlay);

        imgEl     = overlay.querySelector('.tpa-lightbox-kep');
        captionEl = overlay.querySelector('.tpa-lightbox-felirat');
        prevBtn   = overlay.querySelector('.tpa-lightbox-prev');
        nextBtn   = overlay.querySelector('.tpa-lightbox-next');
        closeBtn  = overlay.querySelector('.tpa-lightbox-close');

        closeBtn.addEventListener('click', close);
        prevBtn.addEventListener('click', function (e) { e.stopPropagation(); mutat(aktIndex - 1); });
        nextBtn.addEventListener('click', function (e) { e.stopPropagation(); mutat(aktIndex + 1); });
        overlay.addEventListener('click', function (e) { if (e.target === overlay) close(); });

        // Mobil-swipe balra/jobbra
        var x0 = null;
        overlay.addEventListener('touchstart', function (e) { x0 = e.changedTouches[0].clientX; }, { passive: true });
        overlay.addEventListener('touchend', function (e) {
            if (x0 === null) return;
            var dx = e.changedTouches[0].clientX - x0;
            if (Math.abs(dx) > 40) mutat(aktIndex + (dx < 0 ? 1 : -1));
            x0 = null;
        }, { passive: true });
    }

    function mutat(i) {
        if (i < 0) i = kepek.length - 1;
        if (i >= kepek.length) i = 0;
        aktIndex = i;
        imgEl.src = kepek[i].url;
        imgEl.alt = kepek[i].caption;
        captionEl.textContent = kepek[i].caption;
        captionEl.style.display = kepek[i].caption ? '' : 'none';
        var tobb = kepek.length > 1;
        prevBtn.style.display = tobb ? '' : 'none';
        nextBtn.style.display = tobb ? '' : 'none';
    }

    function open(i) {
        if (!overlay) build();
        mutat(i);
        overlay.classList.add('tpa-lightbox--open');
        document.body.style.overflow = 'hidden';
        document.addEventListener('keydown', onKey);
        closeBtn.focus();
    }

    function close() {
        overlay.classList.remove('tpa-lightbox--open');
        document.body.style.overflow = '';
        document.removeEventListener('keydown', onKey);
    }

    function onKey(e) {
        if (e.key === 'Escape') close();
        else if (e.key === 'ArrowLeft') mutat(aktIndex - 1);
        else if (e.key === 'ArrowRight') mutat(aktIndex + 1);
    }

    linkek.forEach(function (a, i) {
        a.addEventListener('click', function (e) { e.preventDefault(); open(i); });
    });
})();
