<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\GraphQL\Relay;

use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Relay\Connection\ConnectionBuilder;
use Overblog\GraphQLBundle\Relay\Connection\Output\Connection;
use Overblog\GraphQLBundle\Relay\Connection\PageInfoInterface;

final class PageAwareConnection
{
    /** @var \Overblog\GraphQLBundle\Relay\Connection\Output\Edge[] */
    public $edges = [];

    /** @var \Overblog\GraphQLBundle\Relay\Connection\PageInfoInterface */
    public $pageInfo;

    /** @var int */
    public $totalCount;

    /** @var Page[] */
    public $pages;

    public function __construct(array $edges, PageInfoInterface $pageInfo)
    {
        $this->edges = $edges;
        $this->pageInfo = $pageInfo;
    }

    public static function fromConnection(Connection $connection, Argument $args): PageAwareConnection
    {
        $return = new self($connection->edges, $connection->pageInfo);
        $return->totalCount = $connection->totalCount;

        $return->pages = [];

        $perPage = $args['first'] ?? $args['last'] ?? 10;
        $totalPages = ceil($return->totalCount / $perPage);
        $connectionBuilder = new ConnectionBuilder();
        for ($pageNumber = 2; $pageNumber <= $totalPages; ++$pageNumber) {
            $offset = ($pageNumber - 1) * $perPage - 1;
            $return->pages[] = new Page($pageNumber, $connectionBuilder->offsetToCursor($offset));
        }

        return $return;
    }
}

class_alias(PageAwareConnection::class, 'EzSystems\EzPlatformGraphQL\GraphQL\Relay\PageAwareConnection');
