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

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class AujaConfigurator {

    const ID_PREFIX = '_id';

    /**
     * @var String[] the model names, as provided in init().
     */
    private $modelNames;

    /**
     * @var Model[] the models, as generated in init().
     */
    private $models;

    public function __construct($modelNames) {
        $this->modelNames = $modelNames;
    }

    public function configure() {
        foreach ($this->modelNames as $modelName) {
            $model = new Model($modelName);

            $this->findColumns($model);
            $this->findRelationShips($model);

            $this->models[] = $model;
        }
        $this->findManyToManyRelationships($this->models);
    }

    private function findColumns(Model $model) {
        Log::debug('Finding columns for model ' . $model->getName());
        $tableName = $model->getTableName();

        if (!Schema::hasTable($tableName)) {
            throw new \InvalidArgumentException(sprintf('Table for %s does not exist!', $model->getName()));
        }

        $columns = Schema::getColumnListing($tableName); // TODO dependency injection
        foreach ($columns as $column) {
            Log::debug(sprintf('Adding column %s to %s', $column, $model->getName()));
            $model->addColumn(new Column($column, null));
        }
    }

    /**
     * Finds and defines one-to-one and one-to-many relationships for given model.
     *
     * @param $model Model
     */
    private function findRelationShips(Model $model) {
        Log::debug(sprintf('Finding relationships for %s', $model->getName()));
        foreach ($model->getColumns() as $column) {
            if (ends_with($column->getName(), self::ID_PREFIX)) {
                $this->defineRelationship($model, $column->getName());
            }
        }
    }

    /**
     * Defines the relationship between given model and the model corresponding to the column name.
     * Does nothing if the other model was not declared in init().
     *
     * @param $model      Model the model which has given columnName.
     * @param $columnName String the column name, which corresponds to another model.
     */
    private function defineRelationship(Model $model, $columnName) {
        $otherModel = ucfirst(camel_case(substr($columnName, 0, strpos($columnName, self::ID_PREFIX))));

        if (!in_array($otherModel, $this->modelNames)) {
            Log::warning(sprintf('Found foreign id %s in model %s, but no model with name %s was registered', $columnName, $model->getName(), $otherModel));
            return;
        }

        Log::info(sprintf('%s has a %s', $model->getName(), $otherModel));
        echo $model->getName() . ' has a ' . $otherModel . PHP_EOL;
    }

    /**
     * Finds and defines many to many relationships between models in given array.
     *
     * @param $models Model[] the model names to look for relationships for.
     */
    private function findManyToManyRelationships($models) {
        Log::debug('Finding many to many relationships');
        for ($i = 0; $i < sizeof($models); $i++) {
            for ($j = $i + 1; $j < sizeof($models); $j++) {
                $model1 = $models[$i];
                $model2 = $models[$j];

                if (strcasecmp($model1->getName(), $model2->getName()) < 0) {
                    $tableName = strtolower($model1->getName()) . '_' . strtolower($model2->getName());
                } else {
                    $tableName = strtolower($model2->getName()) . '_' . strtolower($model1->getName());
                }

                if (Schema::hasTable($tableName)) {
                    $this->defineManyToManyRelationship($model1, $model2);
                }

            }
        }
    }

    /**
     * Defines a many-to-many relationship between given models.
     *
     * @param $model1 Model the first model.
     * @param $model2 Model the second model.
     */
    private function defineManyToManyRelationship(Model $model1, Model $model2) {
        Log::info(sprintf('%s has and belongs to many %s', $model1->getName(), str_plural($model2->getName())));
        echo $model1->getName() . ' has and belongs to many ' . str_plural($model2->getName()) . PHP_EOL;
    }

} 