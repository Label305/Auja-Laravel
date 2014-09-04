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

namespace Label305\AujaLaravel;


class Model {

    /**
     * @var String the name of the Model.
     */
    private $name;

    /**
     * @var String the name of the table. Defaults to the pluralized form of $name.
     */
    private $tableName;

    /**
     * @var array an array of key value pairs with the column names and the columns in the Model.
     */
    private $columns = array();

    /**
     * Creates a new Model.
     *
     * @param $name String the name of the Model.
     */
    function __construct($name) {
        $this->name = $name;
        $this->tableName = str_plural(snake_case($name));
    }

    /**
     * @return String the name of the Model.
     */
    function getName() {
        return $this->name;
    }

    /**
     * Adds a column to the definition of the Model.
     *
     * @param Column $column the column to add.
     */
    function addColumn(Column $column) {
        $this->columns[$column->getName()] = $column;
    }

    /**
     * @return Column[] the columns as defined using addColumn.
     */
    public function getColumns() {
        return array_values($this->columns);
    }

    /**
     * Returns the column with given column name.
     *
     * @param $columnName String column name.
     *
     * @return Column the column with given name.
     */
    public function getColumn($columnName){
        return $this->columns[$columnName];
    }

    /**
     * @param $tableName String table name to set.
     */
    public function setTableName($tableName) {
        $this->tableName = $tableName;
    }

    /**
     * @return String the table name.
     */
    public function getTableName() {
        return $this->tableName;
    }
}
