# Travelpont Ajánlatok plugin – dokumentáció

> Verzió: 1.13.2 · Az aktivbalaton.hu egyedi plugin-konvenciók mintájára
> (1.13.2: a lépés-gombok kompaktabbak – kisebb padding, 16px lekerekítés,
> feszesebb sorköz; mobilon nem hatnak felfújt léggömbnek)
> (minta: `E:\aktivbalaton.hu\Saját pluginok\_AKTIV\balaton-szallasok`)
> SZABÁLY: minden módosításkor verziót emelünk a fő fájl fejlécében
> (cache-buster + követhetőség).
>
> **1.13.1** – mobil-javítás (Gabesz képernyőképei alapján, 2026-07-18):
> ≤600px-en a `.tpa-single-doboz` kisimul (nincs saját keret/padding – a téma
> kártyája már keretez; eddig ~250px maradt a tartalomnak és szavak törtek
> ketté), az infó-chipek teljes szélességű sorok (címke balra, érték jobbra),
> a `.tpa-single-gombok .tpa-gomb` feliratai a gombon BELÜL törnek
> (`white-space: normal` – eddig a "Szállás foglalása – Booking.com" kilógott),
> mérsékelt téma-padding + kisebb H1 (`.single-ajanlat` scope), 2 oszlopos
> galéria, kompaktabb ár-panel.
>
> **1.13.0** – konverzió- és tartalom-csomag (a 2026-07-18-i ötletelés 6 pontja).
> (1) **Számozott foglalási út**: két linkes ajánlatnál a gombok „1. lépés /
> 2. lépés" felirattal lépésekké válnak (`.tpa-gombok-lepesek`,
> `.tpa-gomb-lepes`); az affiliate-közzététel pontosítva: bármelyik link
> külön is jutalékot hoz, de a kettő együtt a legjobb.
> (2) **„Miért szuper ez az ajánlat?"**: új `tpa_miert_szuper` textarea
> (soronként egy pont), az aloldalon pipás lista (`.tpa-miert-szuper`,
> helper: `tpa_miert_szuper_pontok()`).
> (3) **Poggyász-jelző**: új `tpa_poggyasz` select (kis kézi / kézi / +feladott,
> csak repülősnél), chip az aloldalon; a `tpa_ar_tartalom_szoveg()`
> poggyász-tudatos lett (nem állítja, hogy nincs feladott poggyász, ha van).
> (4) **Megosztás-sor** (`.tpa-megosztas`): WhatsApp / Facebook / E-mail /
> Link másolása (új `assets/js/megosztas.js`, single-ön enqueue-olva).
> (5) **Hasonló ajánlatok sáv** (`.tpa-hasonlo`) az aloldal alján: 3 élő
> ajánlat ugyanarra az úticélra (ha nincs, a legfrissebbek), a lejárt
> ajánlat oldalán „nézd meg a frisseket" címmel – kártya-sablon
> újrafelhasználásával. Közös helper: `tpa_nem_lejart_meta_query()`
> (a shortcode is ezt használja).
> (6) **Schema.org JSON-LD** (`single-display.php`, `wp_head`): TouristTrip +
> Offer (ár HUF-ban, validThrough az érvényességi dátumból, lejártnál
> Discontinued availability).
>
> **1.12.0** – repülős útvonal + úticél-ajánló.
> (1) **Reptér-mezők** (csak `repulo_szallas` típusnál): `tpa_indulas_iata`,
> `tpa_cel_iata`, `tpa_cel_varos`; a meglévő `tpa_indulas` label pontosítva
> („város"). Új helper: `tpa_utvonal()` → `['kod' => 'BUD → PVK',
> 'varos' => 'Budapest → Preveza (Lefkada)']` vagy null. Kártyán útvonal-sor
> (`.tpa-card-utvonal`: kód félkövéren, alatta kisbetűs városok), aloldalon
> „Repülő" chip (`.tpa-chip-utvonal`, kétsoros). A Portálon a repterek
> kereshető legördülőből választhatók (bővített európai IATA-lista,
> `iata-airports.js` a portal repóban), a kiválasztás a városmezőket és a
> Kiwi-linkösszeállítót is kitölti.
> (2) **Úticél-ajánló doboz** (`.tpa-uticel-ajanlo`) az aloldali doboz alján:
> az összekötött Úticél kiemelt képe + címe + kivonata + „Útikalauz
> megnyitása" gomb – belső linkelés, csak publikált úticélnál jelenik meg.
>
> **1.11.0** – „utazásiasítás": a 2026-07-18-i szakmai review nyomán.
> (1) **Szállás-mezők**, új „🏨 Szállás adatai" szekció: `tpa_szallas_nev`
> (text), `tpa_szallas_csillag` (select 1–5), `tpa_szallas_ellatas` (select:
> önellátás/reggeli/félpanzió/teljes panzió/all inclusive) – kártyán
> szállás-sor, aloldalon Szállás+Ellátás chip. Helper: `tpa_szallas_csillag_html()`,
> `tpa_ellatas_nev()`.
> (2) **Strukturált dátumok**: `tpa_indulas_datum` + `tpa_hazaut_datum` (date).
> A kiírt időpontot (`tpa_idopont_megjelenites()`, magyar tartomány-formátum)
> és az éjszakák számát (`tpa_ejszakak_szam()`) ezekből VEZETJÜK LE; a régi
> `tpa_idopont`/`tpa_ejszakak` szabad mezők fallbackká váltak („csak ha nincs
> pontos dátum"). Ez zárja ki a kézi dátum/éjszaka-eltéréseket (élő példa volt:
> aug. 20–27. + „6 éjszaka" kiírás szept. 20–27-es linkek mellett).
> (3) **Ár-kanonizálás (2 fős csomagár)** + BUGFIX: `tpa_teljes_ar()` mostantól
> TÍPUSFÜGGŐ – buszos ajánlatnál a buszjegy-árat adja a szálláshoz (eddig
> kihagyta!), és a típusváltás után bent ragadt, az űrlapon nem látható részár
> nem számít bele. Az ár-megjegyzés `default`-ja megszűnt, helyette
> `tpa_ar_megjegyzes_megjelenites()`: kézi szöveg vagy típus szerinti alapszöveg
> (filter: `tpa_ar_megjegyzes_alapok`).
> (4) **Aloldali ár-bontás**: 2+ kitöltött részár esetén tételsorok a végösszeg
> fölött, alatta „mit tartalmaz az ár" típusfüggő sor (filter:
> `tpa_ar_tartalom_szoveg`) + „Árak ellenőrizve: <utolsó módosítás dátuma>".
> Az érvényességi dátum magyarul formázva (`tpa_datum_magyar()`).
> (5) **Ár-rendezés javítva**: mentéskor `tpa_ar_szamitott` meta íródik
> (`tpa_ar_szamitott_frissit()`, a `tpa_after_save_meta` hookon), a shortcode
> `ar_novekvo/ar_csokkeno` erre rendez – a számított árú ajánlatok is jó helyre
> kerülnek. Egyszeri backfill `admin_init`-en (`tpa_ar_szamitott_verzio` option).
> (6) REST `tpa_api_format()` új mezői: `idopont_megjelenites`, `ejszakak_szam`,
> `ar_megjegyzes_megjelenites`. Új ikonok: `hotel`, `utensils` (icons.php).
>
> **1.10.0**: az Ajánlat ↔ Úticél kapcsolat kétirányúvá vált a
> megjelenítésben. (1) A `tpa_uticel` legördülő a WP adminban mostantól a
> DRAFT úticélokat is mutatja (`meta-boxes.php`, `wp_dropdown_pages` →
> `post_status: publish,draft`), egyezően a Portál /meta végpontjával – így
> a frissen felvitt (még vázlat) városok is kiválaszthatók. (2) Új
> `tpa_uticel_breadcrumb()` és `tpa_hely_megjelenites()` helper
> (`fields.php`): az ajánlat kártyáján és aloldalán a hely mostantól az
> összekötött úticél teljes útvonalaként (Ország › Régió › Város) jelenhet
> meg. Prioritás: a kézzel írt `tpa_celallomas` felülír; ha üres és van
> úticél összekötve, a breadcrumb jelenik meg. Az aloldalon linkelt
> morzsamenü (a hierarchia böngészhető), a REST `tpa_api_format()` új
> `uticel_breadcrumb` mezőt ad. Visszafelé kompatibilis: kézi célállomású
> ajánlatok változatlanok.
>
> **1.9.1**: aloldali dizájn-frissítés (első "csak szállás" ajánlat éles
> visszajelzése alapján) – a kiemelt kép mostantól megjelenik az aloldal
> tetején (eddig SOSEM jelent meg ott, csak a kártyán), az ár+CTA-gombok
> egy kiemelt panelbe kerültek, a galéria-rács `auto-fit`-re javítva (a
> régi `auto-fill` felesleges üres oszlopokat tartott fenn kevés kép
> esetén). Új `includes/icons.php` (`tpa_icon()`) – a kártya és az
> aloldal infó-sorának emoji-ikonjai (📍📅🛏️⏳) saját, egyszínű SVG
> ikonokra cserélve a konzisztens megjelenésért.
>
> **1.9.0**: az ajánlat többé nincs kizárólag repülő+szállás kombóra
> kihegyezve. Új `tpa_ajanlat_tipus` select mező ("Utazás adatai" doboz,
> opciók: `repulo_szallas` / `busz_szallas` / `csak_szallas`) az admin
> űrlapon és a Portálon vezérli, mely mezők látszanak (JS-es
> `show_if_tipus` mechanizmus, ld. `fields.php` + `assets/js/admin-tipus-toggle.js`).
> Új `tpa_busz_link` és `tpa_busz_ar` mező (Flixbus/busz kombóhoz), a
> meglévő `tpa_kiwi_link`/`tpa_repjegy_ar` mostantól csak `repulo_szallas`
> típusnál látszik. A meglévő ajánlatok változatlanul működnek (implicit
> alapérték: `repulo_szallas`), migráció nem szükséges.
>
> **1.2.0**: új `tpa_repjegy_ar` és `tpa_szallas_ar` mező ("Ár és
> érvényesség" doboz) – külön repjegy/szállás ár, amit a kezdőlap
> (`travelpont-kezdolap` plugin) az ajánlat-kártyákon külön sorban jelenít
> meg. A meglévő `tpa_ar` mostantól opcionális: ha üresen hagyod, a
> `tpa_teljes_ar()` a repjegy+szállás összegét adja vissza helyette
> (a meglévő kártya/aloldal sablonok is ezt hívják).
>
> **1.1.0**: új `tpa_uticel` mező ("Utazás adatai" doboz) – összeköti az
> ajánlatot egy Úticél oldallal (`travelpont-uticelok` plugin). Ehhez új,
> általános mezőtípus is bekerült: `post_select` (hierarchikus legördülő
> bármely post type-hoz, `wp_dropdown_pages()` alapon).

## Mit tud?

- **"Ajánlatok" menüpont** a WP adminban → repülő+szállás, busz (Flixbus)+
  szállás vagy csak szállás (egyéni utazás) ajánlatok felvitele űrlapon (nem
  kell kódolni): célállomás, indulás, időpont, éjszakák, ár, érvényességi
  dátum, Kiwi.com deep link / busz link, szállás affiliate link. Az "Ajánlat
  típusa" mező szabja meg, mely mezők látszanak.
- **`[travelpont_ajanlatok]` shortcode** → reszponzív kártyarács bárhová
  (Elementor Shortcode widget VAGY Gutenberg Shortcode blokk – téma-független).
- **Ajánlat aloldal** → minden ajánlatnak saját oldala van (kártya "Megnézem"
  gombja ide visz), rajta a két affiliate gomb + automatikus affiliate
  közzétételi szöveg.
- **Lejárat-kezelés**: az érvényességi dátum után az ajánlat magától eltűnik
  a listából; az aloldalán "lejárt" jelzés jelenik meg a gombok helyett.

## Shortcode használat

```
[travelpont_ajanlatok]
[travelpont_ajanlatok limit="3" kategoria="tengerpart"]
[travelpont_ajanlatok limit="-1" rendezes="ar_novekvo" oszlopok="4"]
```

| Paraméter | Alapérték | Lehetőségek |
|---|---|---|
| `limit` | 6 | szám, `-1` = összes |
| `kategoria` | (mind) | kategória slug, vesszővel több is: `tengerpart,egzotikus` |
| `rendezes` | `ujak` | `ujak`, `ar_novekvo`, `ar_csokkeno`, `lejarat` |
| `lejartak` | `nem` | `igen` = a lejártak is látszanak |
| `oszlopok` | 3 | 2, 3, 4 (széles képernyőn) |

## Fájlszerkezet

```
travelpont-ajanlatok/
├── travelpont-ajanlatok.php   ← fő fájl: konstansok, modul-betöltés, enqueue
├── includes/
│   ├── fields.php             ← ⭐ KÖZPONTI MEZŐ-DEFINÍCIÓK (bővítés itt!)
│   ├── cpt.php                ← "ajanlat" CPT + "ajanlat_kategoria" taxonómia
│   ├── meta-boxes.php         ← admin űrlap (a fields.php-ból épül, generikus)
│   ├── shortcodes.php         ← [travelpont_ajanlatok]
│   ├── single-display.php     ← ajánlat-doboz az aloldalon (the_content elé)
│   └── rest-api.php           ← CSONTVÁZ: csak /status ping (portál helye)
├── templates/
│   ├── lista-template.php     ← kártyarács
│   ├── card-template.php      ← egy kártya
│   └── single-content.php     ← aloldali ajánlat-doboz
└── assets/css/
    ├── frontend.css           ← kártya + aloldal (branding CSS-változókban!)
    └── admin.css              ← admin űrlap
```

## Hogyan bővítsd? (SEMMI SINCS BEÉGETVE)

### Új mező hozzáadása
1. `includes/fields.php` → `tpa_get_fields()` tömbjébe új bejegyzés, pl.:
   ```php
   'tpa_fok_szama' => array(
       'label'   => 'Utasok száma',
       'type'    => 'number',        // text | number | url | date | select | textarea | post_select
       'section' => 'utazas',        // melyik admin dobozba kerüljön
       'desc'    => 'Hány főre szól az ár.',
   ),
   ```
   → az admin űrlapon és a mentésben AUTOMATIKUSAN megjelenik.
2. Ha a kártyán/aloldalon is látszódjon: a sablonban kiírod:
   `tpa_mezo( get_the_ID(), 'tpa_fok_szama' )`

### Új admin szekció (meta box)
`fields.php` → `tpa_get_sections()` tömbjébe új sor.

### Hookok (kódból, akár másik pluginból)
- `tpa_fields`, `tpa_sections` – mezők/szekciók módosítása filterrel
- `tpa_lista_query_args` – a lista-lekérdezés módosítása
- `tpa_shortcode_defaults` – shortcode alapértékek
- `tpa_after_save_meta` – fut minden ajánlat-mentés után (portál-szinkron helye!)
- `tpa_single_doboz_vege` – extra tartalom az aloldali doboz aljára
- `tpa_rest_api_init` – későbbi REST endpointok regisztrálása
- `tpa_affiliate_kozzetetel_szoveg`, `tpa_ures_lista_szoveg` – szövegek átírása

### Branding / színek
`assets/css/frontend.css` tetején CSS-változók (`--tpa-primary`,
`--tpa-accent`…) – a végleges arculatnál csak ezeket kell átírni.

## Portál-kommunikáció (v1.4.0-tól KÉSZ)

`includes/rest-api.php` – teljes CRUD API a Travelpont Portal (Firebase, külön
repó) Cloud Functions proxyja számára, az aktivbalaton `bsza/v1` mintáját
követve. Auth: Application Password (Basic Auth) + `publish_posts`.

- `GET /tpa/v1/ajanlatok` – lista (szűrés: `search`, `status`, `kategoria`, `uticel_id`)
- `GET/PUT /tpa/v1/ajanlat/{id}`, `POST /tpa/v1/ajanlat` – egy ajánlat / létrehozás / frissítés
- `POST /tpa/v1/ajanlat/{id}/kep` – kiemelt kép sideload URL-ből (Firebase Storage → WP média)
- `GET /tpa/v1/meta` – kategóriák + Úticélok flat listája (a Portál form legördülőihez)
- `GET /tpa/v1/status` – publikus ping

A REST paraméterek neve megegyezik a nyers meta-kulccsal (`tpa_celallomas`,
`tpa_ar` stb.) – nincs külön "portál-nevesítés". A mentés-sanitizálás
(`tpa_sanitize_field_value()`, `fields.php`) közös a klasszikus admin mentéssel.

## Telepítés

1. A `travelpont-ajanlatok` mappát felmásolni ide:
   `wp-content/plugins/`
2. WP admin → Bővítmények → "Travelpont Ajánlatok" → Bekapcsolás
3. (Aktiváláskor a permalink szabályok automatikusan frissülnek.)
4. Ajánlatok → Új ajánlat → mezők kitöltése + kiemelt kép beállítása
5. A kezdőlapra: `[travelpont_ajanlatok]` shortcode beszúrása
