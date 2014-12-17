<?php
/*   _            _          _ ____   ___  _____
 *  | |          | |        | |___ \ / _ \| ____|
 *  | |      __ _| |__   ___| | __) | | | | |__
 *  | |     / _` | '_ \ / _ \ ||__ <|  -  |___ \
 *  | |____| (_| | |_) |  __/ |___) |     |___) |
 *  |______|\__,_|_.__/ \___|_|____/ \___/|____/
 *
 *  Copyright Label305 B.V. All rights reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Label305\AujaLaravel\Database;

use Doctrine\DBAL\Schema\Table;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * A DatabaseHelper which uses the DB facade to access the DoctrineSchemaManager.
 * Also caches the data using the Cache facade.
 *
 * @author  Niek Haarman - <niek@label305.com>
 *
 * @package Label305\AujaLaravel\Database
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
class MySQLDatabaseHelper implements DatabaseHelper {

    const KEY_TABLE_INFO = 'auja-laravel.table_info';

    /**
     * @var Table[]
     */
    private $tables;

    public function hasTable($tableName) {
        $tables = $this->getTables();
        return isset($tables[$tableName]);
    }

    public function getColumnListing($tableName) {
        $tables = $this->getTables();
        $table = $tables[$tableName];
        return array_keys($table->getColumns());
    }

    public function getColumnType($tableName, $columnName) {
        $tables = $this->getTables();
        $table = $tables[$tableName];
        return $table->getColumn($columnName)->getType()->getName();
    }

    /**
     * @return Table[] The tables, either from cache or from the DoctrineSchemaManager.
     */
    private function getTables() {
        if ($this->tables == null) {
            if (Cache::has(self::KEY_TABLE_INFO)) {
                $this->tables = Cache::get(self::KEY_TABLE_INFO);
            } else {
                $this->tables = array();
                $doctrineSchemaManager = DB::getDoctrineSchemaManager();
                $tables = $doctrineSchemaManager->listTables();

                // Register enum as string
                // https://wildlyinaccurate.com/doctrine-2-resolving-unknown-database-type-enum-requested
                $doctrineSchemaManager
                    ->getConnection()
                    ->getDatabasePlatform()
                    ->registerDoctrineTypeMapping('enum', 'string');

                /* @var $tables Table[] */
                foreach ($tables as $table) {
                    $this->tables[$table->getName()] = $table;
                }

                Cache::put(self::KEY_TABLE_INFO, $this->tables, 1);
            }
        }
        return $this->tables;
    }
}