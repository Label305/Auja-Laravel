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
    private $clubModel;

    /**
     * @var Model
     */
    private $teamModel;

    /**
     * @var Model
     */
    private $matchModel;

    /**
     * @var DatabaseRepository a mocked DatabaseRepository.
     */
    private $databaseRepository;

    protected function setUp() {
        $this->databaseRepository = m::mock('Label305\AujaLaravel\Repositories\DatabaseRepository');

        $this->clubModel = m::mock('Label305\AujaLaravel\Model');
        $this->clubModel->shouldReceive('getName')->andReturn('Club');
        $this->teamModel = m::mock('Label305\AujaLaravel\Model');
        $this->teamModel->shouldReceive('getName')->andReturn('Team');
        $this->matchModel = m::mock('Label305\AujaLaravel\Model');
        $this->matchModel->shouldReceive('getName')->andReturn('Match');

        $this->aujaConfigurator = new AujaConfigurator($this->databaseRepository);
    }

    public function testInitialState() {
        assertThat($this->aujaConfigurator->getModels(), is(emptyArray()));
        assertThat($this->aujaConfigurator->getRelations(), is(emptyArray()));

        assertThat($this->aujaConfigurator->getRelationsForModel($this->clubModel), is(emptyArray()));
    }

    public function testConfigureSimpleSingleModel() {
        /* Given there is a table for 'Club' */
        $this->databaseRepository->shouldReceive('hasTable')->times(1)->with('clubs')->andReturn(true);
        $this->databaseRepository->shouldReceive('getColumnListing')->times(1)->with('clubs')->andReturn(array('id', 'name'));

        /* When we configure with Club */
        $this->aujaConfigurator->configure(array('Club'));

        /* We want a configuration with no relationships and exactly one model. */
        assertThat($this->aujaConfigurator->getModels(), is(arrayWithSize(1)));
        assertThat($this->aujaConfigurator->getRelations(), is(arrayWithSize(1)));
        assertThat($this->aujaConfigurator->getRelations()['Club'], is(emptyArray()));
        assertThat($this->aujaConfigurator->getRelationsForModel($this->clubModel), is(emptyArray()));
    }

    public function testConfigureSimpleBelongsTo() {
        /* Given there are tables for Club and Team */
        $this->databaseRepository->shouldReceive('hasTable')->times(1)->with('clubs')->andReturn(true);
        $this->databaseRepository->shouldReceive('hasTable')->times(1)->with('teams')->andReturn(true);
        $this->databaseRepository->shouldReceive('hasTable')->times(1)->with('club_team')->andReturn(false);
        $this->databaseRepository->shouldReceive('getColumnListing')->times(1)->with('clubs')->andReturn(array('id', 'name'));
        $this->databaseRepository->shouldReceive('getColumnListing')->times(1)->with('teams')->andReturn(array('id', 'name', 'club_id'));

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

} 