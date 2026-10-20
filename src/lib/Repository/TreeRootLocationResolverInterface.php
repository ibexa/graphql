<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\GraphQL\Repository;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;

/**
 * Resolves the tree root location and its excluded locations from the
 * `content.tree_root.location_id` and `content.tree_root.excluded_uri_prefixes` settings.
 *
 * @internal
 */
interface TreeRootLocationResolverInterface
{
    /**
     * @throws NotFoundException if the configured
     *         `content.tree_root.location_id` does not match an existing Location
     * @throws UnauthorizedException if the current user
     *         is not allowed to read the tree root Location
     */
    public function resolveRootLocation(): Location;

    /**
     * Resolves `content.tree_root.excluded_uri_prefixes` entries into Locations.
     * Entries that can't be resolved to an existing Location are logged and skipped.
     *
     * @return Location[]
     *
     * @throws UnauthorizedException if the current user
     *         is not allowed to read one of the excluded Locations
     */
    public function resolveExcludedLocations(): array;
}
