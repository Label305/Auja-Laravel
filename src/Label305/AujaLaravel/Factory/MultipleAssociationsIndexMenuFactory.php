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


use Label305\Auja\Menu\LinkMenuItem;
use Label305\Auja\Menu\Menu;
use Label305\Auja\Menu\SpacerMenuItem;
use Label305\AujaLaravel\Config\Relation;
use Label305\AujaLaravel\I18N\Translator;
use Label305\AujaLaravel\Routing\AujaRouter;

class MultipleAssociationsIndexMenuFactory {

    /**
     * @var Translator
     */
    private $translator;

    /**
     * @var AujaRouter
     */
    private $aujaRouter;

    public function __construct(Translator $translator, AujaRouter $aujaRouter) {
        $this->translator = $translator;
        $this->aujaRouter = $aujaRouter;
    }

    /**
     * Builds a menu for a single model entry, where the model has multiple relationships with other models.
     *
     * The menu will include:
     *  - An Edit LinkMenuItem;
     *  - A SpacerMenuItem;
     *  - For each of the Relations, a LinkMenuItem for the associated model.
     *
     * @param String     $modelName the name of the model.
     * @param int        $modelId   the id of the model entry.
     * @param Relation[] $relations the Relations this model has with associated models.
     *
     * @return Menu the Menu, which can be configured further.
     */
    public function create($modelName, $modelId, array $relations) {
        $menu = new Menu();

        $addMenuItem = new LinkMenuItem();
        $addMenuItem->setText($this->translator->trans('Edit'));
        $addMenuItem->setTarget(route($this->aujaRouter->getEditName($modelName), $modelId));
        $menu->addMenuItem($addMenuItem);

        $spacerMenuItem = new SpacerMenuItem();
        $spacerMenuItem->setText($this->translator->trans('Properties'));
        $menu->addMenuItem($spacerMenuItem);

        foreach ($relations as $relation) {
            $otherModelName = $relation->getRight()->getName();

            $associationMenuItem = new LinkMenuItem();
            $associationMenuItem->setText($this->translator->trans(str_plural($otherModelName)));
            $associationMenuItem->setTarget(route($this->aujaRouter->getAssociationMenuName($modelName, $otherModelName), $modelId));
            $menu->addMenuItem($associationMenuItem);
        }

        return $menu;
    }

} 