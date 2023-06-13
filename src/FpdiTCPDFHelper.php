<?php

namespace Softplaceweb\PdfMerger;

use Illuminate\Support\Facades\Config;
use setasign\Fpdi\Tcpdf\Fpdi;

class FpdiTCPDFHelper extends Fpdi
{
    protected $headerCallback;

    protected $footerCallback;

    public function Header(): void
    {
        if ($this->headerCallback != null && is_callable($this->headerCallback)) {
            $cb = $this->headerCallback;
            $cb($this);
        } elseif (Config::get('pdf-merger.tcpdf.use_original_header')) {
            parent::Header();
        }
    }

    public function Footer(): void
    {
        if ($this->footerCallback != null && is_callable($this->footerCallback)) {
            $cb = $this->footerCallback;
            $cb($this);
        } elseif (Config::get('pdf-merger.tcpdf.use_original_footer')) {
            parent::Footer();
        }
    }

    public function setHeaderCallback($callback): void
    {
        $this->headerCallback = $callback;
    }

    public function setFooterCallback($callback): void
    {
        $this->footerCallback = $callback;
    }
}
