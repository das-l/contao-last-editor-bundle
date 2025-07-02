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

use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AsHook('loadDataContainer')]
class AddLastEditorFieldsListener
{
    public function __construct(
        private readonly array $tables,
        private readonly TranslatorInterface $translator,
    ) {
    }

    public function __invoke(string $table): void
    {
        if (!isset($GLOBALS['TL_DCA'][$table]) || !\in_array($table, $this->tables, true)) {
            return;
        }

        $GLOBALS['TL_DCA'][$table]['fields']['lastEditor'] = [
            'label' => [$this->translator->trans('MSC.lastEditor', [], 'contao_default'), ''],
            'foreignKey' => 'tl_user.username',
            'sql' => ['type' => 'integer', 'unsigned' => true, 'default' => 0],
            'relation' => ['type' => 'hasOne', 'load' => 'lazy'],
        ];
        $GLOBALS['TL_DCA'][$table]['config']['onsubmit_callback'][] = [
            StoreUserOnSubmitListener::class, 'onsubmitCallback',
        ];
    }
}
