<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\GraphQL\Relay;

use GraphQL\Executor\Promise\Adapter\SyncPromiseAdapter;
use GraphQL\Executor\Promise\Promise;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Relay\Connection\ConnectionBuilder;
use Overblog\GraphQLBundle\Relay\Connection\ConnectionInterface;
use Overblog\GraphQLBundle\Relay\Connection\PageInfoInterface;

/**
 * @phpstan-template T
 */
final class PageAwareConnection
{
    /** @var iterable<\Overblog\GraphQLBundle\Relay\Connection\EdgeInterface<T>> */
    public iterable $edges = [];

    public PageInfoInterface $pageInfo;

    /** @var int */
    public int $totalCount;

    /** @var Page[] */
    public array $pages;

    /**
     * @param iterable<\Overblog\GraphQLBundle\Relay\Connection\EdgeInterface<T>> $edges
     */
    public function __construct(iterable $edges, PageInfoInterface $pageInfo)
    {
        $this->edges = $edges;
        $this->pageInfo = $pageInfo;
    }

    /**
     * @param \GraphQL\Executor\Promise\Promise|\Overblog\GraphQLBundle\Relay\Connection\ConnectionInterface<T> $connection
     *
     * @return \Ibexa\GraphQL\Relay\PageAwareConnection<T>
     */
    public static function fromConnection(ConnectionInterface|Promise $connection, Argument $args): PageAwareConnection
    {
        $connection = self::resolvePromise(
            $connection,
            static fn ($resolved) => $resolved instanceof ConnectionInterface,
            'Resolved result is not a ConnectionInterface'
        );

        $return = new self($connection->getEdges(), $connection->getPageInfo());

        $totalCount = self::resolvePromise(
            $connection->getTotalCount(),
            static fn ($resolved) => is_int($resolved) || null === $resolved,
            'Resolved result is not an int or null'
        );

        $return->totalCount = (int)$totalCount;

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

    private static function resolvePromise(mixed $value, callable $validator, string $errorMessage): mixed
    {
        if ($value instanceof Promise) {
            $promiseAdapter = new SyncPromiseAdapter();
            $resolvedValue = $promiseAdapter->wait($value);

            if (!$validator($resolvedValue)) {
                throw new \UnexpectedValueException($errorMessage);
            }

            return $resolvedValue;
        }

        return $value;
    }
}
