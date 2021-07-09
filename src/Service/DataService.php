<?php

namespace App\Service;

use App\Model\Data\Content;
use App\NodeHandle;
use App\Service\Dump\DumpDirectory;
use App\Service\Dump\DumpZipArchive;
use App\Service\Scan\Scan;
use App\Service\Scan\ScanFactory;
use App\TypeMap\TypeMapInterface;
use DOMDocument;
use LogicException;
use RuntimeException;
use Twig\Environment;
use Twig\Error\Error as TwigError;

/**
 * Class DataService
 * @package App\Service
 */
class DataService
{
    private Environment $twig;

    /**
     * @var NodeHandle\NodeHandleInterface[]
     */
    private array $nodeHandleList;

    /**
     * DataService constructor.
     * @param Environment $twig
     */
    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
        $this->nodeHandleList = [];
    }

    /**
     * @param NodeHandle\NodeHandleInterface $nodeHandle
     * @return DataService
     */
    public function addNodeHandle(NodeHandle\NodeHandleInterface $nodeHandle): DataService
    {
        $this->nodeHandleList[$nodeHandle->getIdentifier()] = $nodeHandle;
        return $this;
    }

    /**
     * @param TypeMapInterface $typeMap
     * @param DOMDocument $document
     * @param string $documentElementName
     * @return Content
     */
    public function getModel(TypeMapInterface $typeMap, DOMDocument $document, string $documentElementName = 'schema'): Content
    {
        return $this->walk(new Content(), $typeMap, $document, $documentElementName);
    }

    /**
     * @param Content $content
     * @param TypeMapInterface $typeMap
     * @param DOMDocument $document
     * @param string $documentElementName
     * @return Content
     */
    public function walk(
        Content $content,
        TypeMapInterface $typeMap,
        DOMDocument $document,
        string $documentElementName = 'schema'
    ): Content
    {
        $content = ScanFactory::create()
            ->setSelectMode(Scan::SEL_ANY)
            ->setNodeHandleList(
                $this->nodeHandleList
            )
            ->make()
            ->run($content, $document, $documentElementName);

        $typeMap->process($content);

        return $content;
    }

    /**
     * @param Content $content
     * @param string $view
     * @param array $context
     * @param string $path
     * @param callable $nameFactory
     * @param bool $compress
     * @param string|null $compressName
     * @return bool
     */
    public function dump(Content $content, string $view, array $context, string $path, callable $nameFactory, bool $compress = false, ?string $compressName = null): bool
    {
        $result = true;

        if ($compress) {
            if (null === $compressName) {
                throw new LogicException('The compress name must not be empty, when compress is set to "true".');
            }

            $dump = new DumpZipArchive($path, $nameFactory, $compressName);
        } else {
            $dump = new DumpDirectory($path, $nameFactory);
        }

        $dump->begin();

        $typeList = $content->getTypeList();
        foreach ($typeList as $type) {
            $context['type'] = $type;

            try {
                $source = $this->twig->render($view, $context);
            } catch (TwigError $error) {
                throw new RuntimeException('Error: Twig error "' . get_class($error) . '": "'
                    . $error->getMessage() . '" on type "' . $type->getName() . '".', 0, $error);
            }

            $result = $dump->dump($type, $source) && $result;
        }

        $dump->end();

        return $result;
    }
}
