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
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class FormItemFactorySpec extends ObjectBehavior {


    function it_is_initializable() {
        $this->shouldHaveType('Label305\AujaLaravel\Factory\FormItemFactory');
    }

    function it_can_return_a_passwordformitem() {
        $this->getFormItem('type', true)->shouldHaveType('Label305\Auja\Page\FormItem\PasswordFormItem');
    }

    function it_can_return_a_textareaformitem() {
        $this->getFormItem(Type::TEXT, false)->shouldHaveType('Label305\Auja\Page\FormItem\TextAreaFormItem');
        $this->getFormItem(Type::TARRAY, false)->shouldHaveType('Label305\Auja\Page\FormItem\TextAreaFormItem');
        $this->getFormItem(Type::SIMPLE_ARRAY, false)->shouldHaveType('Label305\Auja\Page\FormItem\TextAreaFormItem');
        $this->getFormItem(Type::JSON_ARRAY, false)->shouldHaveType('Label305\Auja\Page\FormItem\TextAreaFormItem');
        $this->getFormItem(Type::OBJECT, false)->shouldHaveType('Label305\Auja\Page\FormItem\TextAreaFormItem');
        $this->getFormItem(Type::BLOB, false)->shouldHaveType('Label305\Auja\Page\FormItem\TextAreaFormItem');
    }

    function it_can_return_an_integerformitem() {
        $this->getFormItem(Type::INTEGER, false)->shouldHaveType('Label305\Auja\Page\FormItem\IntegerFormItem');
        $this->getFormItem(Type::SMALLINT, false)->shouldHaveType('Label305\Auja\Page\FormItem\IntegerFormItem');
        $this->getFormItem(Type::BIGINT, false)->shouldHaveType('Label305\Auja\Page\FormItem\IntegerFormItem');
    }

    function it_can_return_a_numberformitem() {
        $this->getFormItem(Type::DECIMAL, false)->shouldHaveType('Label305\Auja\Page\FormItem\NumberFormItem');
        $this->getFormItem(Type::FLOAT, false)->shouldHaveType('Label305\Auja\Page\FormItem\NumberFormItem');
    }

    function it_can_return_a_checkboxformitem() {
        $this->getFormItem(Type::BOOLEAN, false)->shouldHaveType('Label305\Auja\Page\FormItem\CheckboxFormItem');
    }

    function it_can_return_a_dateformitem() {
        $this->getFormItem(Type::DATE, false)->shouldHaveType('Label305\Auja\Page\FormItem\DateFormItem');
    }

    function it_can_return_a_datetimeformitem() {
        $this->getFormItem(Type::DATETIME, false)->shouldHaveType('Label305\Auja\Page\FormItem\DateTimeFormItem');
        $this->getFormItem(Type::DATETIMETZ, false)->shouldHaveType('Label305\Auja\Page\FormItem\DateTimeFormItem');
    }

    function it_can_return_a_timeformitem() {
        $this->getFormItem(Type::TIME, false)->shouldHaveType('Label305\Auja\Page\FormItem\TimeFormItem');
    }

    function it_can_return_a_textformitem() {
        $this->getFormItem(Type::STRING, false)->shouldHaveType('Label305\Auja\Page\FormItem\TextFormItem');
        $this->getFormItem(Type::GUID, false)->shouldHaveType('Label305\Auja\Page\FormItem\TextFormItem');
        $this->getFormItem('someunknowntype', false)->shouldHaveType('Label305\Auja\Page\FormItem\TextFormItem');
    }
}
