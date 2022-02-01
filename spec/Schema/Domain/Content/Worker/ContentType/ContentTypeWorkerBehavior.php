<?php
namespace spec\Ibexa\GraphQL\Schema\Domain\Content\Worker\ContentType;

use Ibexa\Core\Repository\Values\ContentType;
use PhpSpec\ObjectBehavior;

abstract class ContentTypeWorkerBehavior extends ObjectBehavior
{
    const GROUP_IDENTIFIER = 'test_group';
    const TYPE_IDENTIFIER = 'test_type';
    const TYPE_DESCRIPTION = 'Type description';

    protected function args(): array
    {
        return [
            'ContentTypeGroup' => new ContentType\ContentTypeGroup([
                'identifier' => self::GROUP_IDENTIFIER,
            ]),
            'ContentType' => new ContentType\ContentType([
                'identifier' => self::TYPE_IDENTIFIER,
                'descriptions' => ['eng-GB' => self::TYPE_DESCRIPTION]
            ])
        ];
    }
}
class_alias(ContentTypeWorkerBehavior::class, 'spec\EzSystems\EzPlatformGraphQL\Schema\Domain\Content\Worker\ContentType\ContentTypeWorkerBehavior');
