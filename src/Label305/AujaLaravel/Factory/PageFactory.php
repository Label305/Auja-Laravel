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

namespace Label305\AujaLaravel\Factory;


use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\URL;
use Label305\Auja\Page\Form;
use Label305\Auja\Page\FormItem\SubmitFormItem;
use Label305\Auja\Page\Page;
use Label305\Auja\Page\PageHeader;
use Label305\Auja\Shared\Button;
use Label305\AujaLaravel\Config\AujaConfigurator;
use Label305\AujaLaravel\Routing\AujaRouter;

class PageFactory {

    /**
     * @var AujaConfigurator
     */
    private $aujaConfigurator;

    /**
     * @var AujaRouter
     */
    private $aujaRouter;

    /**
     * @var FormItemFactory
     */
    private $formItemFactory;

    public function __construct(AujaConfigurator $aujaConfigurator, AujaRouter $aujaRouter, FormItemFactory $formItemFactory) {
        $this->aujaConfigurator = $aujaConfigurator;
        $this->aujaRouter = $aujaRouter;
        $this->formItemFactory = $formItemFactory;
    }

    public function create($modelName, $modelId = 0) {
        $page = new Page();

        $header = new PageHeader();
        $header->setText('Create ' . $modelName);

        if ($modelId != 0) {
            $header->setText('Edit ' . $modelName);
            $deleteButton = new Button();
            $deleteButton->setText(Lang::trans('Delete'));
            $deleteButton->setConfirmationMessage(Lang::trans('Are you sure?'));
            $deleteButton->setTarget(URL::route($this->aujaRouter->getDeleteName($modelName), $modelId));
            $deleteButton->setMethod('delete');
            $header->addButton($deleteButton);
        }

        $page->addPageComponent($header);

        $form = new Form();
        $action = $modelId == 0 ? URL::route($this->aujaRouter->getStoreName($modelName)) : URL::route($this->aujaRouter->getUpdateName($modelName), $modelId);
        $form->setAction($action);
        $form->setMethod($modelId == 0 ? 'POST' : 'PUT');

        $model = $this->aujaConfigurator->getModel($modelName);
        $visibleFields = $this->aujaConfigurator->getVisibleFields($model);
        foreach ($visibleFields as $columnName) {
            $column = $model->getColumn($columnName);
            $item = $this->formItemFactory->getFormItem($column->getType(), false); // TODO: Password?
            $item->setName($column->getName());
            $item->setLabel(Lang::trans($column->getName())); // TODO: 'Human readable name'
            $form->addFormItem($item);
        }

        $submit = new SubmitFormItem();
        $submit->setText(Lang::trans('Submit'));
        $form->addFormItem($submit);

        $page->addPageComponent($form);

        return $page;
    }

} 