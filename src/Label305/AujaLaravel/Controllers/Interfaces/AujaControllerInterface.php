<?php
/**
 * Created by PhpStorm.
 * User: Thijs
 * Date: 12-12-14
 * Time: 16:11
 */

namespace Label305\AujaLaravel\Controllers\Interfaces;

use Illuminate\Http\Response;

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