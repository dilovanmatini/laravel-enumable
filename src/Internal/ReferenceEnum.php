<?php

namespace DilovanMatini\Enumable\Internal;

use DilovanMatini\Enumable\Traits\Enumable;

/**
 * @internal Used only for static analysis of the Enumable trait.
 */
enum ReferenceEnum: string
{
    use Enumable;

    case Example = 'example';
}
