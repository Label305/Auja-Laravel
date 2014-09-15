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

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Label305\AujaLaravel\Database\DatabaseHelper;
use Label305\AujaLaravel\Logging\Logger;

/**
 * A class which determines properties and relations for models.
 *
 * @package Label305\AujaLaravel
 */
class AujaConfigurator {

    const ID_PREFIX = '_id';

    /**
     * @var Application
     */
    private $app;

    /**
     * @var DatabaseHelper the DatabaseRepository which provides information about the database.
     */
    private $databaseRepository;

    /**
     * @var Logger The Logger to use.
     */
    private $logger;

    /**
     * @var array a key-value pair of model names and the Model instances.
     */
    private $models = array();

    /**
     * @var array a key-value pair of model names and an array of their relations.
     */
    private $relations = array();

    /**
     * @var array a key-value pair of model names and their generated configs.
     */
    private $configs = array();

    /**
     * Creates a new AujaConfigurator.
     *
     * @param Application    $app
     * @param DatabaseHelper $databaseRepository
     * @param Logger         $logger ;
     */
    public function __construct(Application $app, DatabaseHelper $databaseRepository, Logger $logger) {
        $this->app = $app;
        $this->databaseRepository = $databaseRepository;
        $this->logger = $logger;
    }

    /**
     * Defines Models, Columns and Relations between the Models.
     * This method should be called before using any other methods.
     *
     * @param  String[] $modelNames an array of model names to use.
     */
    public function configure(array $modelNames) {
        /* First define the models and their columns. */
        foreach ($modelNames as $modelName) {
            $this->models[$modelName] = new Model($modelName);
            $this->relations[$modelName] = array();

            $this->findColumns($this->models[$modelName]);

            $configResolver = new ConfigResolver($this->app, $this->models[$modelName]);
            $this->configs[$modelName] = $configResolver->resolve();
        }

        /* Find relations */
        $this->findRelations(array_values($this->models));
    }

    /**
     * @return Model[] the array of Model instances.
     */
    public function getModels() {
        return array_values($this->models);
    }

    /**
     * @param $modelName String the name of the model.
     *
     * @return Model the Model corresponding to given Model name.
     */
    public function getModel($modelName) {
        if (!isset($this->models[$modelName])) {
            throw new \LogicException(sprintf("Model for name %s doesn't exist!", $modelName));
        }

        return $this->models[$modelName];
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
     *
     * @return Relation[] the Relations.
     */
    public function getRelationsForModel(Model $model) {
        return isset($this->relations[$model->getName()]) ? $this->relations[$model->getName()] : array();
    }

    /**
     * Finds which display field to use for given Model, using the Config class for the Model.
     * Uses the overridden value if present, or falls back to the generated value.
     *
     * @param $model Model the Model to find the display field for.
     *
     * @return String the name of the field to use for displaying the Model.
     */
    public function getDisplayField(Model $model) {
        $modelConfig = $this->configs[$model->getName()];
        /* @var $modelConfig ModelConfig */
        return $modelConfig->getDisplayField();
    }

    /**
     * Finds the icon to use for given Model, using the Config class for the Model.
     * Uses the overridden value if present, or falls back to the generated value.
     *
     * @param Model $model The Model to find the icon for.
     *
     * @return String The name of the icon.
     */
    public function getIcon(Model $model) {
        $modelConfig = $this->configs[$model->getName()];
        /* @var $modelConfig ModelConfig */
        return $modelConfig->getIcon();
    }

    /**
     * Finds and configures the Columns for given Model.
     *
     * @param Model $model the Model to find the Columns for.
     */
    private function findColumns(Model $model) {
        $this->logger->debug('Finding columns for model ' . $model->getName());
        $tableName = $model->getTableName();

        if (!$this->databaseRepository->hasTable($tableName)) {
            throw new \InvalidArgumentException(sprintf('Table for %s does not exist!', $model->getName()));
        }

        $columns = $this->databaseRepository->getColumnListing($tableName);
        foreach ($columns as $columnName) {
            $this->logger->debug(sprintf('Adding column %s to %s', $columnName, $model->getName()));
            $columnType = $this->databaseRepository->getColumnType($tableName, $columnName);
            $model->addColumn(new Column($columnName, $columnType));
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
        $this->logger->debug(sprintf('Finding relations for %s', $model->getName()));
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
            $this->logger->warn(sprintf('Found foreign id %s in model %s, but no model with name %s was registered', $columnName, $model->getName(), $otherModelName));
            return;
        }

        $this->logger->info(sprintf('%s has a %s', $model->getName(), $otherModelName));

        $this->relations[$model->getName()][] = new Relation($model, $this->models[$otherModelName], Relation::BELONGS_TO);
        $this->relations[$otherModelName][] = new Relation($this->models[$otherModelName], $model, Relation::HAS_MANY);
    }

    /**
     * Finds and defines many to many relations between models in given array.
     *
     * @param $models Model[] the model names to look for relations for.
     */
    private function findManyToManyRelations(array $models) {
        $this->logger->debug('Finding many to many relations');
        for ($i = 0; $i < sizeof($models); $i++) {
            for ($j = $i + 1; $j < sizeof($models); $j++) {
                $model1 = $models[$i];
                $model2 = $models[$j];

                /* We assume names of pivot tables are alphabetically ordered */
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
        $this->logger->info(sprintf('%s has and belongs to many %s', $model1->getName(), str_plural($model2->getName())));
        $this->relations[$model1->getName()][] = new Relation($model1, $model2, Relation::HAS_AND_BELONGS_TO);
        $this->relations[$model2->getName()][] = new Relation($model2, $model1, Relation::HAS_AND_BELONGS_TO);
    }

}