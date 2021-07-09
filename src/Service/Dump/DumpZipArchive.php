<?php
/*
 * MIT License
 *
 * Copyright (c) 2021-2021 machinateur
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace App\Service\Dump;

use App\Model\Data\Content;
use Closure;
use ZipArchive;

/**
 * Class DumpZipArchive
 * @package App\Service\Dump
 */
class DumpZipArchive implements DumpInterface
{
    protected string $path;

    protected Closure $nameFactory;

    protected string $filename;

    protected ?ZipArchive $archive;

    /**
     * DumpZipArchive constructor.
     * @param string $path
     * @param Closure $nameFactory
     * @param string $filename
     */
    public function __construct(string $path, Closure $nameFactory, string $filename)
    {
        $this->path = $path;
        $this->nameFactory = $nameFactory;
        $this->filename = $filename;
        $this->archive = null;
    }

    public function begin(): void
    {
        $this->archive = new ZipArchive();

        $this->archive->open($this->path . '/' . $this->filename, ZipArchive::CREATE | ZipArchive::OVERWRITE);
    }

    /**
     * @inheritDoc
     */
    public function dump(Content\Type $type, string $source): bool
    {
        $pathname = ($this->getNameFactory())($type);
        $path = dirname($pathname);

        // First ensure the path exists...
        $this->archive->addEmptyDir($path);

        return $this->archive->addFromString($pathname, $source);
    }

    public function end(): void
    {
        $this->archive->close();

        $this->archive = null;
    }

    /**
     * @return Closure
     */
    protected function getNameFactory(): Closure
    {
        return $this->nameFactory;
    }
}
