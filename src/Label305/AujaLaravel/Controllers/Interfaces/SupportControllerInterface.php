<?php

namespace Label305\Controllers\Interfaces;

interface SupportControllerInterface {

    /**
     * @return Response
     */
    public function index();

    /**
     * @return Response
     */
    public function main();

    /**
     * @return Response
     */
    public function login();

    /**
     * @return Response
     */
    public function logout();

}