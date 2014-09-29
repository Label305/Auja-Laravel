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
use Behat\Gherkin\Node\PyStringNode;
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
     * @Given /^I have a model "([^"]*)"$/
     */
    public function iHaveAModel($modelName) {
        foreach ($this->models as $model) {
            $this->databaseHelper->shouldReceive('hasTable')->with(sprintf('%s_%s', strtolower($modelName), strtolower($model)))->andReturn(false);
            $this->databaseHelper->shouldReceive('hasTable')->with(sprintf('%s_%s', strtolower($model), strtolower($modelName)))->andReturn(false);
        }

        $this->models[] = $modelName;
        $this->lastModel = $modelName;

        $config = new ModelConfig();
        $config->setTableName($modelName);
        $this->application->shouldReceive('make')->with($modelName . 'Config')->andReturn($config);

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
        $this->databaseHelper->shouldReceive('getColumnListing')->andReturn($columns);
    }

    /**
     * @When /^I configure the Auja Configurator$/
     */
    public function iConfigureTheAujaConfigurator() {
        $this->aujaConfigurator->configure($this->models);
    }

    private function mockLogger() {
        $result = m::mock('Label305\AujaLaravel\Logging\Logger');
        $result->shouldReceive('debug');
        $result->shouldReceive('info');
        $result->shouldReceive('warn');
        return $result;
    }

    /**
     * @Then /^I should get a configuration with the following models:$/
     */
    public function iShouldGetAConfigurationWithTheFollowingModels(TableNode $table) {
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
        $relations = $this->aujaConfigurator->getRelations();

        foreach ($table->getHash() as $relationHash) {
            $leftModelName = $relationHash['left'];
            $rightModelName = $relationHash['right'];

            if (!isset($relations[$leftModelName])) {
                throw new Exception(sprintf('No relations for model %s', $leftModelName));
            }

            $relations = $this->aujaConfigurator->getRelationsForModel($this->aujaConfigurator->getModel($leftModelName));
            $relationshipExists = false;
            foreach ($relations as $relation) {
                if ($relation->getType() == Relation::BELONGS_TO && $relation->getRight()->getName() == $rightModelName) {
                    $relationshipExists = true;
                }
            }

            if (!$relationshipExists) {
                throw new Exception(sprintf('There is no belongs to relationship between %s and %s.', $leftModelName, $rightModelName));
            }
        }
    }
}