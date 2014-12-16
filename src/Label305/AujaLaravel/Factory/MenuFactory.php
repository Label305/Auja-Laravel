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
use Label305\Auja\Icons;
use Label305\Auja\Menu\LinkMenuItem;
use Label305\Auja\Menu\Menu;
use Label305\Auja\Menu\Property\Searchable;
use Label305\Auja\Menu\ResourceMenuItem;
use Label305\Auja\Menu\SpacerMenuItem;
use Label305\AujaLaravel\Config\AujaConfigurator;
use Label305\AujaLaravel\Config\ModelConfig;
use Label305\AujaLaravel\Routing\AujaRouter;

class MenuFactory {

    /**
     * @var AujaRouter
     */
    private $aujaRouter;

    /**
     * @var ResourceItemFactory
     */
    private $resourceItemFactory;

    public function __construct(ResourceItemFactory $resourceItemFactory, AujaRouter $aujaRouter) {

        $this->aujaRouter = $aujaRouter;
        $this->resourceItemFactory = $resourceItemFactory;
    }

    /**
     * Builds a simple menu for given model, where typically this model should not have any relations to other models.
     *
     * The menu will include:
     *  - An Add LinkMenuItem;
     *  - A SpacerMenuItem with the model's name;
     *  - A ResourceMenuItem to hold entries of the model.
     *
     * @param String      $modelName The name of the model.
     * @param ModelConfig $config    (optional) The `ModelConfig` to use.
     *
     * @return Menu the Menu, which can be configured further.
     */
    public function create($modelName, ModelConfig $config = null) {

        $menu = new Menu();

        $addMenuItem = new LinkMenuItem();
        $addMenuItem->setText(Lang::trans('Add'));
        $addMenuItem->setIcon(Icons::ion_plus);
        $addMenuItem->setTarget(URL::route($this->aujaRouter->getCreateName($modelName)));
        $menu->addMenuItem($addMenuItem);

        $spacerMenuItem = new SpacerMenuItem();
        $spacerMenuItem->setText(Lang::trans($modelName));
        $menu->addMenuItem($spacerMenuItem);

        $menu->addMenuItem(
            $this->resourceItemFactory->create($modelName, $config)
        );

        return $menu;
    }
}