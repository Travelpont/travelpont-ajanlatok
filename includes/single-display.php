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

// ── Schema.org strukturált adat (JSON-LD) az ajánlat-aloldalakra ──────────────
// TouristTrip + beágyazott Offer: a Google így árat, pénznemet és érvényességi
// dátumot is társíthat a találathoz. A Yoast az Article/WebPage sémát adja,
// ez azt egészíti ki az utazás-specifikus résszel.
add_action( 'wp_head', function() {
    if ( ! is_singular( 'ajanlat' ) ) return;

    $post_id = get_queried_object_id();
    if ( ! $post_id ) return;

    $adat = array(
        '@context' => 'https://schema.org',
        '@type'    => 'TouristTrip',
        'name'     => get_the_title( $post_id ),
        'url'      => get_permalink( $post_id ),
        'provider' => array(
            '@type' => 'Organization',
            'name'  => 'Travelpont',
            'url'   => home_url( '/' ),
        ),
    );

    $kivonat = has_excerpt( $post_id )
        ? get_the_excerpt( $post_id )
        : wp_trim_words( wp_strip_all_tags( get_post_field( 'post_content', $post_id ) ), 40, '…' );
    if ( $kivonat ) $adat['description'] = $kivonat;

    $kep = get_the_post_thumbnail_url( $post_id, 'large' );
    if ( $kep ) $adat['image'] = $kep;

    $ar = tpa_teljes_ar( $post_id );
    if ( $ar !== '' ) {
        $offer = array(
            '@type'         => 'Offer',
            'price'         => (string) $ar,
            'priceCurrency' => 'HUF',
            'url'           => get_permalink( $post_id ),
            'availability'  => tpa_deal_lejart( $post_id ) ? 'https://schema.org/Discontinued' : 'https://schema.org/InStock',
        );
        $ervenyes = tpa_mezo( $post_id, 'tpa_ervenyes' );
        if ( $ervenyes ) {
            $offer['validThrough']    = $ervenyes;
            $offer['priceValidUntil'] = $ervenyes;
        }
        $adat['offers'] = $offer;
    }

    echo '<script type="application/ld+json">'
        . wp_json_encode( $adat, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES )
        . '</script>' . "\n";
} );

add_filter( 'the_content', function( $content ) {
    if ( ! is_singular( 'ajanlat' ) || ! in_the_loop() || ! is_main_query() ) {
        return $content;
    }

    wp_enqueue_style( 'travelpont-ajanlatok' );

    ob_start();
    include TPA_PATH . 'templates/single-content.php';
    $felso = ob_get_clean();

    ob_start();
    include TPA_PATH . 'templates/single-also.php';
    $also = ob_get_clean();

    // A leírás a felső rész (hero, kompakt ár, tények, "miért szuper") és az
    // alsó rész (galéria, ár-panel + gombok, megosztás, ajánlók) KÖZÉ kerül –
    // így a szállás szövege és képei egymás mellett vannak, a foglalási döntés
    // pedig a teljes információ után jön (2026-07-19 UX-átrendezés).
    return $felso . $content . $also;
} );
