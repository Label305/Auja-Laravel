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

namespace Label305\AujaLaravel\Repositories;


interface DatabaseRepository {

    /**
     * Returns whether the database has given table name.
     *
     * @param $tableName String the name of the table to look for.
     *
     * @return bool true if the table exists, false otherwise.
     */
    public function hasTable($tableName);

    /**
     * Returns an array of column names for given table name.
     *
     * @param $tableName String the table name.
     *
     * @return String[] the columns in the table.
     */
    public function getColumnListing($tableName);

    /**
     * Returns the type of the given column in given table.
     *
     * @param $tableName String the name of the table.
     * @param $columnName String the name of the column.
     *
     * @return String the type of the column. See Doctrine\DBAL\Types\Type for supported column names.
     */
    public function getColumnType($tableName, $columnName);

} 