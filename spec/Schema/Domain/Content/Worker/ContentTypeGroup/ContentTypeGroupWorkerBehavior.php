<?php
namespace Ibexa\Spec\GraphQL\Schema\Domain\Content\Worker\ContentTypeGroup;

use Ibexa\Core\Repository\Values\ContentType\ContentTypeGroup;
use PhpSpec\ObjectBehavior;

abstract class ContentTypeGroupWorkerBehavior extends ObjectBehavior
{
    const GROUP_IDENTIFIER = 'test_group';
    const GROUP_DESCRIPTION = 'Description of the group';

    protected function args(): array
    {
        return [
            'ContentTypeGroup' => new ContentTypeGroup([
                'identifier' => self::GROUP_IDENTIFIER,
                'descriptions' => ['eng-GB' => self::GROUP_DESCRIPTION]
            ])
        ];
    }

}
class_alias(ContentTypeGroupWorkerBehavior::class, 'spec\EzSystems\EzPlatformGraphQL\Schema\Domain\Content\Worker\ContentTypeGroup\ContentTypeGroupWorkerBehavior');
