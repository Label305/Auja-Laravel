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

namespace Label305\AujaLaravel\Factory;


use Label305\Auja\Page\Form;
use Label305\Auja\Page\FormItem\PasswordFormItem;
use Label305\Auja\Page\FormItem\SubmitFormItem;
use Label305\Auja\Page\FormItem\TextFormItem;
use Label305\AujaLaravel\I18N\Translator;

class AuthenticationFormFactory {

    /**
     * @var Translator
     */
    private $translator;

    public function __construct(Translator $translator) {
        $this->translator = $translator;
    }

    public function create($title, $target) {
        $result = new Form();

        $result->setAction($target);
        $result->setMethod('POST');

        // TODO: add Header

        $usernameTextFormItem = new TextFormItem();
        $usernameTextFormItem->setName('username');
        $usernameTextFormItem->setLabel($this->translator->trans('Username'));
        $result->addFormItem($usernameTextFormItem);

        $passwordFormItem = new PasswordFormItem();
        $passwordFormItem->setName('password');
        $passwordFormItem->setLabel($this->translator->trans('Password'));
        $result->addFormItem($passwordFormItem);

        $submitFormItem = new SubmitFormItem();
        $submitFormItem->setText($this->translator->trans('Login'));
        $result->addFormItem($submitFormItem);

        return $result;
    }

    protected function createHeader($title) {
        // TODO: add Header
    }


} 