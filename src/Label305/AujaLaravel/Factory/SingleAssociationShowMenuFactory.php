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
use Label305\Auja\Menu\ResourceMenuItem;
use Label305\Auja\Menu\SpacerMenuItem;
use Label305\AujaLaravel\Config\ModelConfig;
use Label305\AujaLaravel\Config\Relation;
use Label305\AujaLaravel\Routing\AujaRouter;

class SingleAssociationShowMenuFactory {

    /**
     * @var AujaRouter
     */
    private $aujaRouter;

    public function __construct(AujaRouter $aujaRouter) {
        $this->aujaRouter = $aujaRouter;
    }

    /**
     * Builds a menu for a single model entry, where the model has exactly one relationship with another model.
     *
     * The menu will include:
     *  - An Edit LinkMenuItem to edit the model entry.
     *  - A SpacerMenuItem with the name of the associated model;
     *  - An Add LinkMenuItem to add an entry of the associated model;
     *  - A ResourceMenuItem to hold entries of the associated model.
     *
     * @param String      $modelName The name of the model.
     * @param int         $modelId   The id of the model entry.
     * @param Relation    $relation  The Relation this model has with the associated model.
     * @param ModelConfig $config    (optional) The `ModelConfig` to use.
     *
     * @return Menu The Menu, which can be configured further.
     */
    public function create($modelName, $modelId, Relation $relation, ModelConfig $config = null) {
        $otherModelName = $relation->getRight()->getName();

        $menu = new Menu();

        $editMenuItem = new LinkMenuItem();
        $editMenuItem->setText(Lang::trans('Edit'));
        $editMenuItem->setTarget(URL::route($this->aujaRouter->getEditName($modelName), $modelId));
        $menu->addMenuItem($editMenuItem);

        $headerMenuItem = new SpacerMenuItem();
        $headerMenuItem->setText(Lang::trans(str_plural($otherModelName)));
        $menu->addMenuItem($headerMenuItem);

        $addMenuItem = new LinkMenuItem();
        $addMenuItem->setText(sprintf('%s %s', Lang::trans('Add'), Lang::trans($otherModelName)));
        $addMenuItem->setIcon(Icons::ion_plus);
        $addMenuItem->setTarget(URL::route($this->aujaRouter->getCreateAssociationName($modelName, $otherModelName), $modelId));
        $menu->addMenuItem($addMenuItem);

        $resourceMenuItem = new ResourceMenuItem();
        $resourceMenuItem->setTarget(URL::route($this->aujaRouter->getAssociationName($modelName, $otherModelName), $modelId));
        $menu->addMenuItem($resourceMenuItem);

        return $menu;
    }

} 