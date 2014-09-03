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

/**
 * A class which defines a column in a database table.
 *
 * @package Label305\AujaLaravel
 */
class Column {

    /**
     * @var String the name of the column.
     */
    private $name;

    /**
     * @var String the type of the column. See Doctrine\DBAL\Types\Type for supported column names.
     */
    private $type;

    /**
     * Creates a new Column.
     *
     * @param $name String the name of the Column.
     * @param $type String the type of the Column. See Doctrine\DBAL\Types\Type for supported column names.
     */
    function __construct($name, $type) {
        $this->name = $name;
        $this->type = $type;
    }

    /**
     * @return String the name of the column.
     */
    function getName() {
        return $this->name;
    }

    /**
     * @return String the type of the column.
     */
    function getType() {
        return $this->type;
    }

}
