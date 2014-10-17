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
use Label305\AujaLaravel\Routing\AujaRouter;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Config\Definition\Exception\Exception;

class AssociationMenuFactorySpec extends ObjectBehavior {
    /**
     * @var AujaRouter
     */
    private $aujaRouter;

    function let(AujaRouter $aujaRouter) {
        $this->aujaRouter = $aujaRouter;

        $this->beConstructedWith($aujaRouter);

        Url::shouldReceive('route');
        Lang::shouldReceive('trans')->with('Add')->andReturn('Add');
        Lang::shouldReceive('trans')->with('Username')->andReturn('Username');
        Lang::shouldReceive('trans')->with('Association')->andReturn('Association');
        Lang::shouldReceive('trans')->with('Associations')->andReturn('Associations');
        Lang::shouldReceive('trans')->with('Password')->andReturn('Password');
    }

    function it_is_initializable() {
        $this->shouldHaveType('Label305\AujaLaravel\Factory\AssociationMenuFactory');
    }

    function it_can_create_a_menu() {
        $this->create('Name', 1, 'Association')->shouldHaveType('Label305\Auja\Menu\Menu');
    }

    function its_created_menu_should_have_an_add_linkmenuitem_as_a_first_item() {
        $result = $this->create('Name', 1, 'Association');

        $menu = $result->getWrappedObject();
        /* @var $menu Menu */

        if (!($menu->getMenuItems()[0] instanceof LinkMenuItem)) {
            throw new \Exception('First item is not of type LinkMenuItem');
        }

        $item = $menu->getMenuItems()[0];
        /* @var $item LinkMenuItem */
        if (strpos($item->getText(), 'Add') === false) {
            throw new Exception('First item does not contain \'Add\'');
        }
    }

    function its_created_menu_should_have_a_spacermenuitem_as_a_second_item() {
        $result = $this->create('Name', 1, 'Association');

        $menu = $result->getWrappedObject();
        /* @var $menu Menu */

        if (!($menu->getMenuItems()[1] instanceof SpacerMenuItem)) {
            throw new \Exception('Second item is not of type SpacerMenuItem');
        }
    }

    function its_created_menu_should_have_a_resourcemenuitem_as_a_third_item() {
        $result = $this->create('Name', 1, 'Association');

        $menu = $result->getWrappedObject();
        /* @var $menu Menu */

        if (!($menu->getMenuItems()[2] instanceof ResourceMenuItem)) {
            throw new \Exception('Second item is not of type ResourceMenuItem');
        }
    }
}
