<?php

namespace Label305\AujaLaravel\Controllers\Interfaces;

use Illuminate\Http\Response;

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