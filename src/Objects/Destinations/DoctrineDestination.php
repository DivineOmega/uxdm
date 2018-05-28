<?php

namespace DivineOmega\uxdm\Objects\Destinations;

use DivineOmega\uxdm\Interfaces\DestinationInterface;
use DivineOmega\uxdm\Objects\DataRow;
use Doctrine\ORM\EntityRepository;

class DoctrineDestination implements DestinationInterface
{
    private $entityRepository;

    public function __construct(EntityRepository $entityRepository)
    {
        $this->entityManager = $entityManager;
        $this->entityRepository = $entityRepository;
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
        $newRecord = new $className;

        foreach ($dataItems as $dataItem) {
            $methodName = 'set'.$dataItem->fieldName;
            call_user_func_array([$newRecord, $methodName], [$dataItem->value]);
        }

        $entityManager = $this->entityRepository->getEntityManager();
        $entityManager->persist($newRecord);
        $entityManager->flush();
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
            $methodName = 'set'.$dataItem->fieldName;
            call_user_func_array([$record, $methodName], [$dataItem->value]);
        }

        $entityManager = $this->entityRepository->getEntityManager();
        $entityManager->flush();
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
