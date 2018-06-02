<?php

namespace DivineOmega\uxdm\Objects\Destinations;

use DivineOmega\uxdm\Interfaces\DestinationInterface;
use DivineOmega\uxdm\Objects\DataRow;
use Doctrine\ORM\EntityManager;
use Symfony\Component\PropertyAccess\PropertyAccess;

class DoctrineDestination implements DestinationInterface
{
    private $entityManager;
    private $propertyAccessor;

    public function __construct(EntityManager $entityManager, $entityClassName)
    {
        $this->entityManager = $entityManager;
        $this->entityRepository = $this->entityManager->getRepository($entityClassName);
        $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
    }

    private function alreadyExists(array $keyDataItems)
    {
        $criteria = [];

        foreach ($keyDataItems as $keyDataItem) {
            $criteria[$keyDataItem->fieldName] = $keyDataItem->value;
        }

        return $this->entityRepository->count($criteria) > 0;
    }

    private function insertDataRow(DataRow $dataRow)
    {
        $dataItems = $dataRow->getDataItems();

        $className = $this->entityRepository->getClassName();
        $newRecord = new $className();

        foreach ($dataItems as $dataItem) {
            $this->propertyAccessor->setValue($newRecord, $dataItem->fieldName, $dataItem->value);
        }

        $this->entityManager->persist($newRecord);
        $this->entityManager->flush();
    }

    private function updateDataRow(DataRow $dataRow)
    {
        $dataItems = $dataRow->getDataItems();
        $keyDataItems = $dataRow->getKeyDataItems();

        $criteria = [];

        foreach ($keyDataItems as $keyDataItem) {
            $criteria[$keyDataItem->fieldName] = $keyDataItem->value;
        }

        $record = $this->entityRepository->findOneBy($criteria);

        foreach ($dataItems as $dataItem) {
            $this->propertyAccessor->setValue($record, $dataItem->fieldName, $dataItem->value);
        }

        $this->entityManager->flush();
    }

    public function putDataRows(array $dataRows)
    {
        foreach ($dataRows as $dataRow) {
            $keyDataItems = $dataRow->getKeyDataItems();

            if (!$keyDataItems) {
                $this->insertDataRow($dataRow);
                continue;
            }

            if ($this->alreadyExists($keyDataItems)) {
                $this->updateDataRow($dataRow);
            } else {
                $this->insertDataRow($dataRow);
            }
        }
    }
}
