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

namespace Label305\AujaLaravel\Routing;

use Illuminate\Routing\Router;
use Label305\AujaLaravel\Auja;
use Label305\AujaLaravel\Config\Relation;
use Label305\AujaLaravel\Exceptions\ExpectedAujaControllerException;
use Label305\AujaLaravel\Exceptions\ExpectedSupportControllerException;

/**
 * A class for quick Auja Routing.
 *
 * @author  Niek Haarman - <niek@label305.com>
 *
 * @package Label305\AujaLaravel\Routing
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
class AujaRouter {

    /**
     * @var Auja
     */
    private $auja;

    /**
     * @var Router
     */
    private $router;

    /**
     * @var string Group prefix
     */
    private $prefix;

    public function __construct(Auja $auja, Router $router, $prefix) {
        $this->auja = $auja;
        $this->router = $router;
        $this->prefix = $prefix;
    }

    /**
     * Returns the name of the route used for the index url of a model.
     *
     * @param String $modelName The name of the model.
     *
     * @return String The name of the route.
     */
    public function getIndexName($modelName) {
        return sprintf('auja.%s.index', $this->toUrlName($modelName));
    }

    /**
     * Returns the name of the route used for the menu url of a model.
     *
     * @param String $modelName The name of the model.
     *
     * @return String The name of the route.
     */
    public function getMenuName($modelName) {
        return sprintf('auja.%s.menu', $this->toUrlName($modelName));
    }

    /**
     * Returns the name of the route used for the menu url of an entry of a model.
     * The resulting route will take the id of the entry as a parameter.
     *
     * @param String $modelName The name of the model.
     *
     * @return String The name of the route.
     */
    public function getShowMenuName($modelName) {
        return sprintf('auja.%s.show.menu', $this->toUrlName($modelName));
    }

    /**
     * Returns the name of the route used for the create url of a model.
     *
     * @param String $modelName The name of the model.
     *
     * @return String The name of the route.
     */
    public function getCreateName($modelName) {
        return sprintf('auja.%s.create', $this->toUrlName($modelName));
    }

    /**
     * Returns the name of the route used for the url that corresponds to the creation of an associated model.
     *
     * @param String $modelName      The name of the model.
     * @param String $otherModelName The name of the associated model.
     *
     * @return String The name of the route.
     */
    public function getCreateAssociationName($modelName, $otherModelName) {
        return sprintf('auja.%s.%s.create', $this->toUrlName($modelName), $this->toUrlName($otherModelName));
    }

    /**
     * Returns the name of the route used for the store url of a model.
     *
     * @param String $modelName The name of the model.
     *
     * @return String The name of the route.
     */
    public function getStoreName($modelName) {
        return sprintf('auja.%s.store', $this->toUrlName($modelName));
    }

    /**
     * Returns the name of the route used for the show url of an entry of a model.
     * The resulting route will take the id of the entry as a parameter.
     *
     * @param String $modelName The name of the model.
     *
     * @return String The name of the route.
     */
    public function getShowName($modelName) {
        return sprintf('auja.%s.show', $this->toUrlName($modelName));
    }

    /**
     * Returns the name of the route used for the edit url of an entry of a model.
     * The resulting route will take the id of the entry as a parameter.
     *
     * @param String $modelName The name of the model.
     *
     * @return String The name of the route.
     */
    public function getEditName($modelName) {
        return sprintf('auja.%s.edit', $this->toUrlName($modelName));
    }

    /**
     * Returns the name of the route used for the update url of an entry of a model.
     * The resulting route will take the id of the entry as a parameter.
     *
     * @param String $modelName The name of the model.
     *
     * @return String The name of the route.
     */
    public function getUpdateName($modelName) {
        return sprintf('auja.%s.update', $this->toUrlName($modelName));
    }

    /**
     * Returns the name of the route used for the delete url of an entry of a model.
     *
     * @param String $modelName The name of the model.
     *
     * @return String The name of the route.
     */
    public function getDeleteName($modelName) {
        return sprintf('auja.%s.delete', $this->toUrlName($modelName));
    }

    /**
     * Returns the name of the route used for the index url of an associated model.
     *
     * @param String $modelName      The name of the model.
     * @param String $otherModelName The name of the associated model.
     *
     * @return String The name of the route.
     */
    public function getAssociationName($modelName, $otherModelName) {
        return sprintf('auja.%s.%s', $this->toUrlName($modelName), $this->toUrlName($otherModelName));
    }

    /**
     * Returns the name of the route used for the menu url of an associated model.
     *
     * @param String $modelName      The name of the model.
     * @param String $otherModelName The name of the associated model.
     *
     * @return String The name of the route.
     */
    public function getAssociationMenuName($modelName, $otherModelName) {
        return sprintf('auja.%s.%s.menu', $this->toUrlName($modelName), $this->toUrlName($otherModelName));
    }

    /**
     * Route an Auja configuration for a model to a controller.
     *
     * @param String $modelName  The name of the model.
     * @param String $controller The name of the Controller.
     */
    public function resource($modelName, $controller) {
        if (php_sapi_name() == 'cli') {
            /* Don't run when we're running artisan commands. */
            return;
        }

        if (!class_exists($controller)) {
            throw new ExpectedAujaControllerException($controller . ' does not exist.');
        }

        if (!is_subclass_of($controller, 'Label305\AujaLaravel\Controllers\Interfaces\AujaControllerInterface')) {
            throw new ExpectedAujaControllerException(
                $controller . ' does not implement Label305\AujaLaravel\Controllers\Interfaces\AujaControllerInterface'
            );
        }

        /* Default routes */
        $this->registerIndex($modelName, $controller);
        $this->registerMenu($modelName, $controller);
        $this->registerShowMenu($modelName, $controller);
        $this->registerCreate($modelName, $controller);
        $this->registerStore($modelName, $controller);
        $this->registerShow($modelName, $controller);
        $this->registerEdit($modelName, $controller);
        $this->registerUpdate($modelName, $controller);
        $this->registerDelete($modelName, $controller);

        /* Associated routes */
        $model = $this->auja->getModel(ucfirst(str_singular(camel_case($modelName)))); // TODO: prettify
        $relations = $this->auja->getRelationsForModel($model);
        foreach ($relations as $relation) {
            $otherModelName = $relation->getRight()->getName();
            if ($relation->getType() == Relation::BELONGS_TO) {
                $this->registerBelongsToAssociationMenu($modelName, $otherModelName, $controller);
            } else {
                $this->registerAssociation($modelName, $otherModelName, $controller);
                $this->registerAssociationMenu($modelName, $otherModelName, $controller);
                $this->registerCreateAssociation($modelName, $otherModelName, $controller);
            }
        }
    }

    /**
     * @param $options
     * @param $closure
     */
    public function group($options, $closure) {
        if (!is_null($this->prefix)) {
            $options = array_merge($options, ['prefix' => $this->prefix]);
        }

        $this->router->group($options, $closure);
    }

    /**
     * @param $controller
     * @throws ExpectedSupportControllerException
     */
    public function support($controller) {
        if (php_sapi_name() == 'cli') {
            /* Don't run when we're running artisan commands. */
            return;
        }

        if (!is_subclass_of($controller, 'Label305\AujaLaravel\Controllers\Interfaces\SupportControllerInterface')) {
            throw new ExpectedSupportControllerException(
                $controller . ' does not implement Label305\AujaLaravel\Controllers\Interfaces\SupportControllerInterface'
            );
        }

        $this->registerSupportIndex($controller);
        $this->registerSupportMain($controller);
        $this->registerSupportLogin($controller);
        $this->registerSupportLogout($controller);
    }

    private function registerIndex($modelName, $controller) {
        $url = sprintf('%s', $this->toUrlName($modelName));
        $routeName = $this->getIndexName($modelName);
        $action = $controller . '@index';

        $this->router->get($url, ['as' => $routeName, 'uses' => $action]);
    }

    private function registerMenu($modelName, $controller) {
        $url = sprintf('%s/menu', $this->toUrlName($modelName));
        $routeName = $this->getMenuName($modelName);
        $action = $controller . '@menu';

        $this->router->get($url, ['as' => $routeName, 'uses' => $action]);
    }

    private function registerShowMenu($modelName, $controller) {
        $url = sprintf('%s/{id}/menu', $this->toUrlName($modelName));
        $routeName = $this->getShowMenuName($modelName);
        $action = $controller . '@menu';

        $this->router->get($url, ['as' => $routeName, 'uses' => $action]);
    }

    private function registerCreate($modelName, $controller) {
        $url = sprintf('%s/create', $this->toUrlName($modelName));
        $routeName = $this->getCreateName($modelName);
        $action = $controller . '@create';

        $this->router->get($url, ['as' => $routeName, 'uses' => $action]);
    }

    private function registerStore($modelName, $controller) {
        $url = sprintf('%s', $this->toUrlName($modelName));
        $routeName = $this->getStoreName($modelName);
        $action = $controller . '@store';

        $this->router->post($url, ['as' => $routeName, 'uses' => $action]);
    }

    private function registerShow($modelName, $controller) {
        $url = sprintf('%s/{id}', $this->toUrlName($modelName));
        $routeName = $this->getShowName($modelName);
        $action = $controller . '@show';

        $this->router->get($url, ['as' => $routeName, 'uses' => $action]);
    }

    private function registerEdit($modelName, $controller) {
        $url = sprintf('%s/{id}/edit', $this->toUrlName($modelName));
        $routeName = $this->getEditName($modelName);
        $action = $controller . '@edit';

        $this->router->get($url, ['as' => $routeName, 'uses' => $action]);
    }

    private function registerUpdate($modelName, $controller) {
        $url = sprintf('%s/{id}', $this->toUrlName($modelName));
        $routeName = $this->getUpdateName($modelName);
        $action = $controller . '@update';

        $this->router->put($url, ['as' => $routeName, 'uses' => $action]);
    }

    private function registerDelete($modelName, $controller) {
        $url = sprintf('%s/{id}', $this->toUrlName($modelName));
        $routeName = $this->getDeleteName($modelName);
        $action = $controller . '@delete';

        $this->router->delete($url, ['as' => $routeName, 'uses' => $action]);
    }

    private function registerAssociation($modelName, $otherModelName, $controller) {
        $url = sprintf('%s/{id}/%s', $this->toUrlName($modelName), $this->toUrlName($otherModelName));
        $routeName = $this->getAssociationName($modelName, $otherModelName);
        $action = $controller . '@' . str_plural(camel_case($otherModelName));

        $this->router->get($url, ['as' => $routeName, 'uses' => $action]);
    }

    private function registerBelongsToAssociationMenu($modelName, $otherModelName, $controller) {
        $url = sprintf('%s/{id}/%s/menu', $this->toUrlName($modelName), $this->toUrlName($otherModelName));
        $routeName = $this->getAssociationMenuName($modelName, $otherModelName);
        $action = $controller . '@' . camel_case($otherModelName) . 'Menu';

        $this->router->get($url, ['as' => $routeName, 'uses' => $action]);
    }


    private function registerAssociationMenu($modelName, $otherModelName, $controller) {
        $url = sprintf('%s/{id}/%s/menu', $this->toUrlName($modelName), $this->toUrlName($otherModelName));
        $routeName = $this->getAssociationMenuName($modelName, $otherModelName);
        $action = $controller . '@' . str_plural(camel_case($otherModelName)) . 'Menu';

        $this->router->get($url, ['as' => $routeName, 'uses' => $action]);
    }

    private function registerCreateAssociation($modelName, $otherModelName, $controller) {
        $url = sprintf('%s/{id}/%s/create', $this->toUrlName($modelName), $this->toUrlName($otherModelName));
        $routeName = $this->getCreateAssociationName($modelName, $otherModelName);
        $action = $controller . '@' . 'create' . ucfirst(camel_case($otherModelName));

        $this->router->get($url, ['as' => $routeName, 'uses' => $action]);
    }

    private function registerSupportIndex($controller) {
        $action = $controller . '@index';
        $this->router->get('/', ['as' => 'auja.support.index', 'uses' => $action]);
    }

    private function registerSupportMain($controller) {
        $action = $controller . '@main';
        $this->router->get('main', ['as' => 'auja.support.main', 'uses' => $action]);
    }

    private function registerSupportLogin($controller) {
        $action = $controller . '@login';
        $this->router->post('login', ['as' => 'auja.support.login', 'uses' => $action]);
    }

    private function registerSupportLogout($controller) {
        $action = $controller . '@logout';
        $this->router->get('logout', ['as' => 'auja.support.logout', 'uses' => $action]);
    }

    private function toUrlName($modelName) {
        return strtolower(str_plural($modelName));
    }
} 