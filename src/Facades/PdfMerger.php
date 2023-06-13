<?php

namespace Softplaceweb\PdfMerger\Facades;

use Illuminate\Support\Facades\Facade;

class PdfMerger extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'PdfMerger';
    }
}
