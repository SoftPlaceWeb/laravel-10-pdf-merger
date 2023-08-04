<?php

namespace Softplaceweb\PdfMerger\Tests\Feature;

use AntonAm\PDFVersionConverter\Guesser\RegexGuesser;
use Softplaceweb\PdfMerger\Facades\PdfMerger;
use Softplaceweb\PdfMerger\Tests\TestCase;
use Symfony\Component\Filesystem\Filesystem;

class MergeTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $filesystem = new Filesystem();

        $filesystem->copy(__DIR__.'/../test_files/testPDF_Original_Version.4.x.pdf', __DIR__.'/../test_files/testPDF_Version.4.x.pdf', true);
        $filesystem->copy(__DIR__.'/../test_files/testPDF_Original_Version.5.x.pdf', __DIR__.'/../test_files/testPDF_Version.5.x.pdf', true);
        $filesystem->copy(__DIR__.'/../test_files/testPDF_Original_Version.6.x.pdf', __DIR__.'/../test_files/testPDF_Version.6.x.pdf', true);
        $filesystem->copy(__DIR__.'/../test_files/testPDF_Original_Version.7.x.pdf', __DIR__.'/../test_files/testPDF_Version.7.x.pdf', true);
        $filesystem->copy(__DIR__.'/../test_files/testPDF_Original_Version.8.x.pdf', __DIR__.'/../test_files/testPDF_Version.8.x.pdf', true);
    }

    /**
     * @test
     *
     * @covers
     */
    public function it_can_merge_some_4_and_5_pdf_versions()
    {
        $PDFMerger = PdfMerger::init();
        $guesser = new RegexGuesser();

        $pdfVersion4File = __DIR__.'/../test_files/testPDF_Version.4.x.pdf';
        $this->assertTrue($guesser->guess($pdfVersion4File) === '1.3');
        $pdfVersion5File = __DIR__.'/../test_files/testPDF_Version.5.x.pdf';
        $this->assertTrue($guesser->guess($pdfVersion5File) === '1.4');

        $PDFMerger->addPDF($pdfVersion4File);
        $PDFMerger->addPDF($pdfVersion5File);


        $this->assertTrue((bool)preg_match("/^%PDF-1.7/", $PDFMerger->merge()->save('merged_file.pdf', 'string')));
    }

    /**
     * @test
     *
     * @covers
     */
    public function it_can_merge_some_pdf_versions()
    {
        $PDFMerger = PdfMerger::init();
        $guesser = new RegexGuesser();

        $pdfVersion4File = __DIR__.'/../test_files/testPDF_Version.4.x.pdf';
        $this->assertTrue($guesser->guess($pdfVersion4File) === '1.3');
        $pdfVersion5File = __DIR__.'/../test_files/testPDF_Version.5.x.pdf';
        $this->assertTrue($guesser->guess($pdfVersion5File) === '1.4');
        $pdfVersion6File = __DIR__.'/../test_files/testPDF_Version.6.x.pdf';
        $this->assertTrue($guesser->guess($pdfVersion6File) === '1.5');
        $pdfVersion7File = __DIR__.'/../test_files/testPDF_Version.7.x.pdf';
        $this->assertTrue($guesser->guess($pdfVersion7File) === '1.6');
        $pdfVersion8File = __DIR__.'/../test_files/testPDF_Version.8.x.pdf';
        $this->assertTrue($guesser->guess($pdfVersion8File) === '1.7');


        $PDFMerger->addPDF($pdfVersion4File);
        $PDFMerger->addPDF($pdfVersion5File);
        $PDFMerger->addPDF($pdfVersion6File);
        $PDFMerger->addPDF($pdfVersion7File);
        $PDFMerger->addPDF($pdfVersion8File);

        $this->assertTrue((bool)preg_match("/^%PDF-1.7/", $PDFMerger->merge()->save('merged_file.pdf', 'string')));

        $this->assertTrue($guesser->guess($pdfVersion4File) === '1.3');
        $pdfVersion5File = __DIR__.'/../test_files/testPDF_Version.5.x.pdf';
        $this->assertTrue($guesser->guess($pdfVersion5File) === '1.4');
        $pdfVersion6File = __DIR__.'/../test_files/testPDF_Version.6.x.pdf';
        $this->assertTrue($guesser->guess($pdfVersion6File) === '1.4');
        $pdfVersion7File = __DIR__.'/../test_files/testPDF_Version.7.x.pdf';
        $this->assertTrue($guesser->guess($pdfVersion7File) === '1.4');
        $pdfVersion8File = __DIR__.'/../test_files/testPDF_Version.8.x.pdf';
        $this->assertTrue($guesser->guess($pdfVersion8File) === '1.4');
    }
}
