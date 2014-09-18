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
use Label305\Auja\Main\Item;
use Label305\Auja\Main\Main;
use Label305\Auja\Menu\LinkMenuItem;
use Label305\Auja\Menu\Menu;
use Label305\Auja\Menu\ResourceItemsMenuItems;
use Label305\Auja\Menu\ResourceMenuItem;
use Label305\Auja\Menu\SpacerMenuItem;
use Label305\Auja\Page\Form;
use Label305\Auja\Page\PageHeader;
use Label305\Auja\Page\Page;
use Label305\Auja\Shared\Button;
use Label305\AujaLaravel\Config\AujaConfigurator;
use Label305\AujaLaravel\Config\Model;
use Label305\AujaLaravel\Config\Relation;
use Label305\AujaLaravel\Factory\AssociationMenuFactory;
use Label305\AujaLaravel\Factory\AuthenticationFormFactory;
use Label305\AujaLaravel\Factory\MainFactory;
use Label305\AujaLaravel\Factory\MultipleAssociationsIndexMenuFactory;
use Label305\AujaLaravel\Factory\NoAssociationsIndexMenuFactory;
use Label305\AujaLaravel\Factory\PageFactory;
use Label305\AujaLaravel\Factory\SingleAssociationIndexMenuFactory;
use Label305\AujaLaravel\I18N\Translator;
use Label305\AujaLaravel\Logging\Logger;

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
     * @var Translator The Translator to use.
     */
    private $translator;

    /**
     * @var Logger The Logger to use.
     */
    private $logger;

    /**
     * @var AujaConfigurator
     */
    private $aujaConfigurator;

    /**
     * Creates a new Auja instance.
     *
     * @param Application $app        The Illuminate Application instance.
     * @param String[]    $modelNames The names of the models to use for Auja.
     */
    function __construct(Application $app, array $modelNames) {
        if (empty($modelNames)) {
            throw new \InvalidArgumentException('Provide models!');
        }

        $this->app = $app;
        $this->translator = $this->app->make('Label305\AujaLaravel\I18N\Translator');
        $this->logger = $this->app->make('Label305\AujaLaravel\Logging\Logger');

        $this->logger->debug('Initializing Auja with models:', $modelNames);
        $this->aujaConfigurator = $app['Label305\AujaLaravel\Config\AujaConfigurator'];
        $this->aujaConfigurator->configure($modelNames);
    }

    /**
     * @return Model[] the array of Model instances.
     */
    public function getModels() {
        return $this->aujaConfigurator->getModels();
    }

    public function getModel($modelName) {
        return $this->aujaConfigurator->getModel($modelName);
    }

    /**
     * @param Model $model
     *
     * @return Config\Relation[]
     */
    public function getRelationsForModel(Model $model) {
        return $this->aujaConfigurator->getRelationsForModel($model);
    }

    /**
     * Builds a default authentication `Form` to be used in a `Main` instance.
     *
     * @return Form
     */
    public function buildAuthenticationForm($title, $target) {
        $formFactory = $this->app->make('Label305\AujaLaravel\Factory\AuthenticationFormFactory');
        /* @var $formFactory AuthenticationFormFactory */
        return $formFactory->create($title, $target);
    }

    /**
     * Builds the initial Auja view based on the models as initialized in init().
     *
     * @param String $title              The title to be shown.
     * @param Form   $authenticationForm (optional) The `Form` to use for authentication, or `null` if none.
     *
     * @return Main the Main instance which can be configured further.
     */
    public function buildMain($title, Form $authenticationForm = null) {
        $mainFactory = $this->app->make('Label305\AujaLaravel\Factory\MainFactory');
        /* @var $mainFactory MainFactory */
        return $mainFactory->create($title, $authenticationForm);
    }

    /**
     * Intelligently builds an index menu for given model, and optionally model id.
     *
     * @param String $modelName the name of the model to build the menu for.
     * @param int    $modelId   (optional) the id of an instance of the model.
     *
     * @return Menu the built menu instance, which can be configured further.
     */
    public function buildIndexMenu($modelName, $modelId = 0) {
        if (is_null($this->aujaConfigurator)) {
            throw new \LogicException('Auja not initialized. Call Auja::init first.');
        }

        if ($modelId == 0) {
            $menu = $this->buildNoAssociationsIndexMenu($modelName);
        } else {
            $menu = $this->buildComplexIndexMenu($modelName, $modelId);
        }

        return $menu;
    }

    private function buildComplexIndexMenu($modelName, $modelId) {
        $model = $this->aujaConfigurator->getModel($modelName);
        $relations = $this->aujaConfigurator->getRelationsForModel($model);

        $associationRelations = array();
        foreach ($relations as $relation) {
            if ($relation->getType() == Relation::HAS_MANY || $relation->getType() == Relation::HAS_AND_BELONGS_TO) { // TODO: What to do with one-to-one relations?
                $associationRelations[] = $relation;
            }
        }

        switch (count($associationRelations)) {
            case 0:
                $menu = $this->buildNoAssociationsIndexMenu($modelName);
                break;
            case 1:
                $menu = $this->buildSingleAssociationIndexMenu($modelName, $modelId, $associationRelations[0]);
                break;
            default:
                $menu = $this->buildMultipleAssociationsIndexMenu($modelName, $modelId, $associationRelations);
                break;
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
     * @param String          $modelName   the name of the model the items represent.
     * @param array|Paginator $items       an array of instances of the model to be shown, or a Paginator containing the instances.
     * @param String          $nextPageUrl (optional) The url to the next page, if any.
     * @param int             $offset      (optional) The offset to start the order from.
     *
     * @return ResourceItemsMenuItems[] the built LinkMenuItems.
     */
    public function buildResourceItems($modelName, $items, $nextPageUrl = null, $offset = -1) {
        $factory = $this->app->make('Label305\AujaLaravel\Factory\ResourceItemsFactory');
        /* @var $factory MainFactory */
        return $factory->create($modelName, $items, $nextPageUrl, $offset);
    }

    /**
     * Builds a simple menu for given model, where typically this model should not have any relations to other models.
     *
     * The menu will include:
     *  - An Add LinkMenuItem;
     *  - A SpacerMenuItem with the model's name;
     *  - A ResourceMenuItem to hold entries of the model.
     *
     * @param String $modelName the name of the model.
     *
     * @return Menu the Menu, which can be configured further.
     */
    public function buildNoAssociationsIndexMenu($modelName) {
        $menuFactory = $this->app->make('Label305\AujaLaravel\Factory\NoAssociationsIndexMenuFactory');
        /* @var $menuFactory NoAssociationsIndexMenuFactory */
        return $menuFactory->create($modelName);
    }

    /**
     * Builds a menu for a single model entry, where the model has exactly one relationship with another model.
     *
     * The menu will include:
     *  - An Edit LinkMenuItem;
     *  - A SpacerMenuItem with the name of the associated model;
     *  - A ResourceMenuItem to hold entries of the associated model.
     *
     * @param String   $modelName the name of the model.
     * @param int      $modelId   the id of the model entry.
     * @param Relation $relation  the Relation this model has with the associated model.
     *
     * @return Menu the Menu, which can be configured further.
     */
    public function buildSingleAssociationIndexMenu($modelName, $modelId, Relation $relation) {
        $menuFactory = $this->app->make('Label305\AujaLaravel\Factory\SingleAssociationIndexMenuFactory');
        /* @var $menuFactory SingleAssociationIndexMenuFactory */
        return $menuFactory->create($modelName, $modelId, $relation);
    }

    /**
     * Builds a menu for a single model entry, where the model has multiple relationships with other models.
     *
     * The menu will include:
     *  - An Edit LinkMenuItem;
     *  - For each of the Relations, a LinkMenuItem for the associated model.
     *
     * @param String     $modelName the name of the model.
     * @param int        $modelId   the id of the model entry.
     * @param Relation[] $relations the Relations this model has with associated models.
     *
     * @return Menu the Menu, which can be configured further.
     */
    public function buildMultipleAssociationsIndexMenu($modelName, $modelId, array $relations) {
        $menuFactory = $this->app->make('Label305\AujaLaravel\Factory\MultipleAssociationsIndexMenuFactory');
        /* @var $menuFactory MultipleAssociationsIndexMenuFactory */
        return $menuFactory->create($modelName, $modelId, $relations);
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
    public function buildAssociationMenu($modelName, $modelId, $associationName) {
        $menuFactory = $this->app->make('Label305\AujaLaravel\Factory\AssociationMenuFactory');
        /* @var $menuFactory AssociationMenuFactory */
        return $menuFactory->create($modelName, $modelId, $associationName);
    }

    public function buildPage($modelName, $modelId = 0) {
        $pageFactory = $this->app->make('Label305\AujaLaravel\Factory\PageFactory');
        /* @var $pageFactory PageFactory */
        return $pageFactory->create($modelName, $modelId);
    }

}