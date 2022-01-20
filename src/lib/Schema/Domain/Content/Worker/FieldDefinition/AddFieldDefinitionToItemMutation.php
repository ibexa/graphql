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
use Ibexa\GraphQL\Schema\Domain\Content\Worker\BaseWorker;
use Ibexa\GraphQL\Schema\Worker;

class AddFieldDefinitionToItemMutation extends BaseWorker implements Worker
{
    public const OPERATION_CREATE = 'create';
    public const OPERATION_UPDATE = 'update';

    /**
     * @var \Ibexa\Contracts\GraphQL\Schema\Domain\Content\Mapper\FieldDefinition\FieldDefinitionMapper
     */
    private $mapper;

    /**
     * @param \Ibexa\Contracts\GraphQL\Schema\Domain\Content\Mapper\FieldDefinition\FieldDefinitionMapper $mapper
     */
    public function __construct(FieldDefinitionMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    public function work(Builder $schema, array $args)
    {
        $properties = ['description' => $this->mapDescription($args)];

        $schema->addFieldToType(
            $this->nameCreateInputType($args),
            new Builder\Input\Field(
                $this->nameFieldDefinitionField($args),
                $this->nameFieldType($args, self::OPERATION_CREATE),
                $properties
            )
        );

        $schema->addFieldToType(
            $this->nameUpdateInputType($args),
            new Builder\Input\Field(
                $this->nameFieldDefinitionField($args),
                $this->nameFieldType($args, self::OPERATION_UPDATE),
                $properties
            )
        );
    }

    public function canWork(Builder $schema, array $args): bool
    {
        return
            isset($args['ContentType'])
            && $args['ContentType'] instanceof ContentType
            && isset($args['FieldDefinition'])
            && $args['FieldDefinition'] instanceof FieldDefinition
            && !$schema->hasTypeWithField($this->nameCreateInputType($args), $this->nameFieldDefinitionField($args));
    }

    protected function nameCreateInputType(array $args): string
    {
        return $this->getNameHelper()->itemCreateInputName($args['ContentType']);
    }

    private function nameUpdateInputType(array $args): string
    {
        return $this->getNameHelper()->itemUpdateInputName($args['ContentType']);
    }

    protected function nameFieldDefinitionField(array $args): string
    {
        return $this->getNameHelper()->fieldDefinitionField($args['FieldDefinition']);
    }

    private function nameFieldType(array $args, $operation): string
    {
        $fieldDefinition = $args['FieldDefinition'];
        $contentType = $args['ContentType'];

        $type = $this->mapper->mapToFieldValueInputType($contentType, $fieldDefinition);
        $requiredFlag = $operation == self::OPERATION_CREATE && $fieldDefinition->isRequired ? '!' : '';

        return $type . $requiredFlag;
    }

    /**
     * Extracts the description of a field definition.
     *
     * @param array $args
     */
    private function mapDescription($args): ?string
    {
        return $args['FieldDefinition']->getDescription($args['ContentType']->mainLanguageCode);
    }
}

class_alias(AddFieldDefinitionToItemMutation::class, 'EzSystems\EzPlatformGraphQL\Schema\Domain\Content\Worker\FieldDefinition\AddFieldDefinitionToItemMutation');
