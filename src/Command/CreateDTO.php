<?php

declare(strict_types=1);

namespace SoftFineWare\OpenapiGenerator\Command;

use SoftFineWare\OpenapiGenerator\Deffenition\ClassDefinitionData;
use SoftFineWare\OpenapiGenerator\NamespaceHelper;
use SoftFineWare\OpenapiGenerator\OpenApi\Reader;
use RuntimeException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Exception\LogicException;
use JsonException;
use SoftFineWare\OpenapiGenerator\FileClassGenerator;
use SoftFineWare\OpenapiGenerator\OpenApi\Crawler;
use SoftFineWare\OpenapiGenerator\FileClassGeneratorFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Exception\ParseException;

#[AsCommand('create-dto')]
final class CreateDTO extends Command
{
    private FileClassGenerator $fileClassGenerator;
    private Crawler $crawler;
    private Reader $reader;
    private NamespaceHelper $namespaceHelper;

    /**
     * @throws LogicException
     */
    public function __construct(
        Reader $reader,
        Crawler $crawler,
        FileClassGenerator $fileClassGenerator,
        NamespaceHelper $namespaceHelper,
        string $name = null
    ) {
        parent::__construct($name);
        $this->crawler = $crawler;
        $this->reader = $reader;
        $this->fileClassGenerator = $fileClassGenerator;
        $this->namespaceHelper = $namespaceHelper;
    }

    /**
     * @throws InvalidArgumentException
     */
    protected function configure(): void
    {
        $this
            ->setDescription('Generate class from json')
            ->addArgument('input-file', InputArgument::REQUIRED, 'From which class will be generated')
            ->addArgument(
                'path-out',
                InputArgument::OPTIONAL,
                'Where put generated content',
                sprintf('%s/out', getcwd())
            )
            ->addOption(
                'namespace',
                null,
                InputOption::VALUE_OPTIONAL,
                'Namespace what all generated classes will have',
                'Generated'
            );
    }

    /**
     * @throws JsonException|InvalidArgumentException|ParseException|RuntimeException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string $path */
        $path = $input->getArgument('input-file');
        $openapi = $this->reader->extractPhpArrayFromFile($path);

        /** @var string $outputDirectory */
        $outputDirectory = $input->getArgument('path-out');
        $this->fileClassGenerator->setOutputDirectory($outputDirectory);
        /** @var string $globalNamespace */
        $globalNamespace = $input->getOption('namespace');
        $this->namespaceHelper->setGlobalNamespace($globalNamespace);

        foreach ($this->crawler->walk($openapi) as $definition) {
            /** @var ClassDefinitionData $definition */
            $definition->class->setFinal();
            $this->fileClassGenerator->generateClassFile($definition);
        }
        return Command::SUCCESS;
    }
}
