<?php

namespace spec\Label305\AujaLaravel\Factory;

use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use Label305\Auja\Menu\LinkMenuItem;
use Label305\Auja\Menu\Menu;
use Label305\Auja\Menu\ResourceMenuItem;
use Label305\Auja\Menu\SpacerMenuItem;
use Label305\Auja\Page\Form;
use Label305\Auja\Page\FormItem\PasswordFormItem;
use Label305\Auja\Page\FormItem\SubmitFormItem;
use Label305\Auja\Page\FormItem\TextFormItem;
use Label305\AujaLaravel\I18N\Translator;
use Label305\AujaLaravel\Routing\AujaRouter;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Config\Definition\Exception\Exception;

class AuthenticationFormFactorySpec extends ObjectBehavior {

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

//        throw new Exception('Not implemented'); // TODO
    }

    function its_created_form_has_a_username_textformitem() {
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
