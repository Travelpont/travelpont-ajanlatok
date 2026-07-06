<?php
/**
 * Travelpont Ajánlatok – Shortcode-ok
 *
 * [travelpont_ajanlatok]
 *   limit="6"          hány ajánlat jelenjen meg (-1 = összes)
 *   kategoria=""       úti cél kategória slug (pl. "tengerpart")
 *   rendezes="ujak"    ujak | ar_novekvo | ar_csokkeno | lejarat
 *   lejartak="nem"     "igen" esetén a lejárt ajánlatok is megjelennek
 *   oszlopok="3"       2 | 3 | 4 – a kártyák kívánt oszlopszáma széles képernyőn
 *
 * A shortcode Elementorban (Shortcode widget) és blokk-témában
 * (Shortcode blokk) is ugyanúgy használható.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

function tpa_ajanlatok_shortcode( $atts ) {
    $atts = shortcode_atts( apply_filters( 'tpa_shortcode_defaults', array(
        'limit'    => 6,
        'kategoria'=> '',
        'rendezes' => 'ujak',
        'lejartak' => 'nem',
        'oszlopok' => 3,
    ) ), $atts, 'travelpont_ajanlatok' );

    $args = array(
        'post_type'      => 'ajanlat',
        'post_status'    => 'publish',
        'posts_per_page' => (int) $atts['limit'],
    );

    // Rendezés
    switch ( $atts['rendezes'] ) {
        case 'ar_novekvo':
            $args['meta_key'] = 'tpa_ar';
            $args['orderby']  = 'meta_value_num';
            $args['order']    = 'ASC';
            break;
        case 'ar_csokkeno':
            $args['meta_key'] = 'tpa_ar';
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
        $args['meta_query'][] = array(
            'relation' => 'OR',
            array( 'key' => 'tpa_ervenyes', 'value' => current_time( 'Y-m-d' ), 'compare' => '>=', 'type' => 'DATE' ),
            array( 'key' => 'tpa_ervenyes', 'compare' => 'NOT EXISTS' ),
            array( 'key' => 'tpa_ervenyes', 'value' => '', 'compare' => '=' ),
        );
    }

    // Bővítési pont: a lekérdezés kívülről is módosítható
    $args = apply_filters( 'tpa_lista_query_args', $args, $atts );

    $tpa_query = new WP_Query( $args );
    $tpa_atts  = $atts;

    wp_enqueue_style( 'travelpont-ajanlatok' );

    ob_start();
    include TPA_PATH . 'templates/lista-template.php';
    return ob_get_clean();
}
add_shortcode( 'travelpont_ajanlatok', 'tpa_ajanlatok_shortcode' );
