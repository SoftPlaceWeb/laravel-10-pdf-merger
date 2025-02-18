<?php

namespace Softplaceweb\PdfMerger\Converter;

use Symfony\Component\Process\Process;

/**
 * This file is part of the PDF Version Converter.
 * (c) Thiago Rodrigues <xthiago@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 * Encapsulates the knowledge about gs command.
 *
 * @author Thiago Rodrigues <xthiago@gmail.com>
 */
class GhostscriptConverterCommand
{
    private $baseCommand = 'gs -sDEVICE=pdfwrite -dCompatibilityLevel=%s -dPDFSETTINGS=/screen -dNOPAUSE -dQUIET -dBATCH -dColorConversionStrategy=/LeaveColorUnchanged -dEncodeColorImages=false -dEncodeGrayImages=false -dEncodeMonoImages=false -dDownsampleMonoImages=false -dDownsampleGrayImages=false -dDownsampleColorImages=false -dAutoFilterColorImages=false -dAutoFilterGrayImages=false -dColorImageFilter=/FlateEncode -dGrayImageFilter=/FlateEncode';

    public function __construct($command = null)
    {
        if (null !== $command) {
            $this->baseCommand = $command;
        }

        $this->baseCommand .= ' -o %s $SOURCE_PDF';
    }

    public function run($originalFile, $newFile, $newVersion)
    {
        $command = sprintf($this->baseCommand, $newVersion, $newFile);

        $process = Process::fromShellCommandline($command);
        $process->run(null, ['SOURCE_PDF' => $originalFile]);

        if (!$process->isSuccessful()) {
            throw new \RuntimeException($process->getErrorOutput());
        }
    }
}
