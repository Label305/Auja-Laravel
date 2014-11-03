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
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\URL;
use Label305\Auja\Page\FormItem\TextFormItem;
use Label305\Auja\Page\Page;
use Label305\Auja\Page\PageHeader;
use Label305\AujaLaravel\Config\AujaConfigurator;
use Label305\AujaLaravel\Config\Column;
use Label305\AujaLaravel\Config\Model;
use Label305\AujaLaravel\Factory\FormItemFactory;
use Label305\AujaLaravel\Routing\AujaRouter;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Config\Definition\Exception\Exception;

class PageFactorySpec extends ObjectBehavior {

    private $visibleFields = ['field1', 'field2'];

    function let(AujaConfigurator $aujaConfigurator, AujaRouter $aujaRouter, FormItemFactory $formItemFactory, TextFormItem $formItem, Model $model, Column $column1, Column $column2) {
        $this->beConstructedWith($aujaConfigurator, $aujaRouter, $formItemFactory);

        $aujaConfigurator->getModel('MyModel')->willReturn($model);
        $aujaConfigurator->getVisibleFields($model)->willReturn($this->visibleFields);

        $formItemFactory->getFormItem($column1, null)->willReturn($formItem);
        $formItemFactory->getFormItem($column2, null)->willReturn($formItem);

        $model->getColumn('field1')->willReturn($column1);
        $column1->getName()->willReturn('field1');
        $column1->getType()->willReturn(Type::STRING);

        $model->getColumn('field2')->willReturn($column2);
        $column2->getName()->willReturn('field2');
        $column2->getType()->willReturn(Type::STRING);

        Lang::shouldReceive('trans')->with('field1')->andReturn('field1');
        Lang::shouldReceive('trans')->with('field2')->andReturn('field2');
        Lang::shouldReceive('trans')->with('Submit')->andReturn('Submit');
        Lang::shouldReceive('trans')->with('Delete')->andReturn('Delete');
        Lang::shouldReceive('trans')->with('Are you sure?')->andReturn('Are you sure?');

        URL::shouldReceive('route');
    }

    function it_is_initializable() {
        $this->shouldHaveType('Label305\AujaLaravel\Factory\PageFactory');
    }

    function it_can_create_a_page() {
        $this->create('MyModel')->shouldHaveType('Label305\Auja\Page\Page');
    }

    function it_can_create_an_add_page() {
        $page = $this->create('MyModel')->getWrappedObject();
        /* @var $page Page */

        $headerComponent = $page->getPageComponents()[0];
        if (!($headerComponent instanceof PageHeader)) {
            throw new Exception('First item is not an instance of PageHeader');
        }

        /* @var $headerComponent PageHeader */
        if (strpos($headerComponent->getText(), 'Create') !== 0) {
            throw new Exception('Header text does not start with \'Create\'');
        }
    }

    // TODO: Test more properties of the created Page.
}