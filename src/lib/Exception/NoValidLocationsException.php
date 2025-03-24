<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\GraphQL\Exception;

use Exception;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;

class NoValidLocationsException extends Exception
{
    /**
     * @var \Ibexa\Contracts\Core\Repository\Values\Content\Content|\Ibexa\Contracts\Core\Repository\Values\Content\Content[]
     */
    private Content $content;

    /**
     * NoValidLocationsException constructor.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Content $content
     */
    public function __construct(Content $content)
    {
        parent::__construct("No valid location could be determined for content #{$content->id}");
        $this->content = $content;
    }

    public function getContent(): Content
    {
        return $this->content;
    }
}
