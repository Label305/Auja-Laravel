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
use Label305\Auja\Menu\LinkMenuItem;
use Label305\Auja\Menu\ResourceItemsMenuItems;
use Label305\AujaLaravel\Config\AujaConfigurator;
use Label305\AujaLaravel\Config\Relation;

class ResourceItemsFactory {

    /**
     * @var AujaConfigurator
     */
    private $aujaConfigurator;

    public function __construct(AujaConfigurator $aujaConfigurator) {
        $this->aujaConfigurator = $aujaConfigurator;
    }

    /**
     * Builds a ResourceItemsMenuItems instance for given items.
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
     * @return ResourceItemsMenuItems[] the built LinkMenuItems.
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
            return new ResourceItemsMenuItems();
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

//        if (count($associationRelations) == 0) {
//            $target = sprintf('/%s/%s/edit', self::toUrlName($modelName), '%s');
//        } else {
//            $target = sprintf('/%s/%s/menu', self::toUrlName($modelName), '%s');
//        }
        $target = ''; // TODO: proper target

        /* Build the actual items to return */
        $resourceItems = new ResourceItemsMenuItems();
        $displayField = $this->aujaConfigurator->getDisplayField($model);
        $icon = $this->aujaConfigurator->getIcon($model);
        for ($i = 0; $i < count($items); $i++) {
            $menuItem = new LinkMenuItem();
            $menuItem->setName($items[$i]->$displayField);
            $menuItem->setTarget(sprintf($target, $items[$i]->id));
            $menuItem->setOrder($offset + $i);
            $menuItem->setIcon($icon);
            $resourceItems->add($menuItem);
        }

        /* Add pagination if necessary */
//        if ($nextPageUrl != null) {
            $resourceItems->setNextPageUrl($nextPageUrl);
//        } else if ($paginator != null && $paginator->getCurrentPage() != $paginator->getLastPage()) {
//            $resourceItems->setNextPageUrl(sprintf('/%s?page=%d', self::toUrlName($modelName), $paginator->getCurrentPage() + 1));
//        }
        // TODO: proper target

        return $resourceItems;
    }
} 