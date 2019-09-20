<?php
/**
 * Contains the InvoicePrinter class.
 *
 * @author      Farjad Tahir
 * @see         http://www.splashpk.com
 * @license     GPL
 * @since       2017-12-15
 *
 */

namespace Konekt\PdfInvoice;

use FPDF;

class InvoicePrinter extends FPDF
{
    const ICONV_CHARSET_INPUT = 'UTF-8';
    const ICONV_CHARSET_OUTPUT_A = 'ISO-8859-1//TRANSLIT';
    const ICONV_CHARSET_OUTPUT_B = 'windows-1252//TRANSLIT';

    public $angle           = 0;
    public $font            = 'helvetica';        /* Font Name : See inc/fpdf/font for all supported fonts */
    public $columnOpacity   = 0.06;            /* Items table background color opacity. Range (0.00 - 1) */
    public $columnSpacing   = 0.3;                /* Spacing between Item Tables */
    public $referenceformat = ['.', ','];    /* Currency formater */
    public $margins         = [
        'l' => 15,
        't' => 15,
        'r' => 15
    ]; /* l: Left Side , t: Top Side , r: Right Side */

    public $lang;
    public $document;
    public $type;
    public $reference;
    public $logo;
    public $color;
    public $badgeColor;
    public $date;
    public $time;
    public $due;
    public $from;
    public $to;
    public $items;
    public $totals;
    public $badge;
    public $addText;
    public $footernote;
    public $dimensions;
    public $display_tofrom = true;
    protected $displayToFromHeaders = true;
    protected $displayNewPageShowHeader = true;
    protected $addPageTerms = false;
    protected $addPageTermsColumns = 1;
    protected $addPageTermsText1 = '';
    protected $addPageTermsText2 = '';
    protected $columns;

    public function __construct($size = 'A4', $currency = '$', $language = 'en')
    {
        $this->items              = [];
        $this->totals             = [];
        $this->addText            = [];
        $this->firstColumnWidth   = 70;
        $this->currency           = $currency;
        $this->maxImageDimensions = [230, 130];
        $this->setLanguage($language);
        $this->setDocumentSize($size);
        $this->setColor("#222222");

        $this->recalculateColumns();

        parent::__construct('P', 'mm', [$this->document['w'], $this->document['h']]);

        $this->AliasNbPages();
        $this->SetMargins($this->margins['l'], $this->margins['t'], $this->margins['r']);
    }

    private function setLanguage($language)
    {
        $this->language = $language;
        include(dirname(__DIR__) . '/inc/languages/' . $language . '.inc');
        $this->lang = $lang;
    }

    private function setDocumentSize($dsize)
    {
        switch ($dsize) {
            case 'A4':
                $document['w'] = 210;
                $document['h'] = 297;
                break;
            case 'letter':
                $document['w'] = 215.9;
                $document['h'] = 279.4;
                break;
            case 'legal':
                $document['w'] = 215.9;
                $document['h'] = 355.6;
                break;
            default:
                $document['w'] = 210;
                $document['h'] = 297;
                break;
        }

        $this->document = $document;
    }

    private function resizeToFit($image)
    {
        list($width, $height) = getimagesize($image);
        $newWidth  = $this->maxImageDimensions[0] / $width;
        $newHeight = $this->maxImageDimensions[1] / $height;
        $scale     = min($newWidth, $newHeight);

        return [
            round($this->pixelsToMM($scale * $width)),
            round($this->pixelsToMM($scale * $height))
        ];
    }

    private function pixelsToMM($val)
    {
        $mm_inch = 25.4;
        $dpi     = 96;

        return ($val * $mm_inch) / $dpi;
    }

    private function hex2rgb($hex)
    {
        $hex = str_replace("#", "", $hex);
        if (strlen($hex) == 3) {
            $r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
            $g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
            $b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
        } else {
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
        }
        $rgb = [$r, $g, $b];

        return $rgb;
    }

    private function br2nl($string)
    {
        return preg_replace('/\<br(\s*)?\/?\>/i', "\n", $string);
    }

    public function isValidTimezoneId($zone)
    {
        try {
            new DateTimeZone($zone);
        } catch (Exception $e) {
            return false;
        }

        return true;
    }

    public function setTimeZone($zone = "")
    {
        if (!empty($zone) and $this->isValidTimezoneId($zone) === true) {
            date_default_timezone_set($zone);
        }
    }

    public function setType($title)
    {
        $this->title = $title;
    }

    public function setColor($rgbcolor)
    {
        $this->color = $this->hex2rgb($rgbcolor);
    }

    public function setDate($date)
    {
        $this->date = $date;
    }

    public function setTime($time)
    {
        $this->time = $time;
    }

    public function setDue($date)
    {
        $this->due = $date;
    }

    public function setLogo($logo = 0, $maxWidth = 0, $maxHeight = 0)
    {
        if ($maxWidth and $maxHeight) {
            $this->maxImageDimensions = [$maxWidth, $maxHeight];
        }
        $this->logo       = $logo;
        $this->dimensions = $this->resizeToFit($logo);
    }

    public function hide_tofrom()
    {
        $this->display_tofrom = false;
    }
	
	public function hideToFromHeaders()
    {
        $this->displayToFromHeaders = false;
    }

    public function setFrom($data)
    {
        $this->from = $data;
    }

    public function setTo($data)
    {
        $this->to = $data;
    }

    public function setReference($reference)
    {
        $this->reference = $reference;
    }

    public function setNumberFormat($decimals, $thousands_sep)
    {
        $this->referenceformat = [$decimals, $thousands_sep];
    }

    public function setAddTermsPage($data)
    {
        $this->addPageTerms = $data;
    }
    
    public function setAddTermsPageColumns($data)
    {
        $this->addPageTermsColumns = $data;
    }
    
    public function setAddTermsPageText1($data)
    {
        $this->addPageTermsText1 = $data;
    }
    
    public function setAddTermsPageText2($data)
    {
        $this->addPageTermsText2 = $data;
    }

    public function flipflop()
    {
        $this->flipflop = true;
    }

    public function addItem($item, $description = "", $quantity, $vat, $price, $discount = 0, $total)
    {
        $p['item']        = $item;
        $p['description'] = $this->br2nl($description);

        if ($vat !== false) {
            $p['vat'] = $vat;
            if (is_numeric($vat)) {
                $p['vat'] = $this->currency . ' ' . number_format($vat, 2, $this->referenceformat[0],
                        $this->referenceformat[1]);
            }
            $this->vatField = true;
            $this->recalculateColumns();
        }
        $p['quantity'] = $quantity;
        $p['price']    = $price;
        $p['total']    = $total;

        if ($discount !== false) {
            $this->firstColumnWidth = 58;
            $p['discount']          = $discount;
            if (is_numeric($discount)) {
                $p['discount'] = $this->currency . ' ' . number_format($discount, 2, $this->referenceformat[0],
                        $this->referenceformat[1]);
            }
            $this->discountField = true;
            $this->recalculateColumns();
        }
        $this->items[] = $p;
    }

    public function addTotal($name, $value, $colored = false)
    {
        $t['name']  = $name;
        $t['value'] = $value;
        if (is_numeric($value)) {
            $t['value'] = $this->currency . ' ' . number_format($value, 2, $this->referenceformat[0],
                    $this->referenceformat[1]);
        }
        $t['colored']   = $colored;
        $this->totals[] = $t;
    }

    public function addTitle($title)
    {
        $this->addText[] = ['title', $title];
    }

    public function addParagraph($paragraph)
    {
        $paragraph       = $this->br2nl($paragraph);
        $this->addText[] = ['paragraph', $paragraph];
    }

    public function addBadge($badge, $color = false)
    {
        $this->badge = $badge;

        if ($color) {
            $this->badgeColor = $this->hex2rgb($color);
        } else {
            $this->badgeColor = $this->color;
        }
    }

    public function setFooternote($note)
    {
        $this->footernote = $note;
    }

    public function render($name = '', $destination = '')
    {
        $this->AddPage();
        $this->Body();
        $this->AliasNbPages();
        if($this->addPageTerms === true){
            $this->displayNewPageShowHeader = false;
            $this->AddPage();
            $this->terms();
        }
        return $this->Output($destination, $name);
    }

    public function Header()
    {
        if ($this->displayNewPageShowHeader === true) {
            if (isset($this->logo) and !empty($this->logo)) {
                $this->Image($this->logo, $this->margins['l'], $this->margins['t'], $this->dimensions[0],
                    $this->dimensions[1]);
            }

            //Title
            $this->SetTextColor(0, 0, 0);
            $this->SetFont($this->font, 'B', 20);
            if(isset($this->title) and !empty($this->title)) {
                $this->Cell(0, 5, iconv(self::ICONV_CHARSET_INPUT, self::ICONV_CHARSET_OUTPUT_A, mb_strtoupper($this->title, self::ICONV_CHARSET_INPUT)), 0, 1, 'R');
            }
            $this->SetFont($this->font, '', 9);
            $this->Ln(5);

            $lineheight = 5;
            //Calculate position of strings
            $this->SetFont($this->font, 'B', 9);
            $positionX = $this->document['w'] - $this->margins['l'] - $this->margins['r'] - max(mb_strtoupper($this->GetStringWidth($this->lang['number'], self::ICONV_CHARSET_INPUT)),
                    mb_strtoupper($this->GetStringWidth($this->lang['date'], self::ICONV_CHARSET_INPUT)),
                    mb_strtoupper($this->GetStringWidth($this->lang['due'], self::ICONV_CHARSET_INPUT))) - 35;

            //Number
            if (!empty($this->reference)) {
                $this->Cell($positionX, $lineheight);
                $this->SetTextColor($this->color[0], $this->color[1], $this->color[2]);
                $this->Cell(32, $lineheight, iconv(self::ICONV_CHARSET_INPUT, self::ICONV_CHARSET_OUTPUT_A, mb_strtoupper($this->lang['number'], self::ICONV_CHARSET_INPUT) . ':'), 0, 0,
                    'L');
                $this->SetTextColor(50, 50, 50);
                $this->SetFont($this->font, '', 9);
                $this->Cell(0, $lineheight, $this->reference, 0, 1, 'R');
            }
            //Date
            $this->Cell($positionX, $lineheight);
            $this->SetFont($this->font, 'B', 9);
            $this->SetTextColor($this->color[0], $this->color[1], $this->color[2]);
            $this->Cell(32, $lineheight, iconv(self::ICONV_CHARSET_INPUT, self::ICONV_CHARSET_OUTPUT_A, mb_strtoupper($this->lang['date'], self::ICONV_CHARSET_INPUT)) . ':', 0, 0, 'L');
            $this->SetTextColor(50, 50, 50);
            $this->SetFont($this->font, '', 9);
            $this->Cell(0, $lineheight, $this->date, 0, 1, 'R');

            //Time
            if (!empty($this->time)) {
                $this->Cell($positionX, $lineheight);
                $this->SetFont($this->font, 'B', 9);
                $this->SetTextColor($this->color[0], $this->color[1], $this->color[2]);
                $this->Cell(32, $lineheight, iconv(self::ICONV_CHARSET_INPUT, self::ICONV_CHARSET_OUTPUT_A, mb_strtoupper($this->lang['time'], self::ICONV_CHARSET_INPUT)) . ':', 0, 0,
                    'L');
                $this->SetTextColor(50, 50, 50);
                $this->SetFont($this->font, '', 9);
                $this->Cell(0, $lineheight, $this->time, 0, 1, 'R');
            }
            //Due date
            if (!empty($this->due)) {
                $this->Cell($positionX, $lineheight);
                $this->SetFont($this->font, 'B', 9);
                $this->SetTextColor($this->color[0], $this->color[1], $this->color[2]);
                $this->Cell(32, $lineheight, iconv(self::ICONV_CHARSET_INPUT, self::ICONV_CHARSET_OUTPUT_A, mb_strtoupper($this->lang['due'], self::ICONV_CHARSET_INPUT)) . ':', 0, 0, 'L');
                $this->SetTextColor(50, 50, 50);
                $this->SetFont($this->font, '', 9);
                $this->Cell(0, $lineheight, $this->due, 0, 1, 'R');
            }
        }

        //First page
        if ($this->PageNo() == 1) {
            if (($this->margins['t'] + $this->dimensions[1]) > $this->GetY()) {
                $this->SetY($this->margins['t'] + $this->dimensions[1] + 5);
            } else {
                $this->SetY($this->GetY() + 10);
            }
            $this->Ln(5);
            $this->SetFillColor($this->color[0], $this->color[1], $this->color[2]);
            $this->SetTextColor($this->color[0], $this->color[1], $this->color[2]);

            $this->SetDrawColor($this->color[0], $this->color[1], $this->color[2]);
            $this->SetFont($this->font, 'B', 10);
            $width = ($this->document['w'] - $this->margins['l'] - $this->margins['r']) / 2;
            if (isset($this->flipflop)) {
                $to                 = $this->lang['to'];
                $from               = $this->lang['from'];
                $this->lang['to']   = $from;
                $this->lang['from'] = $to;
                $to                 = $this->to;
                $from               = $this->from;
                $this->to           = $from;
                $this->from         = $to;
            }

            if ($this->display_tofrom === true) {
                if ($this->displayToFromHeaders === true) {
                    $this->Cell($width, $lineheight, iconv(self::ICONV_CHARSET_INPUT, self::ICONV_CHARSET_OUTPUT_A, mb_strtoupper($this->lang['from'], self::ICONV_CHARSET_INPUT)), 0, 0, 'L');
                    $this->Cell(0, $lineheight, iconv(self::ICONV_CHARSET_INPUT, self::ICONV_CHARSET_OUTPUT_A, mb_strtoupper($this->lang['to'], self::ICONV_CHARSET_INPUT)), 0, 0, 'L');
                    $this->Ln(7);
                    $this->SetLineWidth(0.4);
                    $this->Line($this->margins['l'], $this->GetY(), $this->margins['l'] + $width - 10, $this->GetY());
                    $this->Line($this->margins['l'] + $width, $this->GetY(), $this->margins['l'] + $width + $width,
                        $this->GetY());
                } else {
                    $this->Ln(2);
                }

                //Information
                $this->Ln(5);
                $this->SetTextColor(50, 50, 50);
                $this->SetFont($this->font, 'B', 10);
                $this->Cell($width, $lineheight, $this->from[0], 0, 0, 'L');
                $this->Cell(0, $lineheight, iconv(self::ICONV_CHARSET_INPUT, self::ICONV_CHARSET_OUTPUT_A, $this->to[0]), 0, 0, 'L');
                $this->SetFont($this->font, '', 8);
                $this->SetTextColor(100, 100, 100);
                $this->Ln(7);
                for ($i = 1, $iMax = max($this->from === null ? 0 : count($this->from), $this->to === null ? 0 : count($this->to)); $i < $iMax; $i++) {
                    // check if the TO or FROM array value is not empty.
                    if($this->to[$i] !== ""){
                        $this->Cell($width, $lineheight, iconv(self::ICONV_CHARSET_INPUT, self::ICONV_CHARSET_OUTPUT_A, $this->from[$i]), 0, 0, 'L');
                        $this->Cell(0, $lineheight, iconv(self::ICONV_CHARSET_INPUT, self::ICONV_CHARSET_OUTPUT_A, $this->to[$i]), 0, 0, 'L');
                        $this->Ln(5);
                    }
                }
                $this->Ln(-6);
                $this->Ln(5);
            } else {
                $this->Ln(-10);
            }
        }
        //Table header
        if (!isset($this->productsEnded)) {
            $width_other = ($this->document['w'] - $this->margins['l'] - $this->margins['r'] - $this->firstColumnWidth - ($this->columns * $this->columnSpacing)) / ($this->columns - 1);
            $this->SetTextColor(50, 50, 50);
            $this->Ln(12);
            $this->SetFont($this->font, 'B', 9);
            $this->Cell(1, 10, '', 0, 0, 'L', 0);
            $this->Cell($this->firstColumnWidth, 10, iconv(self::ICONV_CHARSET_INPUT, self::ICONV_CHARSET_OUTPUT_A, mb_strtoupper($this->lang['product'], self::ICONV_CHARSET_INPUT)),
                0, 0, 'L', 0);
            $this->Cell($this->columnSpacing, 10, '', 0, 0, 'L', 0);
            $this->Cell($width_other, 10, iconv(self::ICONV_CHARSET_INPUT, self::ICONV_CHARSET_OUTPUT_A, mb_strtoupper($this->lang['qty'], self::ICONV_CHARSET_INPUT)), 0, 0, 'C', 0);
            if (isset($this->vatField)) {
                $this->Cell($this->columnSpacing, 10, '', 0, 0, 'L', 0);
                $this->Cell($width_other, 10, iconv(self::ICONV_CHARSET_INPUT, self::ICONV_CHARSET_OUTPUT_A, mb_strtoupper($this->lang['vat'], self::ICONV_CHARSET_INPUT)), 0, 0, 'C',
                    0);
            }
            $this->Cell($this->columnSpacing, 10, '', 0, 0, 'L', 0);
            $this->Cell($width_other, 10, iconv(self::ICONV_CHARSET_INPUT, self::ICONV_CHARSET_OUTPUT_A, mb_strtoupper($this->lang['price'], self::ICONV_CHARSET_INPUT)), 0, 0, 'C', 0);
            if (isset($this->discountField)) {
                $this->Cell($this->columnSpacing, 10, '', 0, 0, 'L', 0);
                $this->Cell($width_other, 10, iconv(self::ICONV_CHARSET_INPUT, self::ICONV_CHARSET_OUTPUT_A, mb_strtoupper($this->lang['discount'], self::ICONV_CHARSET_INPUT)), 0, 0,
                    'C', 0);
            }
            $this->Cell($this->columnSpacing, 10, '', 0, 0, 'L', 0);
            $this->Cell($width_other, 10, iconv(self::ICONV_CHARSET_INPUT, self::ICONV_CHARSET_OUTPUT_A, mb_strtoupper($this->lang['total'], self::ICONV_CHARSET_INPUT)), 0, 0, 'C', 0);
            $this->Ln();
            $this->SetLineWidth(0.3);
            $this->SetDrawColor($this->color[0], $this->color[1], $this->color[2]);
            $this->Line($this->margins['l'], $this->GetY(), $this->document['w'] - $this->margins['r'], $this->GetY());
            $this->Ln(2);
        } elseif ($this->displayNewPageShowHeader === false) {
        } else {
            $this->Ln(12);
        }
    }

    public function Body()
    {
        $width_other = ($this->document['w'] - $this->margins['l'] - $this->margins['r'] - $this->firstColumnWidth - ($this->columns * $this->columnSpacing)) / ($this->columns - 1);
        $cellHeight  = 8;
        $bgcolor     = (1 - $this->columnOpacity) * 255;
        if ($this->items) {
            foreach ($this->items as $item) {
                if ($item['description']) {
                    //Precalculate height
                    $calculateHeight = new self;
                    $calculateHeight->addPage();
                    $calculateHeight->setXY(0, 0);
                    $calculateHeight->SetFont($this->font, '', 7);
                    $calculateHeight->MultiCell($this->firstColumnWidth, 3,
                        iconv(self::ICONV_CHARSET_INPUT, self::ICONV_CHARSET_OUTPUT_A, $item['description']), 0, 'L', 1);
                    $descriptionHeight = $calculateHeight->getY() + $cellHeight + 2;
                    $pageHeight        = $this->document['h'] - $this->GetY() - $this->margins['t'] - $this->margins['t'];
                    if ($pageHeight < 35) {
                        $this->AddPage();
                    }
                }
                $cHeight = $cellHeight;
                $this->SetFont($this->font, 'b', 8);
                $this->SetTextColor(50, 50, 50);
                $this->SetFillColor($bgcolor, $bgcolor, $bgcolor);
                $this->Cell(1, $cHeight, '', 0, 0, 'L', 1);
                $x = $this->GetX();
                $this->Cell($this->firstColumnWidth, $cHeight, iconv(self::ICONV_CHARSET_INPUT, self::ICONV_CHARSET_OUTPUT_A, $item['item']), 0, 0, 'L',
                    1);
                if ($item['description']) {
                    $resetX = $this->GetX();
                    $resetY = $this->GetY();
                    $this->SetTextColor(120, 120, 120);
                    $this->SetXY($x, $this->GetY() + 8);
                    $this->SetFont($this->font, '', 7);
                    $this->MultiCell($this->firstColumnWidth, 3, iconv(self::ICONV_CHARSET_INPUT, self::ICONV_CHARSET_OUTPUT_A, $item['description']), 0,
                        'L', 1);
                    //Calculate Height
                    $newY    = $this->GetY();
                    $cHeight = $newY - $resetY + 2;
                    //Make our spacer cell the same height
                    $this->SetXY($x - 1, $resetY);
                    $this->Cell(1, $cHeight, '', 0, 0, 'L', 1);
                    //Draw empty cell
                    $this->SetXY($x, $newY);
                    $this->Cell($this->firstColumnWidth, 2, '', 0, 0, 'L', 1);
                    $this->SetXY($resetX, $resetY);
                }
                $this->SetTextColor(50, 50, 50);
                $this->SetFont($this->font, '', 8);
                $this->Cell($this->columnSpacing, $cHeight, '', 0, 0, 'L', 0);
                $this->Cell($width_other, $cHeight, $item['quantity'], 0, 0, 'C', 1);
                $this->Cell($this->columnSpacing, $cHeight, '', 0, 0, 'L', 0);
                if (isset($this->vatField)) {
                    $this->Cell($this->columnSpacing, $cHeight, '', 0, 0, 'L', 0);
                    if (isset($item['vat'])) {
                        $this->Cell($width_other, $cHeight, iconv(self::ICONV_CHARSET_INPUT, self::ICONV_CHARSET_OUTPUT_B, $item['vat']), 0, 0, 'C', 1);
                    } else {
                        $this->Cell($width_other, $cHeight, '', 0, 0, 'C', 1);
                    }

                }
                $this->Cell($this->columnSpacing, $cHeight, '', 0, 0, 'L', 0);
                $this->Cell($width_other, $cHeight, iconv(self::ICONV_CHARSET_INPUT, self::ICONV_CHARSET_OUTPUT_B,
                    $this->currency . ' ' . number_format($item['price'], 2, $this->referenceformat[0],
                        $this->referenceformat[1])), 0, 0, 'C', 1);
                if (isset($this->discountField)) {
                    $this->Cell($this->columnSpacing, $cHeight, '', 0, 0, 'L', 0);
                    if (isset($item['discount'])) {
                        $this->Cell($width_other, $cHeight, iconv(self::ICONV_CHARSET_INPUT, self::ICONV_CHARSET_OUTPUT_B, $item['discount']), 0, 0,
                            'C', 1);
                    } else {
                        $this->Cell($width_other, $cHeight, '', 0, 0, 'C', 1);
                    }
                }
                $this->Cell($this->columnSpacing, $cHeight, '', 0, 0, 'L', 0);
                $this->Cell($width_other, $cHeight, iconv(self::ICONV_CHARSET_INPUT, self::ICONV_CHARSET_OUTPUT_B,
                    $this->currency . ' ' . number_format($item['total'], 2, $this->referenceformat[0],
                        $this->referenceformat[1])), 0, 0, 'C', 1);
                $this->Ln();
                $this->Ln($this->columnSpacing);
            }
        }
        $badgeX = $this->getX();
        $badgeY = $this->getY();

        //Add totals
        if ($this->totals) {
            foreach ($this->totals as $total) {
                $this->SetTextColor(50, 50, 50);
                $this->SetFillColor($bgcolor, $bgcolor, $bgcolor);
                $this->Cell(1 + $this->firstColumnWidth, $cellHeight, '', 0, 0, 'L', 0);
                for ($i = 0; $i < $this->columns - 3; $i++) {
                    $this->Cell($width_other, $cellHeight, '', 0, 0, 'L', 0);
                    $this->Cell($this->columnSpacing, $cellHeight, '', 0, 0, 'L', 0);
                }
                $this->Cell($this->columnSpacing, $cellHeight, '', 0, 0, 'L', 0);
                if ($total['colored']) {
                    $this->SetTextColor(255, 255, 255);
                    $this->SetFillColor($this->color[0], $this->color[1], $this->color[2]);
                }
                $this->SetFont($this->font, 'b', 8);
                $this->Cell(1, $cellHeight, '', 0, 0, 'L', 1);
                $this->Cell($width_other - 1, $cellHeight, iconv(self::ICONV_CHARSET_INPUT, self::ICONV_CHARSET_OUTPUT_B, $total['name']), 0, 0, 'L',
                    1);
                $this->Cell($this->columnSpacing, $cellHeight, '', 0, 0, 'L', 0);
                $this->SetFont($this->font, 'b', 8);
                $this->SetFillColor($bgcolor, $bgcolor, $bgcolor);
                if ($total['colored']) {
                    $this->SetTextColor(255, 255, 255);
                    $this->SetFillColor($this->color[0], $this->color[1], $this->color[2]);
                }
                $this->Cell($width_other, $cellHeight, iconv(self::ICONV_CHARSET_INPUT, self::ICONV_CHARSET_OUTPUT_B, $total['value']), 0, 0, 'C', 1);
                $this->Ln();
                $this->Ln($this->columnSpacing);
            }
        }
        $this->productsEnded = true;
        $this->Ln();
        $this->Ln(3);


        //Badge
        if ($this->badge) {
            $badge  = ' ' . mb_strtoupper($this->badge, self::ICONV_CHARSET_INPUT) . ' ';
            $resetX = $this->getX();
            $resetY = $this->getY();
            $this->setXY($badgeX, $badgeY + 15);
            $this->SetLineWidth(0.4);
            $this->SetDrawColor($this->badgeColor[0], $this->badgeColor[1], $this->badgeColor[2]);
            $this->setTextColor($this->badgeColor[0], $this->badgeColor[1], $this->badgeColor[2]);
            $this->SetFont($this->font, 'b', 15);
            $this->Rotate(10, $this->getX(), $this->getY());
            $this->Rect($this->GetX(), $this->GetY(), $this->GetStringWidth($badge) + 2, 10);
            $this->Write(10,  iconv(self::ICONV_CHARSET_INPUT, self::ICONV_CHARSET_OUTPUT_B,mb_strtoupper($badge, self::ICONV_CHARSET_INPUT)));
            $this->Rotate(0);
            if ($resetY > $this->getY() + 20) {
                $this->setXY($resetX, $resetY);
            } else {
                $this->Ln(18);
            }
        }

        //Add information
        foreach ($this->addText as $text) {
            if ($text[0] == 'title') {
                $this->SetFont($this->font, 'b', 9);
                $this->SetTextColor(50, 50, 50);
                $this->Cell(0, 10, iconv(self::ICONV_CHARSET_INPUT, self::ICONV_CHARSET_OUTPUT_A, mb_strtoupper($text[1], self::ICONV_CHARSET_INPUT)), 0, 0, 'L', 0);
                $this->Ln();
                $this->SetLineWidth(0.3);
                $this->SetDrawColor($this->color[0], $this->color[1], $this->color[2]);
                $this->Line($this->margins['l'], $this->GetY(), $this->document['w'] - $this->margins['r'],
                    $this->GetY());
                $this->Ln(4);
            }
            if ($text[0] == 'paragraph') {
                $this->SetTextColor(80, 80, 80);
                $this->SetFont($this->font, '', 8);
                $this->MultiCell(0, 4, iconv(self::ICONV_CHARSET_INPUT, self::ICONV_CHARSET_OUTPUT_A, $text[1]), 0, 'L', 0);
                $this->Ln(4);
            }
        }
    }

    public function terms(){
        if($this->addPageTermsColumns == 1){
            $txt = file_get_contents($this->addPageTermsText1);
            $this->SetY(10);
            $this->SetFont($this->font, 'b', 6);
            $this->SetTextColor(50, 50, 50);
            $this->MultiCell(180,2.2,$txt);
        } elseif($this->addPageTermsColumns == 2){
            $txt = file_get_contents($this->addPageTermsText1);
            $this->SetY(10);
            $this->SetFont($this->font, 'b', 6);
            $this->SetTextColor(50, 50, 50);
            $this->MultiCell(90,2.2,$txt);
            // define column 2
            $x = 6+2*50;
            $this->SetLeftMargin($x);
            $this->SetX($x);
            $this->SetY(10);
            $txt = file_get_contents($this->addPageTermsText2);
            $this->SetFont($this->font, 'b', 6);
            $this->SetTextColor(50, 50, 50);
            $this->MultiCell(90,2.3,$txt);
            $this->SetLeftMargin(15);
        }
    }

    public function Footer()
    {
        $this->SetY(-$this->margins['t']);
        $this->SetFont($this->font, '', 8);
        $this->SetTextColor(50, 50, 50);
        $this->Cell(0, 10, $this->footernote, 0, 0, 'L');
        $this->Cell(0, 10, iconv('UTF-8', 'ISO-8859-1', $this->lang['page']) . ' ' . $this->PageNo() . ' ' . $this->lang['page_of'] . ' {nb}', 0, 0,
            'R');
    }

    public function Rotate($angle, $x = -1, $y = -1)
    {
        if ($x == -1) {
            $x = $this->x;
        }
        if ($y == -1) {
            $y = $this->y;
        }
        if ($this->angle != 0) {
            $this->_out('Q');
        }
        $this->angle = $angle;
        if ($angle != 0) {
            $angle *= M_PI / 180;
            $c     = cos($angle);
            $s     = sin($angle);
            $cx    = $x * $this->k;
            $cy    = ($this->h - $y) * $this->k;
            $this->_out(sprintf('q %.5F %.5F %.5F %.5F %.2F %.2F cm 1 0 0 1 %.2F %.2F cm', $c, $s, -$s, $c, $cx, $cy,
                -$cx, -$cy));
        }
    }

    public function _endpage()
    {
        if ($this->angle != 0) {
            $this->angle = 0;
            $this->_out('Q');
        }
        parent::_endpage();
    }

    private function recalculateColumns()
    {
        $this->columns = 4;

        if (isset($this->vatField))
            $this->columns += 1;

        if (isset($this->discountField))
            $this->columns += 1;
    }
}
