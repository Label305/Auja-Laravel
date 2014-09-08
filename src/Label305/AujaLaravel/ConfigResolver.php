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


use Doctrine\DBAL\Types\Type;

class ConfigResolver {

    private $displayFieldNames = ['name', 'title'];

    /**
     * @var Model
     */
    private $model;

    function __construct(Model $model) {
        $this->model = $model;
    }

    public function resolve() {
        $result = new Config();

        $result->setDisplayField($this->resolveDisplayField());

        return $result;
    }

    /**
     * Tries to resolve the display field for the Model.
     * The possible column names are defined in $displayFieldNames.
     *
     * @return String the name of the column to use as display field.
     */
    private function resolveDisplayField() {
        $result = null;

        $columns = $this->model->getColumns();
        foreach ($columns as $column) {
            if ($column->getType() == Type::STRING && in_array($column->getName(), $this->displayFieldNames)) {
                $result = $column->getName();
            }
        }

        if ($result == null) {
            /* If we couldn't find a display field, just return the first one */
            return empty($columns) ? '' : $columns[0]->getName();
        }

        return $result;
    }

}