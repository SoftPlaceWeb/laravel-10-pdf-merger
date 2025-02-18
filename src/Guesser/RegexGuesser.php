<?php

namespace Softplaceweb\PdfMerger\Guesser;

use \RuntimeException;

/**
 * This file is part of the PDF Version Converter.
 * (c) Thiago Rodrigues <xthiago@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 * Guesser that reads the first 1024 bytes of given PDF file and try find the version with regular expression (regex).
 *
 * @author Thiago Rodrigues <xthiago@gmail.com>
 */
class RegexGuesser implements GuesserInterface
{
    /**
     * @param string $file
     * @return int|string|null
     */
    public function guess($file)
    {
        $version = $this->guessVersion($file);

        if ($version === null) {
            throw new RuntimeException("Can't guess version. The file '{$file}' is a PDF file?");
        }

        return $version;
    }

    /**
     * This implementation is not the best, but doesn't require external modules or libs. For now, works fine for me.
     * Inspired by Sameer Borate's snippet http://www.codediesel.com/php/read-the-version-of-a-pdf-in-php/
     *
     * @param string $filename
     * @return string|null|int
     */
    protected function guessVersion(string $filename)
    {
        $fp = @fopen($filename, 'rb');

        if (!$fp) {
            return 0;
        }

        // Reset file pointer to the start
        fseek($fp, 0);

        // Read 1024 bytes from the start of the PDF
        if ($file = fread($fp, 1024)) {
            preg_match('/%PDF-(\d\.\d)/', $file, $match);

            if (isset($match[1])) {
                return $match[1];
            }
        }

        fclose($fp);

        return null;
    }
}
