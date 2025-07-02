<?php

declare(strict_types=1);

/*
 * This file is part of Contao Last Editor Bundle.
 *
 * (c) Das L – Alex Wuttke Software & Media
 *
 * @license LGPL-3.0-or-later
 */

namespace DasL\ContaoLastEditorBundle\EventListener\DataContainer;

use Contao\BackendUser;
use Contao\DataContainer;
use Doctrine\DBAL\Connection;
use Symfony\Component\Security\Core\Security;

class StoreUserOnSubmitListener
{
    public function __construct(
        private readonly Connection $db,
        private readonly Security $security,
        private readonly array $tables,
    ) {
    }

    public function onsubmitCallback(DataContainer $dc): void
    {
        $user = $this->security->getUser();

        if (!$dc->id || !\in_array($dc->table, $this->tables, true) || !$user instanceof BackendUser) {
            return;
        }

        $this->db->update($dc->table, ['lastEditor' => $user->id], ['id' => $dc->id]);
    }
}
