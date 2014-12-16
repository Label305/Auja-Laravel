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

namespace Label305\AujaLaravel\Controllers;

use Auja;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Label305\AujaLaravel\Controllers\Interfaces\AujaControllerInterface;

/**
 * Controller abstract the developer can use to create a resource controller without implementing all required methods.
 *
 * @author  Thijs Scheepers - <thijs@label305.com>
 *
 * @package Label305\AujaLaravel
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
abstract class AujaControler extends Controller implements AujaControllerInterface {

    /**
     * Returns a list of all the objects for this controller.
     *
     * @return Response
     */
    public function index()
    {
        return new JsonResponse(
            Auja::itemsFor($this)
        );
    }

    /**
     * Returns the general menu which often contains a "add" button and a resource which
     * in turn calls for the resource in a separate request to index()
     *
     * @return Response
     */
    public function menu()
    {
        return new JsonResponse(
            Auja::menuFor($this)
        );
    }

    /**
     * Returns the menu of a specific object. For example to show all relations.
     *
     * If you have a Clubs and select one it can contain Teams, Members etc. but also the Edit button for that
     * specific Club instance.
     *
     * @param int $id
     * @return Response
     */
    public function showMenu($id)
    {
        return new JsonResponse(
            Auja::menuFor($this, $id)
        );
    }

    /**
     * Returns the form for creating a new instance of an object.
     *
     * @return Response
     */
    public function create()
    {
        return new JsonResponse(
            Auja::pageFor($this)
        );
    }

    /**
     * This is called with a POST function to create a new instance of an object.
     * You can return a json encoded Auja message.
     *
     * @return Response
     */
    public abstract function store();

    /**
     * Returns the form for creating a new instance of an object.
     *
     * @param $id
     * @return Response
     */
    public function edit($id)
    {
        return new JsonResponse(
            Auja::pageFor($this, $id)
        );
    }

    /**
     * This is called with a PUT function to edit an instance of an object.
     * You can return a json encoded Auja message.
     *
     * @param $id
     * @return Response
     */
    public abstract function update($id);

    /**
     * Called to delete an object. You can return a json encoded Auja message.
     *
     * @param $id
     * @return mixed
     */
    public abstract function delete($id);
}