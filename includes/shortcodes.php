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
 *   uticel="nem"       "aktualis": úticél-aloldalon a megnyitott úticél ÉS
 *                      az összes leszármazottja ajánlatait hozza (Ausztria →
 *                      a schladmingi deal is). Találat híján nincs fallback,
 *                      helyette terelő szöveg az Ajánlatok oldalra.
 *                      Más oldalon nincs hatása.
 *   nezet="teljes"     "kompakt": kis kártya oldalsávba – borítókép, cím,
 *                      időpont, ár + "Megnézem" gomb az ajánlat aloldalára
 *                      (nincs ár-bontás, foglalás-gombok, frissesség-sáv).
 *   cim="auto"         a lista fölé írt címsor. "auto": hasonló-módban
 *                      "További ajánlatok", úticél-módban "Ajánlataink
 *                      ehhez az úticélhoz", egyébként nincs cím. Saját
 *                      szöveg megadható, cim="" = soha nincs cím.
 *
 * Ajánlott oldalsáv-használat:
 *   ajánlat-aloldal:  [travelpont_ajanlatok limit="3" hasonlo="igen" oszlopok="1" nezet="kompakt"]
 *   úticél-aloldal:   [travelpont_ajanlatok limit="4" uticel="aktualis" oszlopok="1" nezet="kompakt"]
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
        'uticel'   => 'nem',
        'nezet'    => 'teljes',
        'cim'      => 'auto',
    ) ), $atts, 'travelpont_ajanlatok' );

    $args = array(
        'post_type'      => 'ajanlat',
        'post_status'    => 'publish',
        'posts_per_page' => (int) $atts['limit'],
    );

    $tpa_ures_html = ''; // a lista-template üres-állapot felülírása (úticél-mód tölti)

    // "Úticél" mód (az úticél-aloldal oldalsávjában): a megnyitott úticél ÉS az
    // összes leszármazottja ajánlatai – ugyanaz a kör, mint a korábbi tartalmi
    // "Ajánlataink ehhez az úticélhoz" blokké. Találat híján NINCS fallback
    // (Ausztriánál egy görög deal zavaró lenne), helyette terelő szöveg.
    if ( $atts['uticel'] === 'aktualis' && is_singular( 'uticel' ) ) {
        $uticel_id  = get_queried_object_id();
        $uticel_idk = array( $uticel_id );
        if ( function_exists( 'tpu_get_leszarmazott_idk' ) ) {
            $uticel_idk = array_merge( $uticel_idk, tpu_get_leszarmazott_idk( $uticel_id ) );
        }
        $args['meta_query'][] = array(
            'key'     => 'tpa_uticel',
            'value'   => array_map( 'strval', $uticel_idk ),
            'compare' => 'IN',
        );

        $ures_link     = function_exists( 'tpk_ajanlatok_url' ) ? tpk_ajanlatok_url() : home_url( '/ajanlatok/' );
        $tpa_ures_html = '<p class="tpa-empty">Ehhez az úticélhoz most nincs aktív ajánlatunk – <a href="'
            . esc_url( $ures_link ) . '">nézd meg az összes ajánlatot</a>!</p>';
    }

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

    // Lista-cím: az "auto" a mód szerinti alapcímet adja – így minden
    // oldalsáv egységes felirattal jelenik meg, külön widget-cím nélkül.
    $tpa_lista_cim = $atts['cim'];
    if ( $tpa_lista_cim === 'auto' ) {
        if ( $atts['hasonlo'] === 'igen' && is_singular( 'ajanlat' ) ) {
            $tpa_lista_cim = 'További ajánlatok';
        } elseif ( $atts['uticel'] === 'aktualis' && is_singular( 'uticel' ) ) {
            $tpa_lista_cim = 'Ajánlataink ehhez az úticélhoz';
        } else {
            $tpa_lista_cim = '';
        }
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
