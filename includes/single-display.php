<?php
/**
 * Travelpont Ajánlatok – Aloldal (single) megjelenítés
 *
 * SZÁNDÉKOS ELTÉRÉS az aktivbalaton mintától: ott a plugin teljes
 * single-{cpt}.php sablont ad (single_template filter), ami klasszikus
 * témát (header.php/footer.php) feltételez. Itt ehelyett a tartalom ELÉ
 * fűzzük be az ajánlat-dobozt a the_content szűrővel – ez blokk-témával
 * (Twenty Twenty-Five) ÉS Hello Elementorral is ugyanúgy működik, tehát
 * a témaváltás nem töri el.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

add_filter( 'the_content', function( $content ) {
    if ( ! is_singular( 'ajanlat' ) || ! in_the_loop() || ! is_main_query() ) {
        return $content;
    }

    wp_enqueue_style( 'travelpont-ajanlatok' );

    ob_start();
    include TPA_PATH . 'templates/single-content.php';
    $ajanlat_doboz = ob_get_clean();

    // Doboz felül (ár + gombok azonnal láthatók), a leírás alatta
    return $ajanlat_doboz . $content;
} );
