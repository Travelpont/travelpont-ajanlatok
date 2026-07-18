<?php
/**
 * Travelpont Ajánlatok – Egy ajánlatkártya sablonja
 * A loop-on belül fut (lista-template.php hívja).
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$post_id       = get_the_ID();
$hely          = tpa_hely_megjelenites( $post_id ); // kézi célállomás VAGY úticél-morzsamenü (nyers)
$indulas       = tpa_mezo( $post_id, 'tpa_indulas' );
$idopont       = tpa_idopont_megjelenites( $post_id );  // dátumokból képzett tartomány vagy kézi szöveg
$ejszakak      = tpa_ejszakak_szam( $post_id );         // dátumokból számolva vagy kézi érték
$utvonal       = tpa_utvonal( $post_id );               // repülős útvonal (BUD → PVK) vagy null
$szallas_nev   = tpa_mezo( $post_id, 'tpa_szallas_nev' );
$csillagok     = tpa_szallas_csillag_html( $post_id );
$ellatas       = tpa_ellatas_nev( $post_id );
$ar            = tpa_teljes_ar( $post_id );
$ar_megjegyzes = tpa_ar_megjegyzes_megjelenites( $post_id );
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
                <div class="tpa-card-kep-ures">🧳</div>
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
            <?php if ( $hely !== '' ) : ?>
                <li class="tpa-card-hely"><?php echo tpa_icon( 'pin' ); ?><span><?php echo esc_html( $hely ); ?></span></li>
            <?php endif; ?>
            <?php if ( $idopont ) : ?>
                <li><?php echo tpa_icon( 'calendar' ); ?><?php echo esc_html( $idopont ); ?></li>
            <?php endif; ?>
            <?php if ( $ejszakak !== '' ) : ?>
                <li><?php echo tpa_icon( 'moon' ); ?><?php echo esc_html( $ejszakak ); ?> éjszaka<?php echo ( ! $utvonal && $indulas ) ? ' · ' . esc_html( $indulas ) . ' indulással' : ''; ?></li>
            <?php elseif ( ! $utvonal && $indulas ) : ?>
                <li><?php echo tpa_icon( 'send' ); ?>Indulás: <?php echo esc_html( $indulas ); ?></li>
            <?php endif; ?>
            <?php if ( $utvonal ) : ?>
                <li class="tpa-card-utvonal"><?php echo tpa_icon( 'send' ); ?><span>
                    <strong class="tpa-utvonal-kod"><?php echo esc_html( $utvonal['kod'] ); ?></strong>
                    <?php if ( $utvonal['varos'] !== '' ) : ?>
                        <span class="tpa-utvonal-varos"><?php echo esc_html( $utvonal['varos'] ); ?></span>
                    <?php endif; ?>
                </span></li>
            <?php endif; ?>
            <?php if ( $szallas_nev !== '' || $ellatas !== '' ) : ?>
                <li class="tpa-card-szallas"><?php echo tpa_icon( 'hotel' ); ?><span>
                    <?php
                    echo esc_html( $szallas_nev );
                    echo $csillagok; // biztonságos HTML (tpa_szallas_csillag_html)
                    if ( $ellatas !== '' ) {
                        echo ( $szallas_nev !== '' ? ' · ' : '' ) . esc_html( $ellatas );
                    }
                    ?>
                </span></li>
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
