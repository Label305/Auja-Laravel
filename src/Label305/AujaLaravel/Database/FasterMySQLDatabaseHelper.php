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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FasterMySQLDatabaseHelper implements DatabaseHelper {

    /**
     * @var Table[]
     */
    private $tables = array();

    public function __construct() {
        $tables = DB::getDoctrineSchemaManager()->listTables();
        /* @var $tables Table[] */
        foreach ($tables as $table) {
            $this->tables[$table->getName()] = $table;
        }
    }

    public function hasTable($tableName) {
        return isset($this->tables[$tableName]);
    }

    public function getColumnListing($tableName) {
        $table = $this->tables[$tableName];
        return array_keys($table->getColumns());
    }

    public function getColumnType($tableName, $columnName) {
        $table = $this->tables[$tableName];
        return $table->getColumn($columnName)->getType()->getName();
    }
}