=== Facturare WooCommerce ===
Contributors: giucu91
Tags: woocommerce, facturare, persoana fizica, persoana juridica
Requires at least: 3.5
Tested up to: 6.2
Stable tag: 1.1.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Description ==

Datorită legislației în vigoare din România fiecare magazin online trebuie să aibă anumite informații pe factură. Acest modul vine în ajutorul tau si te ajuta sa adaugi respectivele informații mai ușor.

Pentru persoanele fizice poți adăuga:
	- CNP

Pentru persoanele juridice poți adăuga::
	- CUI
	- Nr. Registru Comertului
	- Nume Banca
	- IBAN

Acest plugin nu emite facturi, el doar adaugă câmpurile necesare unei facturi pe pagina de checkout și pe pagina contul meu.

Pentru mai multe informații vizitați [Modul Facturare](https://avianstudio.com/facturare/?utm_source=upsell&utm_medium=wporg&utm_campaign=woofacturare)

Vreti sa emiteti facturi/proforme cu SmartBill ? Nu a fost niciodata mai simplu ! Cu ajutorul pluginului nostru [WooCommerce SmartBill](https://avianstudio.com/?utm_source=upsell&utm_medium=wporg&utm_campaign=woosmartbill) puteti :
- Genera automat facturi dupa statusul comenzii.
- Incasa automat facturile in functie de metoda de plata.
- Gestiune.
- Genera automat proformele.
- Adaugarea documentelor emise in email-uri trimise clientilor.

== Installation ==

Please see [Installing Plugins](http://codex.wordpress.org/Managing_Plugins#Installing_Plugins) in the WordPress Codex.

Dupa activare, du-te la *WooCommerce > Settings > Facturare*

== Frequently Asked Questions ==

= Cum iau informațiile din baza de date ? =

Pentru a optimiza baza de date, câmpurile adăugate de acest plugin sunt salvate într-un singur rând in baza de date în loc de 5. În tabela wp_postmeta, având key-ul 'av_facturare'. Din cauza asta nu este ușoară preluarea din baza de date a informației, așa că am făcut următoarele key-uri, care nu se găsesc în baza de date, dar o să vă returneze valoarea dorită.

`$cnp = get_post_meta( $order_id, '_billing_facturare_cnp', true );`
`$nr_reg_com = get_post_meta( $order_id, '_billing_facturare_nr_reg_com', true );`
`$cui = get_post_meta( $order_id, '_billing_facturare_cui', true );`
`$nume_banca = get_post_meta( $order_id, '_billing_facturare_nume_banca', true );`
`$iban = get_post_meta( $order_id, '_billing_facturare_iban', true );`

== Screenshots ==

1. Setări Generale
2. Setări Persoane Fizice
3. Setări Persoane Juridice

== Changelog ==

** 1.1.3 **
- Adaugat wpml-config.xml pentru a putea traduce optiunile din plugin.

** 1.1.2 **
- Reparat validarea codul IBAN.

** 1.1.1 **
- Reparat integrarea cu WooCommerce SmartBill

** 1.1.0 **
- Adaugarea posibilitatii de a edita informatii de facturare din admin din comanda
- Validarea campurilor de CNP / CUI / IBAN
- Imbunatatit compatibilitatea cu pluginurile de tip Checkout Field Editor
- adaugare compatibilitate cu Advanced Order Export For WooCommerce ( https://wordpress.org/plugins/woo-order-export-lite/ )

** 1.0.8 **
- Integrare cu WooCommerce PDF Invoice (https://codecanyon.net/item/woocommerce-pdf-invoice/5951088)
- Campul CNP nu mai e obligatoriu
- Integrare cu WooCommerce SmatBill

** 1.0.7 **
- Stilizat fieldul de tip facturare

** 1.0.6 **
- Integrarea cu pluginuri pentru modificarea câmpurilor de pe pagina de checkout ( Checkout Field Editor for WooCommerce / Flexible Checkout Fields )
- Adăugarea de funcții pentru a prelua informațiile din baza de date.

** 1.0.5 **
- Adăugarea unui formular de feedback
- Rezolvat incompatibilitatea cu CartFlow plugin.

** 1.0.4 **
- Added return in 'woocommerce_get_settings_pages' filter.
- Added compatibility with WooCommerce Admin

** 1.0.3 **
- Fixed undefined index.

** 1.0.2 **
- Fixed optional/required in translated WooCommerce.

** 1.0.1 **
- Fixed error on orders admin page.

** 1.0 **
- Initial release