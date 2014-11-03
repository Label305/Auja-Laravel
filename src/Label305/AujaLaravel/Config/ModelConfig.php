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

namespace Label305\AujaLaravel\Config;

/**
 * A class for providing configurations for individual models.
 *
 * Extend this class and override the get functions to provide your configuration for a specific model.
 * Auja-Laravel will try to find a class called $modelName.'Config' (e.g. ClubConfig),
 * and uses it to configure the models.
 *
 * You don't need to override all the getters. If a getter is not overridden,
 * Auja-Laravel will try to guess the value itself.
 *
 * @author  Niek Haarman - <niek@label305.com>
 *
 * @package Label305\AujaLaravel
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
class ModelConfig {

    /**
     * @var String The name of the table for this model.
     */
    private $tableName;

    /**
     * @var String The name of the column to use for displaying an entry.
     */
    private $displayField;

    /**
     * @var String The name of the icon to use, as defined in Icons.
     */
    private $icon = '';

    /**
     * @var String[] The fields to display in a Page.
     */
    private $visibleFields;

    public function __construct($modelName){
        $this->tableName = str_plural(snake_case($modelName));
    }

    /**
     * @return String The name of the table for this model.
     */
    public function getTableName() {
        return $this->tableName;
    }

    /**
     * @param String $tableName The name of the table for this model.
     */
    public function setTableName($tableName) {
        $this->tableName = $tableName;
    }

    /**
     * @return String The name of the column that is used for displaying an entry.
     */
    public function getDisplayField() {
        return $this->displayField;
    }

    /**
     * @param String $displayField The name of the column to use for displaying an entry.
     */
    public function setDisplayField($displayField) {
        $this->displayField = $displayField;
    }

    /**
     * @return String The name of the icon that is used, as defined in Icons.
     */
    public function getIcon() {
        return $this->icon;
    }

    /**
     * @param String $icon The name of the icon to use, as defined in Icons.
     */
    public function setIcon($icon) {
        $this->icon = $icon;
    }

    /**
     * @return String[]
     */
    public function getVisibleFields() {
        return $this->visibleFields;
    }

    /**
     * @param String[] $visibleFields
     */
    public function setVisibleFields($visibleFields) {
        $this->visibleFields = $visibleFields;
    }

    /**
     * @return bool `true` if the model should be included in main.
     */
    public function includeInMain() {
        return true;
    }

    /**
     * @return bool `true` if the user should be able to search items.
     */
    public function isSearchable() {
        return true;
    }
}