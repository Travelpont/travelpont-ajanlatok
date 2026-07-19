<?php
/**
 * Travelpont Ajánlatok – KOMPAKT ajánlatkártya (oldalsávokhoz)
 *
 * Csak a lényeg: borítókép → cím → időpont → ár → "Megnézem" gomb, ami az
 * ajánlat ALOLDALÁRA visz (nem affiliate link!). Ár-bontás, hely-morzsa,
 * találat-dátum és frissesség-sáv itt NINCS – azokat az aloldal adja.
 * Lejárt dealnél az ár áthúzva + "Lejárt" címke a képen.
 * A loop-on belül fut (lista-template.php hívja, nezet="kompakt" esetén).
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$post_id        = get_the_ID();
$idopont        = tpa_idopont_megjelenites( $post_id );  // dátumokból képzett tartomány vagy kézi szöveg
$ejszakak       = tpa_ejszakak_szam( $post_id );         // dátumokból számolva vagy kézi érték
$ar             = tpa_teljes_ar( $post_id );
$fo_szam        = tpa_fo_szam( $post_id );
$deal_lejart    = tpa_deal_lejart( $post_id );           // kézi státusz VAGY dátum-lejárat
$hatra          = tpa_hatralevo_napok( $post_id );
$kategoriak     = get_the_terms( $post_id, 'ajanlat_kategoria' );
$elso_kategoria = ( $kategoriak && ! is_wp_error( $kategoriak ) ) ? current( $kategoriak ) : null;
?>
<article class="tpa-card tpa-card-kompakt<?php echo $deal_lejart ? ' tpa-card-lejart' : ''; ?>">
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

            <?php if ( $deal_lejart ) : ?>
                <span class="tpa-badge tpa-badge-lejarat">Lejárt</span>
            <?php elseif ( $hatra !== null && $hatra >= 0 && $hatra <= 7 ) : ?>
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

        <?php if ( $idopont || $ejszakak !== '' ) : ?>
            <p class="tpa-kkartya-idopont">
                <?php echo tpa_icon( 'calendar' ); ?><span><?php
                echo esc_html( $idopont );
                if ( $ejszakak !== '' ) {
                    echo esc_html( ( $idopont ? ' · ' : '' ) . $ejszakak . ' éj' );
                }
                ?></span>
            </p>
        <?php endif; ?>

        <?php if ( $ar !== '' ) : ?>
            <p class="tpa-kkartya-ar"><?php echo esc_html( tpa_ar_format( $ar ) . ' / ' . $fo_szam . ' fő' ); ?></p>
        <?php endif; ?>

        <a class="tpa-gomb tpa-kkartya-gomb" href="<?php the_permalink(); ?>">Megnézem</a>
    </div>
</article>
