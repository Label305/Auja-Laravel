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
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Label305\Auja\Main\Item;
use Label305\Auja\Main\Main;
use Label305\Auja\Menu\Menu;
use Label305\Auja\Menu\ResourceMenuItem;
use Label305\Auja\Page\Form;
use Label305\AujaLaravel\Config\AujaConfigurator;
use Label305\AujaLaravel\Config\Model;
use Label305\AujaLaravel\Config\ModelConfig;
use Label305\AujaLaravel\Config\Relation;
use Label305\AujaLaravel\Factory\AssociationMenuFactory;
use Label305\AujaLaravel\Factory\AuthenticationFormFactory;
use Label305\AujaLaravel\Factory\MainFactory;
use Label305\AujaLaravel\Factory\MenuFactory;
use Label305\AujaLaravel\Factory\MultipleAssociationsShowMenuFactory;
use Label305\AujaLaravel\Factory\PageFactory;
use Label305\AujaLaravel\Factory\ResourceIndexFactory;
use Label305\AujaLaravel\Factory\ResourceItemFactory;
use Label305\AujaLaravel\Factory\Sing;
use Illuminate\Database\Eloquent\Model as Eloquent;


/**
 * The main class to interact with.
 * This class can generate all necessary menus and pages, as well as the main page.
 *
 * Prior to interacting with other functions in this class, call Auja::init($modelNames).
 *
 * @author  Niek Haarman - <niek@label305.com>
 *
 * @package Label305\AujaLaravel
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
class Auja {

    /**
     * @var Application The Application instance.
     */
    private $app;

    /**
     * @var AujaConfigurator
     */
    private $aujaConfigurator;

    /**
     * Creates a new Auja instance.
     *
     * @param Application $app The Illuminate Application instance.
     * @param AujaConfigurator $aujaConfigurator
     * @param [] $models The model configuration.
     */
    function __construct(Application $app, AujaConfigurator $aujaConfigurator, array $modelNames) {
        if (php_sapi_name() == 'cli') {
            /* Don't run when we're running artisan commands. */
            return;
        }

        if (empty($modelNames)) {
            throw new \InvalidArgumentException('Provide models for Auja to manage');
        }

        $this->app = $app;

        Log::debug('Initializing Auja with models:', $modelNames);

        $this->aujaConfigurator = $aujaConfigurator;
        $this->aujaConfigurator->configure($modelNames);
    }

    /**
     * @return Model[] the array of Model instances.
     */
    public function getModels() {
        return $this->aujaConfigurator->getModels();
    }

    /**
     * @param String $modelName The name of the model.
     *
     * @return Model The `Model` with given name.
     */
    public function getModel($modelName) {
        return $this->aujaConfigurator->getModel($modelName);
    }

    /**
     * @param Model $model The `Model` to get relations for.
     *
     * @return Config\Relation[] The `Relation`s for given `Model`.
     */
    public function getRelationsForModel(Model $model) {
        return $this->aujaConfigurator->getRelationsForModel($model);
    }

    /**
     * Creates a default authentication `Form` to be used in a `Main` instance.
     *
     * @param String $title  The title to display.
     * @param String $target The target url to post to when logging in.
     *
     * @return Form The authentication `Form`.
     */
    public function authenticationForm($title, $target) {
        $formFactory = $this->app->make('Label305\AujaLaravel\Factory\AuthenticationFormFactory');
        /* @var $formFactory AuthenticationFormFactory */
        return $formFactory->create($title, $target);
    }

    /**
     * Creates the initial Auja view based on the models as initialized in init().
     *
     * @param String        $title                  The title to show.
     * @param boolean       $authenticated          Whether the user is authenticated.
     * @param String        $username               (optional) The user name to show.
     * @param String        $logoutTarget           (optional) The target url for logging out.
     * @param Form          $authenticationForm     (optional) The `Form` to use for authentication, or `null` if none.
     * @param ModelConfig   $config                 (optional) The `ModelConfig` to use.
     * @param Item[]|null   $additionalMenuItems    (optional) Enter an array of additional Label305\Auja\Main\Item
     *                                              objects to display as main tabs.
     * @param bool          $smartMenuItemInclude   (optional) Enter false to disable all the auto inclusion of menu
     *                                              items for models, you can also set a per model auto inclusion
     *                                              property on a ModelConfig object
     *
     * @return Main the Main instance which can be configured further.
     */
    public function main($title, $authenticated, $username = null, $logoutTarget = null, Form $authenticationForm = null, ModelConfig $config = null, $additionalMenuItems = null, $smartMenuItemInclude = true) {
        $mainFactory = $this->app->make('Label305\AujaLaravel\Factory\MainFactory');
        /* @var $mainFactory MainFactory */
        return $mainFactory->create($title, $authenticated, $username, $logoutTarget, $authenticationForm, $config, $additionalMenuItems, $smartMenuItemInclude);
    }

    /**
     * Creates a `Menu` for given model, with a layout depending on its relations.
     *
     * @param String      $modelName The name of the model to create a `Menu` for.
     * @param int         $modelId   The id of an instance of the model, 0 for none.
     * @param ModelConfig $config    (optional) The `ModelConfig` to use.
     *
     * @return Menu The built `Menu` instance, which can be configured further.
     */
    public function showMenuFor($model, $modelId, ModelConfig $config = null) {
        if (is_null($this->aujaConfigurator)) {
            throw new \LogicException('Auja not initialized. Call Auja::init first.');
        }

        $modelName = $this->resolveModelName($model);
        $model = $this->aujaConfigurator->getModel($modelName);
        $relations = $this->aujaConfigurator->getRelationsForModel($model);

        $associationRelations = array();
        foreach ($relations as $relation) {
            if ($relation->getType() == Relation::HAS_MANY || $relation->getType() == Relation::HAS_AND_BELONGS_TO) { // TODO: What to do with one-to-one relations?
                $associationRelations[] = $relation;
            }
        }


        switch (count($associationRelations)) {
            case 1:
                $menu = $this->singleAssociationShowMenuFor($modelName, $modelId, $associationRelations[0], $config);
                break;
            default:
                $menu = $this->multipleAssociationsShowMenuFor($modelName, $modelId, $associationRelations, $config);
                break;
        }

        return $menu;
    }

    /**
     * Builds a Resource instance for given model.
     * This is typically used when a ResourceMenuItem triggers a call for items.
     *
     * This method also supports pagination, either manually or automatically.
     * To automatically use pagination, simply provide a Paginator as items.
     *
     * @param Controller|Eloquent|String $model       An object which represents the model to build items for.
     * @param array|Paginator|null       $items       An array of instances of the model to be shown, or a Paginator containing the instances.
     * @param String                     $targetUrl   (optional) The target url for the items. Must contain '%d' in the place of the item id.
     * @param String                     $nextPageUrl (optional) The url to the next page, if any.
     * @param int                        $offset      (optional) The offset to start the order from.
     * @param ModelConfig                $config      (optional) The `ModelConfig` to use.
     *
     * @return Resource The built LinkMenuItems.
     */
    public function itemsFor($model, $items = null, $targetUrl = null, $nextPageUrl = null, $offset = -1, ModelConfig $config = null) {
        $modelName = $this->resolveModelName($model);

        if ($items == null) {
            $items = call_user_func(array($modelName, 'simplePaginate'), 10);
        }

        $factory = $this->app->make('Label305\AujaLaravel\Factory\ResourceIndexFactory');
        /* @var $factory ResourceIndexFactory */
        return $factory->create($modelName, $items, $targetUrl, $nextPageUrl, $offset, $config);
    }

    /**
     * Creates a simple menu for given model, where typically this model should not have any relations to other models.
     *
     * The menu will include:
     *  - An Add LinkMenuItem;
     *  - A SpacerMenuItem with the model's name;
     *  - A ResourceMenuItem to hold entries of the model.
     *
     * @param Controller|Eloquent|String $model  An object which represents the model to build items for.
     * @param ModelConfig                $config (optional) The `ModelConfig` to use.
     *
     * @return Menu The `Menu`, which can be configured further.
     */
    public function menuFor($model, ModelConfig $config = null) {
        if (is_null($this->aujaConfigurator)) {
            throw new \LogicException('Auja not initialized. Call Auja::init first.');
        }

        $modelName = $this->resolveModelName($model);

        $menuFactory = $this->app->make('Label305\AujaLaravel\Factory\MenuFactory');
        /* @var $menuFactory MenuFactory */
        return $menuFactory->create($modelName, $config);
    }

    /**
     * Creates a `ResourceMenuItem` for given model, with a layout depending on its relations.
     *
     * @param String      $model     The name of the model to create a `ResourceMenuItem` for.
     * @param ModelConfig $config    (optional) The `ModelConfig` to use.
     *
     * @return ResourceMenuItem The built `ResourceMenuItem` instance, which can be configured further.
     */
    public function resourceItemFor($model, ModelConfig $config = null) {
        if (is_null($this->aujaConfigurator)) {
            throw new \LogicException('Auja not initialized. Call Auja::init first.');
        }

        $modelName = $this->resolveModelName($model);

        $factory = $this->app->make('Label305\AujaLaravel\Factory\ResourceItemFactory');
        /* @var $factory ResourceItemFactory */
        return $factory->create($modelName, $config);
    }


    /**
     * Builds a menu for a single model entry, where the model has exactly one relationship with another model.
     *
     * The menu will include:
     *  - An Edit LinkMenuItem;
     *  - A SpacerMenuItem with the name of the associated model;
     *  - A ResourceMenuItem to hold entries of the associated model.
     *
     * @param Controller|Eloquent|String $model    An object which represents the model to build items for.
     * @param int                        $modelId  The id of the model entry.
     * @param Relation                   $relation The Relation this model has with the associated model.
     * @param ModelConfig                $config   (optional) The `ModelConfig` to use.
     *
     * @return Menu the `Menu`, which can be configured further.
     */
    public function singleAssociationShowMenuFor($model, $modelId, Relation $relation, ModelConfig $config = null) {
        $modelName = $this->resolveModelName($model);

        $menuFactory = $this->app->make('Label305\AujaLaravel\Factory\SingleAssociationShowMenuFactory');
        /* @var $menuFactory SingleAssociationShowMenuFactory */
        return $menuFactory->create($modelName, $modelId, $relation, $config);
    }

    /**
     * Builds a menu for a single model entry, where the model has multiple relationships with other models.
     *
     * The menu will include:
     *  - An Edit LinkMenuItem;
     *  - For each of the Relations, a LinkMenuItem for the associated model.
     *
     * @param Controller|Eloquent|String $model     An object which represents the model to build items for.
     * @param int                        $modelId   The id of the model entry.
     * @param Relation[]                 $relations The `Relation`s this model has with associated models.
     * @param ModelConfig                $config    (optional) The `ModelConfig` to use.
     *
     * @return Menu the Menu, which can be configured further.
     */
    public function multipleAssociationsShowMenuFor($model, $modelId, array $relations, ModelConfig $config = null) {
        $modelName = $this->resolveModelName($model);

        $menuFactory = $this->app->make('Label305\AujaLaravel\Factory\MultipleAssociationsShowMenuFactory');
        /* @var $menuFactory MultipleAssociationsShowMenuFactory */
        return $menuFactory->create($modelName, $modelId, $relations, $config);
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
    public function associationMenuFor($modelName, $modelId, $associationName, ModelConfig $config = null) {
        $menuFactory = $this->app->make('Label305\AujaLaravel\Factory\AssociationMenuFactory');
        /* @var $menuFactory AssociationMenuFactory */
        return $menuFactory->create($modelName, $modelId, $associationName, $config);
    }

    /**
     * Creates a Page for given model.
     *
     * @param Controller|Eloquent|String $model  An object which represents the model to build items for.
     * @param int                        $itemId (optional) The id of an instance of the model.
     * @param ModelConfig                $config (optional) The `ModelConfig` to use.
     *
     * @return \Label305\Auja\Page\Page The created Page
     */
    public function pageFor($model, $itemId = 0, ModelConfig $config = null) {
        $modelName = $this->resolveModelName($model);
        $item = $this->findItem($modelName, $itemId);

        $pageFactory = $this->app->make('Label305\AujaLaravel\Factory\PageFactory');
        /* @var $pageFactory PageFactory */
        return $pageFactory->create($modelName, $item, $config);
    }

    /**
     * Uses Eloquent::find($id) to find the item matching given id, if available.
     *
     * @param String $modelName The name of the model.
     * @param int    $itemId    The id of the instance of the model to find.
     *
     * @return Eloquent The model instance, or null if none.
     */
    private function findItem($modelName, $itemId) {
        return $itemId = 0 ? null : call_user_func(array($modelName, 'find'), $itemId);
    }

    /**
     * Resolves the model name.
     *
     * @param Controller|Eloquent|String $model An object which represents the model.
     *
     * @return String the model name.
     */
    private function resolveModelName($model) {
        if ($model instanceof Controller) {
            $exploded = explode('\\', get_class($model));
            $controllerName = array_pop($exploded);
            return str_singular(str_replace('Controller', '', $controllerName));
        } else if ($model instanceof Eloquent) {
            return get_class($model);
        } else {
            return $model;
        }
    }
}