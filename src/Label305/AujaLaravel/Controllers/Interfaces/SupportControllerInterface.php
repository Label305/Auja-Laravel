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

namespace Label305\AujaLaravel\Controllers\Interfaces;

use Illuminate\Http\Response;

/**
 * Controller interface for supporting basic Auja functions like authentication and creating the main interface.
 *
 * @author  Thijs Scheepers - <thijslabel305.com>
 *
 * @package Label305\AujaLaravel
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
interface SupportControllerInterface {

    /**
     * Returns the HTML view in which the JavaScript is loaded for the auja interface.
     *
     * @return Response
     */
    public function index();

    /**
     * Returns the Auja main manifest containing information about the login state, theme of the interface
     * and the tabs on the side bar.
     *
     * @return Response
     */
    public function main();

    /**
     * @return Response json response with a Auja message
     */
    public function login();

    /**
     * @return Response redirects to the index after logout.
     */
    public function logout();

}