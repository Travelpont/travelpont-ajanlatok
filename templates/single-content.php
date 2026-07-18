<?php
/**
 * Travelpont Ajánlatok – Ajánlat-doboz az aloldalon
 * (a leírás elé fűzve jelenik meg, lásd includes/single-display.php)
 *
 * Szerkezet: hero kép → infó-sor (chip-ek) → kiemelt ár+CTA panel → galéria.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$post_id       = get_the_ID();
$celallomas    = tpa_mezo( $post_id, 'tpa_celallomas' );
$indulas       = tpa_mezo( $post_id, 'tpa_indulas' );
$idopont       = tpa_idopont_megjelenites( $post_id );  // dátumokból képzett tartomány vagy kézi szöveg
$ejszakak      = tpa_ejszakak_szam( $post_id );         // dátumokból számolva vagy kézi érték
$szallas_nev   = tpa_mezo( $post_id, 'tpa_szallas_nev' );
$csillagok     = tpa_szallas_csillag_html( $post_id );
$ellatas       = tpa_ellatas_nev( $post_id );
$tipus         = tpa_mezo( $post_id, 'tpa_ajanlat_tipus' );
$ar            = tpa_teljes_ar( $post_id );
$ar_megjegyzes = tpa_ar_megjegyzes_megjelenites( $post_id );
$ervenyes      = tpa_mezo( $post_id, 'tpa_ervenyes' );
$kiwi_link     = tpa_mezo( $post_id, 'tpa_kiwi_link' );
$busz_link     = tpa_mezo( $post_id, 'tpa_busz_link' );
$szallas_link  = tpa_mezo( $post_id, 'tpa_szallas_link' );
$platform_nev  = tpa_szallas_platform_nev( $post_id );
$lejart        = tpa_lejart( $post_id );
$morzsa        = tpa_uticel_breadcrumb( tpa_mezo( $post_id, 'tpa_uticel' ), array( 'linkelt' => true ) );

// Ár-bontás sorai (csak a típushoz tartozó, kitöltött részárak).
// Csak akkor rajzoljuk ki, ha legalább 2 tétel van – egytételes bontás nem mond
// többet a végösszegnél.
$ar_reszek = array();
if ( $tipus === 'repulo_szallas' || $tipus === '' ) {
    $repjegy_ar = tpa_mezo( $post_id, 'tpa_repjegy_ar' );
    if ( $repjegy_ar !== '' ) $ar_reszek[] = array( 'Repülőjegy (oda-vissza)', $repjegy_ar );
}
if ( $tipus === 'busz_szallas' ) {
    $busz_ar = tpa_mezo( $post_id, 'tpa_busz_ar' );
    if ( $busz_ar !== '' ) $ar_reszek[] = array( 'Buszjegy (oda-vissza)', $busz_ar );
}
$szallas_ar = tpa_mezo( $post_id, 'tpa_szallas_ar' );
if ( $szallas_ar !== '' ) {
    $ar_reszek[] = array( 'Szállás' . ( $ejszakak !== '' ? ' (' . $ejszakak . ' éj)' : '' ), $szallas_ar );
}
?>
<div class="tpa-single-doboz">

    <?php if ( $lejart ) : ?>
        <p class="tpa-lejart-jelzes">⚠️ Ez az ajánlat sajnos már lejárt – az árak és a linkek már nem érvényesek. Nézd meg az aktuális ajánlatainkat!</p>
    <?php endif; ?>

    <?php if ( has_post_thumbnail( $post_id ) ) : ?>
        <div class="tpa-single-hero">
            <?php echo get_the_post_thumbnail( $post_id, 'large' ); ?>
        </div>
    <?php endif; ?>

    <?php if ( $morzsa !== '' ) : ?>
        <nav class="tpa-single-morzsa" aria-label="Úticél útvonal"><?php echo $morzsa; // linkelt, escapelt (tpa_uticel_breadcrumb) ?></nav>
    <?php endif; ?>

    <ul class="tpa-single-info">
        <?php if ( $celallomas ) : ?>
            <li><?php echo tpa_icon( 'pin' ); ?><span class="tpa-info-cimke">Úti cél</span><span class="tpa-info-ertek"><?php echo esc_html( $celallomas ); ?></span></li>
        <?php endif; ?>
        <?php if ( $indulas ) : ?>
            <li><?php echo tpa_icon( 'send' ); ?><span class="tpa-info-cimke">Indulás</span><span class="tpa-info-ertek"><?php echo esc_html( $indulas ); ?></span></li>
        <?php endif; ?>
        <?php if ( $idopont ) : ?>
            <li><?php echo tpa_icon( 'calendar' ); ?><span class="tpa-info-cimke">Időpont</span><span class="tpa-info-ertek"><?php echo esc_html( $idopont ); ?></span></li>
        <?php endif; ?>
        <?php if ( $ejszakak !== '' ) : ?>
            <li><?php echo tpa_icon( 'moon' ); ?><span class="tpa-info-cimke">Éjszakák</span><span class="tpa-info-ertek"><?php echo esc_html( $ejszakak ); ?></span></li>
        <?php endif; ?>
        <?php if ( $szallas_nev !== '' ) : ?>
            <li><?php echo tpa_icon( 'hotel' ); ?><span class="tpa-info-cimke">Szállás</span><span class="tpa-info-ertek"><?php echo esc_html( $szallas_nev ); ?><?php echo $csillagok; // biztonságos HTML ?></span></li>
        <?php endif; ?>
        <?php if ( $ellatas !== '' ) : ?>
            <li><?php echo tpa_icon( 'utensils' ); ?><span class="tpa-info-cimke">Ellátás</span><span class="tpa-info-ertek"><?php echo esc_html( $ellatas ); ?></span></li>
        <?php endif; ?>
        <?php if ( $ervenyes && ! $lejart ) : ?>
            <li><?php echo tpa_icon( 'clock' ); ?><span class="tpa-info-cimke">Érvényes</span><span class="tpa-info-ertek"><?php echo esc_html( tpa_datum_magyar( $ervenyes, 'Y. F j' ) ); ?>-ig</span></li>
        <?php endif; ?>
    </ul>

    <?php if ( $ar !== '' || ( ! $lejart && ( $kiwi_link || $busz_link || $szallas_link ) ) ) : ?>
        <div class="tpa-single-ar-panel">
            <?php if ( $ar !== '' && count( $ar_reszek ) >= 2 ) : ?>
                <ul class="tpa-single-ar-bontas">
                    <?php foreach ( $ar_reszek as $ar_resz ) : ?>
                        <li><span><?php echo esc_html( $ar_resz[0] ); ?></span><span><?php echo esc_html( tpa_ar_format( $ar_resz[1] ) ); ?></span></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <?php if ( $ar !== '' ) : ?>
                <div class="tpa-single-ar-blokk">
                    <span class="tpa-single-ar"><?php echo esc_html( tpa_ar_format( $ar ) ); ?></span>
                    <?php if ( $ar_megjegyzes ) : ?>
                        <span class="tpa-single-ar-megjegyzes"><?php echo esc_html( $ar_megjegyzes ); ?></span>
                    <?php endif; ?>
                </div>
                <p class="tpa-ar-tartalom">
                    <?php
                    $ar_tartalom = tpa_ar_tartalom_szoveg( $post_id );
                    if ( $ar_tartalom !== '' ) echo esc_html( $ar_tartalom ) . ' ';
                    ?>Árak ellenőrizve: <?php echo esc_html( get_the_modified_date( 'Y. F j.', $post_id ) ); ?> – a partneroldalakon az aktuális ár ettől eltérhet.
                </p>
            <?php endif; ?>

            <?php if ( ! $lejart && ( $kiwi_link || $busz_link || $szallas_link ) ) : ?>
                <div class="tpa-single-gombok">
                    <?php if ( $kiwi_link ) : ?>
                        <a class="tpa-gomb tpa-gomb-repjegy" href="<?php echo esc_url( $kiwi_link ); ?>"
                           target="_blank" rel="nofollow sponsored noopener">
                            ✈️ Repülőjegy megnézése
                        </a>
                    <?php endif; ?>
                    <?php if ( $busz_link ) : ?>
                        <a class="tpa-gomb tpa-gomb-busz" href="<?php echo esc_url( $busz_link ); ?>"
                           target="_blank" rel="nofollow sponsored noopener">
                            🚌 Buszjegy megnézése
                        </a>
                    <?php endif; ?>
                    <?php if ( $szallas_link ) : ?>
                        <a class="tpa-gomb tpa-gomb-szallas" href="<?php echo esc_url( $szallas_link ); ?>"
                           target="_blank" rel="nofollow sponsored noopener">
                            🏨 Szállás megnézése<?php echo $platform_nev ? ' – ' . esc_html( $platform_nev ) : ''; ?>
                        </a>
                    <?php endif; ?>
                </div>
                <p class="tpa-affiliate-kozzetetel">
                    <?php echo esc_html( apply_filters(
                        'tpa_affiliate_kozzetetel_szoveg',
                        'A fenti linkek affiliate linkek: ha rajtuk keresztül foglalsz, a Travelpont jutalékot kap – neked ez semmivel sem kerül többe. Köszönjük, hogy így támogatod a munkánkat! 💛'
                    ) ); ?>
                </p>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php
    // ── Galéria (a Portálból feltöltött további fotók, tpa_galeria_ids meta) ──
    $galeria_idk = get_post_meta( $post_id, 'tpa_galeria_ids', true );
    $galeria_idk = is_array( $galeria_idk ) ? array_map( 'intval', $galeria_idk ) : array();
    if ( $galeria_idk ) : ?>
        <div class="tpa-galeria">
            <?php foreach ( $galeria_idk as $kep_id ) :
                if ( ! wp_attachment_is_image( $kep_id ) ) continue;
                $felirat = wp_get_attachment_caption( $kep_id );
                $alt     = $felirat ? $felirat : get_the_title( $post_id ); ?>
                <figure class="tpa-galeria-cella">
                    <a href="<?php echo esc_url( wp_get_attachment_url( $kep_id ) ); ?>" class="tpa-galeria-elem" data-caption="<?php echo esc_attr( $felirat ); ?>">
                        <?php echo wp_get_attachment_image( $kep_id, 'medium_large', false, array( 'loading' => 'lazy', 'alt' => $alt ) ); ?>
                    </a>
                    <?php if ( $felirat ) : ?>
                        <figcaption class="tpa-galeria-felirat"><?php echo esc_html( $felirat ); ?></figcaption>
                    <?php endif; ?>
                </figure>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php do_action( 'tpa_single_doboz_vege', $post_id ); // bővítési pont ?>
</div>
