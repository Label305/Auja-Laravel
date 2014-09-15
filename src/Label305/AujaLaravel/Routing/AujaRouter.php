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


use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use Label305\AujaLaravel\Auja;

class AujaRouter {

    /**
     * @var Auja
     */
    private $auja;

    /**
     * @var Router
     */
    private $router;

    public function __construct(Auja $auja, Router $router) {
        $this->auja = $auja;
        $this->router = $router;
    }

    public function getIndexName($modelName) {
        return sprintf('auja.%s.index', $this->toUrlName($modelName));
    }

    public function getMenuName($modelName) {
        return sprintf('auja.%s.menu', $this->toUrlName($modelName));
    }

    public function getShowMenuName($modelName) {
        return sprintf('auja.%s.show.menu', $this->toUrlName($modelName));
    }

    public function getCreateName($modelName) {
        return sprintf('auja.%s.create', $this->toUrlName($modelName));
    }

    public function getStoreName($modelName) {
        return sprintf('auja.%s.store', $this->toUrlName($modelName));
    }

    public function getShowName($modelName) {
        return sprintf('auja.%s.show', $this->toUrlName($modelName));
    }

    public function getEditName($modelName) {
        return sprintf('auja.%s.edit', $this->toUrlName($modelName));
    }

    public function getUpdateName($modelName) {
        return sprintf('auja.%s.update', $this->toUrlName($modelName));
    }

    public function getDeleteName($modelName) {
        return sprintf('auja.%s.delete', $this->toUrlName($modelName));
    }

    public function getAssociationName($modelName, $otherModelName) {
        return sprintf('auja.%s.%s', $this->toUrlName($modelName), $this->toUrlName($otherModelName));
    }

    public function getAssociationMenuName($modelName, $otherModelName) {
        return sprintf('auja.%s.%s.menu', $this->toUrlName($modelName), $this->toUrlName($otherModelName));
    }

    public function resource($modelName, $controller) {
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
        $model = $this->auja->getModel(ucfirst(str_singular(camel_case($modelName))));
        $relations = $this->auja->getRelationsForModel($model);
        foreach ($relations as $relation) {
            $otherModelName = $relation->getRight()->getName();
            $this->registerAssociation($modelName, $otherModelName, $controller);
            $this->registerAssociationMenu($modelName, $otherModelName, $controller);
        }
    }

    private function registerIndex($modelName, $controller) {
        $url = sprintf('%s/index', $this->toUrlName($modelName));
        $routeName = $this->getIndexName($modelName);
        $action = $controller . '@index';

        $this->router->get($url, array('as' => $routeName, 'uses' => $action));
    }

    private function registerMenu($modelName, $controller) {
        $url = sprintf('%s/menu', $this->toUrlName($modelName));
        $routeName = $this->getMenuName($modelName);
        $action = $controller . '@menu';

        $this->router->get($url, array('as' => $routeName, 'uses' => $action));
    }

    private function registerShowMenu($modelName, $controller) {
        $url = sprintf('%s/{id}/menu', $this->toUrlName($modelName));
        $routeName = $this->getShowMenuName($modelName);
        $action = $controller . '@menu';

        $this->router->get($url, array('as' => $routeName, 'uses' => $action));
    }

    private function registerCreate($modelName, $controller) {
        $url = sprintf('%s/create', $this->toUrlName($modelName));
        $routeName = $this->getCreateName($modelName);
        $action = $controller . '@create';

        $this->router->get($url, array('as' => $routeName, 'uses' => $action));
    }

    private function registerStore($modelName, $controller) {
        $url = sprintf('%s', $this->toUrlName($modelName));
        $routeName = $this->getStoreName($modelName);
        $action = $controller . '@store';

        $this->router->post($url, array('as' => $routeName, 'uses' => $action));
    }

    private function registerShow($modelName, $controller) {
        $url = sprintf('%s/{id}', $this->toUrlName($modelName));
        $routeName = $this->getShowName($modelName);
        $action = $controller . '@show';

        $this->router->post($url, array('as' => $routeName, 'uses' => $action));
    }

    private function registerEdit($modelName, $controller) {
        $url = sprintf('%s/{id}/edit', $this->toUrlName($modelName));
        $routeName = $this->getEditName($modelName);
        $action = $controller . '@edit';

        $this->router->post($url, array('as' => $routeName, 'uses' => $action));
    }

    private function registerUpdate($modelName, $controller) {
        $url = sprintf('%s/{id}', $this->toUrlName($modelName));
        $routeName = $this->getUpdateName($modelName);
        $action = $controller . '@update';

        $this->router->put($url, array('as' => $routeName, 'uses' => $action));
    }

    private function registerDelete($modelName, $controller) {
        $url = sprintf('%s/{id}', $this->toUrlName($modelName));
        $routeName = $this->getDeleteName($modelName);
        $action = $controller . '@delete';

        $this->router->post($url, array('as' => $routeName, 'uses' => $action));
    }

    private function registerAssociation($modelName, $otherModelName, $controller) {
        $url = sprintf('%s/{id}/%s', $this->toUrlName($modelName), $this->toUrlName($otherModelName));
        $routeName = $this->getAssociationName($modelName, $otherModelName);
        $action = $controller . '@' . str_plural(camel_case($otherModelName));

        $this->router->post($url, array('as' => $routeName, 'uses' => $action));
    }

    private function registerAssociationMenu($modelName, $otherModelName, $controller) {
        $url = sprintf('%s/{id}/%s/menu', $this->toUrlName($modelName), $this->toUrlName($otherModelName));
        $routeName = $this->getAssociationMenuName($modelName, $otherModelName);
        $action = $controller . '@' . str_plural(camel_case($otherModelName)).'Menu';

        $this->router->post($url, array('as' => $routeName, 'uses' => $action));
    }

    private function toUrlName($modelName) {
        return strtolower(str_plural($modelName));
    }
} 