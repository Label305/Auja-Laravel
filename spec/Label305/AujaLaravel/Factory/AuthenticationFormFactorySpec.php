<?php

namespace spec\Label305\AujaLaravel\Factory;

use Illuminate\Support\Facades\Lang;
use Label305\Auja\Page\Form;
use Label305\Auja\Page\FormItem\PasswordFormItem;
use Label305\Auja\Page\FormItem\SubmitFormItem;
use Label305\Auja\Page\FormItem\TextFormItem;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class AuthenticationFormFactorySpec extends ObjectBehavior {

    function it_is_initializable() {
        $this->shouldHaveType('Label305\AujaLaravel\Factory\AuthenticationFormFactory');
    }

    function it_can_create_a_form() {
        Lang::shouldReceive('trans')->with('Login')->andReturn('Login');

        $this->create('Title', 'Target')->shouldHaveType('Label305\Auja\Page\Form');
    }

    function its_created_form_has_a_proper_action() {
        Lang::shouldReceive('trans')->with('Login')->andReturn('Login');

        $form = $this->create('Title', 'Target')->getWrappedObject();
        /* @var $form Form */

        if ($form->getAction() != 'Target') {
            throw new \Exception('Created Form has wrong action');
        }

        if ($form->getMethod() != 'POST') {
            throw new \Exception('Created Form has wrong action method');
        }
    }

    function its_created_form_has_a_proper_header() {
        Lang::shouldReceive('trans')->with('Login')->andReturn('Login');

        $form = $this->create('Title', 'Target')->getWrappedObject();
        /* @var $form Form */

//        throw new Exception('Not implemented'); // TODO
    }

    function its_created_form_has_a_username_textformitem() {
        Lang::shouldReceive('trans')->with('Login')->andReturn('Login');

        $form = $this->create('Title', 'Target')->getWrappedObject();
        /* @var $form Form */

        $hasUsernameTextFormItem = false;
        $formItems = $form->getFormItems();
        foreach ($formItems as $formItem) {
            if ($formItem instanceof TextFormItem && $formItem->getName() == 'username') {
                $hasUsernameTextFormItem = true;
            }
        }

        if (!$hasUsernameTextFormItem) {
            throw new \Exception('Created Form has no username TextFormItem');
        }
    }

    function its_created_form_has_a_passwordformitem() {
        Lang::shouldReceive('trans')->with('Login')->andReturn('Login');

        $form = $this->create('Title', 'Target')->getWrappedObject();
        /* @var $form Form */

        $hasUsernameTextFormItem = false;
        $formItems = $form->getFormItems();
        foreach ($formItems as $formItem) {
            if ($formItem instanceof PasswordFormItem && $formItem->getName() == 'password') {
                $hasUsernameTextFormItem = true;
            }
        }

        if (!$hasUsernameTextFormItem) {
            throw new \Exception('Created Form has no username TextFormItem');
        }
    }

    function its_created_form_has_a_submitformitem() {
        Lang::shouldReceive('trans')->with('Login')->andReturn('Login');

        $form = $this->create('Title', 'Target')->getWrappedObject();
        /* @var $form Form */

        $hasUsernameTextFormItem = false;
        $formItems = $form->getFormItems();
        foreach ($formItems as $formItem) {
            if ($formItem instanceof SubmitFormItem) {
                $hasUsernameTextFormItem = true;
            }
        }

        if (!$hasUsernameTextFormItem) {
            throw new \Exception('Created Form has no username TextFormItem');
        }
    }
}
