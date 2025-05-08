<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\Bundle\GraphQL\Command;

use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\GraphQL\Schema\Generator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

#[AsCommand(
    name: 'ibexa:graphql:generate-schema',
    description: 'Generates the GraphQL schema for the Ibexa DXP instance',
)]
class GeneratePlatformSchemaCommand extends Command
{
    private Generator $generator;

    private Repository $repository;

    private string $schemaRootDir;

    public function __construct(Generator $generator, Repository $repository, string $schemaRootDir)
    {
        parent::__construct();
        $this->generator = $generator;
        $this->repository = $repository;
        $this->schemaRootDir = $schemaRootDir;
    }

    protected function configure()
    {
        $this
            ->addOption('dry-run', null, InputOption::VALUE_OPTIONAL, 'Do not write, output the schema only', false)
            ->addOption('include', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Type to output or write', [])
            ->addOption(
                'user',
                'u',
                InputOption::VALUE_REQUIRED,
                'Ibexa DXP username',
                'admin'
            );
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->repository->getPermissionResolver()->setCurrentUserReference(
            $this->repository->getUserService()->loadUserByLogin($input->getOption('user'))
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $schema = $this->generator->generate();

        $include = $input->getOption('include');
        $doWrite = $input->getOption('dry-run') === false;

        $fs = new Filesystem();
        foreach ($schema as $type => $definition) {
            if (count($include) && !in_array($type, $include)) {
                continue;
            }
            $typeFilePath = $this->schemaRootDir . "/$type.types.yaml";

            $yaml = Yaml::dump([$type => $definition], 6);
            if ($doWrite) {
                $fs->dumpFile($typeFilePath, $yaml);
                $output->writeln("Written $typeFilePath");
            } else {
                $output->writeln("\n# $type\n$yaml\n");
            }
        }

        $output->writeln('');
        $this->compileTypes($output);

        return Command::SUCCESS;
    }

    private function compileTypes(OutputInterface $output): void
    {
        $command = $this->getApplication()->find('graphql:compile');
        $command->run(new StringInput('graphql:compile'), $output);
    }
}
