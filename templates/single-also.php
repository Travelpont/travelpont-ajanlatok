<?php
/**
 * Travelpont Ajánlatok – Aloldal ALSÓ rész (a leírás MÖGÉ fűzve)
 *
 * Sorrend: galéria (a szállás képei közvetlenül a róla szóló szöveg után) →
 * teljes ár-panel + foglalás gombok (a döntési pont, minden infó birtokában) →
 * megosztás → úticél-ajánló → hasonló ajánlatok (zárás).
 * A felső részt a single-content.php adja – a kettő a the_content szűrőben
 * ugyanabban a hatókörben fut, de ez a fájl önállóan is megállja a helyét.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$post_id       = get_the_ID();
$tipus         = tpa_mezo( $post_id, 'tpa_ajanlat_tipus' );
$ejszakak      = tpa_ejszakak_szam( $post_id );
$fo_szam       = tpa_fo_szam( $post_id );
$utazas        = tpa_utazas_ar_fo( $post_id );          // főnkénti repjegy/buszjegy ár + címke
$szallas_ar    = tpa_mezo( $post_id, 'tpa_szallas_ar' );
$ar            = tpa_teljes_ar( $post_id );
$ar_megjegyzes = tpa_ar_megjegyzes_megjelenites( $post_id );
$ervenyes      = tpa_mezo( $post_id, 'tpa_ervenyes' );
$kiwi_link     = tpa_mezo( $post_id, 'tpa_kiwi_link' );
$busz_link     = tpa_mezo( $post_id, 'tpa_busz_link' );
$szallas_link  = tpa_mezo( $post_id, 'tpa_szallas_link' );
$platform_nev  = tpa_szallas_platform_nev( $post_id );
$lejart        = tpa_deal_lejart( $post_id ); // kézi "Lejárt" státusz VAGY érvényességi dátum

// Ár-bontás sorai: a repjegy/buszjegy mező FŐNKÉNTI árat tárol, itt a fő-számmal
// felszorzott tétel szerepel, hogy a sorok összege kiadja a végösszeget.
// Csak akkor rajzoljuk ki, ha legalább 2 tétel van.
$ar_reszek = array();
if ( $utazas['ar'] !== '' ) {
    $utazas_cimke = $utazas['cimke'] === 'Buszjegy' ? 'Buszjegy' : 'Repülőjegy';
    $ar_reszek[] = array(
        $utazas_cimke . ' (oda-vissza, ' . $fo_szam . ' fő)',
        (float) $utazas['ar'] * $fo_szam,
    );
}
if ( $szallas_ar !== '' ) {
    $ar_reszek[] = array( 'Szállás' . ( $ejszakak !== '' ? ' (' . $ejszakak . ' éj)' : '' ), $szallas_ar );
}
?>
<div class="tpa-single-doboz tpa-single-also">

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

    <?php // ── Teljes ár-panel: bontás + összesen + foglalás gombok ─────────────── ?>
    <?php if ( $ar !== '' || $kiwi_link || $busz_link || $szallas_link ) : ?>
        <div class="tpa-single-ar-panel<?php echo $lejart ? ' tpa-ar-lejart' : ''; ?>" id="tpa-foglalas">
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
                    // A találat dátuma az árak tényleges ellenőrzésének napja – a
                    // módosítás dátuma bármilyen szerkesztéstől frissülne, az félrevezető.
                    $arak_ellenorizve = tpa_mezo( $post_id, 'tpa_talalat_datuma' );
                    if ( $arak_ellenorizve === '' ) $arak_ellenorizve = get_the_modified_date( 'Y-m-d', $post_id );
                    ?>Árak ellenőrizve: <?php echo esc_html( tpa_datum_magyar( $arak_ellenorizve ) ); ?> – a foglalási oldalon az aktuális ár ettől eltérhet.<?php
                    if ( $ervenyes && ! $lejart ) {
                        echo ' Az ajánlat ' . esc_html( tpa_datum_magyar( $ervenyes, 'Y. F j' ) ) . '-ig érvényes.';
                    }
                    ?>
                </p>
            <?php endif; ?>

            <?php if ( $lejart ) : ?>
                <?php
                // Lejárt deal: az árak halványítva (CSS), egyetlen gomb az
                // aktuális ár ellenőrzésére – a kártya-viselkedéssel egyezően.
                $utazas_link   = $kiwi_link ? $kiwi_link : $busz_link;
                $aktualis_link = $utazas_link ? $utazas_link : $szallas_link;
                ?>
                <?php if ( $aktualis_link ) : ?>
                    <div class="tpa-single-gombok">
                        <a class="tpa-gomb tpa-gomb-repjegy" href="<?php echo esc_url( $aktualis_link ); ?>"
                           target="_blank" rel="nofollow sponsored noopener">Nézd meg az aktuális árat</a>
                    </div>
                <?php endif; ?>
            <?php elseif ( $kiwi_link || $busz_link || $szallas_link ) : ?>
                <?php
                // Kétgombos (utazás + szállás) ajánlatnál a gombok számozott
                // lépésekké válnak – így a vendég látja, hogy a foglalás két külön
                // helyen történik, és mindkettőt innen érdemes indítania.
                $utazas_link = $kiwi_link ? $kiwi_link : $busz_link;
                $ket_lepes   = ( $utazas_link && $szallas_link );
                ?>
                <div class="tpa-single-gombok<?php echo $ket_lepes ? ' tpa-gombok-lepesek' : ''; ?>">
                    <?php if ( $kiwi_link ) : ?>
                        <a class="tpa-gomb tpa-gomb-repjegy" href="<?php echo esc_url( $kiwi_link ); ?>"
                           target="_blank" rel="nofollow sponsored noopener">
                            <?php if ( $ket_lepes ) : ?><span class="tpa-gomb-lepes">1. lépés</span><?php endif; ?>
                            ✈️ Repülőjegy foglalása
                        </a>
                    <?php endif; ?>
                    <?php if ( $busz_link ) : ?>
                        <a class="tpa-gomb tpa-gomb-busz" href="<?php echo esc_url( $busz_link ); ?>"
                           target="_blank" rel="nofollow sponsored noopener">
                            <?php if ( $ket_lepes ) : ?><span class="tpa-gomb-lepes">1. lépés</span><?php endif; ?>
                            🚌 Buszjegy foglalása
                        </a>
                    <?php endif; ?>
                    <?php if ( $szallas_link ) : ?>
                        <a class="tpa-gomb tpa-gomb-szallas" href="<?php echo esc_url( $szallas_link ); ?>"
                           target="_blank" rel="nofollow sponsored noopener">
                            <?php if ( $ket_lepes ) : ?><span class="tpa-gomb-lepes">2. lépés</span><?php endif; ?>
                            🏨 Szállás foglalása<?php echo $platform_nev ? ' – ' . esc_html( $platform_nev ) : ''; ?>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php
    // ── "Küldd el az útitársadnak" – megosztás (az utazásról párban döntenek) ──
    $megoszt_url   = get_permalink( $post_id );
    $megoszt_szoveg = get_the_title( $post_id ) . ( $ar !== '' ? ' – ' . tpa_ar_format( $ar ) : '' );
    ?>
    <div class="tpa-megosztas">
        <span class="tpa-megosztas-cimke"><?php echo tpa_icon( 'share' ); ?>Küldd el az útitársadnak:</span>
        <a class="tpa-megosztas-gomb" target="_blank" rel="noopener"
           href="https://wa.me/?text=<?php echo rawurlencode( $megoszt_szoveg . ' ' . $megoszt_url ); ?>">WhatsApp</a>
        <a class="tpa-megosztas-gomb" target="_blank" rel="noopener"
           href="https://www.facebook.com/sharer/sharer.php?u=<?php echo rawurlencode( $megoszt_url ); ?>">Facebook</a>
        <a class="tpa-megosztas-gomb"
           href="mailto:?subject=<?php echo rawurlencode( 'Ezt nézd meg: ' . get_the_title( $post_id ) ); ?>&body=<?php echo rawurlencode( $megoszt_szoveg . "\n" . $megoszt_url ); ?>">E-mail</a>
        <button type="button" class="tpa-megosztas-gomb tpa-link-masolas" data-url="<?php echo esc_attr( $megoszt_url ); ?>">🔗 Link másolása</button>
    </div>

    <?php
    // ── Úticél-ajánló: az összekötött Úticél oldal kedvcsináló doboza ─────────
    // A morzsamenü (fent) a navigációt adja; ez a doboz a leírás/galéria után
    // "hova utazol valójában?" kedvcsinálóként visszaköti az ajánlatot az
    // úticél-tartalomba (belső linkelés + a látogató az oldalon marad).
    $uticel_id   = absint( tpa_mezo( $post_id, 'tpa_uticel' ) );
    $uticel_post = $uticel_id ? get_post( $uticel_id ) : null;
    if ( $uticel_post && $uticel_post->post_type === 'uticel' && $uticel_post->post_status === 'publish' ) :
        $uticel_url     = get_permalink( $uticel_id );
        $uticel_kep     = get_the_post_thumbnail( $uticel_id, 'medium_large' );
        $uticel_kivonat = has_excerpt( $uticel_id )
            ? get_the_excerpt( $uticel_id )
            : wp_trim_words( wp_strip_all_tags( $uticel_post->post_content ), 28, '…' );
    ?>
        <aside class="tpa-uticel-ajanlo">
            <?php if ( $uticel_kep ) : ?>
                <a class="tpa-uticel-ajanlo-kep" href="<?php echo esc_url( $uticel_url ); ?>" tabindex="-1" aria-hidden="true"><?php echo $uticel_kep; ?></a>
            <?php endif; ?>
            <div class="tpa-uticel-ajanlo-torzs">
                <p class="tpa-uticel-ajanlo-cimke">Ismerd meg az úti célt</p>
                <h3 class="tpa-uticel-ajanlo-cim"><?php echo esc_html( get_the_title( $uticel_id ) ); ?></h3>
                <?php if ( $uticel_kivonat ) : ?>
                    <p class="tpa-uticel-ajanlo-kivonat"><?php echo esc_html( $uticel_kivonat ); ?></p>
                <?php endif; ?>
                <a class="tpa-gomb" href="<?php echo esc_url( $uticel_url ); ?>">Útikalauz megnyitása</a>
            </div>
        </aside>
    <?php endif; ?>

    <?php
    // ── Hasonló ajánlatok: ugyanarra az úticélra, ha nincs, a legfrissebbek ───
    // Lejárt ajánlatnál különösen fontos: ne zsákutca legyen az oldal, hanem
    // azonnal mutassuk a friss alternatívákat.
    $hasonlo_args = array(
        'post_type'      => 'ajanlat',
        'post_status'    => 'publish',
        'posts_per_page' => 3,
        'post__not_in'   => array( $post_id ),
        'orderby'        => 'date',
        'order'          => 'DESC',
        'meta_query'     => array( tpa_nem_lejart_meta_query() ),
    );
    if ( $uticel_id ) {
        $hasonlo_args['meta_query'][] = array( 'key' => 'tpa_uticel', 'value' => (string) $uticel_id );
    }
    $hasonlo_query = new WP_Query( $hasonlo_args );

    // Ha ugyanarra az úticélra nincs másik élő ajánlat, a legfrissebbeket mutatjuk.
    if ( ! $hasonlo_query->have_posts() && $uticel_id ) {
        array_pop( $hasonlo_args['meta_query'] );
        $hasonlo_query = new WP_Query( $hasonlo_args );
    }

    if ( $hasonlo_query->have_posts() ) :
        $tpa_single_id = $post_id; // a kártya-sablon felülírja a lokális változókat – a végén visszaállítjuk
    ?>
        <div class="tpa-hasonlo">
            <h3 class="tpa-hasonlo-cim">
                <?php echo $lejart ? 'Ez az ajánlat lejárt – nézd meg a frisseket:' : 'Hasonló ajánlatok'; ?>
            </h3>
            <div class="tpa-grid" style="--tpa-card-min: 240px;">
                <?php while ( $hasonlo_query->have_posts() ) : $hasonlo_query->the_post(); ?>
                    <?php include TPA_PATH . 'templates/card-template.php'; ?>
                <?php endwhile; wp_reset_postdata(); ?>
            </div>
        </div>
    <?php
        $post_id = $tpa_single_id;
    endif; ?>

    <?php do_action( 'tpa_single_doboz_vege', $post_id ); // bővítési pont ?>
</div>
