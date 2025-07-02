<?php

declare(strict_types=1);

/*
 * This file is part of Contao Last Editor Bundle.
 *
 * (c) Das L – Alex Wuttke Software & Media
 *
 * @license LGPL-3.0-or-later
 */

namespace DasL\ContaoLastEditorBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class ContaoLastEditorBundle extends Bundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
