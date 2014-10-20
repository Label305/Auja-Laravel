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

use Label305\Auja\Shared\Message;
use Label305\AujaLaravel\Auja;

class AujaController extends \Controller {

    /**
     * @var Auja the Auja instance.
     */
    protected $auja;

    function __construct(Auja $auja) {
        $this->auja = $auja;
    }

    public function index() {
        return View::make('index');
    }

    public function main() {
        $authenticationForm = $this->auja->buildAuthenticationForm('Auja-Laravel-Example', '/login');
        $main = $this->auja->buildMain('Auja-Laravel-Example', $authenticationForm);
        $main->setAuthenticated(true);
        return $main;
    }

    public function login() {
        $message = new Message();
        $message->setAuthenticated(true);
        return $message;
    }

} 