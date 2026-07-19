<?php
/**
 * Travelpont Ajánlatok – Egy ajánlatkártya sablonja
 * A loop-on belül fut (lista-template.php hívja).
 *
 * A kártya dolga a FIGYELEMFELKELTÉS, nem a foglalás: alap infók + összár +
 * "Megnézem" gomb az aloldalra. Ár-bontás, szállás-részletek, frissesség-
 * figyelmeztetés és foglalás gombok az ALOLDALON vannak (single-content/also).
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$post_id       = get_the_ID();
$hely          = tpa_hely_megjelenites( $post_id ); // kézi célállomás VAGY "Város, Ország" az úticélból (nyers)
$indulas       = tpa_mezo( $post_id, 'tpa_indulas' );
$idopont       = tpa_idopont_megjelenites( $post_id );  // dátumokból képzett tartomány vagy kézi szöveg
$ejszakak      = tpa_ejszakak_szam( $post_id );         // dátumokból számolva vagy kézi érték
$utvonal       = tpa_utvonal( $post_id );               // repülős útvonal (BUD → PVK) vagy null
$ar            = tpa_teljes_ar( $post_id );
$fo_szam       = tpa_fo_szam( $post_id );
$talalat       = tpa_mezo( $post_id, 'tpa_talalat_datuma' );
$deal_lejart   = tpa_deal_lejart( $post_id );            // kézi státusz VAGY dátum-lejárat
$hatra         = tpa_hatralevo_napok( $post_id );
$kategoriak    = get_the_terms( $post_id, 'ajanlat_kategoria' );
$elso_kategoria = ( $kategoriak && ! is_wp_error( $kategoriak ) ) ? current( $kategoriak ) : null;
?>
<article class="tpa-card<?php echo $deal_lejart ? ' tpa-card-lejart' : ''; ?>">
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
        </ul>

        <div class="tpa-card-lablec tpa-card-lablec-deal">
            <?php if ( $ar !== '' ) : ?>
                <ul class="tpa-card-ar-bontas">
                    <li class="tpa-card-ar-osszesen tpa-card-ar-osszesen-egyedul<?php echo $deal_lejart ? ' tpa-ar-athuzva' : ''; ?>"><span>Összesen:</span><span class="tpa-card-ar-ertek"><?php echo esc_html( tpa_osszeg_format( $post_id, $ar ) ); ?> / <?php echo esc_html( $fo_szam ); ?> fő</span></li>
                </ul>
            <?php endif; ?>

            <?php if ( $talalat ) : ?>
                <span class="tpa-card-talalat">Találat: <?php echo esc_html( tpa_datum_magyar_rovid( $talalat ) ); ?></span>
            <?php endif; ?>

            <?php if ( $deal_lejart ) : ?>
                <p class="tpa-card-lejart-uzenet">Ez a deal lejárt — de a jó árak visszatérnek!</p>
            <?php endif; ?>

            <div class="tpa-card-gombok">
                <a class="tpa-gomb" href="<?php the_permalink(); ?>">Megnézem</a>
            </div>
        </div>
    </div>
</article>
