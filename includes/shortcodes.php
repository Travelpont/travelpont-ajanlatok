<?php
/**
 * Travelpont Ajánlatok – Shortcode-ok
 *
 * [travelpont_ajanlatok]
 *   limit="6"          hány ajánlat jelenjen meg (-1 = összes)
 *   kategoria=""       úti cél kategória slug (pl. "tengerpart")
 *   rendezes="ujak"    ujak | ar_novekvo | ar_csokkeno | lejarat
 *   lejartak="nem"     "igen" esetén a lejárt ajánlatok is megjelennek
 *   oszlopok="3"       1 | 2 | 3 | 4 – kívánt oszlopszám széles képernyőn
 *                      (1 = mindig egy hasáb, pl. oldalsávba)
 *   hasonlo="nem"      "igen": ajánlat-aloldalon az AKTUÁLIS ajánlatot
 *                      kihagyja, és az azonos úticélra szólókat hozza
 *                      előre (ha arra nincs élő ajánlat, a legfrissebbeket)
 *                      – oldalsávos "További ajánlatok" blokkhoz való.
 *                      Más oldalon nincs hatása.
 *
 * A shortcode Elementorban (Shortcode widget), blokk-témában (Shortcode
 * blokk) és widget-területen (oldalsáv) is ugyanúgy használható.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

function tpa_ajanlatok_shortcode( $atts ) {
    $atts = shortcode_atts( apply_filters( 'tpa_shortcode_defaults', array(
        'limit'    => 6,
        'kategoria'=> '',
        'rendezes' => 'ujak',
        'lejartak' => 'nem',
        'oszlopok' => 3,
        'hasonlo'  => 'nem',
    ) ), $atts, 'travelpont_ajanlatok' );

    $args = array(
        'post_type'      => 'ajanlat',
        'post_status'    => 'publish',
        'posts_per_page' => (int) $atts['limit'],
    );

    // "Hasonló" mód (pl. az ajánlat-aloldal oldalsávjában): az épp nézett
    // ajánlat kimarad, és ha van bekötött úticél, az arra szóló élő ajánlatok
    // jönnek – a lekérdezés után, találat híján a szűkítést elengedjük.
    $hasonlo_uticel_szures = false;
    if ( $atts['hasonlo'] === 'igen' && is_singular( 'ajanlat' ) ) {
        $aktualis_id          = get_queried_object_id();
        $args['post__not_in'] = array( $aktualis_id );
        $hasonlo_uticel_id    = absint( tpa_mezo( $aktualis_id, 'tpa_uticel' ) );
        if ( $hasonlo_uticel_id ) {
            $args['meta_query']['tpa_hasonlo_uticel'] = array( 'key' => 'tpa_uticel', 'value' => (string) $hasonlo_uticel_id );
            $hasonlo_uticel_szures = true;
        }
    }

    // Rendezés
    // Ár szerint a mentéskor eltárolt SZÁMÍTOTT ár (tpa_ar_szamitott) alapján
    // rendezünk – így azok az ajánlatok is jó helyre kerülnek, ahol a tpa_ar
    // üres és a teljes ár a részárak összegéből adódik.
    switch ( $atts['rendezes'] ) {
        case 'ar_novekvo':
            $args['meta_key'] = 'tpa_ar_szamitott';
            $args['orderby']  = 'meta_value_num';
            $args['order']    = 'ASC';
            break;
        case 'ar_csokkeno':
            $args['meta_key'] = 'tpa_ar_szamitott';
            $args['orderby']  = 'meta_value_num';
            $args['order']    = 'DESC';
            break;
        case 'lejarat': // ami hamarabb lejár, előrébb
            $args['meta_key'] = 'tpa_ervenyes';
            $args['orderby']  = 'meta_value';
            $args['order']    = 'ASC';
            break;
        default: // ujak
            $args['orderby'] = 'date';
            $args['order']   = 'DESC';
    }

    // Kategória szűrő
    if ( $atts['kategoria'] ) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'ajanlat_kategoria',
                'field'    => 'slug',
                'terms'    => array_map( 'trim', explode( ',', $atts['kategoria'] ) ),
            ),
        );
    }

    // Lejárt ajánlatok kiszűrése (alapértelmezés)
    if ( $atts['lejartak'] !== 'igen' ) {
        $args['meta_query'][] = tpa_nem_lejart_meta_query();
    }

    // Bővítési pont: a lekérdezés kívülről is módosítható
    $args = apply_filters( 'tpa_lista_query_args', $args, $atts );

    $tpa_query = new WP_Query( $args );

    // Hasonló mód: ha ugyanarra az úticélra nincs másik élő ajánlat, a
    // szűkítést elengedjük és a legfrissebbeket mutatjuk (mint az aloldali
    // hasonló-blokk) – az oldalsáv ne maradjon üresen.
    if ( $hasonlo_uticel_szures && ! $tpa_query->have_posts() ) {
        unset( $args['meta_query']['tpa_hasonlo_uticel'] );
        $tpa_query = new WP_Query( $args );
    }

    $tpa_atts = $atts;

    wp_enqueue_style( 'travelpont-ajanlatok' );

    ob_start();
    include TPA_PATH . 'templates/lista-template.php';
    return ob_get_clean();
}
add_shortcode( 'travelpont_ajanlatok', 'tpa_ajanlatok_shortcode' );
