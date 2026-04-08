# GitHub Repository Details

This file contains ready-to-paste metadata and content for the GitHub repository.

## 1) Repository "About" section

### Description (EN)

WordPress plugin that embeds the Ofiaruje.pl donation form via shortcode, with one-time/monthly modes, UTM tracking, and no donor data stored in WordPress.

### Description (PL)

Wtyczka WordPress osadzająca formularz darowizn Ofiaruje.pl przez shortcode, z trybem jednorazowym/miesięcznym, UTM i bez zapisu danych darczyńców w WordPress.

### Website

https://ofiaruje.pl

### Suggested topics

wordpress
wordpress-plugin
donations
fundraising
nonprofit
stripe
utm-tracking
php
shortcode
ofiaruje

## 2) Repository short README intro (EN)

Ofiaruje - Donation Form is a WordPress plugin that lets you embed a ready-to-use donation form from Ofiaruje.pl using shortcode [ofiaruje_formularz].

Version 2.0.0 introduces dual donation modes (one-time and monthly), monthly-specific address fields, mode visibility controls in wp-admin, and default mode selection when both modes are enabled.

## 3) Repo "Pinned" one-liner

### EN

Donation form plugin for WordPress with one-time/monthly modes and direct POST to Ofiaruje.pl.

### PL

Wtyczka formularza darowizn dla WordPress z trybem jednorazowym/miesięcznym i bezpośrednim POST do Ofiaruje.pl.

## 4) Release metadata template

### Tag

v2.0.0

### Release title

Ofiaruje Donation Form v2.0.0

### Release assets

ofiaruje-donation-form-v2.0.0.zip

## 5) Release notes (PL)

### Najważniejsze zmiany

- Dodano dwa tryby wpłaty: jednorazowa i miesięczna (zakładki w formularzu).
- Dodano pola dla wpłaty miesięcznej: ulica, miasto, kod pocztowy, kraj (domyślnie Polska).
- Dodano ustawienia widoczności trybów (osobno dla jednorazowej i miesięcznej).
- Dodano ustawienie domyślnego typu wpłaty (aktywne tylko gdy oba tryby są włączone).
- Ulepszono UX panelu: selektor domyślnego typu jest automatycznie ukrywany/blokowany przy jednym aktywnym trybie.
- Zachowano kompatybilność shortcode: [ofiaruje_formularz].

### Aktualizacja z wersji 1.x

- Nie trzeba odinstalowywać poprzedniej wersji.
- Wgraj ZIP 2.0.0 przez: Wtyczki -> Dodaj nową -> Wyślij wtyczkę.
- Potwierdź nadpisanie.
- Po aktualizacji zapisz ustawienia w: Ustawienia -> Ofiaruje.

## 6) Release notes (EN)

### Highlights

- Added two donation modes: one-time and monthly (tab switcher in form).
- Added monthly-only fields: street, city, postal code, country (default: Poland).
- Added mode visibility settings (separate toggles for one-time/monthly).
- Added default donation mode setting (active only when both modes are enabled).
- Improved admin UX: default mode selector auto-hides/disables when only one mode is active.
- Preserved shortcode compatibility: [ofiaruje_formularz].

### Upgrade path from 1.x

- No uninstall required.
- Upload v2.0.0 ZIP via: Plugins -> Add New -> Upload Plugin.
- Confirm overwrite.
- After upgrade, save settings in: Settings -> Ofiaruje.

## 7) Publish checklist

- Tag exists on remote: v2.0.0
- ZIP asset available: ofiaruje-donation-form-v2.0.0.zip
- README includes v2.0.0 changes
- Subpage content updated for v2.0.0
- Release notes pasted in PL + EN
- Release visibility set to Public
