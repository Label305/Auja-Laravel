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

class NoAssociationsIndexMenuFactorySpec extends ObjectBehavior {

    function let(AujaConfigurator $aujaConfigurator, AujaRouter $aujaRouter, Model $model) {
        $this->beConstructedWith($aujaConfigurator, $aujaRouter);

        URL::shouldReceive('route');
        Lang::shouldReceive('trans')->with('Add')->andReturn('Add');
        Lang::shouldReceive('trans')->with('Model')->andReturn('Model');

        $aujaConfigurator->getModel('Model')->willReturn($model);
        $aujaConfigurator->isSearchable($model, null)->willReturn(false);
    }

    function it_is_initializable() {
        $this->shouldHaveType('Label305\AujaLaravel\Factory\NoAssociationsIndexMenuFactory');
    }

    function it_can_create_a_menu() {
        $this->create('Model')->shouldHaveType('Label305\Auja\Menu\Menu');
    }

    function its_created_menu_has_an_add_LinkMenuItem_as_a_first_item() {
        $menu = $this->create('Model')->getWrappedObject();
        /* @var Menu $menu */

        if (!($menu->getMenuItems()[0] instanceof LinkMenuItem)) {
            throw new \Exception('Created Menu has no LinkMenuItem as a first item');
        }

        $menuItem = $menu->getMenuItems()[0];
        /* @var $menuItem LinkMenuItem */

        if (strpos($menuItem->getText(), 'Add') === false) {
            throw new \Exception('Text of LinkMenuItem does not start with \'Add\'');
        }
    }

    function its_created_menu_has_a_SpacerMenuItem_as_a_second_item() {
        $menu = $this->create('Model')->getWrappedObject();
        /* @var Menu $menu */

        if (!($menu->getMenuItems()[1] instanceof SpacerMenuItem)) {
            throw new \Exception('Created Menu has no SpacerMenuItem as a second item');
        }

        $menuItem = $menu->getMenuItems()[1];
        /* @var $menuItem SpacerMenuItem */

        if ($menuItem->getText() != 'Model') {
            throw new \Exception('Text of SpacerMenuItem does equal \'Model\'');
        }
    }

    function its_created_menu_has_a_ResourceMenuItem_as_a_third_item() {
        $menu = $this->create('Model')->getWrappedObject();
        /* @var Menu $menu */

        if (!($menu->getMenuItems()[2] instanceof ResourceMenuItem)) {
            throw new \Exception('Created Menu has no ResourceMenuItem as a second item');
        }
    }
}
