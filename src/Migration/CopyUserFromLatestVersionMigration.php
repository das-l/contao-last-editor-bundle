<?php

declare(strict_types=1);

/*
 * This file is part of Contao Last Editor Bundle.
 *
 * (c) Das L – Alex Wuttke Software & Media
 *
 * @license LGPL-3.0-or-later
 */

namespace DasL\ContaoLastEditorBundle\Migration;

use Contao\CoreBundle\Migration\AbstractMigration;
use Contao\CoreBundle\Migration\MigrationResult;
use Doctrine\DBAL\Connection;

/**
 * @internal
 */
class CopyUserFromLatestVersionMigration extends AbstractMigration
{
    public function __construct(
        private readonly Connection $connection,
        private readonly array $tables,
    ) {
    }

    public function shouldRun(): bool
    {
        $schemaManager = $this->connection->createSchemaManager();

        if (!$schemaManager->tablesExist(['tl_version']) || !$schemaManager->tablesExist($this->tables)) {
            return false;
        }

        $hasUncopiedVersionUsers = false;

        foreach ($this->tables as $table) {
            $columns = $schemaManager->listTableColumns($table);

            if (!isset($columns['lasteditor'])) {
                return true;
            }

            $test = $this->connection->fetchOne(
                "
                    SELECT TRUE
                    FROM tl_version
                    LEFT JOIN $table
                    ON tl_version.pid = $table.id
                    WHERE tl_version.fromTable = :table AND tl_version.userid != 0 AND $table.lastEditor = 0
                ",
                ['table' => $table],
            );
            if (false !== $test) {
                $hasUncopiedVersionUsers = true;

                break;
            }
        }

        return $hasUncopiedVersionUsers;
    }

    public function run(): MigrationResult
    {
        $schemaManager = $this->connection->createSchemaManager();

        foreach ($this->tables as $table) {
            $columns = $schemaManager->listTableColumns($table);

            if (!isset($columns['lasteditor'])) {
                $this->connection->executeStatement("
                    ALTER TABLE $table
                    ADD lastEditor int(10) unsigned NOT NULL default 0
                ");
            }

            // TODO: Surely, this could be a simpler query!?
            $this->connection->executeStatement(
                "
                    UPDATE $table
                    INNER JOIN (
                        SELECT tl_version.userid, tl_version.pid, tl_version.fromTable
                        FROM (
	                        SELECT MAX(version) AS maxVersion, pid
	                        FROM tl_version
	                        GROUP BY pid
                        ) AS maxVersionList
                        LEFT JOIN tl_version
                        ON tl_version.pid = maxVersionList.pid AND tl_version.version = maxVersionList.maxVersion
                    ) as maxVersionUserList
                    ON $table.id = maxVersionUserList.pid AND maxVersionUserList.fromTable = :table
                    SET $table.lastEditor = maxVersionUserList.userid
                    WHERE maxVersionUserList.userid != 0
                ",
                ['table' => $table],
            );
        }

        return $this->createResult(true);
    }
}
