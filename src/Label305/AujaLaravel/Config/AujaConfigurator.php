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
use Label305\AujaLaravel\Database\DatabaseHelper;

/**
 * A class which determines properties and relations for models.
 *
 * @package Label305\AujaLaravel
 */
class AujaConfigurator {

    const ID_SUFFIX = '_id';

    /**
     * @var Application
     */
    private $app;

    /**
     * @var DatabaseHelper the DatabaseRepository which provides information about the database.
     */
    private $databaseRepository;

    /**
     * @var array a key-value pair of model names and the Model instances.
     */
    private $models = [];

    /**
     * @var Relation[][] A key-value pair of model names and an array of their relations.
     */
    private $relations = [];

    /**
     * @var ModelConfig[] A key-value pair of model names and their generated configs.
     */
    private $configs = [];

    /**
     * Creates a new AujaConfigurator.
     *
     * @param Application    $app
     * @param DatabaseHelper $databaseHelper
     */
    public function __construct(Application $app, DatabaseHelper $databaseHelper) {
        $this->app = $app;
        $this->databaseRepository = $databaseHelper;
    }

    /**
     * Defines Models, Columns and Relations between the Models.
     * This method should be called before using any other methods.
     *
     * @param  String[] $modelNames an array of model names to use.
     */
    public function configure(array $modelNames) {
        if (empty($modelNames)) {
            throw new \LogicException('Supply at least one model name!');
        }

        /* First define the models and their columns. */
        foreach ($modelNames as $modelName) {
            $this->models[$modelName] = new Model($modelName);
            $this->relations[$modelName] = [];

            $configResolver = new ConfigResolver($this->app, $this->models[$modelName]);
            $this->configs[$modelName] = $configResolver->resolve();
            $this->findColumns($this->models[$modelName]);
            $this->configs[$modelName] = $configResolver->resolve(); // TODO: Find a workaround for doing this twice.
        }

        /* Find relations */
        $this->findRelations(array_values($this->models));
    }

    /**
     * @return Model[] the array of Model instances.
     */
    public function getModels() {
        if (empty($this->models)) {
            throw new \LogicException('AujaConfigurator not configured yet! Call configure first.');
        }

        return array_values($this->models);
    }

    /**
     * @param $modelName String the name of the model.
     *
     * @return Model the Model corresponding to given Model name.
     */
    public function getModel($modelName) {
        if (!isset($this->models[$modelName])) {
            throw new \LogicException(sprintf('Model for name %s doesn\'t exist!', $modelName));
        }

        return $this->models[$modelName];
    }

    /**
     * @return Relation[] A key-value pair of model names and an array of their relations.
     */
    public function getRelations() {
        if (empty($this->models)) {
            throw new \LogicException('AujaConfigurator not configured yet! Call configure first.');
        }

        return $this->relations;
    }

    /**
     * Returns an array of `Relations` for given `Model`.
     *
     * @param Model $model The `Model`.
     *
     * @return Relation[] The `Relations`.
     */
    public function getRelationsForModel(Model $model) {
        if (empty($this->models)) {
            throw new \LogicException('AujaConfigurator not configured yet! Call configure first.');
        }

        return !is_null($model) && isset($this->relations[$model->getName()]) ? $this->relations[$model->getName()] : [];
    }

    /**
     * Finds the name of the table for given Model, using the `ModelConfig` class for the `Model`.
     * Uses the overridden value if present, or falls back to the generated value.
     *
     * @param Model       $model  The `Model` to find the table name for.
     * @param ModelConfig $config The `ModelConfig` to use for retrieving the table name.
     *
     * @return String The name of table for given Model.
     */
    public function getTableName(Model $model, ModelConfig $config = null) {
        if (empty($this->models)) {
            throw new \LogicException('AujaConfigurator not configured yet! Call configure first.');
        }

        if (!isset($this->configs[$model->getName()])) {
            throw new \LogicException(sprintf('AujaConfigurator not configured for model %s', $model->getName()));
        }

        $result = null;
        if ($config != null && $config->getTableName() != null) {
            $result = $config->getTableName();
        } else {
            $modelConfig = $this->configs[$model->getName()];
            $result = $modelConfig->getTableName();
        }

        return $result;
    }

    /**
     * Finds which display field to use for given Model, using the `ModelConfig` class for the `Model`.
     * Uses the overridden value if present, or falls back to the generated value.
     *
     * @param Model       $model  The `Model` to find the display field for.
     * @param ModelConfig $config The `ModelConfig` to use for retrieving the display field.
     *
     * @return String The name of the field to use for displaying the `Model`.
     */
    public function getDisplayField(Model $model, ModelConfig $config = null) {
        if (empty($this->models)) {
            throw new \LogicException('AujaConfigurator not configured yet! Call configure first.');
        }

        if (!isset($this->configs[$model->getName()])) {
            throw new \LogicException(sprintf('AujaConfigurator not configured for model %s', $model->getName()));
        }

        $result = null;
        if ($config != null && $config->getDisplayField() != null) {
            $result = $config->getDisplayField();
        } else {

            $modelConfig = $this->configs[$model->getName()];
            $result = $modelConfig->getDisplayField();
        }
        return $result;
    }

    /**
     * Finds the icon to use for given `Model`, using the `ModelConfig` class for the `Model`.
     * Uses the overridden value if present, or falls back to the generated value.
     *
     * @param Model       $model  The `Model` to find the icon for.
     * @param ModelConfig $config The `ModelConfig` to use for retrieving the icon.
     *
     * @return String The name of the icon.
     */
    public function getIcon(Model $model, ModelConfig $config = null) {
        if (empty($this->models)) {
            throw new \LogicException('AujaConfigurator not configured yet! Call configure first.');
        }

        if (!isset($this->configs[$model->getName()])) {
            throw new \LogicException(sprintf('AujaConfigurator not configured for model %s', $model->getName()));
        }

        $result = null;
        if ($config != null && $config->getIcon() != null) {
            $result = $config->getIcon();
        } else {
            $modelConfig = $this->configs[$model->getName()];
            $result = $modelConfig->getIcon();
        }
        return $result;
    }

    /**
     * Finds the visible fields to show for given `Model`, using the `ModelConfig` class for the `Model`.
     * Uses the overridden value if present, or falls back to the generated value.
     *
     * @param Model       $model  The `Model` to find the visible fields for.
     * @param modelConfig $config The `ModelConfig` to use for retrieving the visible fields.
     *
     * @return \String[] An array of names of the visible fields.
     */
    public function getVisibleFields(Model $model, ModelConfig $config = null) {
        if (empty($this->models)) {
            throw new \LogicException('AujaConfigurator not configured yet! Call configure first.');
        }

        if (!isset($this->configs[$model->getName()])) {
            throw new \LogicException(sprintf('AujaConfigurator not configured for model %s', $model->getName()));
        }

        $result = null;
        if ($config != null && $config->getVisibleFields() != null) {
            $result = $config->getVisibleFields();
        } else {
            $modelConfig = $this->configs[$model->getName()];
            $result = $modelConfig->getVisibleFields();
        }
        return $result;
    }

    /**
     * Returns whether given model should be included in the main view, using the `ModelConfig` class for the `Model`.
     * Uses the overridden value if present, or falls back to the generated value.
     *
     * @param Model       $model  The `Model` to check.
     * @param ModelConfig $config The `ModelConfig` to use for checking.
     *
     * @return bool `true` if the model should be included in main.
     */
    public function shouldIncludeInMain(Model $model, ModelConfig $config = null) {
        if (empty($this->models)) {
            throw new \LogicException('AujaConfigurator not configured yet! Call configure first.');
        }

        if (!isset($this->configs[$model->getName()])) {
            throw new \LogicException(sprintf('AujaConfigurator not configured for model %s', $model->getName()));
        }

        $result = false;
        if ($config != null) {
            $result = $config->includeInMain();
        } else {
            $modelConfig = $this->configs[$model->getName()];
            $result = $modelConfig->includeInMain();
        }
        return $result;
    }

    /**
     * Finds and configures the `Columns` for given `Model`.
     *
     * @param Model $model the Model to find the `Columns` for.
     */
    private function findColumns(Model $model) {
        Log::debug('Finding columns for model ' . $model->getName());
        $tableName = $this->getTableName($model);

        if (!$this->databaseRepository->hasTable($tableName)) {
            throw new \InvalidArgumentException(sprintf('Table %s for %s does not exist!', $tableName, $model->getName()));
        }

        $columns = $this->databaseRepository->getColumnListing($tableName);
        foreach ($columns as $columnName) {
            Log::debug(sprintf('Adding column %s to %s', $columnName, $model->getName()));
            $columnType = $this->databaseRepository->getColumnType($tableName, $columnName);
            $model->addColumn(new Column($columnName, $columnType));
        }
    }

    /**
     * @param Model[] $models Finds Relations for given Models.
     */
    private function findRelations(array $models) {
        foreach ($models as $model) {
            $this->findSimpleRelations($model);
        }

        // TODO: Can this be prettier?
        /* Find BELONGS_TO relations which do not have a BELONGS_TO reverse relation. These need to have a HAS_MANY reverse relation. */
        foreach ($this->relations as $modelRelations) {
            foreach ($modelRelations as $relation) {
                if ($relation->getType() == Relation::BELONGS_TO) {
                    /* Try to find a matching reversed relation */
                    $otherModelRelations = &$this->relations[$relation->getRight()->getName()];
                    $hasReverseRelation = false;
                    foreach ($otherModelRelations as $otherModelRelation) {
                        if ($otherModelRelation->getRight() == $relation->getLeft()) {
                            $hasReverseRelation = true;
                        }
                    }
                    if (!$hasReverseRelation) {
                        /* There is no one-to-one relation. Define a hasMany relation. */
                        $otherModelRelations[] = new Relation($relation->getRight(), $relation->getLeft(), Relation::HAS_MANY);
                    }
                }
            }
        }

        $this->findManyToManyRelations(array_values($this->models));
    }

    /**
     * Finds and defines one-to-one and one-to-many relations for given model.
     *
     * @param $model Model the Model to find the relations for.
     */
    private function findSimpleRelations(Model $model) {
        Log::debug(sprintf('Finding relations for %s', $model->getName()));
        foreach ($model->getColumns() as $column) {
            if (ends_with($column->getName(), self::ID_SUFFIX)) {
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
        $otherModelName = ucfirst(camel_case(substr($columnName, 0, strpos($columnName, self::ID_SUFFIX)))); // TODO: prettify

        if (!in_array($otherModelName, array_keys($this->models))) {
            Log::warning(sprintf('Found foreign id %s in model %s, but no model with name %s was registered', $columnName, $model->getName(), $otherModelName));
            return;
        }

        Log::info(sprintf('%s has a %s', $model->getName(), $otherModelName));

        $this->relations[$model->getName()][] = new Relation($model, $this->models[$otherModelName], Relation::BELONGS_TO);
    }

    /**
     * Finds and defines many to many relations between models in given array.
     *
     * @param $models Model[] the model names to look for relations for.
     */
    private function findManyToManyRelations(array $models) {
        Log::debug('Finding many to many relations');
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
        Log::info(sprintf('%s has and belongs to many %s', $model1->getName(), str_plural($model2->getName())));
        $this->relations[$model1->getName()][] = new Relation($model1, $model2, Relation::HAS_AND_BELONGS_TO);
        $this->relations[$model2->getName()][] = new Relation($model2, $model1, Relation::HAS_AND_BELONGS_TO);
    }
}