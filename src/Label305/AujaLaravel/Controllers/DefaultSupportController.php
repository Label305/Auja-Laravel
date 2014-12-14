<?php

namespace Label305\AujaLaravel\Controllers;

use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Label305\Auja\Main\Main;
use Label305\Auja\Shared\Message;
use Label305\AujaLaravel\Auja;
use Label305\AujaLaravel\Controllers\Interfaces\SupportControllerInterface;

class DefaultSupportController extends Controller implements SupportControllerInterface {

    /**
     * @var Application
     */
	public $app;

    /**
     * @param Application $app
     * @param Auja $auja
     */
	public function __construct(Application $app) {
        $this->app = $app;
    }

    /**
     * Returns the HTML view in which the JavaScript is loaded for the auja interface.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {

        return $this->app['view']->make('auja-laravel::admin.index');
    }

    /**
     * Returns the Auja main manifest containing information about the login state, theme of the interface
     * and the tabs on the side bar.
     *
     * @return \Illuminate\Http\Response json response with the auja main manifest
     */
    public function main() {

        $config = $this->app['config']['auja'] ?: $this->app['config']['auja-laravel::config'];

        $authenticationForm = $this->app['auja']->authenticationForm(
            $config['title'],
            $this->app['url']->route('auja.support.login', [], false)
        );

        $username = ($this->app['auth']->user() == null) ? null : $this->app['auth']->user()->name;

        $main = $this->app['auja']->main(
            $config['title'],
            $this->app['auth']->check(),
        	$username,
            $this->app['url']->route('auja.support.logout', [], false),
        	$authenticationForm
        );

        $main->setColor(Main::COLOR_MAIN, $config['color']['main']);
        $main->setColor(Main::COLOR_ALERT, $config['color']['alert']);
        $main->setColor(Main::COLOR_SECONDARY, $config['color']['secondary']);

        return new JsonResponse($main);
    }

    /**
     * @return \Illuminate\Http\Response json response with a auja message
     */
    public function login() {

        $this->app['auth']->attempt([
            'email' => $this->app['request']->input('email'),
            'password' => $this->app['request']->input('password')
        ]);

        $message = new Message();
        $message->setAuthenticated($this->app['auth']->check());

        return new JsonResponse($message);
    }

    /**
     * @return \Illuminate\Http\Response redirects to the index after logout.
     */
    public function logout() {

        $this->app['auth']->logout();

        return $this->app['redirect']->route(
            'auja.support.index'
        );
    }

}