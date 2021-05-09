<?php

use Konekt\PdfInvoice\InvoicePrinter;

include '../src/InvoicePrinter.php';
$invoice = new InvoicePrinter();
  /* Header Settings */
  $invoice->setLogo('images/simple_sample.png');
  $invoice->setColor('#677a1a');
  $invoice->setType('Simple Invoice');
  $invoice->setReference('55033645');
  $invoice->setDate(date('d-m-Y', time()));
  $invoice->setDue(date('d-m-Y', strtotime('+3 months')));
  $invoice->hide_tofrom();
  /* Adding Items in table */
  $invoice->addItem('AMD Athlon X2DC-7450', '2.4GHz/1GB/160GB/SMP-DVD/VB', 6, false, 580, false, 3480);
  $invoice->addItem('PDC-E5300', '2.6GHz/1GB/320GB/SMP-DVD/FDD/VB', 4, false, 645, false, 2580);
  $invoice->addItem('LG 18.5" WLCD', '', 10, false, 230, false, 2300);
  $invoice->addItem('HP LaserJet 5200', '', 1, false, 1100, false, 1100);
  /* Add totals */
  $invoice->addTotal('Total', 9460);
  $invoice->addTotal('Total due', 9460, true);
  /* Render */
  $invoice->render('example2.pdf', 'I'); /* I => Display on browser, D => Force Download, F => local path save, S => return document path */
