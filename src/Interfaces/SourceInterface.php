<?php

namespace RapidWeb\uxdm\Interfaces;

interface SourceInterface
{
    public function getDataRows($page = 1, $fieldsToRetrieve = []);

    public function countDataRows();

    public function getFields();
}
