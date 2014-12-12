<?php

namespace Label305\Controllers;

class SupportController implements SupportControllerInterface {

	public $view;

	public $auja;

	public $auth;

	public $input;

	public $redirect;

	public function __construct(View $view, Auja $auja) {
        $this->view = $app;
    }

    public function index() {
        return View::make('Auja::admin/index');
    }

    public function main() {
        $username = Auth::user() == null ? null : Auth::user()->name;

        $authenticationForm = Auja::authenticationForm(
        	'Nedap Healthcare',
        	'admin/login'
        );

        return Auja::main(
        	'Nedap Healthcare',
        	Auth::check(),
        	$username,
        	'admin/logout',
        	$authenticationForm
        );
    }

    public function login() {
        Auth::attempt(['email' => Input::get('email'), 'password' => Input::get('password')]);

        $message = new Message();
        $message->setAuthenticated(\Auth::check());
        return $message;
    }

    public function logout() {
        Auth::logout();
        return Redirect::to('Admin');
    }

}