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
 * Controller interface for a resource controller.
 *
 * @author  Thijs Scheepers - <thijs@label305.com>
 *
 * @package Label305\AujaLaravel
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
interface AujaControllerInterface {

    /**
     * Returns a list of all the objects for this controller.
     *
     * @return Response
     */
    public function index();

    /**
     * Returns the menu of a specific object. For example to show all relations.
     *
     * If you have a Clubs and select one it can contain Teams, Members etc. but also the Edit button for that
     * specific Club instance.
     *
     * @param int $id
     * @return Response
     */
    public function menu($id = 0);

    /**
     * Returns the form for creating a new instance of an object.
     *
     * @return Response
     */
    public function create();

    /**
     * This is called with a POST function to create a new instance of an object.
     * You can return a json encoded Auja message.
     *
     * @return Response
     */
    public function store();

    /**
     * Returns the form for creating a new instance of an object.
     *
     * @param $id
     * @return Response
     */
    public function edit($id);

    /**
     * This is called with a PUT function to edit an instance of an object.
     * You can return a json encoded Auja message.
     *
     * @param $id
     * @return Response
     */
    public function update($id);

    /**
     * Called to delete an object. You can return a json encoded Auja message.
     *
     * @param $id
     * @return mixed
     */
    public function delete($id);

} 