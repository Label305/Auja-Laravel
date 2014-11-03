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

use Doctrine\DBAL\Types\Type;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Label305\Auja\Main\Main;
use Label305\Auja\Page\Form;
use Label305\AujaLaravel\Config\AujaConfigurator;
use Label305\AujaLaravel\Config\Model;
use Label305\AujaLaravel\Routing\AujaRouter;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class MainFactorySpec extends ObjectBehavior {

    function let(AujaConfigurator $aujaConfigurator, AujaRouter $aujaRouter) {
        $this->beConstructedWith($aujaConfigurator, $aujaRouter);

        $model = new Model('Model');
        $aujaConfigurator->getModels()->willReturn([$model]);
        $aujaConfigurator->getIcon($model, null)->willReturn('Icon');
        $aujaConfigurator->shouldIncludeInMain($model, null)->willReturn(true);
    }

    function it_is_initializable() {
        $this->shouldHaveType('Label305\AujaLaravel\Factory\MainFactory');
    }

    function it_can_create_a_main() {
        URL::shouldReceive('route');

        $this->create('Title')->shouldHaveType('Label305\Auja\Main\Main');
    }

    function its_created_main_has_a_proper_title(Form $form) {
        URL::shouldReceive('route');

        $main = $this->create('Title', 'Username', 'target', $form)->getWrappedObject();
        /* @var $main Main */

        if ($main->getTitle() != 'Title') {
            throw new \Exception('Created Main has wrong title');
        }
    }

    function its_created_main_has_a_proper_username(Form $form) {
        URL::shouldReceive('route');

        $main = $this->create('Title', 'Username', 'target', $form)->getWrappedObject();
        /* @var $main Main */

        if ($main->getUsername() != 'Username') {
            throw new \Exception('Created Main has wrong Username');
        }
    }

    function its_created_main_has_a_proper_logoutbutton(Form $form) {
        URL::shouldReceive('route');

        $main = $this->create('Title', 'Username', 'target', $form)->getWrappedObject();
        /* @var $main Main */

        if ($main->getButtons()[0]->getTarget() != 'target') {
            throw new \Exception('Created Main has wrong logout target');
        }
    }

    function its_created_main_has_proper_models(Form $form){
        URL::shouldReceive('route');

        $main = $this->create('Title', 'Username', 'target', $form)->getWrappedObject();
        /* @var $main Main */

        if(count($main->getItems()) != 1){
            throw new \Exception('Created Main has wrong number of items');
        }

        if($main->getItems()[0]->getTitle() != 'Model'){
            throw new \Exception('Created Main has wrong title for item');
        }
    }

    function its_created_main_has_a_proper_authentication_form(){
        URL::shouldReceive('route');

        $form = new Form();

        $main = $this->create('Title', 'Username', 'target', $form)->getWrappedObject();
        /* @var $main Main */

        if($main->getAuthenticationForm() != $form){
            throw new \Exception('Created Main has wrong authentication form');
        }
    }
}
