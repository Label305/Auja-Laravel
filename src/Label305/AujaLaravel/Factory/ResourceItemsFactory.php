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


use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\URL;
use Label305\Auja\Menu\LinkMenuItem;
use Label305\Auja\Menu\Resource;
use Label305\AujaLaravel\Config\AujaConfigurator;
use Label305\AujaLaravel\Config\Relation;
use Label305\AujaLaravel\Routing\AujaRouter;

class ResourceItemsFactory {

    /**
     * @var AujaConfigurator
     */
    private $aujaConfigurator;

    /**
     * @var AujaRouter
     */
    private $aujaRouter;

    public function __construct(AujaConfigurator $aujaConfigurator, AujaRouter $aujaRouter) {
        $this->aujaConfigurator = $aujaConfigurator;
        $this->aujaRouter = $aujaRouter;
    }

    /**
     * Builds a Resource instance for given items.
     * This is typically used when a ResourceMenuItem triggers a call for items.
     *
     * This method also supports pagination, either manually or automatically.
     * To automatically use pagination, simply provide a Paginator as items.
     *
     * @param String          $modelName   the name of the model the items represent.
     * @param array|Paginator $items       an array of instances of the model to be shown, or a Paginator containing the instances.
     * @param String          $nextPageUrl (optional) The url to the next page, if any.
     * @param int             $offset      (optional) The offset to start the order from.
     *
     * @return Resource The built LinkMenuItems.
     */
    public function create($modelName, $items, $nextPageUrl = null, $offset = -1) { // TODO: create separate methods for pagination and no pagination?
        /* Extract items from Paginator if necessary */
        $paginator = null;
        if ($items instanceof Paginator) {
            $paginator = $items;
            $items = $paginator->getCollection();

            if ($offset == -1) {
                $offset = ($paginator->getCurrentPage() - 1) * $paginator->getPerPage();
            }
        }

        /* If the offset is not set, use no offset */
        if ($offset == -1) {
            $offset = 0;
        }

        /* No items. */
        if (count($items) == 0) {
            return new Resource();
        }

        /* If the items are not iterable */
        if (!($items instanceof \IteratorAggregate)) {
            $items = new Collection([$items]);
        }

        $model = $this->aujaConfigurator->getModel($modelName);

        /* Find relations for this model, so we can know the target */
        $relations = $this->aujaConfigurator->getRelationsForModel($model);
        $associationRelations = array();
        foreach ($relations as $relation) {
            if ($relation->getType() == Relation::HAS_MANY || $relation->getType() == Relation::HAS_AND_BELONGS_TO) {
                $associationRelations[] = $relation;
            }
        }

        /* Build the actual items to return */
        $resourceItems = new Resource();
        $displayField = $this->aujaConfigurator->getDisplayField($model);
        $icon = $this->aujaConfigurator->getIcon($model);
        for ($i = 0; $i < count($items); $i++) {
            if (count($associationRelations) == 0) {
                $target = URL::route($this->aujaRouter->getEditName($modelName), $items[$i]->id);
            } else {
                $target = URL::route($this->aujaRouter->getShowMenuName($modelName), $items[$i]->id);
            }

            $menuItem = new LinkMenuItem();
            $menuItem->setText($items[$i]->$displayField);
            $menuItem->setTarget($target);
            $menuItem->setOrder($offset + $i);
            $menuItem->setIcon($icon);
            $resourceItems->addItem($menuItem);
        }

        /* Add pagination if necessary */
        if ($nextPageUrl != null) {
            $resourceItems->setNextPageUrl($nextPageUrl);
        } else if ($paginator != null && $paginator->getCurrentPage() != $paginator->getLastPage()) {
            $target = route($this->aujaRouter->getIndexName($modelName), ['page' => ($paginator->getCurrentPage() + 1)]);
            $resourceItems->setNextPageUrl($target);
        }

        return $resourceItems;
    }
} 