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

use Behat\Behat\Context\BehatContext;
use Behat\Gherkin\Node\TableNode;
use Illuminate\Foundation\Application;
use Label305\AujaLaravel\Config\AujaConfigurator;

use Label305\AujaLaravel\Config\ModelConfig;
use Label305\AujaLaravel\Config\Relation;
use Label305\AujaLaravel\Database\DatabaseHelper;
use Label305\AujaLaravel\Logging\Logger;
use \Mockery as m;

class FeatureContext extends BehatContext {

    /**
     * @var String[]
     */
    private $models = [];

    /**
     * @var String
     */
    private $lastModel;

    /**
     * @var Application
     */
    private $application;

    /**
     * @var DatabaseHelper
     */
    private $databaseHelper;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var AujaConfigurator
     */
    private $aujaConfigurator;

    function __construct() {
        $this->application = m::mock('Illuminate\Foundation\Application');
        $this->databaseHelper = m::mock('Label305\AujaLaravel\Database\DatabaseHelper');

        $this->logger = $this->mockLogger();

        $this->aujaConfigurator = new AujaConfigurator($this->application, $this->databaseHelper, $this->logger);
    }

    /**
     * @Given /^there is a model "([^"]*)"$/
     */
    public function thereIsAModel($modelName) {
        $this->models[] = $modelName;
        $this->lastModel = $modelName;

        $config = new ModelConfig($modelName);
        $config->setTableName($modelName);
        $this->application->shouldReceive('make')->with($modelName . 'Config', $modelName)->andReturn($config);

        $this->databaseHelper->shouldReceive('hasTable')->with($modelName)->andReturn(true);
    }

    /**
     * @Given /^it has the following columns:$/
     */
    public function itHasTheFollowingColumns(TableNode $table) {
        $columns = [];
        foreach ($table->getHash() as $columnHash) {
            $columns[] = $columnHash['column'];
            $this->databaseHelper->shouldReceive('getColumnType')->with($this->lastModel, $columnHash['column'])->andReturn($columnHash['type']);
        }
        $this->databaseHelper->shouldReceive('getColumnListing')->with($this->lastModel)->andReturn($columns);
    }

    /**
     * @When /^I configure the Auja Configurator$/
     */
    public function iConfigureTheAujaConfigurator() {
        /* We still need to configure pivot tables that don't exist */
        for ($i = 0; $i < sizeof($this->models); $i++) {
            $model = $this->models[$i];
            for ($j = $i + 1; $j < sizeof($this->models); $j++) {
                $otherModel = $this->models[$j];
                $this->databaseHelper->shouldReceive('hasTable')->with(sprintf('%s_%s', strtolower($otherModel), strtolower($model)))->andReturn(false);
                $this->databaseHelper->shouldReceive('hasTable')->with(sprintf('%s_%s', strtolower($model), strtolower($otherModel)))->andReturn(false);
            }
        }


        $this->aujaConfigurator->configure($this->models);
    }

    /**
     * @Then /^I should get a configuration with the following models:$/
     */
    public function iShouldGetAConfigurationWithTheFollowingModels(TableNode $table) {
        if (sizeof($table->getHash()) != sizeof($this->aujaConfigurator->getModels())) {
            throw new Exception('Invalid model count');
        }

        foreach ($table->getHash() as $modelHash) {
            $name = $modelHash['name'];
            $model = $this->aujaConfigurator->getModel($name);
            if (is_null($model)) {
                throw new Exception(sprintf('Returned model for %s is null', $name));
            }
        }
    }

    /**
     * @Given /^it should have a belongs to relationship between:$/
     */
    public function itShouldHaveABelongsToRelationshipBetween(TableNode $table) {
        $this->assertRelationshipsExist($table, Relation::BELONGS_TO);
    }

    /**
     * @Given /^it should have a has many relationship between:$/
     */
    public function itShouldHaveAHasManyRelationshipBetween(TableNode $table) {
        $this->assertRelationshipsExist($table, Relation::HAS_MANY);
    }

    /**
     * @Given /^it should have a many to many relationship between:$/
     */
    public function itShouldHaveAManyToManyRelationshipBetween(TableNode $table) {
        $this->assertRelationshipsExist($table, Relation::HAS_AND_BELONGS_TO);
    }

    /**
     * @Given /^there should be no relations\.$/
     */
    public function thereShouldBeNoRelations() {
        $modelRelations = $this->aujaConfigurator->getRelations();
        foreach ($modelRelations as $relations) {
            /**
             * @var $relations Relation[]
             */
            if (!empty($relations)) {
                throw new Exception(sprintf('Found a %s relation between %s and %s', $relations[0]->getType(), $relations[0]->getLeft(), $relations[0]->getRight()));
            }
        }
    }

    /**
     * @Given /^there is a pivot table "([^"]*)"$/
     */
    public function thereIsAPivotTable($tableName) {
        $this->databaseHelper->shouldReceive('hasTable', $tableName)->andReturn(true);
    }

    private function mockLogger() {
        $result = m::mock('Label305\AujaLaravel\Logging\Logger');
        $result->shouldReceive('debug');
        $result->shouldReceive('info');
        $result->shouldReceive('warn');
        return $result;
    }

    private function assertRelationshipsExist(TableNode $table, $type) {
        $relations = $this->aujaConfigurator->getRelations();

        foreach ($table->getHash() as $relationHash) {
            $leftModelName = $relationHash['left'];
            $rightModelName = $relationHash['right'];

            if (!isset($relations[$leftModelName])) {
                throw new Exception(sprintf('No relations for model %s', $leftModelName));
            }

            $modelRelations = $this->aujaConfigurator->getRelationsForModel($this->aujaConfigurator->getModel($leftModelName));
            $relationshipExists = false;
            foreach ($modelRelations as $relation) {
                if ($relation->getType() == $type && $relation->getRight()->getName() == $rightModelName) {
                    $relationshipExists = true;
                }
            }

            if (!$relationshipExists) {
                throw new Exception(sprintf('There is no %s relationship between %s and %s.', $type, $leftModelName, $rightModelName));
            }
        }
    }
}