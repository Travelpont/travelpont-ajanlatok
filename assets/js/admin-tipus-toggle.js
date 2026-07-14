/**
 * Travelpont Ajánlatok – admin űrlap: "Ajánlat típusa" alapján
 * elrejti/megjeleníti a típus-specifikus mezőket (repjegy/Kiwi-link
 * vagy busz-ár/busz-link), fields.php 'show_if_tipus' definíciója alapján.
 */
document.addEventListener( 'DOMContentLoaded', function () {
    var tipusSelect = document.getElementById( 'tpa_ajanlat_tipus' );
    if ( ! tipusSelect ) return;

    var feltetelesMezok = document.querySelectorAll( '[data-tpa-show-if-tipus]' );

    function frissit() {
        var aktualisTipus = tipusSelect.value;
        feltetelesMezok.forEach( function ( mezo ) {
            var engedelyezettTipusok = mezo.getAttribute( 'data-tpa-show-if-tipus' ).split( ',' );
            mezo.style.display = engedelyezettTipusok.indexOf( aktualisTipus ) !== -1 ? '' : 'none';
        } );
    }

    tipusSelect.addEventListener( 'change', frissit );
    frissit();
} );
