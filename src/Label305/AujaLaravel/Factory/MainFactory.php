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


use Illuminate\Support\Facades\URL;
use Label305\Auja\Main\Item;
use Label305\Auja\Main\Main;
use Label305\Auja\Page\Form;
use Label305\Auja\Shared\Button;
use Label305\AujaLaravel\Config\AujaConfigurator;

class MainFactory {

    /**
     * @var AujaConfigurator
     */
    private $aujaConfigurator;

    public function __construct(AujaConfigurator $aujaConfigurator) {
        $this->aujaConfigurator = $aujaConfigurator;
    }

    public function create($title, Form $authenticationForm = null) {
        $main = new Main();

        $main->setTitle($title);

        $main->setColor(MAIN::COLOR_MAIN, '#1ebab8'); // TODO: remove colors
        $main->setColor(MAIN::COLOR_SECONDARY, '#E7EFEF');
        $main->setColor(Main::COLOR_ALERT, '#e85840');

        $button = new Button();
        $button->setTitle('Logout');
        $button->setTarget('#logout'); // TODO proper url
        $main->addButton($button);

        $main->setUsername('Niek Haarman'); // TODO proper user

        foreach ($this->aujaConfigurator->getModels() as $model) {
            $item = new Item();
            $item->setTitle($model->getName());
            $item->setIcon($this->aujaConfigurator->getIcon($model));
//            $item->setTarget(sprintf('/%s/menu', self::toUrlName($model->getName()))); //  TODO: proper target
            $main->addItem($item);
        }

        $main->setAuthenticationForm($authenticationForm);

        return $main;
    }

} 