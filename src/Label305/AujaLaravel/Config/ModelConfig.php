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
     * @var String namespace and classname of the model;
     * For example: '/YourApp/Models/User'
     */
    private $modelClass;

    /**
     * @var String The name of the model to display.
     * For example: 'User'
     */
    private $displayName;

    /**
     * @var String[] A key value pair of column names and their display names.
     */
    private $columnDisplayNames;

    /**
     * @var String The name of the table for this model.
     * For example: 'users'
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

    /**
     * @param bool `true` if the user should be able to search items.
     */
    private $searchable;

    /**
     * @var bool `true` if the smartController should be included in main.
     */
    private $smartIncludeInMain;

    public function __construct($modelClass = null) {

        $this->modelClass = $modelClass;
        $this->smartIncludeInMain = true;
        $this->searchable = true;
    }

    /**
     * @return String The name that is displayed.
     */
    public function getDisplayName() {
        $modelClass = $this->getModelClass();
        if (is_null($this->displayName) && !is_null($modelClass)) {
            $displayNameResults = array_slice(explode('\\', $modelClass), -1, 1, false);
            $this->setDisplayName($displayNameResults[0]);
        }

        return $this->displayName;
    }

    /**
     * @param String $displayName The name to display.
     * @return $this
     */
    public function setDisplayName($displayName) {
        $this->displayName = $displayName;
        return $this;
    }

    /**
     * Returns the name to display for given column name.
     *
     * @param String $columnName The name of the column.
     *
     * @return String The name that is displayed for given column name.
     */
    public function getColumnDisplayName($columnName) {
        return isset($this->columnDisplayNames[$columnName]) ? $this->columnDisplayNames[$columnName] : $columnName;
    }

    /**
     * Sets the name to display for given column name.
     *
     * @param String $columnName The name of the column.
     * @param String $displayName The name to display for the column.
     * @return $this
     */
    public function setColumnDisplayName($columnName, $displayName) {
        $this->columnDisplayNames[$columnName] = $displayName;
        return $this;
    }

    /**
     * @return String The name of the table for this model.
     */
    public function getTableName() {
        $displayName = $this->getDisplayName();
        if (is_null($this->tableName) && !is_null($displayName)) {
            $this->setTableName(str_plural(snake_case($displayName)));
        }

        return $this->tableName;
    }

    /**
     * @param String $tableName The name of the table for this model.
     * @return $this
     */
    public function setTableName($tableName) {
        $this->tableName = $tableName;
        return $this;
    }

    /**
     * @return String The name of the column that is used for displaying an entry.
     */
    public function getDisplayField() {
        return $this->displayField;
    }

    /**
     * @param String $displayField The name of the column to use for displaying an entry.
     * @return $this
     */
    public function setDisplayField($displayField) {
        $this->displayField = $displayField;
        return $this;
    }

    /**
     * @return String The name of the icon that is used, as defined in Icons.
     */
    public function getIcon() {
        return $this->icon;
    }

    /**
     * @param String $icon The name of the icon to use, as defined in Icons.
     * @return $this
     */
    public function setIcon($icon) {
        $this->icon = $icon;
        return $this;
    }

    /**
     * @return String[]
     */
    public function getVisibleFields() {
        return $this->visibleFields;
    }

    /**
     * @param String[] $visibleFields
     * @return $this
     */
    public function setVisibleFields($visibleFields) {
        $this->visibleFields = $visibleFields;
        return $this;
    }

    /**
     * @return bool `true` if the model should be included in main.
     */
    public function getSmartIncludeInMain() {
        return $this->smartIncludeInMain;
    }

    /**
     * @param $smartInclude
     * @return $this
     */
    public function setSmartIncludeInMain($smartInclude) {
        $this->smartIncludeInMain = $smartInclude;
        return $this;
    }

    /**
     * @return bool `true` if the user should be able to search items.
     */
    public function isSearchable() {
        return $this->searchable;
    }

    /**
     * @param $searchable
     * @return $this
     */
    public function setSearchable($searchable) {
        $this->searchable = $searchable;
        return $this;
    }

    /**
     * @return String
     */
    public function getModelClass()
    {
        return $this->modelClass;
    }

    /**
     * @param String $modelClass
     */
    public function setModelClass($modelClass)
    {
        $this->modelClass = $modelClass;
        return $this;
    }

}