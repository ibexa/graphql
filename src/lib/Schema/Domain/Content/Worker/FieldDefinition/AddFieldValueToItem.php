<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\GraphQL\Schema\Domain\Content\Worker\FieldDefinition;

use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use Ibexa\GraphQL\Schema\Builder;
use Ibexa\GraphQL\Schema\Builder\Input;
use Ibexa\Contracts\GraphQL\Schema\Domain\Content\Mapper\FieldDefinition\FieldDefinitionMapper;
use Ibexa\GraphQL\Schema\Domain\Content\Worker\BaseWorker;
use Ibexa\GraphQL\Schema\Worker;

class AddFieldValueToItem extends BaseWorker implements Worker
{
    /**
     * @var \EzSystems\EzPlatformGraphQL\Schema\Domain\Content\Mapper\FieldDefinition\FieldDefinitionMapper
     */
    private $fieldDefinitionMapper;

    public function __construct(FieldDefinitionMapper $fieldDefinitionMapper)
    {
        $this->fieldDefinitionMapper = $fieldDefinitionMapper;
    }

    public function work(Builder $schema, array $args)
    {
        $definition = $this->getDefinition($args['FieldDefinition']);
        $schema->addFieldToType(
            $this->typeName($args),
            new Input\Field($this->fieldName($args), $definition['type'], $definition)
        );

        if ($args['FieldDefinition']->isTranslatable) {
            $schema->addArgToField(
                $this->typeName($args),
                $this->fieldName($args),
                new Input\Arg('language', 'RepositoryLanguage')
            );
        }
    }

    private function getDefinition(FieldDefinition $fieldDefinition)
    {
        $definition = [
            'type' => $this->fieldDefinitionMapper->mapToFieldValueType($fieldDefinition),
            'resolve' => $this->fieldDefinitionMapper->mapToFieldValueResolver($fieldDefinition),
        ];

        if (($argsBuilder = $this->fieldDefinitionMapper->mapToFieldValueArgsBuilder($fieldDefinition)) !== null) {
            $definition['argsBuilder'] = $argsBuilder;
        }

        return $definition;
    }

    public function canWork(Builder $schema, array $args)
    {
        return
            isset($args['FieldDefinition'])
            && $args['FieldDefinition'] instanceof FieldDefinition
            & isset($args['ContentType'])
            && $args['ContentType'] instanceof ContentType
            && !$schema->hasTypeWithField($this->typeName($args), $this->fieldName($args));
    }

    protected function typeName(array $args): string
    {
        return $this->getNameHelper()->itemName($args['ContentType']);
    }

    protected function fieldName($args): string
    {
        return $this->getNameHelper()->fieldDefinitionField($args['FieldDefinition']);
    }
}

class_alias(AddFieldValueToItem::class, 'EzSystems\EzPlatformGraphQL\Schema\Domain\Content\Worker\FieldDefinition\AddFieldValueToItem');
