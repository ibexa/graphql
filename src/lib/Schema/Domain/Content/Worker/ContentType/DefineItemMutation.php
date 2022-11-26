<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\GraphQL\Schema\Domain\Content\Worker\ContentType;

use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\GraphQL\Schema\Builder;
use Ibexa\GraphQL\Schema\Domain\Content\Worker\BaseWorker;
use Ibexa\GraphQL\Schema\Initializer;
use Ibexa\GraphQL\Schema\Worker;

class DefineItemMutation extends BaseWorker implements Worker, Initializer
{
    public const MUTATION_TYPE = 'ItemMutation';

    public function init(Builder $schema)
    {
        $schema->addType(new Builder\Input\Type(
            self::MUTATION_TYPE,
            'object',
            ['inherits' => ['PlatformMutation']]
        ));
    }

    public function work(Builder $schema, array $args)
    {
        $contentType = $args['ContentType'];

        // ex: createArticle
        $schema->addFieldToType(
            self::MUTATION_TYPE,
            new Builder\Input\Field(
                $this->getCreateField($contentType),
                $this->getNameHelper()->itemName($contentType) . '!',
                [
                    'resolve' => sprintf(
                        '@=mutation("CreateDomainContent", args["input"], "%s", args["parentLocationId"], args["language"])',
                        $contentType->identifier
                    ), ]
            )
        );

        $schema->addArgToField(
            self::MUTATION_TYPE,
            $this->getCreateField($contentType),
            new Builder\Input\Arg('input', $this->getCreateInputName($contentType) . '!')
        );

        $schema->addArgToField(
            self::MUTATION_TYPE,
            $this->getCreateField($contentType),
            $this->buildLanguageFieldInput()
        );

        $schema->addArgToField(
            self::MUTATION_TYPE,
            $this->getCreateField($contentType),
            new Builder\Input\Arg('parentLocationId', 'Int!')
        );

        $schema->addType(new Builder\Input\Type($this->getCreateInputName($contentType), 'input-object'));

        // Update mutation field
        $schema->addFieldToType(
            self::MUTATION_TYPE,
            new Builder\Input\Field(
                $this->getUpdateField($contentType),
                $this->getNameHelper()->itemName($contentType) . '!',
                ['resolve' => '@=mutation("UpdateDomainContent", args["input"], args, args["versionNo"], args["language"])']
            )
        );

        $schema->addArgToField(
            self::MUTATION_TYPE,
            $this->getUpdateField($contentType),
            new Builder\Input\Arg('input', $this->getUpdateInputName($contentType) . '!')
        );

        $schema->addArgToField(
            self::MUTATION_TYPE,
            $this->getUpdateField($contentType),
            $this->buildLanguageFieldInput()
        );

        $schema->addArgToField(
            self::MUTATION_TYPE,
            $this->getUpdateField($contentType),
            new Builder\Input\Arg('id', 'ID', ['description' => 'ID of the content item to update'])
        );

        $schema->addArgToField(
            self::MUTATION_TYPE,
            $this->getUpdateField($contentType),
            new Builder\Input\Arg('contentId', 'Int', ['description' => 'Repository content ID of the content item to update'])
        );

        $schema->addArgToField(
            self::MUTATION_TYPE,
            $this->getUpdateField($contentType),
            new Builder\Input\Arg('versionNo', 'Int', ['description' => 'Optional version number to update. If it is a draft, it is saved, not published. If it is archived, it is used as the source version for the update, to complete missing fields.'])
        );

        $schema->addType(new Builder\Input\Type($this->getUpdateInputName($contentType), 'input-object'));
    }

    public function canWork(Builder $schema, array $args)
    {
        return isset($args['ContentType'])
               && $args['ContentType'] instanceof ContentType
               && !isset($args['FieldDefinition'])
               && !$schema->hasType($this->getCreateInputName($args['ContentType']));
    }

    /**
     * @param $contentType
     */
    protected function getCreateInputName($contentType): string
    {
        return $this->getNameHelper()->itemCreateInputName($contentType);
    }

    /**
     * @param $contentType
     */
    protected function getUpdateInputName($contentType): string
    {
        return $this->getNameHelper()->itemUpdateInputName($contentType);
    }

    /**
     * @param $contentType
     */
    protected function getCreateField($contentType): string
    {
        return $this->getNameHelper()->itemMutationCreateItemField($contentType);
    }

    /**
     * @param $contentType
     */
    protected function getUpdateField($contentType): string
    {
        return $this->getNameHelper()->itemMutationUpdateItemField($contentType);
    }

    private function buildLanguageFieldInput(): Builder\Input\Arg
    {
        return new Builder\Input\Arg(
            'language',
            'RepositoryLanguage!',
            ['description' => 'The language the content should be created/updated in.']
        );
    }
}

class_alias(DefineItemMutation::class, 'EzSystems\EzPlatformGraphQL\Schema\Domain\Content\Worker\ContentType\DefineItemMutation');
