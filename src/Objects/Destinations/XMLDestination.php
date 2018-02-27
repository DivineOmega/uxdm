<?php

namespace RapidWeb\uxdm\Objects\Destinations;

use DOMDocument;
use DOMElement;
use RapidWeb\uxdm\Interfaces\DestinationInterface;

class XMLDestination implements DestinationInterface
{
    private $file;
    private $domDoc;
    private $domNode;

    public function __construct($file, DOMDocument $domDoc, DOMElement $rootElement, $rowElementName = 'row')
    {
        $this->file = $file;
        $this->domDoc = $domDoc;
        $this->rootElement = $rootElement;
        $this->rowElementName = $rowElementName;
    }

    public function putDataRows(array $dataRows)
    {
        foreach ($dataRows as $dataRow) {
            $dataItems = $dataRow->getDataItems();

            $dataRowDomElement = $this->rootElement->appendChild(new DOMElement($this->rowElementName));

            foreach ($dataItems as $dataItem) {
                $dataItemDomElement = new DOMElement($dataItem->fieldName, $dataItem->value);
                $dataRowDomElement->appendChild($dataItemDomElement);
            }
        }

        file_put_contents($this->file, $this->domDoc->saveXML());
    }
}
