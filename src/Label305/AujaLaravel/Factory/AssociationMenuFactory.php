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
use Label305\Auja\Menu\LinkMenuItem;
use Label305\Auja\Menu\Menu;
use Label305\Auja\Menu\ResourceMenuItem;
use Label305\Auja\Menu\SpacerMenuItem;
use Label305\AujaLaravel\Config\ModelConfig;
use Label305\AujaLaravel\Routing\AujaRouter;

class AssociationMenuFactory {

    /**
     * @var AujaRouter
     */
    private $aujaRouter;

    public function __construct(AujaRouter $aujaRouter) {
        $this->aujaRouter = $aujaRouter;
    }

    /**
     * Builds a menu for displaying associated items to a model entry (i.e. /club/21/team).
     *
     * The menu will include:
     *  - An Add LinkMenuItem;
     *  - A SpacerMenuItem with the name of the associated model;
     *  - A ResourceMenuItem to hold entries of the associated model.
     *
     * @param String      $modelName       The name of the model (i.e. Club).
     * @param int         $modelId         The id of the model entry.
     * @param String      $associationName The name of the associated model (i.e. Team).
     * @param ModelConfig $config          (optional) The `ModelConfig` to use.
     *
     * @return Menu the Menu, which can be configured further.
     */
    public function create($modelName, $modelId, $associationName, ModelConfig $config = null) {
        $menu = new Menu();

        $addMenuItem = new LinkMenuItem();
        $addMenuItem->setText(Lang::trans('Add') . ' ' . Lang::trans($associationName));
        $addMenuItem->setTarget(Url::route($this->aujaRouter->getCreateAssociationName($modelName, $associationName), $modelId));
        $menu->addMenuItem($addMenuItem);

        $headerMenuItem = new SpacerMenuItem();
        $headerMenuItem->setText(Lang::trans(str_plural($associationName)));
        $menu->addMenuItem($headerMenuItem);

        $resourceMenuItem = new ResourceMenuItem();
        $resourceMenuItem->setTarget(Url::route($this->aujaRouter->getAssociationName($modelName, $associationName), $modelId));
        $menu->addMenuItem($resourceMenuItem);

        return $menu;
    }

} 