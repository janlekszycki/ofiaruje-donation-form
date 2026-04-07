# Ofiaruje - Formularz Darowizn

Wtyczka WordPress do osadzania formularza darowizny platformy ofiaruje.pl na dowolnej stronie przez shortcode.

## Co nowego w 2.0.0

- Dodano dwa tryby formularza jako zakladki: Wplata jednorazowa i Wplata miesieczna.
- Dodano pola dodatkowe dla trybu miesiecznego: ulica, miasto, kod pocztowy, kraj (domyslnie Polska).
- Dodano ustawienia widocznosci trybow w panelu: osobno dla jednorazowej i miesiecznej.
- Dodano ustawienie domyslnego typu wplaty, aktywne tylko gdy oba tryby sa wlaczone.
- Utrzymano pelna zgodnosc z dotychczasowym shortcode [ofiaruje_formularz].

## Instalacja (GitHub)

1. Wejdz do repozytorium: https://github.com/janlekszycki/ofiaruje-donation-form.
2. Pobierz ZIP z Releases (zalecane) albo Code -> Download ZIP.
3. Upewnij sie, ze archiwum zawiera katalog ofiaruje-donation-form/.
4. W WordPress przejdz do Wtyczki -> Dodaj nowa -> Wyslij wtyczke.
5. Wybierz ZIP, kliknij Zainstaluj teraz, a potem Aktywuj.

## Konfiguracja

Przejdz do Ustawienia -> Ofiaruje.

| Pole                            | Opis                                                                |
| ------------------------------- | ------------------------------------------------------------------- |
| ID Zbiorki                      | Identyfikator zbiorki (\_id) z panelu ofiaruje.pl                   |
| Predefiniowane kwoty (PLN)      | Kwoty oddzielone przecinkami, minimum 20                            |
| Adres platformy                 | Domyslnie https://ofiaruje.pl                                       |
| UTM tracking + pola utm\_\*     | Opcjonalne parametry kampanii                                       |
| Wlasny CSS formularza           | Nadpisanie wygladu formularza                                       |
| Ikony platnosci pod przyciskiem | Pokaz/ukryj ikony metod platnosci                                   |
| Pokazuj Wplata jednorazowa      | Wlacza/ukrywa tryb jednorazowy                                      |
| Pokazuj Wplata miesieczna       | Wlacza/ukrywa tryb miesieczny                                       |
| Domyslny typ wplaty             | Wybiera startowy tryb (single/recurring), gdy oba tryby sa wlaczone |

Uwagi o trybach:

- Domyslnie oba tryby sa wlaczone.
- Jezeli wlaczony jest tylko jeden tryb, formularz automatycznie uruchamia ten tryb i ukrywa zakladki.
- Ustawienie domyslnego typu jest ukrywane i blokowane, gdy aktywny jest tylko jeden tryb.

## Aktualizacja z wersji legacy (1.x -> 2.0.0)

Nie trzeba odinstalowywac poprzedniej wersji wtyczki.

1. Zrob kopie zapasowa strony (pliki + baza danych).
2. W WordPress przejdz do Wtyczki -> Dodaj nowa -> Wyslij wtyczke.
3. Wybierz paczke ZIP z wersja 2.0.0 i kliknij Zainstaluj teraz.
4. Potwierdz nadpisanie istniejacej wtyczki, jesli WordPress o to zapyta.
5. Po aktualizacji wejdz w Ustawienia -> Ofiaruje i zapisz konfiguracje.

Po aktualizacji sprawdz:

- czy wybrane sa poprawne tryby wplat (jednorazowa/miesieczna),
- czy domyslny typ wplaty jest ustawiony zgodnie z oczekiwaniem,
- czy shortcode [ofiaruje_formularz] dziala poprawnie na stronie.

## Uzycie

Wklej shortcode:

```text
[ofiaruje_formularz]
```

## Zachowanie formularza

Tryb jednorazowy wysyla:

- donation[type]=single

Tryb miesieczny wysyla:

- donation[type]=recurring
- donation[donor][details][street]
- donation[donor][details][city]
- donation[donor][details][postal]
- donation[donor][city][country]

Pozostale pola (imie, nazwisko, email, orgname, anonymous, amount) dzialaja jak dotychczas.

## Wymagania

- WordPress 5.0+
- PHP 7.4+
- Aktywna zbiorka na ofiaruje.pl

## Bezpieczenstwo

- Formularz wysyla dane metoda POST bezposrednio do ofiaruje.pl.
- Dane darczyncy nie sa zapisywane w bazie WordPress.
- Ustawienia sa sanityzowane i wyjscie HTML jest escapowane.

## Wersja

2.0.0
