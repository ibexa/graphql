<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Bundle\GraphQL\DependencyInjection;

use Ibexa\Bundle\GraphQL\DependencyInjection\GraphQL\YamlSchemaProvider;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Yaml\Yaml;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class IbexaGraphQLExtension extends Extension implements PrependExtensionInterface
{
    public const EXTENSION_NAME = 'ibexa_graphql';

    private const SCHEMA_DIR_PATH = '/config/graphql/types';
    private const EZPLATFORM_SCHEMA_DIR_PATH = '/ezplatform';
    private const PACKAGE_DIR_PATH = '/vendor/ibexa/graphql';
    private const PACKAGE_SCHEMA_DIR_PATH = '/src/bundle/Resources/config/graphql';
    private const FIELDS_DEFINITION_FILE_NAME = 'Field.types.yaml';

    public function getAlias(): string
    {
        return self::EXTENSION_NAME;
    }

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services/data_loaders.yaml');
        $loader->load('services/location_guesser.yaml');
        $loader->load('services/mutations.yaml');
        $loader->load('services/resolvers.yaml');
        $loader->load('services/schema.yaml');
        $loader->load('services/services.yaml');
        $loader->load('default_settings.yaml');

        $loader->load('services/bc_aliases.yaml');
    }

    /**
     * {@inheritdoc}
     */
    public function prepend(ContainerBuilder $container)
    {
        $this->setContainerParameters($container);

        $configDir = $container->getParameter('ezplatform.graphql.schema.root_dir');

        $graphQLConfig = $this->getGraphQLConfig($configDir);
        $graphQLConfig['definitions']['mappings']['types'][] = [
            'type' => 'yaml',
            'dir' => $container->getParameter('ezplatform.graphql.package.root_dir') . self::PACKAGE_SCHEMA_DIR_PATH,
        ];
        $graphQLConfig['definitions']['mappings']['types'][] = [
            'type' => 'yaml',
            'dir' => $container->getParameter('kernel.project_dir') . self::SCHEMA_DIR_PATH,
        ];
        $container->prependExtensionConfig('overblog_graphql', $graphQLConfig);
    }

    /**
     * Uses YamlConfigProvider to determinate what schema should be used.
     */
    private function getGraphQLConfig(string $configDir): array
    {
        $initialConfig = Yaml::parseFile(__DIR__ . '/../Resources/config/overblog_graphql.yaml');
        $initialConfig['definitions']['schema'] = (new YamlSchemaProvider($configDir))->getSchemaConfiguration();

        return $initialConfig;
    }

    private function setContainerParameters(ContainerBuilder $container): void
    {
        $rootDir = rtrim($container->getParameter('kernel.project_dir'), '/');

        $appSchemaDir = $rootDir . self::SCHEMA_DIR_PATH;
        $eZPlatformSchemaDir = $appSchemaDir . self::EZPLATFORM_SCHEMA_DIR_PATH;
        $packageRootDir = $rootDir . self::PACKAGE_DIR_PATH;
        $fieldsDefinitionFile = $packageRootDir . self::PACKAGE_SCHEMA_DIR_PATH . \DIRECTORY_SEPARATOR . self::FIELDS_DEFINITION_FILE_NAME;

        $container->setParameter('ezplatform.graphql.schema.root_dir', $appSchemaDir);
        $container->setParameter('ezplatform.graphql.schema.ezplatform_dir', $eZPlatformSchemaDir);
        $container->setParameter('ezplatform.graphql.schema.fields_definition_file', $fieldsDefinitionFile);
        $container->setParameter('ezplatform.graphql.package.root_dir', $packageRootDir);
    }
}

class_alias(IbexaGraphQLExtension::class, 'EzSystems\EzPlatformGraphQL\DependencyInjection\EzSystemsEzPlatformGraphQLExtension');