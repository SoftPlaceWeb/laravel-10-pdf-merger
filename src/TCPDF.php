<?php

namespace Softplaceweb\PdfMerger;

use Illuminate\Support\Facades\Config;
use RuntimeException;

class TCPDF
{
    protected static $format;

    /** @var FpdiTCPDFHelper|TCPDFHelper */
    protected $tcpdf;

    public function __construct()
    {
        $this->init();
    }

    public function __call($method, $args)
    {
        if (method_exists($this->tcpdf, $method)) {
            return call_user_func_array([$this->tcpdf, $method], $args);
        }

        throw new RuntimeException('Method \'' . $method . '\' does not exists in TCPDF');
    }

    public function init(): void
    {
        $class = Config::get('pdf-merger.tcpdf.use_fpdi') ? FpdiTCPDFHelper::class : TCPDFHelper::class;

        $this->tcpdf = new $class(
            Config::get('pdf-merger.tcpdf.page_orientation', 'P'),
            Config::get('pdf-merger.tcpdf.page_units', 'mm'),
            static::$format ?: Config::get('pdf-merger.tcpdf.page_format', 'A4'),
            Config::get('pdf-merger.tcpdf.unicode', true),
            Config::get('pdf-merger.tcpdf.encoding', 'UTF-8'),
            false, // Diskcache is deprecated
            Config::get('pdf-merger.tcpdf.pdfa', false)
        );
    }

    public static function changeFormat($format): void
    {
        static::$format = $format;
    }

    public function setHeaderCallback($headerCallback): void
    {
        $this->tcpdf->setHeaderCallback($headerCallback);
    }

    public function setFooterCallback($footerCallback): void
    {
        $this->tcpdf->setFooterCallback($footerCallback);
    }
}
