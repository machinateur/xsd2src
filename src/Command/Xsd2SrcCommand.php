<?php

namespace App\Command;

use App\Model\Data\Content;
use App\Model\File;
use App\Model\FileInterface;
use App\Service\DataService;
use App\TypeMap\TypeMap;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Twig\Environment;
use function Symfony\Component\String\u;

/**
 * Class XsdCommand
 * @package App\Command
 */
class Xsd2SrcCommand extends Command
{
    /**
     * @var string
     */
    private const CTX_PRIMITIVE_TYPE_LIST = 'primitive_type_list';

    /**
     * @var string
     */
    private const CTX_SIMPLE_TYPE_MAP = 'simple_type_map';

    protected static $defaultName = 'xsd2src';

    private Environment $twig;

    private DataService $service;

    /**
     * Xsd2SrcCommand constructor.
     * @param Environment $twig
     * @param DataService $service
     */
    public function __construct(Environment $twig, DataService $service)
    {
        parent::__construct();
        $this->twig = $twig;
        $this->service = $service;
    }

    /**
     * @inheritDoc
     */
    protected function configure(): void
    {
        $this
            ->setDescription('Create source from xsd.')
            ->addArgument('input', InputArgument::REQUIRED,
                'Set the input pathname.',
                null
            )
            ->addArgument('output', InputArgument::REQUIRED,
                'Set the output path.',
                null
            )
            ->addArgument('extension', InputArgument::REQUIRED,
                'Set the file extension.',
                null
            )
            ->addArgument('context', InputArgument::REQUIRED,
                'Set the context pathname.',
                null
            )
            ->addOption('initialize', 'i', InputOption::VALUE_NEGATABLE,
                'Decide whether to initialize configuration or not. Default to "false".',
                false
            )
            ->addOption('re', 'r', InputOption::VALUE_NONE,
                'Set to re-initialize the existing configuration. Default to "false".',
                null
            )
            ->addArgument('view', InputArgument::OPTIONAL,
                'Set the twig view to use. Default to "xsd2src.{extension}.twig".',
                null
            )
            ->addOption('with', 'x', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                'Add one or more extra xsd.',
                []
            )
            ->addOption('zip', 'z', InputOption::VALUE_REQUIRED,
                'Decide whether to compress the output as archive or not. Default to "false".',
                null
            );
    }

    private InputInterface $input;
    private OutputInterface $output;

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('xsd2src, Copyright (c) 2021-' . date('Y') . ' machinateur');
        $output->writeln(' "Hello, thanks for using xsd2src!"');
        $output->writeln('  https://github.com/machinateur/xsd2src');
        $output->writeln('');

        ////////////////////////////////////////////////////////////////////////////////////////////////////////

        $output->writeln('> Check...');

        /** @var string $argumentInput */
        if (!$this->hasInput($argumentInput = $input->getArgument('input'))) {
            $output->writeln('! Argument "input" ("' . $argumentInput . '") not found.');

            return Command::FAILURE;
        }

        /** @var string $argumentOutput */
        if (!$this->hasOutput($argumentOutput = $input->getArgument('output'))) {
            $output->writeln('! Argument "output" ("' . $argumentOutput . '") not found.');

            return Command::FAILURE;
        }

        /** @var string $argumentExtension */
        if (!$this->hasExtension($argumentExtension = $input->getArgument('extension'))) {
            $output->writeln('! Argument "extension" ("' . $argumentExtension . '") not found.');

            return Command::FAILURE;
        }

        /** @var bool $optionInitialize */
        $optionInitialize = $input->getOption('initialize');
        /** @var bool $optionRe */
        $optionRe = $input->getOption('re');

        /** @var string $argumentContext */
        if (!$this->hasContext($argumentContext = $input->getArgument('context'))) {
            $output->writeln('! Argument "context" ("' . $argumentContext . '") not found.');

            if (!str_ends_with($argumentContext, '.php')) {
                $output->writeln('! Extension of argument "context" ("' . $argumentContext . '") is not ".php".');

                $optionInitialize = false;
            }

            if (!$optionInitialize) {
                return Command::FAILURE;
            }

            $output->writeln('! Option "initialize" (true) found.');
        } elseif ($optionInitialize = $optionRe) {
            $output->writeln('! Option "re" (true) found.');
        }

        /** @var string $argumentView */
        if (!$this->hasView($argumentView = $input->getArgument('view') ?? $argumentExtension)) {
            $output->writeln('! Argument "view" ("' . $argumentView . '") not found.');

            if (!str_starts_with($argumentView, 'xsd2src.')) {
                $argumentView = 'xsd2src.' . $argumentView;

                $output->writeln('! Add "xsd2src." prefix ("' . $argumentView . '")...');
            }

            if (!str_ends_with($argumentView, '.twig')) {
                $argumentView = $argumentView . '.twig';

                $output->writeln('! Add ".twig" suffix ("' . $argumentView . '")...');
            }

            if (!$this->hasView($argumentView)) {
                $output->writeln('! Argument "view" ("' . $argumentView . '") not found.');

                return Command::FAILURE;
            }
        }

        $optionZip = $input->getOption('zip');
        if (null !== $optionZip && !str_ends_with($optionZip, '.zip')) {
            $optionZip = $optionZip . '.zip';

            $output->writeln('! Add ".zip" suffix ("' . $optionZip . '")...');
        }

        $this->input = $input;
        $this->output = $output;

        /** @var string[] $optionWith */
        if (!$this->hasWith($optionWith = $input->getOption('with'))) {
            $output->writeln('! Option "with" (some) not found.');

            return Command::FAILURE;
        }

        $output->writeln('^ Done!');

        if ($optionInitialize) {
            $context = require __DIR__ . '/../../config/configuration.php';
        } else {
            $context = require $argumentContext;
        }

        $typeMap = new TypeMap($context[self::CTX_SIMPLE_TYPE_MAP] ?? [], 'string');

        $primitiveTypeList = $context[self::CTX_PRIMITIVE_TYPE_LIST] ?? [];
        foreach ($primitiveTypeList as $primitiveType) {
            $typeMap->entry($primitiveType, $primitiveType);
        }

        try {
            $output->write('> Parse "' . $argumentInput . '"...');

            $content = $this->service->getModel($typeMap,
                $this->getFile($argumentInput)
                    ->getDocument()
            );

            $output->writeln(' done!');

            foreach ($optionWith as $optionWithValue) {
                $output->write('> Parse "' . $optionWithValue . '"...');
                $this->service->walk($content, $typeMap,
                    $this->getFile($optionWithValue)
                        ->getDocument()
                );
                $output->writeln(' done!');
            }

            // Warn about unresolved types.
            $output->writeln('> Analyse type map...');

            $warningList = $typeMap->getWarningList($content);
            foreach ($warningList as $warning) {
                $output->writeln('! Warning: ' . $warning);
            }

            $output->writeln('^ Done!');

            if ($optionInitialize) {
                $output->write('> Dump context...');
                $this->dumpContext($content, $context, $argumentContext);
            } else {
                $output->write('> Dump...');
                $this->dump($content, $context, $argumentView, $argumentOutput, $argumentExtension,
                    null !== $optionZip, $optionZip);
            }

            $output->writeln(' done!');
        } catch (RuntimeException $exception) {
            $output->writeln(' error!');
            // TODO: Fix up all exception messages with proper in-message context!
            $output->writeln('! ' . $exception->getMessage());
        }

        return Command::SUCCESS;
    }

    /**
     * @param string $argumentInput
     * @return bool
     */
    private function hasInput(string $argumentInput): bool
    {
        return file_exists($argumentInput) && is_file($argumentInput);
    }

    /**
     * @param string $argumentOutput
     * @return bool
     */
    private function hasOutput(string $argumentOutput): bool
    {
        return file_exists($argumentOutput) && is_dir($argumentOutput);
    }

    /**
     * @param string $argumentExtension
     * @return bool
     */
    private function hasExtension(string $argumentExtension): bool
    {
        return 0 !== strlen($argumentExtension) && 1 === preg_match('/^[a-z]+$/', $argumentExtension);
    }

    /**
     * @param string $argumentConfiguration
     * @return bool
     */
    private function hasContext(string $argumentConfiguration): bool
    {
        return file_exists($argumentConfiguration) && is_file($argumentConfiguration)
            && str_ends_with($argumentConfiguration, '.php');
    }

    /**
     * @param string $argumentView
     * @return bool
     */
    private function hasView(string $argumentView): bool
    {
        return $this->twig->getLoader()
            ->exists($argumentView);
    }

    /**
     * @param array $optionWith
     * @return bool
     */
    private function hasWith(array $optionWith): bool
    {
        $has = true;

        foreach ($optionWith as $optionWithValue) {
            if (!$this->hasInput($optionWithValue)) {
                $has = false;

                $this->output->writeln('! Option "with" ("' . $optionWithValue . '") not found...');
            }
        }

        return $has;
    }

    /**
     * @param string $pathname
     * @return FileInterface
     */
    private function getFile(string $pathname): FileInterface
    {
        return new File($pathname);
    }

    /**
     * @param Content $content
     * @param array $context
     * @param string $argumentContext
     */
    private function dumpContext(Content $content, array &$context, string $argumentContext): void
    {
        // TODO: Maybe implement smart type provisioning.

        $source = var_export($context, true);
        $source = <<<PHP
<?php

return {$source};

PHP;

        $this->make($source, $argumentContext);
    }

    /**
     * @param string $source
     * @param string $pathname
     */
    private function make(string $source, string $pathname): void
    {
        $result = file_put_contents($pathname, $source);

        if (false === $result) {
            throw new RuntimeException('Error: No access to pathname "' . $pathname . '".');
        }
    }

    /**
     * @param Content $content
     * @param array $context
     * @param string $argumentView
     * @param string $argumentOutput
     * @param string $argumentExtension
     * @param bool $compress
     * @param string|null $compressName
     */
    private function dump(Content $content, array $context, string $argumentView, string $argumentOutput, string $argumentExtension, bool $compress = false, ?string $compressName = null): void
    {
        $result = $this->service->dump($content, $argumentView, $context, $argumentOutput,
            function (Content\Type $type) use ($argumentOutput, $argumentExtension): string {
                return u($type->getName())->camel()->title() . '.' . $argumentExtension;
            },
            $compress, $compressName
        );

        if (false === $result) {
            throw new RuntimeException('Error: No dump result.');
        }
    }
}
