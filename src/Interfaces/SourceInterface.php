<?php

namespace RapidWeb\uxdm\Interfaces;


interface SourceInterface 
{
    public function getDataRows($page);
    public function getFields();
}