# PDF Invoice

[![Travis Build Status](https://img.shields.io/travis/artkonekt/pdf-invoice.svg?style=flat-square)](https://travis-ci.org/artkonekt/pdf-invoice)
[![Packagist Stable Version](https://img.shields.io/packagist/v/konekt/pdf-invoice.svg?style=flat-square&label=stable)](https://packagist.org/packages/konekt/pdf-invoice)
[![Packagist downloads](https://img.shields.io/packagist/dt/konekt/pdf-invoice.svg?style=flat-square)](https://packagist.org/packages/konekt/pdf-invoice)
[![GPL Software License](https://img.shields.io/badge/license-GPL-blue.svg?style=flat-square)](LICENSE)

This is a streamlined fork of [pdf-invoicr](https://github.com/farjadtahir/pdf-invoicr).

Changes:
- PHP 7.0 Support
- PSR-4 compatible
- Available as composer package
- Dependencies are coming via composer

## Introduction

PHP Invoice is a simple object oriented PHP class to generate beautifully designed invoices, quotes
or orders with just a few lines of code. Brand it with your own logo and theme color, add unlimited
items and total rows with automatic paging. You can deliver the PDF ouput in the user's browser,
save on the server or force a file download. PHP Invoice is fully customizable and can be integrated
into any well known CMS.

### Multi-languages & Currencies

PHP Invoice has built in translations in English, Dutch, French, German, Spanish and Italian (you
can easily add your own if needed) and you can set the currency needed per document.

### Additional Titles, Paragraphs And Badges

Extra content (titles and multi-line paragraphs) can be added to the bottom of the document. You
might use it for payment or shipping information or any other content needed.

## Installation

```bash
composer require konekt/pdf-invoice
```

## Examples

There are 3 examples included in the `examples/` folder of this repo:
- simple.php
- example1.php
- example2.php
- change_timezone.php


### Create A New Invoice

> Make sure that the default php date timezone is set before using the class.

In this simple example we are generating an invoice with custom logo and theme color. It will
contain 2 products and a box on the bottom with VAT and total price. Then we add a "Paid" badge
right before the output.

```php
use Konekt\PdfInvoice\InvoicePrinter;

  $invoice = new InvoicePrinter();
  
  /* Header settings */
  $invoice->setLogo("images/sample1.jpg");   //logo image path
  $invoice->setColor("#007fff");      // pdf color scheme
  $invoice->setType("Sale Invoice");    // Invoice Type
  $invoice->setReference("INV-55033645");   // Reference
  $invoice->setDate(date('M dS ,Y',time()));   //Billing Date
  $invoice->setTime(date('h:i:s A',time()));   //Billing Time
  $invoice->setDue(date('M dS ,Y',strtotime('+3 months')));    // Due Date
  $invoice->setFrom(array("Seller Name","Sample Company Name","128 AA Juanita Ave","Glendora , CA 91740"));
  $invoice->setTo(array("Purchaser Name","Sample Company Name","128 AA Juanita Ave","Glendora , CA 91740"));
  
  $invoice->addItem("AMD Athlon X2DC-7450","2.4GHz/1GB/160GB/SMP-DVD/VB",6,0,580,0,3480);
  $invoice->addItem("PDC-E5300","2.6GHz/1GB/320GB/SMP-DVD/FDD/VB",4,0,645,0,2580);
  $invoice->addItem('LG 18.5" WLCD',"",10,0,230,0,2300);
  $invoice->addItem("HP LaserJet 5200","",1,0,1100,0,1100);
  
  $invoice->addTotal("Total",9460);
  $invoice->addTotal("VAT 21%",1986.6);
  $invoice->addTotal("Total due",11446.6,true);
  
  $invoice->addBadge("Payment Paid");
  
  $invoice->addTitle("Important Notice");
  
  $invoice->addParagraph("No item will be replaced or refunded if you don't have the invoice with you.");
  
  $invoice->setFooternote("My Company Name Here");
  
  $invoice->render('example1.pdf','I'); 
  /* I => Display on browser, D => Force Download, F => local path save, S => return document as string */
```

## Documentation

### Create Instances

```php
use Konekt\PdfInvoice\InvoicePrinter;

// Default Param: Size: A4, Currency: $, Language: en
$invoice = new InvoicePrinter($size, $currency, $language); 
```

| Parameter | Type   | Accepts                          | Note                                                 |
|:----------|:-------|:---------------------------------|:-----------------------------------------------------|
| size      | string | A4 (default)<br>Letter<br>Legal  | Set your document size                               |
| currency  | string | any string (e.g. "$", "£", "€")  | Set the currency that you want to use                |
| language  | string | en (default), nl, fr, de, es, it | A language that exists in the `inc/languages` folder |


### Number Formatting

How do you want to show your numbers?

```php
$invoice->setNumberFormat($decimalpoint, $seperator);
```

| Parameter    | Type   | Accepts                     | Note                                       |
|:-------------|:-------|:----------------------------|:-------------------------------------------|
| decimalpoint | string | Commonly used is '.' or ',' | What string to use for decimal point       |
| seperator    | string | Commonly used is '.' or ',' | What string to use for thousands separator |

### Color

Set a custom color to personalize your invoices.

```php
// Hexadecimal color code
$invoice->setColor($color);
```

### Add Logo

```php
$invoice->setLogo($image, $maxwidth, $maxheight);
```

| Parameter            | Type   | Accepts                               | Note                                                                     |
|:---------------------|:-------|:--------------------------------------|:-------------------------------------------------------------------------|
| image                | string | Local path or remote url of the image | Preferably a good quality transparant png image                          |
| maxwidth (optional)  | int    |                                       | The width (in mm) of the bounding box where the image will be fitted in  |
| maxheight (optional) | int    |                                       | The height (in mm) of the bounding box where the image will be fitted in |

## Document Title

```php
// A string with the document title, will be displayed
// in the right top corner of the document (e.g. 'Invoice' or 'Quote')
$invoice->setType($title);
```

### Invoice Number

```php
// Document reference number that will be displayed in
// the right top corner of the document (e.g. 'INV29782')
$invoice->setReference($number);
```

### Date

```php
//A string with the document's date
$invoice->setDate($date);
```

### Due Date

```php
// A string with the document's due date
$invoice->setDue($duedate);
```

### Issuer Information

Set your company details.

```php
// An array with your company details. The first value of
// the array will be bold on the document so it's suggested
// to use your company's name. You can add as
// many lines as you need.
/** Example: */
$invoice->setFrom([
    'My Company',
    'Address line 1',
    'Address line 2',
    'City and zip',
    'Country',
    'VAT number'    
]);
```

### Client Information

```php
// An array with your clients' details. The first value of the
// array will be bold on the document so we suggest you to use
// the company's name. You can add as many lines as you need.
/** Example */
$invoice->setTo([
   'My Client',
   'Address line 1',
   'Address line 2',
   'City and zip',
   'Country',
   'VAT number'    
]);
```

### Flip Flop

Switch the horizontal positions of your company information and the client information. By default,
your company details are on the left.

```php
$invoice->flipflop();
```

### Issuer and Client header titles

Hide the issuer and client header row

```php
$invoice->hideToFromHeaders();
```

### Add custom text or general terms

Enable extra page with custom text, default is false

```php
$invoice->setAddTermsPage(true);
```

Define number of columns, default is 1

```php
$invoice->setAddTermsPageColumns(2);
```

Path to text file for 1 column

```php
$invoice->setAddTermsPageText1('example_terms01.txt');
```

Optional path to text file for 2nd column

```php
$invoice->setAddTermsPageText1('example_terms02.txt');
```

### Adding Items

Add a new product or service row to your document below the company and client information. PHP
Invoice has automatic paging so there is absolutely no limit.

```php
$invoice->addItem(name,description,amount,vat,price,discount,total);
```

name {string} A string with the
product or service name. description {string} A string with the description with multi-line support.
Use either <br> or \n to add a line-break. amount {decimal} An integer with the amount of this item.
vat {string} or {decimal} Pass a string (e.g. "21%", or any other text you may like) or a decimal if
you want to show an amount instead (e.g. 124.30) price {decimal} A decimal for the unit price.
discount {string}, {decimal} or {boolean}Optional Pass a string (e.g. "10%", or any other text you
may like) or a decimal if you want to show an amount instead (e.g. 50.00) If you do not want to give
discount just enter the boolean false in this field. Note: the final output will not show a discount
column when all of the products haven't set a discount. total {decimal} A decimal for the total
product or service price.

### Adding Totals

Add a row below the products and services for calculations and totals. You can add unlimited rows.

```php
$invoice->addTotal(name,value,background);
```

name {string} A string for the display name of the total
field. value {decimal} A decimal for the value. background {boolean}Optional Set to true to set the
theme color as background color of the row.

### Adding A Badge

Adds a badge to your invoice below the products and services. You can use this for example to
display that the invoice has been payed.

```php
$invoice->addBadge($badge);
```

badge {string} A string with the text of the badge.

It is possible to set the color of the badge as the second parameter:

```php
$invoice->addBadge('Paid', '#00ff00');
// Short hex variant is also supported
$invoice->addBadge('Payment pending', '#f00');
```

> CSS color names ('red', 'cyan', 'fuchsia', etc) are **NOT supported**

### Add Title

You can add titles and paragraphs to display information on the bottom part of your document such as
payment details or shipping information.

```php
$invoice->addTitle($title);
```

title {string} A string with the title to display in the badge.

### Add Paragraph

You can add titles and paragraphs to display information on the bottom part of your document such as
payment details or shipping information.

```php
$invoice->addParagraph($paragraph);
```

Paragraph {string} A string with the paragraph text with
multi-line support. Use either <br> or \n to add a line-break.

### Footer

A small text you want to display on the bottom left corner of the document.

```php
$invoice->setFooternote($note);
```

note {string} A string with the information you want to display in the footer.

### Rendering The Invoice

```php
$invoice->render($name, $output);
```

name {string} A string with the name of your invoice.

Example: 'invoice.pdf'

output {string} Choose how you want the invoice to be delivered to the user. The
following options are available: I (Send the file inline to the browser) D (Send to the browser and
force a file download with the name given by name) F (Save to a local file. Make sure to set pass
the path in the name parameter) S (Return the document as a string)

## Credits

- [Splashpk](https://github.com/farjadtahir/pdf-invoicr)
- [FPDF](http://www.fpdf.org/)
