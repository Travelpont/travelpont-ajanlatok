<?php
/**
 * Travelpont Ajánlatok – egyszerű, egyszínű vonal-ikonok (inline SVG)
 *
 * A kártya/aloldal info-sorának emoji-ikonjait váltja ki, hogy eszköz-
 * és böngésző-függetlenül ugyanúgy nézzenek ki, a márka színét követve
 * (stroke="currentColor" – a CSS `color` tulajdonságával szinezhető).
 */

if ( ! defined( 'ABSPATH' ) ) exit;

function tpa_icon( $key, $class = 'tpa-icon' ) {
    $ikonok = array(
        'pin'      => '<path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/>',
        'calendar' => '<rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>',
        'moon'     => '<path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>',
        'clock'    => '<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>',
        'send'     => '<line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/>',
        'hotel'    => '<path d="M2 20v-8a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v8"/><path d="M4 10V6a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v4"/><path d="M2 17h20"/><path d="M6 8v2"/><path d="M18 8v2"/>',
        'utensils' => '<path d="M3 2v7c0 1.1.9 2 2 2h4a2 2 0 0 0 2-2V2"/><path d="M7 2v20"/><path d="M21 15V2a5 5 0 0 0-5 5v6c0 1.1.9 2 2 2h3z"/><path d="M21 15v7"/>',
    );

    if ( ! isset( $ikonok[ $key ] ) ) return '';

    return '<svg class="' . esc_attr( $class ) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">'
        . $ikonok[ $key ] . '</svg>';
}
