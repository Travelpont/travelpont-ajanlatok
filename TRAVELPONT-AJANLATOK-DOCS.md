# Travelpont Ajánlatok plugin – dokumentáció

> Verzió: 1.18.0 · Az aktivbalaton.hu egyedi plugin-konvenciók mintájára
> (1.18.0: LISTA-KÁRTYA KARCSÚSÍTÁS [Gabesz döntése: a kártya dolga a
> figyelemfelkeltés, a foglalás az aloldalon történik]. A card-template.php-ról
> lekerült: ár-bontás sorok [repjegy/szállás – csak az „Összesen: X Ft / N fő"
> maradt], szállás-sor [név+csillag+ellátás], sárga frissesség-figyelmeztetés
> [a szövege amúgy is a foglalási linkre hivatkozott, ami már nincs a kártyán],
> és MINDKÉT foglalás gomb + lejárt-módú aktuális-ár/úticél-linkek – helyettük
> egyetlen „Megnézem" gomb a permalinkre. Maradt: hely, időpont+éj, útvonal
> [BUD → PVK], kis Találat-dátum, lejárt üzenet. Lejártnál új „Lejárt" badge a
> képen [a kompakt kártya mintája] + áthúzott Összesen. A kompakt kártya és a
> kezdőlap-modul kártyája NEM változott; a single oldal foglalás gombjai/
> frissesség-infója érintetlen. CSS: .tpa-card-frissesseg és
> .tpa-card-uticel-link szabályok törölve [árvák lettek].)
> (1.17.3: az edge-to-edge mobil nézet kiterjesztve a kártyarácsot
> tartalmazó sima oldalakra is [`body.page:has(.tpa-grid)` – pl. az
> /ajanlatok/ lista; a :has() 2026-ban már mindenhol támogatott], és a
> tartalom oldaltávja 16px→20px [1.25rem] – éltől élig futó felületen a
> betűk a kijelző szélére estek. Párja: uticelok v1.20.5.)
> (1.17.2: teljes szélig érő [edge-to-edge] mobil nézet az ajánlat-oldalon
> [Gabesz kérése]: ≤600px-en a téma oldalsó térköze
> [`.content-container.site-container` padding] és a kártya-keret
> [`.entry.single-entry` box-shadow/radius] eltűnik, a fehér felület a
> képernyő széléig fut; a szöveg az `entry-content-wrap` 1rem paddingjével
> kap levegőt, a mobilon alulra kerülő oldalsáv [.primary-sidebar] 1rem
> oldaltávot kap. A párja: travelpont-uticelok v1.20.4.)
> (1.17.1: Playwright-os vizuális kör [1920/2560/mobil screenshotok alapján].
> [1] Új `cim="auto"` shortcode-attribútum: a lista fölé egységes címsort ír
> [`.tpa-lista-cim`, a lista-template rendereli] – hasonló-módban „További
> ajánlatok", úticél-módban „Ajánlataink ehhez az úticélhoz", más
> használatnál nincs; saját szöveg megadható, `cim=""` = kikapcsolva. Így a
> sidebar-widgetekbe NEM kell külön Címsor blokk. [2] Nagy kijelzőn a hero
> nem uralja az első képernyőt: `.tpa-single-hero { max-width: 780px }`
> [KICSINYÍT, nem vág – a képbe szerkesztett felirat egészben marad] ÉS a
> `.single-ajanlat .entry-content-wrap { max-width: 860px }` – 1920/2560-on
> a sorok se nyúljanak olvashatatlanul hosszúra.)
> (1.17.0: KOMPAKT KÁRTYA + ÚTICÉL-MÓD az oldalsávokhoz. Új
> `nezet="kompakt"` attribútum → `templates/card-kompakt.php`: borítókép
> [16:9 marad, feliratos képkonvenció!] → cím → időpont [+ éj] → ár
> ["234 870 Ft / 2 fő"] → egyetlen „Megnézem" gomb az ajánlat ALOLDALÁRA
> [nem affiliate!]; ár-bontás, hely, találat/frissesség nincs; lejártnál
> áthúzott ár + „Lejárt" badge a képen. Új `uticel="aktualis"` attribútum:
> úticél-aloldalon a megnyitott úticél + ÖSSZES leszármazottja ajánlatai
> [`tpu_get_leszarmazott_idk()` helperrel, function_exists-guarddal] –
> találat híján NINCS fallback, helyette terelő szöveg linkkel az Ajánlatok
> oldalra [`$tpa_ures_html` a lista-template-ben]. A két oldalsáv ajánlott
> shortcode-jai a shortcodes.php fejlécében. Plusz: a teljes kártya
> ár-bontás címkéi keskeny helyen nem törnek szó közepén
> [flex-wrap + nowrap a címkén – az „Össze sen:" hiba javítása].
> A párja: travelpont-uticelok v1.20.1 – a tartalmi ajánlat-blokk kivezetve.)
> (1.16.1: a lapalji „Hasonló ajánlatok" blokk kivezetve a
> `single-also.php`-ból [+ a `.tpa-hasonlo` CSS törölve] – a szerepét az
> oldalsávba tett `[travelpont_ajanlatok hasonlo="igen" oszlopok="1"]`
> shortcode vette át [Gabesz döntése]. FIGYELEM: az oldalsáv widget +
> a Kadence oldalsáv-elrendezés beállítása kézi lépés a WP adminban –
> amíg az nincs meg, az aloldalon nincs hasonló-ajánlat szekció.)
> (1.16.0: a `[travelpont_ajanlatok]` shortcode oldalsáv-képes lett a
> „További ajánlatok" sáv kiváltásához. Új `hasonlo="igen"` attribútum:
> ajánlat-aloldalon az aktuális ajánlatot kihagyja [`post__not_in`], és az
> azonos úticélra szóló élő ajánlatokat hozza előre [nevesített
> `tpa_hasonlo_uticel` meta_query-kulcs]; ha arra nincs találat, a szűkítés
> elengedve a legfrissebbeket adja – pontosan a lapalji hasonló-blokk
> logikája. Más oldalon az attribútumnak nincs hatása. Új `oszlopok="1"`
> érték: mindig egy hasáb [minmax(100%, 1fr)], oldalsávba. Ajánlott
> oldalsáv-használat: `[travelpont_ajanlatok limit="3" hasonlo="igen"
> oszlopok="1"]` egy Shortcode blokkban/widgetben. A lapalji hasonló-blokk
> egyelőre MARAD – ha az oldalsáv élesben bevált, onnan kivezethető.)
> (1.15.1: Gabesz screenshot-visszajelzése alapján. [1] LEJÁRT-LOGIKA
> PONTOSÍTÁS: jellemzően a repjegy/buszjegy akciós ára jár le, a szállásé
> nem – lejáratkor csak az UTAZÁSI sor és az Összesen húzódik át
> [`.tpa-ar-athuzva` a sorokon, a blanket áthúzás kivezetve], a szállás
> sora érintetlen; a gombok lejáratkor: „Nézd meg az aktuális árat"
> [utazási link] MELLETT megmarad a normál „Szállás foglalása" gomb is,
> az aloldalon magyarázó sorral [„A repülőjegy akciós ára járt le – a
> szállás továbbra is foglalható", `.tpa-lejart-reszinfo`]. Csak-szállás
> dealnél a szállás sor húzódik át és a szállás link az ellenőrző gomb.
> [2] A plugin-doboz MINDEN kijelzőn keret nélküli [eddig csak mobilon]:
> a leírás így nem két keretezett kártya közé "esik ki", hanem egy
> folyamatos hasáb része – a hero lekerekítése teljes körű lett, a
> mobil-blokk kisimító szabályai a base-be olvadtak.)
> (1.15.0: ALOLDAL UX-ÁTRENDEZÉS – a doboz kettévált, a leírás felkerült a
> törzsbe. Történet-sorrend: kedvcsinálás → részletek → döntés → alternatívák.
> FELSŐ rész [`single-content.php`, a leírás ELÉ]: lejárt-jelzés → hero →
> morzsa → KOMPAKT ÁR-SOR [`.tpa-kompakt-ar`: összesen ár + „Foglalás ↓"
> gomb, ami a `#tpa-foglalas` horgonyra görget] → TÉNY-SÁV [`.tpa-teny-sav`:
> a 8 egyenrangú chip helyett két címzett csoport, Utazás (útvonal/időpont/
> éjszakák/poggyász) és Szállás (név+csillag/ellátás); az „Úti cél" sor csak
> morzsa híján jelenik meg – a hármas hely-duplikáció megszűnt; az „Érvényes"
> az ár-panel apróbetűjébe költözött] → „Miért szuper?". ALSÓ rész [ÚJ
> `single-also.php`, a leírás MÖGÉ]: galéria [a szállás képei így a róla
> szóló szöveg mellett] → teljes ár-panel + foglalás gombok [a döntési pont
> minden infó után; lejárt dealnél halványított árak + „Nézd meg az aktuális
> árat" gomb, a kártyával egyezően] → megosztás → úticél-ajánló → hasonló
> ajánlatok [zárásként, már nem a leírás előtt]. A `the_content` filter
> mostantól `$felso . $content . $also`-t ad vissza. A chip-CSS
> [`.tpa-single-info`, `.tpa-chip-utvonal`] kivezetve, sima görgetés
> `prefers-reduced-motion` tisztelettel.)
> (1.14.1: élő-oldal review finomításai. Kártyán az egytételes ár-bontás
> nem duplázza az Összesent [csak 2 tételnél vannak részár-sorok,
> `.tpa-card-ar-osszesen-egyedul`]; „Találat: júl. 14." helyes magyar
> hónap-rövidítéssel [`tpa_datum_magyar_rovid()` – a WP hu_HU 'M' nem tesz
> pontot]; a kézi időpont-szöveg "2026.augusztus" elütése kiíráskor javul
> [szóköz-pótlás, a mentett érték marad]; az aloldali „Árak ellenőrizve" a
> TALÁLAT dátumát mutatja a módosítás dátuma helyett [az bármely mentéstől
> frissülne – félrevezető]; a Schema.org Offer availability a kézi Lejárt
> státuszra is Discontinued [`tpa_deal_lejart()`].)
> (1.14.0: DEAL-LOGIKA. **ÁR-SZEMANTIKA VÁLTÁS: a `tpa_repjegy_ar` és
> `tpa_busz_ar` mostantól FŐNKÉNTI ár [Ft/fő]** – a korábbi 2 fős csomagár
> helyett; az összesen ár képlete: főnkénti utazási ár × `tpa_fo_szam`
> [új mező, default 2] + szállás ár [`tpa_teljes_ar()`, helper:
> `tpa_utazas_ar_fo()`, `tpa_fo_szam()`]. **MIGRÁCIÓ: a korábban felvitt
> ajánlatokban a repjegy-árat felezni kell [2 fős → per fő], különben az
> összesen duplázódik!** Új mezők: `tpa_statusz` [aktiv/lejart kézi kapcsoló –
> lejárt deal nem tűnik el: halványított/áthúzott árak, „Ez a deal lejárt —
> de a jó árak visszatérnek!" üzenet, „Nézd meg az aktuális árat" gomb +
> kapcsolt úticél-link; helper: `tpa_deal_lejart()` = kézi státusz VAGY
> dátum-lejárat, az aloldal is ezt használja] és `tpa_talalat_datuma`
> [readonly – első publikáláskor automatikusan mentődik,
> `transition_post_status`; a verzió-backfill a régi publikált ajánlatokra
> a publikálás napját pótolja be]. Új mező-attribútum: `readonly => true` –
> az admin csak kiírja, a mentés (metabox ÉS REST) kihagyja. Új beállítások
> oldal: Ajánlatok menü → „Ajánlat beállítások" [`includes/settings.php`,
> Settings API, option: `travelpont_frissesseg_kuszob`, default 3 nap] –
> ennél régebbi találatnál a kártyán: „Az ár azóta változhatott — a friss
> árat a foglalási linken látod" [`tpa_talalat_regi()`]. Kártya-lábléc újra:
> ár-bontás sorok [Repjegy: X Ft/fő · Szállás: Y Ft · Összesen: Z Ft / N fő],
> „Találat: júl. 19." címke, és aktív dealnél KÉT affiliate gomb [Repjegy/
> Buszjegy foglalása + Szállás foglalása, target=_blank,
> rel="nofollow sponsored noopener"] – a permalinkes „Megnézem" csak
> fallback, ha nincs link. A kezdőlap-plugin ajánlat-modulja „/fő" jelölést
> kapott az utazási ár-sorokra. Nincs külső API/cron: a frissesség-ellenőrzés
> megjelenítéskori dátum-összehasonlítás.)
> (1.13.4: KÉPKONVENCIÓ-fix. A borítóképek 1920x1080-asak, a felirat beléjük
> van szerkesztve [lásd travelpont-uticelok], ezért 16:9 kerettel kell mutatni
> őket, hogy semmi ne vágódjon le. A hero [21/8 → 16/9], a kártya-kép
> [16/10 → 16/9] és az Úticél-ajánló kép [stretch → 16/9 keret, align-items
> center] mind erre javítva – eddig levágták/torzították a képbe szerkesztett
> feliratot.)
> (1.13.3: a gombok alatti affiliate-közzététel eltávolítva a látogatói
> nézetből [Gabesz döntése]; az ár-tartalom sorban „partneroldalakon” →
> „foglalási oldalon”. A linkek rel="sponsored" attribútuma megmarad – ez a
> forráskódban van, a látogató nem látja, viszont a Google-nek jelzi a link
> jellegét, ezért SEO-szempontból hasznos megtartani.)
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
