<?php

namespace spec\Ibexa\GraphQL\Resolver;

use Ibexa\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Core\Repository\Values\Content\VersionInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\GraphQL\DataLoader\ContentLoader;
use Ibexa\GraphQL\DataLoader\ContentTypeLoader;
use Ibexa\GraphQL\InputMapper\QueryMapper;
use Ibexa\GraphQL\Resolver\DomainContentResolver;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Core\FieldType;
use Ibexa\GraphQL\Value\Field;
use Overblog\GraphQLBundle\Resolver\TypeResolver;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class DomainContentResolverSpec extends ObjectBehavior
{
    const CONTENT_ID = 1;

    function let(
        Repository $repository,
        TypeResolver $typeResolver,
        QueryMapper $queryMapper,
        ContentLoader $contentLoader,
        ContentTypeLoader $contentTypeLoader
    ) {
        $this->beConstructedWith($repository, $typeResolver, $queryMapper, $contentLoader, $contentTypeLoader);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(DomainContentResolver::class);
    }

    function it_resolves_a_RelationList_field_value_with_multiple_to_an_array(ContentLoader $contentLoader)
    {
        $contentArray = $this->createContentList([self::CONTENT_ID]);
        $field = $this->createRelationListField($contentArray);

        $contentLoader->find($this->createContentIdListQuery($contentArray))->willReturn($contentArray);
        $this->resolveDomainRelationFieldValue($field, true)->shouldReturn($contentArray);
    }

    function it_resolves_an_empty_RelationList_field_value_with_multiple_to_an_empty_array(ContentLoader $contentLoader)
    {
        $contentArray = [];
        $field = $this->createRelationListField($contentArray);

        $contentLoader->find(Argument::any())->shouldNotBeCalled();
        $this->resolveDomainRelationFieldValue($field, true)->shouldReturn([]);
    }

    function it_resolves_a_Relation_field_value_without_multiple_to_a_content_item(ContentLoader $contentLoader)
    {
        $content = $this->createContent(self::CONTENT_ID);
        $field = $this->createRelationField($content);
        $contentLoader->find($this->createContentIdListQuery([$content]))->willReturn([$content]);

        $this->resolveDomainRelationFieldValue($field, false)->shouldReturn($content);
    }

    function it_resolves_an_empty_Relation_field_value_without_multiple_to_null(ContentLoader $contentLoader)
    {
        $field = $this->createEmptyRelationField();

        $contentLoader->find(Argument::any())->shouldNotBeCalled();
        $this->resolveDomainRelationFieldValue($field, false)->shouldReturn(null);

    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Content[] $contentList
     * @return \Ibexa\GraphQL\Value\Field
     */
    private function createRelationListField(array $contentList): Field
    {
        return new Field(['value' => new FieldType\RelationList\Value($this->extractContentIdList($contentList))]);
    }

    private function createRelationField(Content $content): Field
    {
        return new Field(['value' => new FieldType\Relation\Value($content->id ?? null)]);
    }

    private function createEmptyRelationField(): Field
    {
        return new Field(['value' => new FieldType\Relation\Value()]);
    }

    private function createContentIdListQuery(array $contentList)
    {
        return new Query(['filter' => new Query\Criterion\ContentId($this->extractContentIdList($contentList))]);
    }

    private function createContentIdQuery(Content $content): Query
    {
        return new Query(['filter' => new Query\Criterion\ContentId($content->id)]);
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Content[] $contentList
     * @return array
     */
    private function extractContentIdList(array $contentList): array
    {
        return array_map(
            function (Content $content) {
                return $content->id;
            },
            $contentList
        );
    }

    /**
     * @param int[] $contentIdList
     * @return \Ibexa\Core\Repository\Values\Content\Content[]
     */
    private function createContentList(array $contentIdList): array
    {
        return array_map(
            function ($contentId) {
                return $this->createContent($contentId);
            },
            $contentIdList
        );
    }

    /**
     * @param $contentId
     * @return \Ibexa\Core\Repository\Values\Content\Content
     */
    private function createContent($contentId): Content
    {
        return new Content([
            'versionInfo' => new VersionInfo([
                'contentInfo' => new ContentInfo(['id' => $contentId])
            ])
        ]);
    }
}
