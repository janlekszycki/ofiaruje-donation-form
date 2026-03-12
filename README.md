# Ofiaruje – Formularz Darowizn

Wtyczka WordPress umożliwiająca osadzenie formularza darowizny platformy **ofiaruje.pl** na dowolnej stronie WordPress.

---

## Instalacja

1. Skompresuj folder `ofiaruje-donation-form/` do pliku ZIP:
   ```bash
   zip -r ofiaruje-donation-form.zip ofiaruje-donation-form/
   ```
2. W panelu WordPress przejdź do **Wtyczki → Dodaj nową → Wyślij wtyczkę**.
3. Wybierz plik `ofiaruje-donation-form.zip` i kliknij **Zainstaluj teraz**.
4. Aktywuj wtyczkę.

## Instalacja z GitHub

Wtyczka może być udostępniana i aktualizowana przez GitHub.

1. Wejdź do repozytorium: `https://github.com/<OWNER>/<REPO>`
2. Pobierz kod jako ZIP (`Code -> Download ZIP`) albo paczkę z `Releases`.
3. Upewnij się, że archiwum zawiera katalog `ofiaruje-donation-form/`.
4. Wgraj ZIP w WordPress: **Wtyczki -> Dodaj nową -> Wyślij wtyczkę**.
5. Aktywuj wtyczkę.

> Uwaga: Podmień `<OWNER>/<REPO>` na docelowy adres repozytorium.

---

## Konfiguracja

Przejdź do **Ustawienia → Ofiaruje** i wypełnij pola:

| Pole                                | Opis                                                                                                             |
| ----------------------------------- | ---------------------------------------------------------------------------------------------------------------- |
| **ID Zbiórki**                      | Identyfikator zbiórki (`_id`) z platformy Ofiaruje.pl. Widoczny w adresie URL: `ofiaruje.pl/f/<ID>`              |
| **Predefiniowane kwoty (PLN)**      | Kwoty oddzielone przecinkami (np. `50,100,200,500`). Minimalna wartość: **20 PLN**                               |
| **Adres platformy**                 | Domyślnie `https://ofiaruje.pl` – zazwyczaj nie wymaga zmiany                                                    |
| **UTM tracking (on/off)**           | Dodaje parametry `utm_*` do adresu formularza (`/d?fid=...`)                                                     |
| **UTM Source**                      | Domyślnie `wordpress`                                                                                            |
| **UTM Medium**                      | Domyślnie `plugin`                                                                                               |
| **UTM Campaign**                    | Opcjonalna nazwa kampanii                                                                                        |
| **UTM Term**                        | Opcjonalny parametr kampanii                                                                                     |
| **UTM Content**                     | Opcjonalny parametr kampanii                                                                                     |
| **Własny CSS formularza**           | Pole jest automatycznie wypełnione domyślnym CSS. Możesz go edytować i przywrócić przyciskiem **Ustaw domyślne** |
| **Ikony płatności pod przyciskiem** | Jeden przełącznik `on/off` pokazujący rząd ikon BLIK, Visa, Mastercard, Apple Pay, Google Pay, Revolut Pay       |

---

## Użycie

Wklej shortcode na wybranej stronie lub wpisie:

```
[ofiaruje_formularz]
```

---

## Działanie formularza

Formularz zawiera:

- **Predefiniowane kwoty** – wyświetlane jako przyciski radiowe; konfigurowane w ustawieniach wtyczki
- **Pole kwoty** – możliwość wpisania dowolnej kwoty (minimum 20 PLN)
- **Imię** i **Nazwisko** – pola wymagane
- **Adres e-mail** – pole wymagane, walidacja formatu
- **Nazwa firmy/organizacji** – opcjonalne
- **Checkbox anonimowości** – ukrywa dane darczyńcy na stronie zbiórki
- **Rząd ikon metod płatności** pod przyciskiem CTA (włączany jednym przełącznikiem)
- **UTM tracking** (włączany przełącznikiem) z polami: `utm_source`, `utm_medium`, `utm_campaign`, `utm_term`, `utm_content`
- **Automatyczne ładowanie SVG z folderu `assets/`**: `blik-logo.svg`, `visa-logo.svg`, `mastercard-logo.svg`, `apple-pay-logo.svg`, `google-pay-logo.svg`, `revolut-logo.svg` (lub `revolut-pay-logo.svg`)
- **Custom CSS** ładowany inline (z opcją przywrócenia domyślnych styli)

Po zatwierdzeniu formularz kieruje darczyńcę bezpośrednio do strony płatności na **ofiaruje.pl**, gdzie realizowana jest transakcja przez Stripe.

---

## Wymagania

- WordPress 5.0+
- PHP 7.4+
- Aktywna zbiórka na platformie ofiaruje.pl

---

## Bezpieczeństwo

- Formularz przesyła dane metodą `POST` bezpośrednio do `ofiaruje.pl` – dane darczyńcy nigdy nie są przechowywane w bazie danych WordPress.
- Wszystkie wartości ustawień są sanityzowane (`sanitize_text_field`, `esc_url_raw`, `absint`).
- Wyjście HTML jest zawsze escapowane (`esc_attr`, `esc_html`, `esc_url`).

---

## GitHub

- Kod pluginu: `https://github.com/<OWNER>/<REPO>`
- Katalog pluginu: `wordpress-plugin/ofiaruje-donation-form/`
- Zalecenie dla wdrożeń: publikuj gotowy ZIP pluginu jako artefakt w `Releases`.

---

## Wersja

**1.0.0** – pierwsze wydanie
