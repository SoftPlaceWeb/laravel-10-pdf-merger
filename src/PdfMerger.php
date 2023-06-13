<?php

namespace Oriceon\PdfMerger;

use RuntimeException;

class PdfMerger
{
    protected $app;

    private array $_files = []; // ['form.pdf']  ["1,2,4, 5-19"]

    private $_tcpdf;

    public function __construct($app)
    {
        $this->app = $app;

        $this->init();
    }

    public function init(): static
    {
        $this->_files = [];
        $this->_tcpdf = new TCPDF();

        return $this;
    }

    /**
     * Add a PDF for inclusion in the merge with a valid file path. Pages should be formatted: 1,3,6, 12-16.
     */
    public function addPDF(mixed $filePath, mixed $pages = 'all', string $orientation = null): PdfMerger
    {
        if (is_array($filePath)) {
            foreach ($filePath as $file) {
                $this->addPDF($file['filePath'], $file['pages'] ?? 'all', $file['orientation'] ?? null);
            }
        } elseif (file_exists($filePath)) {
            if (strtolower($pages) !== 'all') {
                $pages = $this->_rewritePages($pages);
            }

            $this->_files[] = [$filePath, $pages, $orientation];
        } else {
            throw new RuntimeException('Could not locate PDF on ' . $filePath);
        }

        return $this;
    }

    /**
     * Merges your provided PDFs and outputs to specified location.
     *
     * @throws RuntimeException if there are no PDFs to merge
     */
    public function merge(string $orientation = null, array $meta = []): PdfMerger
    {
        $this->_doMerge($orientation, $meta);

        return $this;
    }

    /**
     * Merges your provided PDFs and adds blank pages between documents as needed to allow duplex printing.
     *
     * @throws RuntimeException if there are no PDFs to merge
     */
    public function duplexMerge(string $orientation = null, array $meta = []): PdfMerger
    {
        $this->_doMerge($orientation, $meta, true);

        return $this;
    }

    /**
     * @throws RuntimeException
     */
    public function save(string $outputPath = 'newfile.pdf', string $outputMode = 'file'): string|bool
    {
        // output operations
        $mode = $this->_switchMode($outputMode);

        if ($mode === 'S') {
            return $this->_tcpdf->Output($outputPath, 'S');
        }

        if ($this->_tcpdf->Output($outputPath, $mode) == '') {
            return true;
        }

        throw new RuntimeException('Error outputting PDF to ' . $outputMode . '.');
    }

    /**
     * Set your meta data in merged pdf.
     *
     * @param array $meta [title => $title, author => $author, subject => $subject, keywords => $keywords, creator => $creator]
     */
    protected function setMeta(array $meta = []): void
    {
        foreach ($meta as $key => $arg) {
            $metodName = 'set' . ucfirst($key);

            if (method_exists($this->_tcpdf, $metodName)) {
                $this->_tcpdf->{$metodName}($arg);
            }
        }
    }

    /**
     * Merges your provided PDFs and outputs to specified location.
     *
     * @param array $meta   [title => $title, author => $author, subject => $subject, keywords => $keywords, creator => $creator]
     * @param bool  $duplex merge with
     *
     * @throws RuntimeException
     *
     * @array $meta [title => $title, author => $author, subject => $subject, keywords => $keywords, creator => $creator]
     */
    private function _doMerge(string $orientation = null, array $meta = [], bool $duplex = false): void
    {
        if (count($this->_files) === 0) {
            throw new RuntimeException('No PDFs to merge.');
        }

        // setting the meta tags
        $this->setMeta($meta);

        // merger operations
        foreach ($this->_files as $file) {
            [$filePath, $filePages, $fileOrientation] = $file;

            if (is_null($fileOrientation)) {
                $fileOrientation = $orientation;
            }

            $size  = [];
            $count = $this->_tcpdf->setSourceFile($filePath);

            // add pages
            if ($filePages == 'all') {
                for ($i = 1; $i <= $count; ++$i) {
                    $template = $this->_tcpdf->importPage($i);
                    $size     = $this->_tcpdf->getTemplateSize($template);

                    if ($orientation == null) {
                        $fileOrientation = $size['width'] < $size['height'] ? 'P' : 'L';
                    }

                    $this->_tcpdf->AddPage($fileOrientation, [$size['width'], $size['height']]);
                    $this->_tcpdf->useTemplate($template);
                }
            } else {
                foreach ($filePages as $page) {
                    if ( ! $template = $this->_tcpdf->importPage($page)) {
                        throw new RuntimeException('Could not load page \'' . $page . '\' in PDF \'' . $filePath . '\'. Check that the page exists.');
                    }

                    $size = $this->_tcpdf->getTemplateSize($template);

                    if ($orientation == null) {
                        $fileOrientation = $size['width'] < $size['height'] ? 'P' : 'L';
                    }

                    $this->_tcpdf->AddPage($fileOrientation, [$size['width'], $size['height']]);
                    $this->_tcpdf->useTemplate($template);
                }
            }

            if ($duplex && $this->_tcpdf->PageNo() % 2) {
                $this->_tcpdf->AddPage($fileOrientation, [$size['width'], $size['height']]);
            }
        }
    }

    /**
     * FPDI uses single characters for specifying the output location. Change our more descriptive string into proper format.
     */
    private function _switchMode(string $mode): string
    {
        // to keep php 7 support, I have to use switch instead new php8 match...
        switch (strtolower($mode)) {
            case 'download':
                return 'D';

                break;
            case 'file':
                return 'F';

                break;
            case 'string':
                return 'S';

                break;
            default:
                return 'I';

                break;
        }
    }

    /**
     * Takes our provided pages in the form of 1,3,4,16-50 and creates an array of all pages.
     *
     * @throws RuntimeException
     */
    private function _rewritePages(string $pages): array
    {
        $newpages = [];

        $pages = str_replace(' ', '', $pages);
        $part  = explode(',', $pages);

        // parse hyphens
        foreach ($part as $i) {
            $ind = explode('-', trim($i));

            if (count($ind) == 2) {
                [$startPage, $endPage] = $ind;

                if ($startPage > $endPage) {
                    throw new RuntimeException('Starting page, \'' . $startPage . '\' is greater than ending page \'' . $endPage . '\'.');
                }

                // add middle pages
                while ($startPage <= $endPage) {
                    $newpages[] = (int) $startPage;
                    ++$startPage;
                }
            } else {
                $newpages[] = (int) $ind[0];
            }
        }

        return $newpages;
    }
}
