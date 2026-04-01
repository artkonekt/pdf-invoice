# Changelog

## 1.15.0
##### 2026-04-01

- Added PHP 8.4 support
- Added PHP 8.5 support
- Dropped PHP 7 support

## 1.14.0
##### 2023-11-28

- Added PHP 8.3 support
- Added the missing Italian translation for "time" [[#73](https://github.com/artkonekt/pdf-invoice/pull/73)] Thanks [Luca Pacitto](https://github.com/Nembie)
- Added encoding conversion to footer note [[#72](https://github.com/artkonekt/pdf-invoice/pull/72)] Thanks [Robszyy](https://github.com/Robszyy)

## 1.13.1
##### 2023-02-19

- Fixed PHP 8.2 deprecation notice [[#68](https://github.com/artkonekt/pdf-invoice/pull/68)] Thanks [Adam Rogers](https://github.com/arctican)

## 1.13.0
##### 2023-01-12

- Changed printing items so that when an item's text is empty, no empty line gets printed [[#62](https://github.com/artkonekt/pdf-invoice/pull/62)]. Thanks [Sébastien Morel](https://github.com/dragonfly4)
- Added PHP 8.2 compatibility by explicitly adding dynamic class properties [[#66](https://github.com/artkonekt/pdf-invoice/pull/66)]. Thanks [deba12](https://github.com/deba12)

### 1.12.0
##### 2022-06-14

- Added Danish language support: Thanks [mkgeeky](https://github.com/mkgeeky)

### 1.11.0
##### 2022-03-26

- Changed height calculation in `Body()` method to use late static binding [[#60](https://github.com/artkonekt/pdf-invoice/pull/60)]. Thanks [George Constantinou](https://github.com/georgeconstantinou)

### 1.10.4
##### 2022-03-23

- Fixed page break issue when big item descriptions [[#50](https://github.com/artkonekt/pdf-invoice/pull/50)]. Thanks [XavLal](https://github.com/XavLal)
- Fixed PHP 8.1 compatibility error when certain strings were null [[#59](https://github.com/artkonekt/pdf-invoice/pull/59)]. Thanks [George Constantinou](https://github.com/georgeconstantinou)
- Improved internal code by adding constants [[#58](https://github.com/artkonekt/pdf-invoice/pull/58)]. Thanks [Matej Bačo](https://github.com/Meldiron)


### 1.10.3
##### 2021-11-16

- Fixed first column width calculation for Invoices having long item the descriptions. [[#49](https://github.com/artkonekt/pdf-invoice/pull/49)]. Thanks [Benoit](https://github.com/benvia)

### 1.10.2
##### 2021-11-15

- Fixed incorrect number of columns when adding multiple items. [[#48](https://github.com/artkonekt/pdf-invoice/pull/48)]. Thanks [Benoit](https://github.com/benvia)

### 1.10.1
##### 2021-09-28

- Fixed bug introduced in 1.10.0: column count issue/broken when a line had a "description" field. Thanks [Ivan Yivoff](https://github.com/yivi)

### 1.10.0
##### 2021-09-28

- Added horizontal alignment of totals. Thanks [Noe Gnanih](https://github.com/noeGnh)
- Added possibility to skip rendering of the quantity column. Thanks [Ivan Yivoff](https://github.com/yivi)

### 1.9.0
##### 2021-04-14

- Added Swedish language support: Thanks [NexeriaAB](https://github.com/NexeriaAB)
- Added encoding conversion to "from" header as well: Thanks [Hans](https://github.com/hankur)
- Changed the price and total values to be optional: Thanks [Hans](https://github.com/hankur)

### 1.8.0
##### 2021-03-17

- Added Estonian language support: Thanks [Hans](https://github.com/hankur)

### 1.7.0
##### 2020-12-26

- X-mas 2020 release 🎄 👑 🦠
- Dropped PHP 5.6 - 7.2 support
- Added PHP 8.0 support
- Added Negative Numbers in Parenthesis feature: Thanks [Tristan Curtis](https://github.com/TCURT15)
- Added changeLanguageTerm function: Thanks [Fabian Pankoke](https://github.com/fabianpnke)
- Added Brazilian language file: Thanks [Saulo Henrique](https://github.com/msaulohenrique)
- Added Romanian language file: Thanks [Sebastian Marinescu](https://github.com/sebastian-marinescu)
- Fixed global namespace import: Thanks [Erwan Nader](https://github.com/ErnadoO)
- Switched from Travis to Github Actions

### 1.6.0
##### 2020-06-21

- Added optional alignment and spacing settings to be set via setNumberFormat
- Fixed "Trying to access array offset on value of type null" when trying to calculate height for description on public function Body()
- Fixed undefined offset exception when to and from array lengths are different.
- Changed to output only a line feed if the address line in both arrays is empty (intentional spacing)
- Fixed the "reference" text overlapping the reference itself (if very long)
- Fixed some Dutch language issues

Thanks [Jaap de Jong](https://github.com/japsen) and [Tahri Ahmed](https://github.com/Ousret) for
the improvements!

### 1.5.0
##### 2019-12-19

- It's possible to change the description font size
- Added option to display the currency after the amount
- Added option to display a price without space between the currency and amount
- Table spacing fixes

### 1.4.0
##### 2019-09-20

- Added option to hide Issuer and Client header row
- Changed: no longer printing empty FROM/TO lines
- Change French 'Total' -> 'Total TTC'

### 1.3.1
##### 2019-04-15

- Dutch translation fix

### 1.3.0
##### 2019-02-14

- Added support for setting badge color

### 1.2.1
##### 2018-12-28

- Spanish translation fix

### 1.2.0
##### 2018-12-20

- Lithuanian language support

### 1.1.5
##### 2018-12-17

- Added missing 'time' key in French

### 1.1.4
##### 2018-11-03

- Fix wrong column number when having discount but not VAT.

### 1.1.3
##### 2018-10-17

- Fixed missing `time` entry in German language file

### 1.1.2
##### 2018-10-02

- Minor code and doc improvements

### 1.1.1
##### 2018-08-09

- Polish language support
- UTF-8 support in company name

### 1.1.0
##### 2018-07-11

- The `render()` method returns the output
- Bugfixes

### 1.0.5
##### 2018-07-06

- Turkish translation has been added

### 1.0.4
##### 2018-06-26

- French language improvements
- FIXED: Uppercase doesn't work on accented characters

### 1.0.3
##### 2018-06-06

- German language improvements

### 1.0.2
##### 2018-03-05

- Old classname bugfix


### 1.0.1
##### 2018-01-16

- Attributes have been made public

### 1.0.0
##### 2017-12-15

- Forked latest version from [pdf-invoicr](https://github.com/farjadtahir/pdf-invoicr)
- Converted to PSR-4 and composer
- PHP 7 support added
- FPDF 1.8.1 supported
