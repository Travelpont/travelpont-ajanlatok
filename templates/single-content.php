<?php
/**
 * Travelpont Ajánlatok – Aloldal FELSŐ rész (a leírás ELÉ fűzve)
 *
 * 2026-07-19 UX-átrendezés: a doboz kettévált. A történet-sorrend:
 * kedvcsinálás → részletek → döntés → alternatívák.
 *
 *   FELSŐ (ez a fájl): lejárt-jelzés → hero → morzsa → kompakt ár-sor
 *     (a horog + ugrás a foglaláshoz) → tény-sáv (Utazás/Szállás csoport)
 *     → "Miért szuper?"
 *   ...itt jön a bejegyzés LEÍRÁSA (the_content)...
 *   ALSÓ (single-also.php): galéria (a leírás képei a szövegénél!) →
 *     teljes ár-panel + foglalás gombok → megosztás → úticél-ajánló →
 *     hasonló ajánlatok
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$post_id       = get_the_ID();
$tipus         = tpa_mezo( $post_id, 'tpa_ajanlat_tipus' );
$celallomas    = tpa_mezo( $post_id, 'tpa_celallomas' );
$indulas       = tpa_mezo( $post_id, 'tpa_indulas' );
$idopont       = tpa_idopont_megjelenites( $post_id );  // dátumokból képzett tartomány vagy kézi szöveg
$ejszakak      = tpa_ejszakak_szam( $post_id );         // dátumokból számolva vagy kézi érték
$utvonal       = tpa_utvonal( $post_id );               // repülős útvonal (BUD → PVK) vagy null
$szallas_nev   = tpa_mezo( $post_id, 'tpa_szallas_nev' );
$csillagok     = tpa_szallas_csillag_html( $post_id );
$ellatas       = tpa_ellatas_nev( $post_id );
$poggyasz      = tpa_poggyasz_nev( $post_id );
$ar            = tpa_teljes_ar( $post_id );
$ar_megjegyzes = tpa_ar_megjegyzes_megjelenites( $post_id );
$lejart        = tpa_deal_lejart( $post_id ); // kézi "Lejárt" státusz VAGY érvényességi dátum
$morzsa        = tpa_uticel_breadcrumb( tpa_mezo( $post_id, 'tpa_uticel' ), array( 'linkelt' => true ) );

$van_szallas_adat = ( $szallas_nev !== '' || $csillagok !== '' || $ellatas !== '' );
?>
<div class="tpa-single-doboz tpa-single-felso">

    <?php if ( $lejart ) : ?>
        <p class="tpa-lejart-jelzes">⚠️ Ez a deal lejárt — de a jó árak visszatérnek! Lejjebb megnézheted az aktuális árat, vagy válogass a friss ajánlatok közül.</p>
    <?php endif; ?>

    <?php if ( has_post_thumbnail( $post_id ) ) : ?>
        <div class="tpa-single-hero">
            <?php echo get_the_post_thumbnail( $post_id, 'large' ); ?>
        </div>
    <?php endif; ?>

    <?php if ( $morzsa !== '' ) : ?>
        <nav class="tpa-single-morzsa" aria-label="Úticél útvonal"><?php echo $morzsa; // linkelt, escapelt (tpa_uticel_breadcrumb) ?></nav>
    <?php endif; ?>

    <?php // ── Kompakt ár-sor: az ár azonnal látszik, a gomb a lenti ár-panelre ugrik ─ ?>
    <?php if ( ! $lejart && $ar !== '' ) : ?>
        <div class="tpa-kompakt-ar">
            <div class="tpa-kompakt-ar-blokk">
                <span class="tpa-kompakt-ar-osszeg"><?php echo esc_html( tpa_ar_format( $ar ) ); ?></span>
                <?php if ( $ar_megjegyzes ) : ?>
                    <span class="tpa-kompakt-ar-megjegyzes"><?php echo esc_html( $ar_megjegyzes ); ?></span>
                <?php endif; ?>
            </div>
            <a class="tpa-gomb" href="#tpa-foglalas">Foglalás ↓</a>
        </div>
    <?php endif; ?>

    <?php // ── Tény-sáv: az infó-chipek helyett két címzett csoport ──────────────── ?>
    <div class="tpa-teny-sav">
        <div class="tpa-teny-csoport">
            <p class="tpa-teny-cim"><?php echo tpa_icon( 'send' ); ?>Utazás</p>
            <?php // Az úti cél nevét a morzsa adja – csak akkor írjuk ki külön, ha nincs bekötött úticél ?>
            <?php if ( $celallomas !== '' && $morzsa === '' ) : ?>
                <div class="tpa-teny-sor"><span class="tpa-teny-cimke">Úti cél</span><span class="tpa-teny-ertek"><?php echo esc_html( $celallomas ); ?></span></div>
            <?php endif; ?>
            <?php if ( $utvonal ) : ?>
                <div class="tpa-teny-sor"><span class="tpa-teny-cimke">Útvonal</span>
                    <span class="tpa-teny-ertek tpa-teny-utvonal">
                        <strong><?php echo esc_html( $utvonal['kod'] ); ?></strong>
                        <?php if ( $utvonal['varos'] !== '' ) : ?>
                            <span class="tpa-utvonal-varos"><?php echo esc_html( $utvonal['varos'] ); ?></span>
                        <?php endif; ?>
                    </span>
                </div>
            <?php elseif ( $indulas !== '' && $tipus !== 'csak_szallas' ) : ?>
                <div class="tpa-teny-sor"><span class="tpa-teny-cimke">Indulás</span><span class="tpa-teny-ertek"><?php echo esc_html( $indulas ); ?></span></div>
            <?php endif; ?>
            <?php if ( $idopont ) : ?>
                <div class="tpa-teny-sor"><span class="tpa-teny-cimke">Időpont</span><span class="tpa-teny-ertek"><?php echo esc_html( $idopont ); ?></span></div>
            <?php endif; ?>
            <?php if ( $ejszakak !== '' ) : ?>
                <div class="tpa-teny-sor"><span class="tpa-teny-cimke">Éjszakák</span><span class="tpa-teny-ertek"><?php echo esc_html( $ejszakak ); ?></span></div>
            <?php endif; ?>
            <?php if ( $poggyasz !== '' && $tipus === 'repulo_szallas' ) : ?>
                <div class="tpa-teny-sor"><span class="tpa-teny-cimke">Poggyász</span><span class="tpa-teny-ertek"><?php echo esc_html( $poggyasz ); ?></span></div>
            <?php endif; ?>
        </div>

        <?php if ( $van_szallas_adat ) : ?>
            <div class="tpa-teny-csoport">
                <p class="tpa-teny-cim"><?php echo tpa_icon( 'hotel' ); ?>Szállás</p>
                <?php if ( $szallas_nev !== '' || $csillagok !== '' ) : ?>
                    <div class="tpa-teny-sor"><span class="tpa-teny-cimke">Név</span><span class="tpa-teny-ertek"><?php echo esc_html( $szallas_nev ); ?><?php echo $csillagok; // biztonságos HTML ?></span></div>
                <?php endif; ?>
                <?php if ( $ellatas !== '' ) : ?>
                    <div class="tpa-teny-sor"><span class="tpa-teny-cimke">Ellátás</span><span class="tpa-teny-ertek"><?php echo esc_html( $ellatas ); ?></span></div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <?php
    // ── "Miért szuper ez az ajánlat?" – Petra személyes érvei, pipás listaként ─
    $miert_pontok = tpa_miert_szuper_pontok( $post_id );
    if ( $miert_pontok ) : ?>
        <div class="tpa-miert-szuper">
            <p class="tpa-miert-szuper-cim">Miért szuper ez az ajánlat?</p>
            <ul>
                <?php foreach ( $miert_pontok as $miert_pont ) : ?>
                    <li><?php echo tpa_icon( 'check', 'tpa-icon tpa-miert-pipa' ); ?><?php echo esc_html( $miert_pont ); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
</div>
