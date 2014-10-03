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


use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Facades\URL;
use Label305\Auja\Menu\LinkMenuItem;
use Label305\Auja\Menu\Menu;
use Label305\Auja\Menu\ResourceMenuItem;
use Label305\Auja\Menu\SpacerMenuItem;
use Label305\AujaLaravel\I18N\Translator;
use Label305\AujaLaravel\Routing\AujaRouter;

class AssociationMenuFactory {

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
     * Builds a menu for displaying associated items to a model entry (i.e. /club/21/team).
     *
     * The menu will include:
     *  - An Add LinkMenuItem;
     *  - A SpacerMenuItem with the name of the associated model;
     *  - A ResourceMenuItem to hold entries of the associated model.
     *
     * @param String $modelName       the name of the model (i.e. Club).
     * @param int    $modelId         the id of the model entry.
     * @param String $associationName the name of the associated model (i.e. Team).
     *
     * @return Menu the Menu, which can be configured further.
     */
    public function create($modelName, $modelId, $associationName) {
        $menu = new Menu();

        $addMenuItem = new LinkMenuItem();
        $addMenuItem->setText('Add ' . $this->translator->trans($associationName));
        $addMenuItem->setTarget(Url::route($this->aujaRouter->getCreateAssociationName($modelName, $associationName), $modelId));
        $menu->addMenuItem($addMenuItem);

        $headerMenuItem = new SpacerMenuItem();
        $headerMenuItem->setText($this->translator->trans(str_plural($associationName)));
        $menu->addMenuItem($headerMenuItem);

        $resourceMenuItem = new ResourceMenuItem();
        $resourceMenuItem->setTarget(Url::route($this->aujaRouter->getAssociationName($modelName, $associationName), $modelId));
        $menu->addMenuItem($resourceMenuItem);

        return $menu;
    }

} 