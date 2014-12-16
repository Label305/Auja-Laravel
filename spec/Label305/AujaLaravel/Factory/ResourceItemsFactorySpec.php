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

namespace spec\Label305\AujaLaravel\Factory;

use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\URL;
use Label305\Auja\Menu\LinkMenuItem;
use Label305\Auja\Menu\Menu;
use Label305\Auja\Menu\ResourceMenuItem;
use Label305\Auja\Menu\SpacerMenuItem;
use Label305\AujaLaravel\Config\AujaConfigurator;
use Label305\AujaLaravel\Config\Model;
use Label305\AujaLaravel\Config\Relation;
use Label305\AujaLaravel\Routing\AujaRouter;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ResourceIndexFactorySpec extends ObjectBehavior {

    /**
     * @var Relation[]
     */
    private $relations;

    function let(AujaConfigurator $aujaConfigurator, AujaRouter $aujaRouter, Relation $relation, Model $left, Model $right) {
        $this->beConstructedWith($aujaConfigurator, $aujaRouter);

        $this->relations = [$relation];
        $relation->getLeft()->willReturn($left);
        $relation->getRight()->willReturn($right);
        $relation->getType()->willReturn('hasMany');

        $left->getName()->willReturn('Model');
        $right->getName()->willReturn('OtherModel');

        $aujaConfigurator->getModel('Model')->willReturn($left);
        $aujaConfigurator->getRelationsForModel($left)->willReturn([$relation]);
        $aujaConfigurator->getDisplayField($left)->willReturn('name');
        $aujaConfigurator->getIcon($left)->willReturn(null);

        URL::shouldReceive('route');
        Lang::shouldReceive('trans')->with('Add')->andReturn('Add');
        Lang::shouldReceive('trans')->with('Model')->andReturn('Model');
    }

    function it_is_initializable() {
        $this->shouldHaveType('Label305\AujaLaravel\Factory\ResourceIndexFactory');
    }

    function it_can_create_a_resource(){
        $this->create('Model', [] )->shouldHaveType('Label305\Auja\Menu\Resource');
    }

}
