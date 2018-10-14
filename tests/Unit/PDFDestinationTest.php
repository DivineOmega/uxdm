<?php

use DivineOmega\uxdm\Objects\DataItem;
use DivineOmega\uxdm\Objects\DataRow;
use DivineOmega\uxdm\Objects\Destinations\PDFDestination;
use Dompdf\Dompdf;
use PHPUnit\Framework\TestCase;

final class PDFDestinationTest extends TestCase
{
    private function createDataRows()
    {
        $faker = Faker\Factory::create();

        $dataRows = [];

        $dataRow = new DataRow();
        $dataRow->addDataItem(new DataItem('name', $faker->word));
        $dataRow->addDataItem(new DataItem('value', $faker->randomNumber));
        $dataRows[] = $dataRow;

        $dataRow = new DataRow();
        $dataRow->addDataItem(new DataItem('name', $faker->word));
        $dataRow->addDataItem(new DataItem('value', $faker->randomNumber));
        $dataRows[] = $dataRow;

        return $dataRows;
    }

    private function getExpectedFileContent(array $dataRows, $paperSize, $paperOrientation, $htmlPrefix = '', $htmlSuffix = '')
    {
        $htmlToRender = '<table class="uxdm-table"><tr class="uxdm-fields"><th class="uxdm-field">name</th><th class="uxdm-field">value</th></tr>';

        foreach ($dataRows as $dataRow) {
            $htmlToRender .= '<tr class="uxdm-values"><td class="uxdm-value">';
            $htmlToRender .= $dataRow->getDataItemByFieldName('name')->value;
            $htmlToRender .= '</td><td class="uxdm-value">';
            $htmlToRender .= $dataRow->getDataItemByFieldName('value')->value;
            $htmlToRender .= '</td></tr>';
        }

        $htmlToRender .= '</table>';

        $htmlToRender = $htmlPrefix.
            $htmlToRender.
            $htmlSuffix;

        $dompdf = new Dompdf();
        $dompdf->loadHtml($htmlToRender);
        $dompdf->setPaper($paperSize, $paperOrientation);

        $dompdf->render();
        $pdfContent = $dompdf->output();

        return $pdfContent;
    }

    public function testPutDataRowsDefaultPaper()
    {
        $paperSize = 'A4';
        $paperOrientation = 'portrait';

        $dataRows = $this->createDataRows();

        $file = __DIR__.'/Data/destination.pdf';

        $destination = new PDFDestination($file);
        $destination->putDataRows($dataRows);
        $destination->finishMigration();

        $fileContent = file_get_contents($file);

        // Compare similarity, as PDF output is never identical.
        $expectedFileContent = $this->getExpectedFileContent($dataRows, $paperSize, $paperOrientation);
        similar_text($expectedFileContent, $fileContent, $percent);

        $this->assertGreaterThanOrEqual(92, $percent,
            'PDF destination\'s output was too different from the expected output.');
    }

    public function testPutDataRowsCustomPaper()
    {
        $paperSize = 'A5';
        $paperOrientation = 'landscape';

        $dataRows = $this->createDataRows();

        $file = __DIR__.'/Data/destination.pdf';

        $destination = new PDFDestination($file);
        $destination->setPaperSize($paperSize);
        $destination->setPaperOrientation($paperOrientation);
        $destination->putDataRows($dataRows);
        $destination->finishMigration();

        $fileContent = file_get_contents($file);

        // Compare similarity, as PDF output is never identical.
        $expectedFileContent = $this->getExpectedFileContent($dataRows, $paperSize, $paperOrientation);
        similar_text($expectedFileContent, $fileContent, $percent);

        $this->assertGreaterThanOrEqual(92, $percent,
            'PDF destination\'s output was too different from the expected output.');
    }

    public function testPutDataRowsPrefixAndSuffix()
    {
        $paperSize = 'A4';
        $paperOrientation = 'portrait';
        $htmlPrefix = '<h1>My Report</h1>
            <style>
                table { width: 100% }
                h1 { text-align: center; }
                th { text-transform: capitalize; text-align: center; } 
                th, td { margin: 0; border: 1px solid #000; }
            </style>';
        $htmlSuffix = '<p>Created by UXDM</p>';

        $dataRows = $this->createDataRows();

        $file = __DIR__.'/Data/destination.pdf';

        $destination = new PDFDestination($file);
        $destination->setHtmlPrefix($htmlPrefix);
        $destination->setHtmlSuffix($htmlSuffix);
        $destination->putDataRows($dataRows);
        $destination->finishMigration();

        $fileContent = file_get_contents($file);

        // Compare similarity, as PDF output is never identical.
        $expectedFileContent = $this->getExpectedFileContent($dataRows, $paperSize, $paperOrientation,
            $htmlPrefix, $htmlSuffix);
        similar_text($expectedFileContent, $fileContent, $percent);

        $this->assertGreaterThanOrEqual(95, $percent,
            'PDF destination\'s output was too different from the expected output.');
    }
}
