/**
 * Travelpont Ajánlatok – "Link másolása" gomb az aloldali megosztás-sorban.
 * Vágólapra másolja az ajánlat URL-jét, és 2 másodpercre visszajelzést ad.
 */
(function () {
    'use strict';

    document.addEventListener('click', function (e) {
        var gomb = e.target.closest('.tpa-link-masolas');
        if (!gomb) return;

        var url = gomb.getAttribute('data-url') || window.location.href;

        function kesz() {
            var eredeti = gomb.textContent;
            gomb.textContent = '✔ Kimásolva!';
            gomb.disabled = true;
            setTimeout(function () {
                gomb.textContent = eredeti;
                gomb.disabled = false;
            }, 2000);
        }

        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(url).then(kesz, function () { fallbackMasolas(url, kesz); });
        } else {
            fallbackMasolas(url, kesz);
        }
    });

    // Régi böngészők / nem-HTTPS környezet: ideiglenes input + execCommand
    function fallbackMasolas(szoveg, kesz) {
        var input = document.createElement('input');
        input.value = szoveg;
        input.setAttribute('readonly', '');
        input.style.position = 'absolute';
        input.style.left = '-9999px';
        document.body.appendChild(input);
        input.select();
        try { document.execCommand('copy'); kesz(); } catch (hiba) { /* csendben elnyeljük */ }
        document.body.removeChild(input);
    }
})();
