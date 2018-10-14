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

    private function getExpectedFileContent(array $dataRows)
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

        $dompdf = new Dompdf();
        $dompdf->loadHtml($htmlToRender);
        $dompdf->setPaper('A4', 'portrait');

        $dompdf->render();
        $pdfContent = $dompdf->output();

        return $pdfContent;
    }

    public function testPutDataRows()
    {
        $dataRows = $this->createDataRows();

        $file = __DIR__.'/Data/destination.pdf';

        $destination = new PDFDestination($file);
        $destination->putDataRows($dataRows);
        $destination->finishMigration();

        $fileContent = file_get_contents($file);

        // Compare similarity, as PDF output is never identical.
        similar_text($this->getExpectedFileContent($dataRows), $fileContent, $percent);

        $this->assertGreaterThanOrEqual(95, $percent,
            'PDF destination\'s output was too different from the expected output.');
    }
}
