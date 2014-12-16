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
use Label305\AujaLaravel\Config\Model;
use Label305\AujaLaravel\Config\Relation;
use Label305\AujaLaravel\Routing\AujaRouter;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class SingleAssociationShowMenuFactorySpec extends ObjectBehavior {

    /**
     * @var Relation
     */
    private $relation;

    function let(AujaRouter $aujaRouter, Relation $relation, Model $left, Model $right) {
        $this->beConstructedWith($aujaRouter);

        $this->relation = $relation;
        $relation->getLeft()->willReturn($left);
        $relation->getRight()->willReturn($right);
        $relation->getType()->willReturn('hasMany');

        $left->getName()->willReturn('Model');
        $right->getName()->willReturn('OtherModel');

        URL::shouldReceive('route');
        Lang::shouldReceive('trans')->with('Edit')->andReturn('Edit');
        Lang::shouldReceive('trans')->with('Properties')->andReturn('Properties');
        Lang::shouldReceive('trans')->with('OtherModel')->andReturn('OtherModel');
    }

    function it_is_initializable() {
        $this->shouldHaveType('Label305\AujaLaravel\Factory\SingleAssociationShowMenuFactory');
    }

    function it_can_create_a_menu(Relation $relation) {
        $this->create('Model', 1, $relation)->shouldHaveType('Label305\Auja\Menu\Menu');
    }

    function its_created_menu_should_have_an_edit_linkmenuitem_as_a_first_item() {
        $menu = $this->create('Model', 1, $this->relation)->getWrappedObject();
        /* @var Menu $menu */

        if (!($menu->getMenuItems()[0] instanceof LinkMenuItem)) {
            throw new \Exception('Created Menu has no LinkMenuItem as a first item');
        }

        $menuItem = $menu->getMenuItems()[0];
        /* @var $menuItem LinkMenuItem */

        if (strpos($menuItem->getText(), 'Edit') === false) {
            throw new \Exception('Text of LinkMenuItem does not start with \'Edit\'');
        }
    }

    function its_created_menu_should_have_a_spacermenuitem_as_a_second_item() {
        $menu = $this->create('Model', 1, $this->relation)->getWrappedObject();
        /* @var Menu $menu */

        if (!($menu->getMenuItems()[1] instanceof SpacerMenuItem)) {
            throw new \Exception('Created Menu has no SpacerMenuItem as a second item');
        }

        $menuItem = $menu->getMenuItems()[1];
        /* @var $menuItem SpacerMenuItem */

        if ($menuItem->getText() != 'OtherModels') {
            throw new \Exception('Text of SpacerMenuItem does equal \'OtherModels\'');
        }
    }

    function its_created_menu_should_have_an_add_model_linkmenuitem() {
        $menu = $this->create('Model', 1, $this->relation)->getWrappedObject();
        /* @var Menu $menu */

        if (!($menu->getMenuItems()[2] instanceof LinkMenuItem)) {
            throw new \Exception('Created Menu has no LinkMenuItem as a third item');
        }

        $menuItem = $menu->getMenuItems()[2];
        /* @var $menuItem LinkMenuItem */

        if ($menuItem->getText() != 'Add OtherModel') {
            throw new \Exception('Text of LinkMenuItem does equal \'Add OtherModel\'');
        }
    }

    function its_created_menu_should_have_a_resourcemenuitem(){
        $menu = $this->create('Model', 1, $this->relation)->getWrappedObject();
        /* @var Menu $menu */

        if (!($menu->getMenuItems()[3] instanceof ResourceMenuItem)) {
            throw new \Exception('Created Menu has no ResourceMenuItem as a fourth item');
        }
    }
}
