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


use Doctrine\DBAL\Types\Type;
use Illuminate\Foundation\Application;

/**
 * A class for resolving configuration for a model.
 *
 * @author  Niek Haarman - <niek@label305.com>
 *
 * @package Label305\AujaLaravel
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
class ConfigResolver {

    private $displayFieldNames = ['name', 'title'];

    /**
     * @var ModelConfig
     */
    private $config;

    /**
     * @var Model
     */
    private $model;

    /**
     * Creates a new ConfigResolver for given Model.
     *
     * @param Application $app
     * @param Model       $model The Model to generate a
     */
    public function __construct(Application $app, Model $model) {
        $this->model = $model;

        try {
            $this->config = $app->make($model->getName() . 'Config', [$model->getName()]);
        } catch (\ReflectionException $e) {
            $this->config = new ModelConfig($model->getName());
        }
    }

    /**
     * Resolves a Config instance.
     *
     * @return ModelConfig The resolved config.
     */
    public function resolve() {
        if (is_null($this->config->getDisplayField()) || $this->config->getDisplayField() == '') {
            $this->config->setDisplayField($this->resolveDisplayField());
            $this->config->setVisibleFields($this->resolveVisibleFields());
        }

        return $this->config;
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
            $result = empty($columns) ? '' : $columns[0]->getName();
        }

        return $result;
    }

    /**
     * Resolves the fields to display in a Page.
     *
     * @return String[] The names of the fields to display.
     */
    private function resolveVisibleFields() {
        $result = [];

        $columns = $this->model->getColumns();
        foreach($columns as $column){
            $result[] = $column->getName();
        }

        return $result;
    }

}