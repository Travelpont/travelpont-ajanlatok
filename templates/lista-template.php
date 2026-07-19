<?php
/**
 * Travelpont Ajánlatok – Lista (kártyarács) sablon
 * Bemenet: $tpa_query (WP_Query), $tpa_atts (shortcode attribútumok)
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$oszlopok = max( 1, min( 4, (int) $tpa_atts['oszlopok'] ) );
// Az oszlopszámot a kártyák minimális szélességén keresztül érjük el,
// így mobilon automatikusan egymás alá rendeződnek.
// Az 1 oszlop (100% minimum) mindig egy hasáb – oldalsávba való.
$min_szelesseg = array( 1 => '100%', 2 => '340px', 3 => '270px', 4 => '220px' );
?>

<?php if ( $tpa_query->have_posts() ) : ?>
    <div class="tpa-grid" style="--tpa-card-min: <?php echo esc_attr( $min_szelesseg[ $oszlopok ] ); ?>;">
        <?php
        while ( $tpa_query->have_posts() ) :
            $tpa_query->the_post();
            include TPA_PATH . 'templates/card-template.php';
        endwhile;
        wp_reset_postdata();
        ?>
    </div>
<?php else : ?>
    <p class="tpa-empty"><?php echo esc_html( apply_filters( 'tpa_ures_lista_szoveg', 'Jelenleg nincs aktív ajánlat – nézz vissza hamarosan! 🧳' ) ); ?></p>
<?php endif; ?>
