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


use Label305\Auja\Page\Form;
use Label305\Auja\Page\Page;
use Label305\Auja\Page\PageHeader;
use Label305\AujaLaravel\Config\AujaConfigurator;
use Label305\AujaLaravel\FormItemFactory;

class PageFactory {

    /**
     * @var AujaConfigurator
     */
    private $aujaConfigurator;

    public function __construct(AujaConfigurator $aujaConfigurator) {
        $this->aujaConfigurator = $aujaConfigurator;
    }

    public function create($modelName, $modelId = 0) {
        $page = new Page();

        $header = new PageHeader();
        $header->setText('Create ' . $modelName);
        $page->addPageComponent($header);

        $form = new Form();
//        $form->setAction(sprintf('/%s%s', self::toUrlName($modelName), $modelId == 0 ? '' : '/' . $modelId)); // TODO: proper target
        $form->setMethod($modelId == 0 ? 'POST' : 'PUT');

        $instance = new $modelName;
        $fillable = $instance->getFillable(); // TODO: other stuff (hidden?)
        /* @var $fillable String[] */
        $hidden = $instance->getHidden();
        /* @var $hidden String[] */

        $model = $this->aujaConfigurator->getModel($modelName);
        foreach ($fillable as $columnName) {
            $column = $model->getColumn($columnName);
            $item = FormItemFactory::getFormItem($column->getType(), in_array($columnName, $hidden));
            $item->setName($column->getName());
//            $item->setLabel(self::toHumanReadableName($column->getName())); // TODO: proper human readable names
            $form->addItem($item);
        }

        $page->addPageComponent($form);

        return $page;
    }

} 