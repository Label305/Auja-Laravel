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
 * Licensed under the Apache License, Version 2.0 (the 'License');
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an 'AS IS' BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Label305\AujaLaravel;

use Illuminate\Foundation\Application;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Label305\Auja\Button;
use Label305\Auja\Item;
use Label305\Auja\Main;
use Label305\Auja\Menu\LinkMenuItem;
use Label305\Auja\Menu\Menu;
use Label305\Auja\Menu\ResourceItemsMenuItems;
use Label305\Auja\Menu\ResourceMenuItem;
use Label305\Auja\Menu\SpacerMenuItem;
use Label305\Auja\Page\PageForm;
use Label305\Auja\Page\PageHeader;
use Label305\Auja\Page\Page;
use Label305\Auja\Page\TextFormItem;

/**
 * The main class to interact with.
 * This class can generate all necessary menu's and pages, as well as the main page.
 *
 * Prior to interacting with other functions in this class, call Auja::init($modelNames).
 *
 * @package Label305\AujaLaravel
 */
class Auja {

    private $app;

    /**
     * @var AujaConfigurator
     */
    private $aujaConfigurator;

    function __construct(Application $app, array $modelNames) {
        if (empty($modelNames)) {
            throw new \InvalidArgumentException('Provide models!');
        }

        $this->app = $app;

        Log::debug('Initializing Auja with models:', $modelNames);
        $this->aujaConfigurator = $app['Label305\AujaLaravel\AujaConfigurator'];
        $this->aujaConfigurator->configure($modelNames);
    }

    /**
     * @return array a key-value pair of model names and the Model instances.
     */
    public function getModels() {
        if (is_null($this->aujaConfigurator)) {
            throw new \LogicException('Auja not initialized. Call Auja::init first.');
        }

        return $this->aujaConfigurator->getModels();
    }


    /**
     * Builds the initial Auja view based on the models as initialized in init().
     *
     * @param $title String the title to be shown.
     *
     * @return Main the Main instance which can be configured further.
     */
    public function buildMain($title) {
        if (is_null($this->aujaConfigurator)) {
            throw new \LogicException('Auja not initialized. Call Auja::init first.');
        }

        $main = new Main();

        $main->setTitle($title);

        $main->setColor('main', '#1ebab8'); // TODO: remove colors
        $main->setColor('secondary', '#E7EFEF');

        $button = new Button();
        $button->setTitle('Logout');
        $button->setTarget('#logout'); // TODO proper url
        $main->addButton($button);

        $main->setUsername('Niek Haarman'); // TODO proper user

        foreach (array_values($this->aujaConfigurator->getModels()) as $model) {
            /* @var $model Model */
            $item = new Item();
            $item->setTitle($model->getName());
            $item->setIcon('tower'); //TODO proper icon
            $item->setTarget(sprintf('/%s/menu', self::toUrlName($model->getName())));
            $main->addItem($item);
        }

        return $main;
    }

    /**
     * Intelligently builds an index menu for given model, and optionally model id.
     *
     * @param $modelName String the name of the model to build the menu for.
     * @param $modelId   int (optional) the id of an instance of the model.
     *
     * @return Menu the built menu instance, which can be configured further.
     */
    public function buildIndexMenu($modelName, $modelId = 0) {
        if (is_null($this->aujaConfigurator)) {
            throw new \LogicException('Auja not initialized. Call Auja::init first.');
        }

        if ($modelId == 0) {
            $menu = self::buildNoAssociationsIndexMenu($modelName);
        } else {
            $models = $this->aujaConfigurator->getModels();
            $model = $models[$modelName];
            $relations = $this->aujaConfigurator->getRelationsForModel($model);

            $associationRelations = array();
            foreach ($relations as $relation) {
                if ($relation->getType() == Relation::HAS_MANY || $relation->getType() == Relation::HAS_AND_BELONGS_TO) {
                    $associationRelations[] = $relation;
                }
            }

            switch (count($associationRelations)) {
                case 0:
                    $menu = self::buildNoAssociationsIndexMenu($modelName);
                    break;
                case 1:
                    $menu = self::buildSingleAssociationIndexMenu($modelName, $modelId, $associationRelations[0]);
                    break;
                default:
                    $menu = self::buildMultipleAssociationsIndexMenu($modelName, $modelId, $associationRelations);
                    break;
            }
        }

        return $menu;
    }

    /**
     * Builds a ResourceItemsMenuItems instance for given items.
     * This is typically used when a ResourceMenuItem triggers a call for items.
     *
     * This method also supports pagination, either manually or automatically.
     * To automatically use pagination, simply provide a Paginator as items.
     *
     * @param $modelName   String the name of the model the items represent.
     * @param $items       array|Paginator an array of instances of the model to be shown, or a Paginator containing the instances.
     * @param $nextPageUrl String (optional) The url to the next page, if any.
     * @param $offset      int (optional) The offset to start the order from.
     *
     * @return ResourceItemsMenuItems[] the built LinkMenuItems.
     */
    public function buildResourceItems($modelName, $items, $nextPageUrl = null, $offset = -1) { // TODO: create separate methods for pagination and no pagination?
        $models = $this->aujaConfigurator->getModels();
        if (!isset($models[$modelName])) {
            throw new \LogicException(sprintf('Auja not constructed with model %s. Make sure you pass in this model when instantiating Auja.', $modelName));
        }

        $paginator = null;
        if ($items instanceof Paginator) {
            $paginator = $items;
            $items = $paginator->getCollection();

            if ($offset == -1) {
                $offset = ($paginator->getCurrentPage() - 1) * $paginator->getPerPage();
            }
        }

        if ($offset == -1) {
            $offset = 0;
        }

        if (!($items instanceof \IteratorAggregate)) {
            $items = new Collection(array($items));
        }

        if (count($items) == 0) {
            return array();
        }

        $model = $models[$modelName];
        $relations = $this->aujaConfigurator->getRelationsForModel($model);

        $associationRelations = array();
        foreach ($relations as $relation) {
            if ($relation->getType() == Relation::HAS_MANY || $relation->getType() == Relation::HAS_AND_BELONGS_TO) {
                $associationRelations[] = $relation;
            }
        }

        if (count($associationRelations) == 0) {
            $target = sprintf('/%s/%s/edit', self::toUrlName($modelName), '%s');
        } else {
            $target = sprintf('/%s/%s/menu', self::toUrlName($modelName), '%s');
        }

        $resourceItems = new ResourceItemsMenuItems();
        $displayField = $this->aujaConfigurator->getDisplayField($model);
        for ($i = 0; $i < count($items); $i++) {
            $menuItem = new LinkMenuItem();
            $menuItem->setName($items[$i]->$displayField);
            $menuItem->setTarget(sprintf($target, $items[$i]->id));
            $menuItem->setOrder($offset + $i);
            $resourceItems->add($menuItem);
        }

        if ($nextPageUrl != null) {
            $resourceItems->setNextPageUrl($nextPageUrl);
        } else if ($paginator != null && $paginator->getCurrentPage() != $paginator->getLastPage()) {
            $resourceItems->setNextPageUrl(sprintf('/%s?page=%d', self::toUrlName($modelName), $paginator->getCurrentPage() + 1));
        }

        return $resourceItems;
    }

    /**
     * Builds a simple menu for given model, where typically this model should not have any relations to other models.
     *
     * The menu will include:
     *  - An Add LinkMenuItem;
     *  - A SpacerMenuItem with the model's name;
     *  - A ResourceMenuItem to hold entries of the model.
     *
     * @param $modelName String the name of the model.
     *
     * @return Menu the Menu, which can be configured further.
     */
    public function buildNoAssociationsIndexMenu($modelName) {
        $menu = new Menu();

        $addMenuItem = new LinkMenuItem();
        $addMenuItem->setName('Add'); // TODO I18N
        $addMenuItem->setTarget(sprintf('/%s/create', self::toUrlName($modelName)));
        $menu->addMenuItem($addMenuItem);

        $spacerMenuItem = new SpacerMenuItem();
        $spacerMenuItem->setName($modelName); // TODO I18N
        $menu->addMenuItem($spacerMenuItem);

        $resourceMenuItem = new ResourceMenuItem();
        $resourceMenuItem->setTarget(sprintf('/%s', self::toUrlName($modelName)));
        $menu->addMenuItem($resourceMenuItem);

        return $menu;
    }

    /**
     * Builds a menu for a single model entry, where the model has exactly one relationship with another model.
     *
     * The menu will include:
     *  - An Edit LinkMenuItem;
     *  - A SpacerMenuItem with the name of the associated model;
     *  - A ResourceMenuItem to hold entries of the associated model.
     *
     *
     * @param $modelName String the name of the model.
     * @param $modelId   int the id of the model entry.
     * @param $relation  Relation the Relation this model has with the associated model.
     *
     * @return Menu the Menu, which can be configured further.
     */
    public function buildSingleAssociationIndexMenu($modelName, $modelId, Relation $relation) {
        $menu = new Menu();

        $addMenuItem = new LinkMenuItem();
        $addMenuItem->setName('Edit'); // TODO I18N
        $addMenuItem->setTarget(sprintf('/%s/%s/edit', self::toUrlName($modelName), $modelId));
        $menu->addMenuItem($addMenuItem);

        $headerMenuItem = new SpacerMenuItem();
        $headerMenuItem->setName($relation->getRight()->getName()); // TODO I18N
        $menu->addMenuItem($headerMenuItem);

        $resourceMenuItem = new ResourceMenuItem();
        $resourceMenuItem->setTarget(sprintf('/%s/%s/%s', self::toUrlName($modelName), $modelId, self::toUrlName($relation->getRight()->getName())));
        $menu->addMenuItem($resourceMenuItem);

        return $menu;
    }

    /**
     * Builds a menu for a single model entry, where the model has multiple relationships with other models.
     *
     * The menu will include:
     *  - An Edit LinkMenuItem;
     *  - For each of the Relations, a LinkMenuItem for the associated model.
     *
     * @param $modelName String the name of the model.
     * @param $modelId   int the id of the model entry.
     * @param $relations Relation[] the Relations this model has with associated models.
     *
     * @return Menu the Menu, which can be configured further.
     */
    public function buildMultipleAssociationsIndexMenu($modelName, $modelId, array $relations) {
        $menu = new Menu();

        $addMenuItem = new LinkMenuItem();
        $addMenuItem->setName('Edit'); // TODO I18N
        $addMenuItem->setTarget(sprintf('/%s/%s/edit', self::toUrlName($modelName), $modelId));
        $menu->addMenuItem($addMenuItem);

        foreach ($relations as $relation) {
            $associationMenuItem = new LinkMenuItem();
            $associationMenuItem->setName($relation->getRight()->getName());
            $associationMenuItem->setTarget(sprintf('/%s/%s/%s/menu', self::toUrlName($modelName), $modelId, self::toUrlName($relation->getRight()->getName())));
            $menu->addMenuItem($associationMenuItem);
        }

        return $menu;
    }

    /**
     * Builds a menu for displaying associated items to a model entry (i.e. /club/21/team).
     *
     * The menu will include:
     *  - An Add LinkMenuItem;
     *  - A SpacerMenuItem with the name of the associated model;
     *  - A ResourceMenuItem to hold entries of the associated model.
     *
     * @param $modelName       String the name of the model (i.e. Club).
     * @param $modelId         int the id of the model entry.
     * @param $associationName String the name of the associated model (i.e. Team).
     *
     * @return Menu the Menu, which can be configured further.
     */
    public function buildAssociationMenu($modelName, $modelId, $associationName) {
        $menu = new Menu();

        $addMenuItem = new LinkMenuItem();
        $addMenuItem->setName('Add ' . $associationName); // TODO I18N
        $addMenuItem->setTarget(sprintf('/%s/create?%s=%s', self::toUrlName($associationName), self::toForeignColumnName($modelName), $modelId));
        $menu->addMenuItem($addMenuItem);

        $headerMenuItem = new SpacerMenuItem();
        $headerMenuItem->setName($modelName); // TODO I18N
        $menu->addMenuItem($headerMenuItem);

        $resourceMenuItem = new ResourceMenuItem();
        $resourceMenuItem->setTarget(sprintf('/%s/%s/%s', self::toUrlName($modelName), $modelId, self::toUrlName($associationName)));
        $menu->addMenuItem($resourceMenuItem);

        return $menu;
    }

    public function buildPage($modelName, $modelId = 0) {
        $page = new Page();

        $header = new PageHeader();
        $header->setText('Create ' . $modelName);
        $page->addPageComponent($header);

        $form = new PageForm();
        $form->setAction(sprintf('/%s%s', self::toUrlName($modelName), $modelId == 0 ? '' : '/' . $modelId));
        $form->setMethod($modelId == 0 ? 'POST' : 'PUT');

        $model = $this->aujaConfigurator->getModels()[$modelName];
        /* @var $model Model */

        $instance = new $modelName;
        $fillable = $instance->getFillable(); // TODO: other stuff
        /* @var $fillable String[] */
        $hidden = $instance->getHidden();
        /* @var $hidden String[] */

        foreach ($fillable as $columnName) {
            $column = $model->getColumn($columnName);
            $item = PageFormItemFactory::getPageFormItem($column->getType(), in_array($columnName, $hidden));
            $item->setName($column->getName());
            $item->setLabel(self::toHumanReadableName($column->getName()));
            $form->addItem($item);
        }

        $page->addPageComponent($form);

        return $page;
    }

    private static function toHumanReadableName($modelName) {
        return preg_replace('/(?<=\\w)(?=[A-Z])/', ' $1', camel_case($modelName));
    }

    private static function toUrlName($modelName) {
        return strtolower($modelName);
    }

    private static function toForeignColumnName($modelName) {
        return strtolower($modelName) . '_id';
    }

}