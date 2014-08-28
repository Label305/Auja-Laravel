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
use Label305\AujaLaravel\Repositories\DatabaseRepository;

class AujaConfigurator {

    const ID_PREFIX = '_id';

    /**
     * @var DatabaseRepository the DatabaseRepository which provides information about the database.
     */
    private $databaseRepository;

    /**
     * @var array a key-value pair of model names and the Model instances.
     */
    private $models = array();

    /**
     * @var array a key-value pair of model names and an array of their relations.
     */
    private $relations = array();

    /**
     * Creates a new AujaConfigurator.
     *
     * @param DatabaseRepository $databaseRepository
     */
    public function __construct(DatabaseRepository $databaseRepository) {
        $this->databaseRepository = $databaseRepository;
    }

    /**
     * Defines Models, Columns and Relations between the Models.
     * This method should be called before using any other methods.
     *
     * @param array $modelNames String[] an array of model names to use.
     */
    public function configure(array $modelNames) {
        /* First define the models and their columns. */
        foreach ($modelNames as $modelName) {
            $model = new Model($modelName);
            $this->models[$modelName] = $model;
            $this->relations[$modelName] = array();

            $this->findColumns($model);
        }

        /* Find relations */
        $this->findRelations(array_values($this->models));
    }

    /**
     * @return array a key-value pair of model names and the Model instances.
     */
    public function getModels() {
        return $this->models;
    }

    /**
     * @return array a key-value pair of model names and an array of their relations.
     */
    public function getRelations() {
        return $this->relations;
    }

    /**
     * Returns an array of Relations for given Model.
     *
     * @param Model $model the Model.
     * @return Relation[] the Relations.
     */
    public function getRelationsForModel(Model $model) {
        return isset($this->relations[$model->getName()]) ? $this->relations[$model->getName()] : array();
    }

    /**
     * Finds and configures the Columns for given Model.
     *
     * @param Model $model the Model to find the Columns for.
     */
    private function findColumns(Model $model) {
//        Log::debug('Finding columns for model ' . $model->getName());
        $tableName = $model->getTableName();

        if (!$this->databaseRepository->hasTable($tableName)) {
            throw new \InvalidArgumentException(sprintf('Table for %s does not exist!', $model->getName()));
        }

        $columns = $this->databaseRepository->getColumnListing($tableName);
        foreach ($columns as $column) {
//            Log::debug(sprintf('Adding column %s to %s', $column, $model->getName()));
            $model->addColumn(new Column($column, null));
        }
    }

    /**
     * @param array $models Finds Relations for given Models.
     */
    private function findRelations(array $models) {
        foreach ($models as $model) {
            $this->findSimpleRelations($model);
        }
        $this->findManyToManyRelations(array_values($this->models));
    }


    /**
     * Finds and defines one-to-one and one-to-many relations for given model.
     *
     * @param $model Model the Model to find the relations for.
     */
    private function findSimpleRelations(Model $model) {
//        Log::debug(sprintf('Finding relations for %s', $model->getName()));
        foreach ($model->getColumns() as $column) {
            if (ends_with($column->getName(), self::ID_PREFIX)) {
                $this->defineRelation($model, $column->getName());
            }
        }
    }

    /**
     * Defines the relation between given model and the model corresponding to the column name.
     * Does nothing if the other model was not declared in init().
     *
     * @param $model      Model the model which has given columnName.
     * @param $columnName String the column name, which corresponds to another model.
     */
    private function defineRelation(Model $model, $columnName) {
        $otherModelName = ucfirst(camel_case(substr($columnName, 0, strpos($columnName, self::ID_PREFIX))));

        if (!in_array($otherModelName, array_keys($this->models))) {
//            Log::warning(sprintf('Found foreign id %s in model %s, but no model with name %s was registered', $columnName, $model->getName(), $otherModelName));
            return;
        }

//        Log::info(sprintf('%s has a %s', $model->getName(), $otherModelName));

        $this->relations[$model->getName()][] = new Relation($model, $this->models[$otherModelName], Relation::BELONGS_TO);
        $this->relations[$otherModelName][] = new Relation($this->models[$otherModelName], $model, Relation::HAS_MANY);
    }

    /**
     * Finds and defines many to many relations between models in given array.
     *
     * @param $models Model[] the model names to look for relations for.
     */
    private function findManyToManyRelations(array $models) {
//        Log::debug('Finding many to many relations');
        for ($i = 0; $i < sizeof($models); $i++) {
            for ($j = $i + 1; $j < sizeof($models); $j++) {
                $model1 = $models[$i];
                $model2 = $models[$j];

                if (strcasecmp($model1->getName(), $model2->getName()) < 0) {
                    $tableName = strtolower($model1->getName()) . '_' . strtolower($model2->getName());
                } else {
                    $tableName = strtolower($model2->getName()) . '_' . strtolower($model1->getName());
                }

                if ($this->databaseRepository->hasTable($tableName)) {
                    $this->defineManyToManyRelation($model1, $model2);
                }
            }
        }
    }

    /**
     * Defines a many-to-many relation between given models.
     *
     * @param $model1 Model the first model.
     * @param $model2 Model the second model.
     */
    private function defineManyToManyRelation(Model $model1, Model $model2) {
//        Log::info(sprintf('%s has and belongs to many %s', $model1->getName(), str_plural($model2->getName())));
        $this->relations[$model1->getName()][] = new Relation($model1, $model2, Relation::HAS_AND_BELONGS_TO);
        $this->relations[$model2->getName()][] = new Relation($model2, $model1, Relation::HAS_AND_BELONGS_TO);
    }

}