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
use LogicException;

/**
 * Class DumpStream
 * @package App\Service\Dump
 */
class DumpStream implements DumpInterface
{
    /**
     * @var resource
     */
    protected $stream;

    /**
     * DumpStream constructor.
     * @param resource $stream
     */
    public function __construct(/*resource*/ $stream)
    {
        $this->stream = $stream;
    }

    public function begin(): void
    {
        if (!is_resource($this->stream)) {
            $metaData = stream_get_meta_data($this->stream);

            if (!in_array($metaData['mode'], [
                'w',
            ])) {
                throw new LogicException('The stream must be a resource and writable.');
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function dump(Content\Type $type, string $source): bool
    {
        return false !== fwrite($this->stream, $source, strlen($source));
    }

    public function end(): void
    {
        rewind($this->stream);
    }
}
