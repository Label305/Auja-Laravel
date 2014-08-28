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

use Label305\AujaLaravel\Repositories\DatabaseRepository;

use \Mockery as m;

require_once 'AujaTestCase.php';

class AujaConfiguratorTest extends AujaTestCase {

    /**
     * @var AujaConfigurator the class under test.
     */
    private $aujaConfigurator;

    /**
     * @var array an array with three mocked Models.
     */


    /**
     * @var DatabaseRepository a mocked DatabaseRepository.
     */
    private $databaseRepository;

    protected function setUp() {
        $this->databaseRepository = m::mock('Label305\AujaLaravel\Repositories\DatabaseRepository');
        $this->aujaConfigurator = new AujaConfigurator($this->databaseRepository);
    }

    public function testInitialState() {
        assertThat($this->aujaConfigurator->getModels(), is(emptyArray()));
        assertThat($this->aujaConfigurator->getRelations(), is(emptyArray()));
    }

} 