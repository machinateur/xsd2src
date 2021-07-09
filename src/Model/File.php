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

namespace App\Model;

use DOMDocument;
use LogicException;

/**
 * Class File
 * @package App\Model
 */
class File implements FileInterface
{
    private string $pathname;

    /**
     * XsdFile constructor.
     * @param string $pathname
     */
    public function __construct(string $pathname)
    {
        $this->pathname = $pathname;
    }

    /**
     * @return string
     */
    public function getPathname(): string
    {
        return $this->pathname;
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * @inheritDoc
     */
    public function getDocument(): DOMDocument
    {
        $this->validate();

        $pathname = $this->getPathname();

        $document = new DOMDocument('1.0', 'utf-8');
        if (!$document->load($pathname)) {
            throw new LogicException('The pathname must be a valid xsd file.');
        }

        return $document;
    }

    protected function validate(): void
    {
        $pathname = $this->getPathname();

        if (!(file_exists($pathname) && is_file($pathname))) {
            throw new LogicException('The pathname must be a file.');
        }
    }
}
