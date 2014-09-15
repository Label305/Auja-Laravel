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

use Illuminate\Foundation\Application;
use Label305\AujaLaravel\Database\DatabaseHelper;
use Label305\AujaLaravel\Logging\Logger;
use \Mockery as m;

require_once 'AujaTestCase.php';

class AujaConfiguratorTest extends AujaTestCase {

    /**
     * @var AujaConfigurator the class under test.
     */
    private $aujaConfigurator;

    /**
     * @var Model
     */
    private $countryModel;

    /**
     * @var Model
     */
    private $clubModel;

    /**
     * @var Model
     */
    private $clubHouseModel;

    /**
     * @var Model
     */
    private $teamModel;

    /**
     * @var Model
     */
    private $matchModel;

    /**
     * @var DatabaseHelper A mocked DatabaseHelper.
     */
    private $databaseHelper;

    /**
     * @var Logger A mocked Logger.
     */
    private $logger;

    /**
     * @var Application A mocked Application.
     */
    private $application;

    protected function setUp() {
        $this->application = m::mock('Illuminate\Foundation\Application');
        $this->databaseHelper = m::mock('Label305\AujaLaravel\Database\DatabaseHelper');
        $this->logger = self::mockLogger();

        $this->countryModel = m::mock('Label305\AujaLaravel\Model');
        $this->countryModel->shouldReceive('getName')->andReturn('Country');
        $this->clubModel = m::mock('Label305\AujaLaravel\Model');
        $this->clubModel->shouldReceive('getName')->andReturn('Club');
        $this->teamModel = m::mock('Label305\AujaLaravel\Model');
        $this->teamModel->shouldReceive('getName')->andReturn('Team');
        $this->clubHouseModel = m::mock('Label305\AujaLaravel\Model');
        $this->clubHouseModel->shouldReceive('getName')->andReturn('ClubHouse');
        $this->matchModel = m::mock('Label305\AujaLaravel\Model');
        $this->matchModel->shouldReceive('getName')->andReturn('Match');

        $this->aujaConfigurator = new AujaConfigurator($this->application, $this->databaseHelper, $this->logger);
    }

    public function testInitialState() {
        assertThat($this->aujaConfigurator->getModels(), is(emptyArray()));
        assertThat($this->aujaConfigurator->getRelations(), is(emptyArray()));

        assertThat($this->aujaConfigurator->getRelationsForModel($this->clubModel), is(emptyArray()));
    }

    public function testConfigureSimpleSingleModel() {
        /* Given there is a table for 'Club' */
        $this->databaseHelper->shouldReceive('hasTable')->times(1)->with('clubs')->andReturn(true);
        $this->databaseHelper->shouldReceive('getColumnListing')->times(1)->with('clubs')->andReturn(array('id', 'name'));
        $this->databaseHelper->shouldReceive('getColumnType')->times(1)->with('clubs', 'id')->andReturn("integer");
        $this->databaseHelper->shouldReceive('getColumnType')->times(1)->with('clubs', 'name')->andReturn("string");

        /* When we configure with Club */
        $this->aujaConfigurator->configure(array('Club'));

        /* We want a configuration with no relationships and exactly one model. */
        assertThat($this->aujaConfigurator->getModels(), is(arrayWithSize(1)));
        assertThat($this->aujaConfigurator->getRelations(), is(arrayWithSize(1)));
        assertThat($this->aujaConfigurator->getRelations()['Club'], is(emptyArray()));
        assertThat($this->aujaConfigurator->getRelationsForModel($this->clubModel), is(emptyArray()));
    }

    /**
     * Tests configuration between two models Club and Team, where a Team belongs to a Club.
     */
    public function testConfigure_simpleBelongsTo() {
        /* Given there are tables for Club and Team */
        $this->databaseHelper->shouldReceive('hasTable')->times(1)->with('clubs')->andReturn(true);
        $this->databaseHelper->shouldReceive('hasTable')->times(1)->with('teams')->andReturn(true);
        $this->databaseHelper->shouldReceive('hasTable')->times(1)->with('club_team')->andReturn(false);
        $this->databaseHelper->shouldReceive('getColumnListing')->times(1)->with('clubs')->andReturn(array('id', 'name'));
        $this->databaseHelper->shouldReceive('getColumnListing')->times(1)->with('teams')->andReturn(array('id', 'name', 'club_id'));
        $this->databaseHelper->shouldReceive('getColumnType')->times(1)->with('clubs', 'id')->andReturn("integer");
        $this->databaseHelper->shouldReceive('getColumnType')->times(1)->with('clubs', 'name')->andReturn("string");
        $this->databaseHelper->shouldReceive('getColumnType')->times(1)->with('teams', 'id')->andReturn("integer");
        $this->databaseHelper->shouldReceive('getColumnType')->times(1)->with('teams', 'name')->andReturn("string");
        $this->databaseHelper->shouldReceive('getColumnType')->times(1)->with('teams', 'club_id')->andReturn("integer");

        /* When we configure with Club and Team */
        $this->aujaConfigurator->configure(array('Club', 'Team'));

        /* We want a configuration with:
         *  - Two models
         *  - A belongs to relationship between Team and Club
         *  - A has many relationship between Club and Team
         */
        assertThat($this->aujaConfigurator->getModels(), is(arrayWithSize(2)));
        assertThat($this->aujaConfigurator->getRelations(), is(arrayWithSize(2)));

        assertThat($this->aujaConfigurator->getRelations()['Club'], is(arrayWithSize(1)));
        assertThat($this->aujaConfigurator->getRelations()['Team'], is(arrayWithSize(1)));

        assertThat($this->aujaConfigurator->getRelationsForModel($this->clubModel), is(arrayWithSize(1)));
        assertThat($this->aujaConfigurator->getRelationsForModel($this->teamModel), is(arrayWithSize(1)));

        $clubRelation = $this->aujaConfigurator->getRelationsForModel($this->clubModel)[0];
        assertThat($clubRelation->getLeft()->getName(), equalTo($this->clubModel->getName()));
        assertThat($clubRelation->getRight()->getName(), equalTo($this->teamModel->getName()));
        assertThat($clubRelation->getType(), is(Relation::HAS_MANY));

        $teamRelation = $this->aujaConfigurator->getRelationsForModel($this->teamModel)[0];
        assertThat($teamRelation->getLeft()->getName(), equalTo($this->teamModel->getName()));
        assertThat($teamRelation->getRight()->getName(), equalTo($this->clubModel->getName()));
        assertThat($teamRelation->getType(), is(Relation::BELONGS_TO));
    }


    /**
     * Tests configuration between three models Country, Club and Team,
     * where a Team belongs to a Club, and Club belongs to a Country.
     */
    public function testConfigure_transitiveBelongsTo() {
        /* Given there are tables for Country, Club and Team */
        $this->databaseHelper->shouldReceive('hasTable')->times(1)->with('countries')->andReturn(true);
        $this->databaseHelper->shouldReceive('hasTable')->times(1)->with('clubs')->andReturn(true);
        $this->databaseHelper->shouldReceive('hasTable')->times(1)->with('teams')->andReturn(true);
        $this->databaseHelper->shouldReceive('hasTable')->times(1)->with('club_country')->andReturn(false);
        $this->databaseHelper->shouldReceive('hasTable')->times(1)->with('club_team')->andReturn(false);
        $this->databaseHelper->shouldReceive('hasTable')->times(1)->with('country_team')->andReturn(false);
        $this->databaseHelper->shouldReceive('getColumnListing')->times(1)->with('countries')->andReturn(array('id', 'name'));
        $this->databaseHelper->shouldReceive('getColumnListing')->times(1)->with('clubs')->andReturn(array('id', 'name', 'country_id'));
        $this->databaseHelper->shouldReceive('getColumnListing')->times(1)->with('teams')->andReturn(array('id', 'name', 'club_id'));
        $this->databaseHelper->shouldReceive('getColumnType')->times(1)->with('countries', 'id')->andReturn("integer");
        $this->databaseHelper->shouldReceive('getColumnType')->times(1)->with('countries', 'name')->andReturn("string");
        $this->databaseHelper->shouldReceive('getColumnType')->times(1)->with('clubs', 'id')->andReturn("integer");
        $this->databaseHelper->shouldReceive('getColumnType')->times(1)->with('clubs', 'name')->andReturn("string");
        $this->databaseHelper->shouldReceive('getColumnType')->times(1)->with('clubs', 'country_id')->andReturn("integer");
        $this->databaseHelper->shouldReceive('getColumnType')->times(1)->with('teams', 'id')->andReturn("integer");
        $this->databaseHelper->shouldReceive('getColumnType')->times(1)->with('teams', 'name')->andReturn("string");
        $this->databaseHelper->shouldReceive('getColumnType')->times(1)->with('teams', 'club_id')->andReturn("integer");

        /* When we configure with Country, Club and Team */
        $this->aujaConfigurator->configure(array('Country', 'Club', 'Team'));

        /* We want a configuration with:
         *  - Three models
         *  - A belongs to relationship between Club and Country
         *  - A has many relationship between Country and Club
         *  - A belongs to relationship between Team and Club
         *  - A has many relationship between Club and Team
         */
        assertThat($this->aujaConfigurator->getModels(), is(arrayWithSize(3)));
        assertThat($this->aujaConfigurator->getRelations(), is(arrayWithSize(3)));

        assertThat($this->aujaConfigurator->getRelations()['Country'], is(arrayWithSize(1)));
        assertThat($this->aujaConfigurator->getRelations()['Club'], is(arrayWithSize(2)));
        assertThat($this->aujaConfigurator->getRelations()['Team'], is(arrayWithSize(1)));

        assertThat($this->aujaConfigurator->getRelationsForModel($this->countryModel), is(arrayWithSize(1)));
        assertThat($this->aujaConfigurator->getRelationsForModel($this->clubModel), is(arrayWithSize(2)));
        assertThat($this->aujaConfigurator->getRelationsForModel($this->teamModel), is(arrayWithSize(1)));

        $countryRelation = $this->aujaConfigurator->getRelationsForModel($this->countryModel)[0];
        assertThat($countryRelation->getLeft()->getName(), equalTo($this->countryModel->getName()));
        assertThat($countryRelation->getRight()->getName(), equalTo($this->clubModel->getName()));
        assertThat($countryRelation->getType(), is(Relation::HAS_MANY));

        $clubCountryRelation = $this->aujaConfigurator->getRelationsForModel($this->clubModel)[0];
        assertThat($clubCountryRelation->getLeft()->getName(), equalTo($this->clubModel->getName()));
        assertThat($clubCountryRelation->getRight()->getName(), equalTo($this->countryModel->getName()));
        assertThat($clubCountryRelation->getType(), is(Relation::BELONGS_TO));

        $clubTeamRelation = $this->aujaConfigurator->getRelationsForModel($this->clubModel)[1];
        assertThat($clubTeamRelation->getLeft()->getName(), equalTo($this->clubModel->getName()));
        assertThat($clubTeamRelation->getRight()->getName(), equalTo($this->teamModel->getName()));
        assertThat($clubTeamRelation->getType(), is(Relation::HAS_MANY));

        $teamRelation = $this->aujaConfigurator->getRelationsForModel($this->teamModel)[0];
        assertThat($teamRelation->getLeft()->getName(), equalTo($this->teamModel->getName()));
        assertThat($teamRelation->getRight()->getName(), equalTo($this->clubModel->getName()));
        assertThat($teamRelation->getType(), is(Relation::BELONGS_TO));
    }

    public function testConfigure_doubleBelongsTo() {
        /* Given there are tables for Club, ClubHouse and Team */
        $this->databaseHelper->shouldReceive('hasTable')->times(1)->with('clubs')->andReturn(true);
        $this->databaseHelper->shouldReceive('hasTable')->times(1)->with('club_houses')->andReturn(true);
        $this->databaseHelper->shouldReceive('hasTable')->times(1)->with('teams')->andReturn(true);
        $this->databaseHelper->shouldReceive('hasTable')->times(1)->with('club_clubhouse')->andReturn(false);
        $this->databaseHelper->shouldReceive('hasTable')->times(1)->with('club_team')->andReturn(false);
        $this->databaseHelper->shouldReceive('hasTable')->times(1)->with('clubhouse_team')->andReturn(false);
        $this->databaseHelper->shouldReceive('getColumnListing')->times(1)->with('clubs')->andReturn(array('id', 'name'));
        $this->databaseHelper->shouldReceive('getColumnListing')->times(1)->with('club_houses')->andReturn(array('id', 'name', 'club_id'));
        $this->databaseHelper->shouldReceive('getColumnListing')->times(1)->with('teams')->andReturn(array('id', 'name', 'club_id'));
        $this->databaseHelper->shouldReceive('getColumnType')->times(1)->with('clubs', 'id')->andReturn("integer");
        $this->databaseHelper->shouldReceive('getColumnType')->times(1)->with('clubs', 'name')->andReturn("string");
        $this->databaseHelper->shouldReceive('getColumnType')->times(1)->with('club_houses', 'id')->andReturn("integer");
        $this->databaseHelper->shouldReceive('getColumnType')->times(1)->with('club_houses', 'name')->andReturn("string");
        $this->databaseHelper->shouldReceive('getColumnType')->times(1)->with('club_houses', 'club_id')->andReturn("integer");
        $this->databaseHelper->shouldReceive('getColumnType')->times(1)->with('teams', 'id')->andReturn("integer");
        $this->databaseHelper->shouldReceive('getColumnType')->times(1)->with('teams', 'name')->andReturn("string");
        $this->databaseHelper->shouldReceive('getColumnType')->times(1)->with('teams', 'club_id')->andReturn("integer");

        /* When we configure with Club, ClubHouse and Team */
        $this->aujaConfigurator->configure(array('Club', 'ClubHouse', 'Team'));

        /* We want a configuration with:
         *  - Three models
         *  - A belongs to relationship between ClubHouse and Club
         *  - A has many relationship between Club and ClubHouse
         *  - A belongs to relationship between Team and Club
         *  - A has many relationship between Club and Team
         */
        assertThat($this->aujaConfigurator->getModels(), is(arrayWithSize(3)));
        assertThat($this->aujaConfigurator->getRelations(), is(arrayWithSize(3)));

        assertThat($this->aujaConfigurator->getRelations()['Club'], is(arrayWithSize(2)));
        assertThat($this->aujaConfigurator->getRelations()['ClubHouse'], is(arrayWithSize(1)));
        assertThat($this->aujaConfigurator->getRelations()['Team'], is(arrayWithSize(1)));

        assertThat($this->aujaConfigurator->getRelationsForModel($this->clubModel), is(arrayWithSize(2)));
        assertThat($this->aujaConfigurator->getRelationsForModel($this->clubHouseModel), is(arrayWithSize(1)));
        assertThat($this->aujaConfigurator->getRelationsForModel($this->teamModel), is(arrayWithSize(1)));

        $clubHouseClubRelation = $this->aujaConfigurator->getRelationsForModel($this->clubHouseModel)[0];
        assertThat($clubHouseClubRelation->getLeft()->getName(), equalTo($this->clubHouseModel->getName()));
        assertThat($clubHouseClubRelation->getRight()->getName(), equalTo($this->clubModel->getName()));
        assertThat($clubHouseClubRelation->getType(), is(Relation::BELONGS_TO));

        $clubClubHouseRelation = $this->aujaConfigurator->getRelationsForModel($this->clubModel)[0];
        assertThat($clubClubHouseRelation->getLeft()->getName(), equalTo($this->clubModel->getName()));
        assertThat($clubClubHouseRelation->getRight()->getName(), equalTo($this->clubHouseModel->getName()));
        assertThat($clubClubHouseRelation->getType(), is(Relation::HAS_MANY));

        $clubTeamRelation = $this->aujaConfigurator->getRelationsForModel($this->clubModel)[1];
        assertThat($clubTeamRelation->getLeft()->getName(), equalTo($this->clubModel->getName()));
        assertThat($clubTeamRelation->getRight()->getName(), equalTo($this->teamModel->getName()));
        assertThat($clubTeamRelation->getType(), is(Relation::HAS_MANY));

        $teamRelation = $this->aujaConfigurator->getRelationsForModel($this->teamModel)[0];
        assertThat($teamRelation->getLeft()->getName(), equalTo($this->teamModel->getName()));
        assertThat($teamRelation->getRight()->getName(), equalTo($this->clubModel->getName()));
        assertThat($teamRelation->getType(), is(Relation::BELONGS_TO));
    }

    private static function mockLogger() {
        $result = m::mock('Label305\AujaLaravel\Logging\Logger');
        $result->shouldReceive('debug');
        $result->shouldReceive('info');
        $result->shouldReceive('warn');
        return $result;
    }
}