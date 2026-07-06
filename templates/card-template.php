<?php
/**
 * Travelpont Ajánlatok – Egy ajánlatkártya sablonja
 * A loop-on belül fut (lista-template.php hívja).
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$post_id       = get_the_ID();
$celallomas    = tpa_mezo( $post_id, 'tpa_celallomas' );
$indulas       = tpa_mezo( $post_id, 'tpa_indulas' );
$idopont       = tpa_mezo( $post_id, 'tpa_idopont' );
$ejszakak      = tpa_mezo( $post_id, 'tpa_ejszakak' );
$ar            = tpa_mezo( $post_id, 'tpa_ar' );
$ar_megjegyzes = tpa_mezo( $post_id, 'tpa_ar_megjegyzes' );
$hatra         = tpa_hatralevo_napok( $post_id );
$kategoriak    = get_the_terms( $post_id, 'ajanlat_kategoria' );
$elso_kategoria = ( $kategoriak && ! is_wp_error( $kategoriak ) ) ? current( $kategoriak ) : null;
?>
<article class="tpa-card">
    <a class="tpa-card-kep-link" href="<?php the_permalink(); ?>">
        <div class="tpa-card-kep">
            <?php if ( has_post_thumbnail() ) : ?>
                <?php the_post_thumbnail( 'medium_large' ); ?>
            <?php else : ?>
                <div class="tpa-card-kep-ures">✈️</div>
            <?php endif; ?>

            <?php if ( $elso_kategoria ) : ?>
                <span class="tpa-badge tpa-badge-kategoria"><?php echo esc_html( $elso_kategoria->name ); ?></span>
            <?php endif; ?>

            <?php if ( $hatra !== null && $hatra >= 0 && $hatra <= 7 ) : ?>
                <span class="tpa-badge tpa-badge-lejarat">
                    <?php echo $hatra === 0 ? 'Utolsó nap!' : esc_html( 'Még ' . $hatra . ' napig' ); ?>
                </span>
            <?php endif; ?>
        </div>
    </a>

    <div class="tpa-card-torzs">
        <h3 class="tpa-card-cim">
            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        </h3>

        <ul class="tpa-card-info">
            <?php if ( $celallomas ) : ?>
                <li>📍 <?php echo esc_html( $celallomas ); ?></li>
            <?php endif; ?>
            <?php if ( $idopont ) : ?>
                <li>📅 <?php echo esc_html( $idopont ); ?></li>
            <?php endif; ?>
            <?php if ( $ejszakak !== '' ) : ?>
                <li>🛏️ <?php echo esc_html( $ejszakak ); ?> éjszaka<?php echo $indulas ? ' · ' . esc_html( $indulas ) . ' indulással' : ''; ?></li>
            <?php elseif ( $indulas ) : ?>
                <li>🛫 Indulás: <?php echo esc_html( $indulas ); ?></li>
            <?php endif; ?>
        </ul>

        <div class="tpa-card-lablec">
            <div class="tpa-card-ar-blokk">
                <?php if ( $ar !== '' ) : ?>
                    <span class="tpa-card-ar"><?php echo esc_html( tpa_ar_format( $ar ) ); ?></span>
                    <?php if ( $ar_megjegyzes ) : ?>
                        <span class="tpa-card-ar-megjegyzes"><?php echo esc_html( $ar_megjegyzes ); ?></span>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            <a class="tpa-gomb" href="<?php the_permalink(); ?>">Megnézem</a>
        </div>
    </div>
</article>
