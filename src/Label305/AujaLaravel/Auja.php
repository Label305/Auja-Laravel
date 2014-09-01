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

namespace Label305\AujaLaravel;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Label305\Auja\Menu\LinkMenuItem;
use Label305\Auja\Menu\Menu;
use Label305\Auja\Menu\ResourceMenuItem;
use Label305\Auja\Menu\SpacerMenuItem;
use Label305\Auja\Utils\JsonGenerator;

class Auja {

    /**
     * @var AujaConfigurator
     */
    private static $aujaConfigurator;

    /**
     * Initializes this class for given models.
     *
     * @param $modelNames String[] an array of model names to use.
     */
    public static function init(array $modelNames) {
        if (empty($modelNames)) {
            throw new \InvalidArgumentException('Provide models!');
        }

        Log::debug('Initializing Auja with models:', $modelNames);

        self::$aujaConfigurator = App::make('Label305\AujaLaravel\AujaConfigurator');
        self::$aujaConfigurator->configure($modelNames);
    }

    public static function buildIndexMenu($modelName) {
        $models = self::$aujaConfigurator->getModels();
        $model = $models[$modelName];
        $relations = self::$aujaConfigurator->getRelationsForModel($model);

        $numAssociations = 0;
        $associationRelations = array();
        foreach ($relations as $relation) {
            if ($relation->getType() == Relation::HAS_MANY || $relation->getType() == Relation::HAS_AND_BELONGS_TO) {
                $numAssociations++;
                $associationRelations[] = $relation;
            }
        }

        switch ($numAssociations) {
            case 0:
                $menu = self::buildNoAssociationsIndexMenu($modelName);
                break;
            case 1:
                $menu = self::buildSingleAssociationIndexMenu($modelName, $associationRelations[0]);
                break;
            default:
                $menu = self::buildMultipleAssociationsIndexMenu($modelName, $associationRelations);
                break;
        }

        return $menu;
    }

    private static function buildNoAssociationsIndexMenu($modelName) {
        $menu = new Menu();

        $addMenuItem = new LinkMenuItem();
        $addMenuItem->setName("Add"); // TODO I18N
        $addMenuItem->setTarget($modelName); // TODO proper name
        $menu->addMenuItem($addMenuItem);

        $headerMenuItem = new SpacerMenuItem();
        $headerMenuItem->setName($modelName); // TODO I18N
        $menu->addMenuItem($headerMenuItem);

        $resourceMenuItem = new ResourceMenuItem();
        $resourceMenuItem->addProperty("Searchable"); // TODO when is something searchable?
        $menu->addMenuItem($resourceMenuItem);

        return $menu;
    }

    private static function buildSingleAssociationIndexMenu($modelName, $relation) {
        $menu = new Menu();

        $addMenuItem = new LinkMenuItem();
        $addMenuItem->setName("Edit"); // TODO I18N
        $addMenuItem->setTarget($modelName); // TODO proper target
        $menu->addMenuItem($addMenuItem);

        $headerMenuItem = new SpacerMenuItem();
        $headerMenuItem->setName($modelName); // TODO I18N
        $menu->addMenuItem($headerMenuItem);

        $resourceMenuItem = new ResourceMenuItem();
        $resourceMenuItem->addProperty("Searchable"); // TODO when is something searchable?
        $menu->addMenuItem($resourceMenuItem);

        return $menu;
    }

    /**
     * @param $modelName
     * @param Relation[] $relations
     * @return Menu
     */
    private static function buildMultipleAssociationsIndexMenu($modelName, array $relations) {
        $menu = new Menu();

        $addMenuItem = new LinkMenuItem();
        $addMenuItem->setName("Edit"); // TODO I18N
        $addMenuItem->setTarget($modelName); // TODO proper name
        $menu->addMenuItem($addMenuItem);

        foreach ($relations as $relation) {
            $associationMenuItem = new LinkMenuItem();
            $associationMenuItem->setName($relation->getRight()->getName());
            $associationMenuItem->setTarget($relation->getRight()->getName()); // TODO proper target
            $menu->addMenuItem($associationMenuItem);
        }

        return $menu;
    }


}