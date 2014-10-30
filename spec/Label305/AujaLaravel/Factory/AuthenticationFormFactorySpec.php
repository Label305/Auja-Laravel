<?php

namespace spec\Label305\AujaLaravel\Factory;

use Illuminate\Support\Facades\Lang;
use Label305\Auja\Page\Form;
use Label305\Auja\Page\FormItem\FormHeader;
use Label305\Auja\Page\FormItem\PasswordFormItem;
use Label305\Auja\Page\FormItem\SubmitFormItem;
use Label305\Auja\Page\FormItem\TextFormItem;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class AuthenticationFormFactorySpec extends ObjectBehavior {

    function let() {
        Lang::shouldReceive('trans')->with('Login')->andReturn('Login');
        Lang::shouldReceive('trans')->with('Email address')->andReturn('Email address');
    }

    function it_is_initializable() {
        $this->shouldHaveType('Label305\AujaLaravel\Factory\AuthenticationFormFactory');
    }

    function it_can_create_a_form() {
        $this->create('Title', 'Target')->shouldHaveType('Label305\Auja\Page\Form');
    }

    function its_created_form_has_a_proper_action() {
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
        $form = $this->create('Title', 'Target')->getWrappedObject();
        /* @var $form Form */

        $hasHeader = false;
        $formItems = $form->getFormItems();
        foreach ($formItems as $formItem) {
            if ($formItem instanceof FormHeader && $formItem->getText() == 'Title') {
                $hasHeader = true;
            }
        }

        if (!$hasHeader) {
            throw new \Exception('Created Form has no Header');
        }
    }

    function its_created_form_has_an_email_textformitem() {
        $form = $this->create('Title', 'Target')->getWrappedObject();
        /* @var $form Form */

        $hasUsernameTextFormItem = false;
        $formItems = $form->getFormItems();
        foreach ($formItems as $formItem) {
            if ($formItem instanceof TextFormItem && $formItem->getName() == 'email') {
                $hasUsernameTextFormItem = true;
            }
        }

        if (!$hasUsernameTextFormItem) {
            throw new \Exception('Created Form has no username TextFormItem');
        }
    }

    function its_created_form_has_a_passwordformitem() {
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
