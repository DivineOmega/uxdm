<?php

namespace RapidWeb\uxdm\Interfaces;


interface SourceInterface 
{
    public function getDataRows($page = 1);
    public function getFields();
}