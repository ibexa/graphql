<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\GraphQL\Schema\Domain\Content\Worker\FieldDefinition;

use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Contracts\Core\Repository\Values\MultiLanguageDescription;
use Ibexa\Contracts\GraphQL\Schema\Domain\Content\Mapper\FieldDefinition\FieldDefinitionMapper;
use Ibexa\GraphQL\Schema\Builder;
use Ibexa\GraphQL\Schema\Builder\Input;
use Ibexa\GraphQL\Schema\Domain\Content\Worker\BaseWorker;
use Ibexa\GraphQL\Schema\Worker;

class AddFieldDefinitionToItemType extends BaseWorker implements Worker
{
    private FieldDefinitionMapper $fieldDefinitionMapper;

    public function __construct(FieldDefinitionMapper $fieldDefinitionMapper)
    {
        $this->fieldDefinitionMapper = $fieldDefinitionMapper;
    }

    public function work(Builder $schema, array $args): void
    {
        $schema->addFieldToType($this->typeName($args), new Input\Field(
            $this->fieldName($args),
            $this->fieldType($args),
            [
                'description' => $this->fieldDescription($args),
                'resolve' => sprintf(
                    '@=value.getFieldDefinition("%s")',
                    $args['FieldDefinition']->identifier
                ),
            ]
        ));
    }

    public function canWork(Builder $schema, array $args): bool
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
        return $this->getNameHelper()->itemTypeName($args['ContentType']);
    }

    protected function fieldName(array $args): string
    {
        return $this->getNameHelper()->fieldDefinitionField($args['FieldDefinition']);
    }

    public function fieldDescription(array $args)
    {
        $description = '';
        if ($args['FieldDefinition'] instanceof MultiLanguageDescription) {
            $description = $args['FieldDefinition']->getDescription('eng-GB') ?? '';
        }

        return $description;
    }

    private function fieldType(array $args): ?string
    {
        return $this->fieldDefinitionMapper->mapToFieldDefinitionType($args['FieldDefinition']);
    }
}
