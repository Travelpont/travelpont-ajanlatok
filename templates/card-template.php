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
$hatra         = tpa_hatralevo_napok( $post_id );
$kategoriak    = get_the_terms( $post_id, 'ajanlat_kategoria' );
$elso_kategoria = ( $kategoriak && ! is_wp_error( $kategoriak ) ) ? current( $kategoriak ) : null;

// ── Deal-adatok: ár-bontás, találat dátuma, státusz, foglalási linkek ─────────
$fo_szam      = tpa_fo_szam( $post_id );
$utazas       = tpa_utazas_ar_fo( $post_id );            // főnkénti repjegy/buszjegy ár + címke
$szallas_ar   = tpa_mezo( $post_id, 'tpa_szallas_ar' );
$talalat      = tpa_mezo( $post_id, 'tpa_talalat_datuma' );
$deal_lejart  = tpa_deal_lejart( $post_id );             // kézi státusz VAGY dátum-lejárat
$utazas_link  = tpa_mezo( $post_id, 'tpa_kiwi_link' );
if ( $utazas_link === '' ) $utazas_link = tpa_mezo( $post_id, 'tpa_busz_link' );
$szallas_link = tpa_mezo( $post_id, 'tpa_szallas_link' );

// Lejárt dealnél a kapcsolt (publikált) Úticél oldalára is linkelünk
$kartya_uticel_id   = absint( tpa_mezo( $post_id, 'tpa_uticel' ) );
$kartya_uticel_post = $kartya_uticel_id ? get_post( $kartya_uticel_id ) : null;
if ( ! $kartya_uticel_post || $kartya_uticel_post->post_type !== 'uticel' || $kartya_uticel_post->post_status !== 'publish' ) {
    $kartya_uticel_post = null;
}
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

            <?php if ( ! $deal_lejart && $hatra !== null && $hatra >= 0 && $hatra <= 7 ) : ?>
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

        <div class="tpa-card-lablec tpa-card-lablec-deal">
            <?php
            // Részár-sorok csak akkor, ha legalább 2 tétel van – egytételes
            // bontás (pl. csak szállás) csak duplázná az Összesen sort.
            $ket_tetel = ( $utazas['ar'] !== '' && $szallas_ar !== '' );
            ?>
            <?php if ( $utazas['ar'] !== '' || $szallas_ar !== '' || $ar !== '' ) : ?>
                <ul class="tpa-card-ar-bontas">
                    <?php if ( $ket_tetel ) : ?>
                        <li><span><?php echo esc_html( $utazas['cimke'] ); ?>:</span><span class="tpa-card-ar-ertek"><?php echo esc_html( tpa_ar_format( $utazas['ar'] ) ); ?>/fő</span></li>
                        <li><span>Szállás:</span><span class="tpa-card-ar-ertek"><?php echo esc_html( tpa_ar_format( $szallas_ar ) ); ?></span></li>
                    <?php endif; ?>
                    <?php if ( $ar !== '' ) : ?>
                        <li class="tpa-card-ar-osszesen<?php echo $ket_tetel ? '' : ' tpa-card-ar-osszesen-egyedul'; ?>"><span>Összesen:</span><span class="tpa-card-ar-ertek"><?php echo esc_html( tpa_ar_format( $ar ) ); ?> / <?php echo esc_html( $fo_szam ); ?> fő</span></li>
                    <?php endif; ?>
                </ul>
            <?php endif; ?>

            <?php if ( $talalat ) : ?>
                <span class="tpa-card-talalat">Találat: <?php echo esc_html( tpa_datum_magyar_rovid( $talalat ) ); ?></span>
            <?php endif; ?>

            <?php if ( $deal_lejart ) : ?>
                <p class="tpa-card-lejart-uzenet">Ez a deal lejárt — de a jó árak visszatérnek!</p>
            <?php elseif ( tpa_talalat_regi( $post_id ) ) : ?>
                <p class="tpa-card-frissesseg">Az ár azóta változhatott — a friss árat a foglalási linken látod</p>
            <?php endif; ?>

            <div class="tpa-card-gombok">
                <?php if ( $deal_lejart ) : ?>
                    <?php $aktualis_link = $utazas_link !== '' ? $utazas_link : $szallas_link; ?>
                    <?php if ( $aktualis_link !== '' ) : ?>
                        <a class="tpa-gomb tpa-gomb-repjegy" href="<?php echo esc_url( $aktualis_link ); ?>"
                           target="_blank" rel="nofollow sponsored noopener">Nézd meg az aktuális árat</a>
                    <?php endif; ?>
                    <?php if ( $kartya_uticel_post ) : ?>
                        <a class="tpa-card-uticel-link" href="<?php echo esc_url( get_permalink( $kartya_uticel_post ) ); ?>">Nézd meg: <?php echo esc_html( get_the_title( $kartya_uticel_post ) ); ?></a>
                    <?php endif; ?>
                <?php elseif ( $utazas_link !== '' || $szallas_link !== '' ) : ?>
                    <?php if ( $utazas_link !== '' ) : ?>
                        <a class="tpa-gomb tpa-gomb-repjegy" href="<?php echo esc_url( $utazas_link ); ?>"
                           target="_blank" rel="nofollow sponsored noopener"><?php echo esc_html( $utazas['cimke'] !== '' ? $utazas['cimke'] : 'Repjegy' ); ?> foglalása</a>
                    <?php endif; ?>
                    <?php if ( $szallas_link !== '' ) : ?>
                        <a class="tpa-gomb" href="<?php echo esc_url( $szallas_link ); ?>"
                           target="_blank" rel="nofollow sponsored noopener">Szállás foglalása</a>
                    <?php endif; ?>
                <?php else : ?>
                    <a class="tpa-gomb" href="<?php the_permalink(); ?>">Megnézem</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</article>
