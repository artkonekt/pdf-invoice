<?php

use Konekt\PdfInvoice\InvoicePrinter;

include '../src/InvoicePrinter.php';
$invoice = new InvoicePrinter();
  /* Header Settings */
  $invoice->setLogo('images/example3.jpg');
  $invoice->setColor('#AA3939');
  $invoice->setType('Sale Invoice');
  $invoice->setReference('INV-55033645');
  $invoice->setDate(date('M dS ,Y', time()));
  $invoice->setDue(date('M dS ,Y', strtotime('+3 months')));
  $invoice->setFrom(['Seller Name', 'Sample Company Name', '128 AA Juanita Ave', 'Glendora , CA 91740', 'United States of America']);
  $invoice->setTo(['Purchaser Name', 'Sample Company Name', '128 AA Juanita Ave', 'Glendora , CA 91740', 'United States of America']);
  /* Adding Items in table */
  $invoice->addItem('AMD Athlon X2DC-7450', '2.4GHz/1GB/160GB/SMP-DVD/VB', 6, 0, 580, 0, 3480);
  $invoice->addItem('PDC-E5300', '2.6GHz/1GB/320GB/SMP-DVD/FDD/VB', 4, 0, 645, 0, 2580);
  $invoice->addItem('LG 18.5" WLCD', '', 10, 0, 230, 0, 2300);
  $invoice->addItem('HP LaserJet 5200', '', 1, 0, 1100, 0, 1100);
  /* Add totals */
  $invoice->addTotal('Total', 9460);
  $invoice->addTotal('VAT 21%', 1986.6);
  $invoice->addTotal('Total due', 11446.6, true);
  /* Set badge */
  $invoice->addBadge('Invoice Copy');
  /* Add title */
  $invoice->addTitle('Important Notice');
  /* Add Paragraph */
  $invoice->addParagraph("No item will be replaced or refunded if you don't have the invoice with you. You can refund within 2 days of purchase.");
  /* Set footer note */
  $invoice->setFooternote('My Company Name Here');
  /* Render */
  $invoice->render('example2.pdf', 'D'); /* I => Display on browser, D => Force Download, F => local path save, S => return document path */
