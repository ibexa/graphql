<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\GraphQL\Schema\Domain\Content\Worker\FieldDefinition;

use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Contracts\GraphQL\Schema\Domain\Content\Mapper\FieldDefinition\FieldDefinitionMapper;
use Ibexa\GraphQL\Schema\Builder;
use Ibexa\GraphQL\Schema\Builder\Input;
use Ibexa\GraphQL\Schema\Domain\Content\Worker\BaseWorker;
use Ibexa\GraphQL\Schema\Worker;

class AddFieldValueToItem extends BaseWorker implements Worker
{
    private FieldDefinitionMapper $fieldDefinitionMapper;

    public function __construct(FieldDefinitionMapper $fieldDefinitionMapper)
    {
        $this->fieldDefinitionMapper = $fieldDefinitionMapper;
    }

    /**
     * @param array{FieldDefinition: \Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition, ContentType: \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType} $args
     */
    public function work(Builder $schema, array $args): void
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

    /**
     * @return array{type: string|null, resolve: string|null, argsBuilder?: string|null}
     */
    private function getDefinition(FieldDefinition $fieldDefinition): array
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

    /**
     * @param array{FieldDefinition?: \Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition|mixed, ContentType?: \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType|mixed} $args
     */
    public function canWork(Builder $schema, array $args): bool
    {
        return
            isset($args['FieldDefinition'])
            && $args['FieldDefinition'] instanceof FieldDefinition
            && isset($args['ContentType'])
            && $args['ContentType'] instanceof ContentType
            && !$schema->hasTypeWithField($this->typeName($args), $this->fieldName($args));
    }

    /**
     * @param array{ContentType: \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType} $args
     */
    protected function typeName(array $args): string
    {
        return $this->getNameHelper()->itemName($args['ContentType']);
    }

    /**
     * @param array{FieldDefinition: \Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition} $args
     */
    protected function fieldName(array $args): string
    {
        return $this->getNameHelper()->fieldDefinitionField($args['FieldDefinition']);
    }
}
