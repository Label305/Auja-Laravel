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

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class FormItemFactorySpec extends ObjectBehavior {


    function it_is_initializable() {
        $this->shouldHaveType('Label305\AujaLaravel\Factory\FormItemFactory');
    }


//    function it_can_return_a_passwordformitem(){
//        $this->getFormItem('type', true)->shouldHaveType('Label305\Auja\Page\FormItem\PasswordFormItem');
//    }
}
