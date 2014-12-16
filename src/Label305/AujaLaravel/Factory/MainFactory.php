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


use Illuminate\Support\Facades\HTML;
use Illuminate\Support\Facades\URL;
use Label305\Auja\Main\Item;
use Label305\Auja\Main\Main;
use Label305\Auja\Page\Form;
use Label305\Auja\Shared\Button;
use Label305\AujaLaravel\Config\AujaConfigurator;
use Label305\AujaLaravel\Config\ModelConfig;
use Label305\AujaLaravel\Routing\AujaRouter;

class MainFactory {

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
     * @param $title
     * @param $authenticated
     * @param null $username
     * @param null $logoutTarget
     * @param Form $authenticationForm
     * @param ModelConfig $config
     * @param null $additionalMenuItems
     * @param bool $smartMenuItemInclude
     * @return Main
     */
    public function create($title, $authenticated, $username = null, $logoutTarget = null, Form $authenticationForm = null, ModelConfig $config = null, $additionalMenuItems = null, $smartMenuItemInclude = true) {
        $main = new Main();

        $main->setTitle($title);
        $main->setUsername($username);
        $main->setAuthenticated($authenticated);

        if ($logoutTarget !== null) {
            $button = new Button();
            $button->setText('Logout');
            $button->setTarget($logoutTarget);
            $main->addButton($button);
        }

        if ($additionalMenuItems !== null) {
            foreach ($additionalMenuItems as $item) {
                $main->addItem($item);
            }
        }

        if ($smartMenuItemInclude) {
            $this->smartIncludeMenuItems($main, $config);
        }

        $main->setAuthenticationForm($authenticationForm);

        return $main;
    }

    private function smartIncludeMenuItems($main, $config) {

        foreach ($this->aujaConfigurator->getModels() as $model) {
            if ($this->aujaConfigurator->shouldSmartIncludeInMain($model, $config)) {
                $item = new Item();
                $item->setTitle($model->getName());
                $item->setIcon($this->aujaConfigurator->getIcon($model, $config));
                $item->setTarget(Url::route($this->aujaRouter->getMenuName($model->getName())));
                $main->addItem($item);
            }
        }
    }


} 