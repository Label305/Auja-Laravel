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


use Illuminate\Support\Facades\Schema;

class AujaBuilder {

    const ID_PREFIX = '_id';

    /**
     * @var String[] the model names, as provided in init().
     */
    private static $models;

    /**
     * Initializes this class for given models.
     *
     * @param $models String[] an array of model names to use.
     */
    public static function init($models) {
        if (empty($models)) {
            throw new \InvalidArgumentException('Provide models!');
        }
        self::$models = array_map('strtolower', $models);

        foreach ($models as $model) {
            self::findRelationShips($model);
        }
        self::findManyToManyRelationships($models);
    }

    /**
     * Finds and definces one-to-one and one-to-many relationships for given model.
     *
     * @param $model String the model name.
     */
    private static function findRelationShips($model) {
        $tableName = str_plural($model);

        if (!Schema::hasTable($tableName)) {
            throw new \InvalidArgumentException(sprintf('Table for %s does not exist!', $model));
        }

        $columns = Schema::getColumnListing($tableName);

        var_dump($columns);

        foreach ($columns as $columnName) {
            if (ends_with($columnName, self::ID_PREFIX)) {
                self::defineRelationship($model, $columnName);
            }
        }
    }

    /**
     * Defines the relationship between given model and the model corresponding to the column name.
     * Does nothing if the other model was not declared in init().
     *
     * @param $model      String the model which has given columnName.
     * @param $columnName String the column name, which corresponds to another model.
     */
    private static function defineRelationship($model, $columnName) {
        $otherModel = substr($columnName, 0, strpos($columnName, self::ID_PREFIX));

        if (!in_array($otherModel, self::$models)) {
            return;
        }

        echo $model . ' has a ' . $otherModel . PHP_EOL;
    }

    /**
     * Finds and defines many to many relationships between models in given array.
     *
     * @param $models String[] the model names to look for relationships for.
     */
    private static function findManyToManyRelationships($models) {
        for ($i = 0; $i < sizeof($models); $i++) {
            for ($j = $i + 1; $j < sizeof($models); $j++) {
                $model1 = $models[$i];
                $model2 = $models[$j];

                if (strcasecmp($model1, $model2) < 0) {
                    $tableName = strtolower($model1) . '_' . strtolower($model2);
                } else {
                    $tableName = strtolower($model2) . '_' . strtolower($model1);
                }

                if (Schema::hasTable($tableName)) {
                    // TODO: YAY! $model1 has and belongs to many $model2!
                    self::defineManyToManyRelationship($model1, $model2);
                }

            }
        }
    }

    /**
     * Defines a many-to-many relationship between given models.
     *
     * @param $model1 String the first model name.
     * @param $model2 String the second model name.
     */
    private static function defineManyToManyRelationship($model1, $model2) {
        echo $model1 . ' has and belongs to many ' . str_plural($model2) . PHP_EOL;
    }

}