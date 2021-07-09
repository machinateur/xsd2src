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

/**
 * Class DumpDirectory
 * @package App\Service\Dump
 */
class DumpDirectory implements DumpInterface
{
    protected string $path;

    protected Closure $nameFactory;

    /**
     * DumpDirectory constructor.
     * @param string $path
     * @param Closure $nameFactory
     */
    public function __construct(string $path, Closure $nameFactory)
    {
        $this->path = $path;
        $this->nameFactory = $nameFactory;
    }

    public function begin(): void
    {
    }

    /**
     * @inheritDoc
     */
    public function dump(Content\Type $type, string $source): bool
    {
        $dump = new DumpFile($this->getNameFactory());
        $dump->begin();
        $result = $dump->dump($type, $source);
        $dump->end();

        return $result;
    }

    public function end(): void
    {
    }

    /**
     * @return Closure
     */
    protected function getNameFactory(): Closure
    {
        return function (Content\Type $type): string {
            return $this->path . '/' . ($this->nameFactory)($type);
        };
    }
}