<?php

namespace DivineOmega\uxdm\Objects\Destinations;

use DivineOmega\uxdm\Interfaces\DestinationInterface;
use Dompdf\Dompdf;

class PDFDestination implements DestinationInterface
{
    private $file = '';
    private $html = '';
    private $htmlPrefix = '';
    private $htmlSuffix = '';
    private $paperSize = 'A4';
    private $paperOrientation = 'portrait';
    private $rowNum = 0;

    public function __construct($file)
    {
        $this->file = $file;
    }

    public function setHtmlPrefix($htmlPrefix)
    {
        $this->htmlPrefix = $htmlPrefix;
    }

    public function setHtmlSuffix($htmlSuffix)
    {
        $this->htmlSuffix = $htmlSuffix;
    }

    public function setPaperSize($paperSize)
    {
        $this->paperSize = $paperSize;
    }

    public function setPaperOrientation($paperOrientation)
    {
        $this->paperOrientation = $paperOrientation;
    }

    public function putDataRows(array $dataRows)
    {
        foreach ($dataRows as $dataRow) {
            $dataItems = $dataRow->getDataItems();

            if ($this->rowNum === 0) {
                $fieldNames = [];
                foreach ($dataItems as $dataItem) {
                    $fieldNames[] = htmlentities($dataItem->fieldName);
                }
                $this->html .= '<table class="uxdm-table">';
                $this->html .= '<tr class="uxdm-fields"><th class="uxdm-field">';
                $this->html .= implode('</th><th class="uxdm-field">', $fieldNames);
                $this->html .= '</th></tr>';
            }

            $values = [];
            foreach ($dataItems as $dataItem) {
                $values[] = htmlentities($dataItem->value);
            }
            $this->html .= '<tr class="uxdm-values"><td class="uxdm-value">';
            $this->html .= implode('</td><td class="uxdm-value">', $values);
            $this->html .= '</td></tr>';

            $this->rowNum++;
        }
    }

    public function finishMigration()
    {
        $this->html .= '</table>';

        $htmlToRender = $this->htmlPrefix.
            $this->html.
            $this->htmlSuffix;

        $dompdf = new Dompdf();
        $dompdf->loadHtml($htmlToRender);
        $dompdf->setPaper($this->paperSize, $this->paperOrientation);

        $dompdf->render();
        $pdfContent = $dompdf->output();

        file_put_contents($this->file, $pdfContent);

    }
}
