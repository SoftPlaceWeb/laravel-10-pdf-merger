<?php

namespace Softplaceweb\PdfMerger;

use Illuminate\Support\Facades\Config;

class TCPDFHelper extends \TCPDF
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

    public function addTOC($page = '', $numbersfont = '', $filler = '.', $toc_name = 'TOC', $style = '', $color = [0, 0, 0]): void
    {
        // sort bookmarks before generating the TOC
        $this->sortBookmarks();

        parent::addTOC($page, $numbersfont, $filler, $toc_name, $style, $color);
    }

    public function addHTMLTOC($page = '', $toc_name = 'TOC', $templates = [], $correct_align = true, $style = '', $color = [0, 0, 0]): void
    {
        // sort bookmarks before generating the TOC
        $this->sortBookmarks();

        parent::addHTMLTOC($page, $toc_name, $templates, $correct_align, $style, $color);
    }
}
