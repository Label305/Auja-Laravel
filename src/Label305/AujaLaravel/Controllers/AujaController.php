<?php
/**
 * Created by PhpStorm.
 * User: Thijs
 * Date: 12-12-14
 * Time: 16:09
 */

namespace Label305\AujaLaravel\Controllers;

use Auja;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Label305\AujaLaravel\Controllers\Interfaces\AujaControllerInterface;

abstract class AujaControler extends Controller implements AujaControllerInterface {

    /**
     * Returns a list of all the objects for this controller.
     *
     * @return Response
     */
    public function index()
    {
        return Auja::itemsFor($this);
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
    public function menu($id = 0)
    {
        return Auja::menuFor($this, $id);
    }

    /**
     * Returns the form for creating a new instance of an object.
     *
     * @return Response
     */
    public function create()
    {
        return Auja::pageFor($this);
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
        return Auja::pageFor($this, $id);
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