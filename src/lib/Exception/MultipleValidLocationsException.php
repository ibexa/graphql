<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\GraphQL\Exception;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;

class MultipleValidLocationsException extends \Exception
{
    /**
     * @var \Ibexa\Contracts\Core\Repository\Values\Content\Location[]
     */
    private $locations = [];

    /**
     * @var \Ibexa\Contracts\Core\Repository\Values\Content\Content
     */
    private $content;

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Content $content
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Location[] $locations
     */
    public function __construct(Content $content, array $locations)
    {
        parent::__construct(
            sprintf(
                'Could not determine which location to return for content with id %s. Possible candidates: %s)',
                $content->id,
                implode(',', array_column($locations, 'pathString'))
            )
        );
        $this->locations = $locations;
        $this->content = $content;
    }

    /**
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Location[]
     */
    public function getLocations(): array
    {
        return $this->locations;
    }

    public function getContent(): Content
    {
        return $this->content;
    }
}
