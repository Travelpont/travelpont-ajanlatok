<?php
/**
 * Travelpont Ajánlatok – Ajánlat-doboz az aloldalon
 * (a leírás elé fűzve jelenik meg, lásd includes/single-display.php)
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$post_id       = get_the_ID();
$celallomas    = tpa_mezo( $post_id, 'tpa_celallomas' );
$indulas       = tpa_mezo( $post_id, 'tpa_indulas' );
$idopont       = tpa_mezo( $post_id, 'tpa_idopont' );
$ejszakak      = tpa_mezo( $post_id, 'tpa_ejszakak' );
$ar            = tpa_teljes_ar( $post_id );
$ar_megjegyzes = tpa_mezo( $post_id, 'tpa_ar_megjegyzes' );
$ervenyes      = tpa_mezo( $post_id, 'tpa_ervenyes' );
$kiwi_link     = tpa_mezo( $post_id, 'tpa_kiwi_link' );
$szallas_link  = tpa_mezo( $post_id, 'tpa_szallas_link' );
$platform_nev  = tpa_szallas_platform_nev( $post_id );
$lejart        = tpa_lejart( $post_id );
?>
<div class="tpa-single-doboz">

    <?php if ( $lejart ) : ?>
        <p class="tpa-lejart-jelzes">⚠️ Ez az ajánlat sajnos már lejárt – az árak és a linkek már nem érvényesek. Nézd meg az aktuális ajánlatainkat!</p>
    <?php endif; ?>

    <ul class="tpa-single-info">
        <?php if ( $celallomas ) : ?>
            <li><span class="tpa-info-cimke">📍 Úti cél</span><span><?php echo esc_html( $celallomas ); ?></span></li>
        <?php endif; ?>
        <?php if ( $indulas ) : ?>
            <li><span class="tpa-info-cimke">🛫 Indulás</span><span><?php echo esc_html( $indulas ); ?></span></li>
        <?php endif; ?>
        <?php if ( $idopont ) : ?>
            <li><span class="tpa-info-cimke">📅 Időpont</span><span><?php echo esc_html( $idopont ); ?></span></li>
        <?php endif; ?>
        <?php if ( $ejszakak !== '' ) : ?>
            <li><span class="tpa-info-cimke">🛏️ Éjszakák</span><span><?php echo esc_html( $ejszakak ); ?></span></li>
        <?php endif; ?>
        <?php if ( $ervenyes && ! $lejart ) : ?>
            <li><span class="tpa-info-cimke">⏳ Érvényes</span><span><?php echo esc_html( $ervenyes ); ?>-ig</span></li>
        <?php endif; ?>
    </ul>

    <?php if ( $ar !== '' ) : ?>
        <div class="tpa-single-ar-blokk">
            <span class="tpa-single-ar"><?php echo esc_html( tpa_ar_format( $ar ) ); ?></span>
            <?php if ( $ar_megjegyzes ) : ?>
                <span class="tpa-single-ar-megjegyzes"><?php echo esc_html( $ar_megjegyzes ); ?></span>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php if ( ! $lejart && ( $kiwi_link || $szallas_link ) ) : ?>
        <div class="tpa-single-gombok">
            <?php if ( $kiwi_link ) : ?>
                <a class="tpa-gomb tpa-gomb-repjegy" href="<?php echo esc_url( $kiwi_link ); ?>"
                   target="_blank" rel="nofollow sponsored noopener">
                    ✈️ Repülőjegy megnézése
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

    <?php do_action( 'tpa_single_doboz_vege', $post_id ); // bővítési pont ?>
</div>
