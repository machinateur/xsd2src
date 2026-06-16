<?php
/*
 * MIT License
 *
 * Copyright (c) 2021-2026 machinateur
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

declare(strict_types=1);

namespace App\Service;

use App\Model\Data\Content;
use App\TypeMap\TypeMapInterface;
use ArrayObject;

/**
 * A service delegating the call based on argument type.
 */
class DelegateDataService implements DataServiceInterface
{
    /**
     * @var array<class-string<DataServiceInterface>, DataServiceInterface>
     */
    private array $delegates = [];

    /**
     * @var class-string<DataServiceInterface>
     */
    private string $selectedDelegate = XsdDataService::class;

    /**
     * @param iterable<DataServiceInterface> $delegates
     */
    public function __construct(DataServiceInterface... $delegates)
    {
        foreach ($delegates as $delegate) {
            $this->delegates[$delegate::class] = $delegate;
        }
    }

    public function getModel(TypeMapInterface $typeMap, $data, array $options = []): Content
    {
        // Auto-init the currently selected delegate...
        $this->selectDelegate(match ($dataType = \get_debug_type($data)) {
            \DOMDocument::class => XsdDataService::class,
            \ArrayObject::class => PdlDataService::class,
            default => throw new \InvalidArgumentException('Unsupported data type "' . $dataType . '" encountered!'),
        });

        return $this->getSelectedDelegate()
            ->getModel($typeMap, $data, ...$options);
    }

    public function walk(Content $content, TypeMapInterface $typeMap, $data, array $options = []): Content
    {
        return $this->getSelectedDelegate()
            ->walk($content, $typeMap, $data, ...$options);
    }

    public function dump(Content $content, string $view, array $context, string $path, callable $nameFactory, bool $compress = false, ?string $compressName = null): bool
    {
        return $this->getSelectedDelegate()
            ->dump($content, $view, $context, $path, $nameFactory, $compress, $compressName);
    }

    public function getSelectedDelegate(): DataServiceInterface
    {
        return $this->findDelegate($this->selectedDelegate);
    }

    /**
     * @param class-string<DataServiceInterface> $delegateClass
     */
    private function findDelegate(string $delegateClass): DataServiceInterface
    {
        if ( ! isset($this->delegates[$delegateClass])
            || ! \class_exists($delegateClass)
        ) {
            throw new \InvalidArgumentException('Unsupported delegate class "' . $delegateClass . '" encountered!.');
        }

        return $this->delegates[$delegateClass];
    }

    /**
     * @param class-string<DataServiceInterface> $delegateClass
     */
    public function selectDelegate(string $delegateClass): void
    {
        if ( ! isset($this->delegates[$delegateClass])
            || ! \class_exists($delegateClass)
        ) {
            throw new \InvalidArgumentException('Unsupported delegate class "' . $delegateClass . '" encountered!.');
        }

        $this->selectedDelegate = $delegateClass;
    }
}
