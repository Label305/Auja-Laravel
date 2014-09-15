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

namespace Label305\AujaLaravel;

use Doctrine\DBAL\Types\Type;
use Label305\Auja\Page\FormItem\CheckboxFormItem;
use Label305\Auja\Page\FormItem\DateFormItem;
use Label305\Auja\Page\FormItem\DateTimeFormItem;
use Label305\Auja\Page\FormItem\FormItem;
use Label305\Auja\Page\FormItem\IntegerFormItem;
use Label305\Auja\Page\FormItem\NumberFormItem;
use Label305\Auja\Page\FormItem\PasswordFormItem;
use Label305\Auja\Page\FormItem\TextAreaFormItem;
use Label305\Auja\Page\FormItem\TextFormItem;
use Label305\Auja\Page\FormItem\TimeFormItem;

/**
 * A factory class for creating PageComponents out of types.
 *
 * @package Label305\AujaLaravel
 */
class FormItemFactory {

    /**
     * @param $type   String the type of the column to create a PageFormItem for. See Doctrine\DBAL\Types\Type for the supported types.
     * @param $hidden bool whether the column is hidden.
     *
     * @return FormItem the created PageFormItem.
     */
    public static function getFormItem($type, $hidden) {
        if ($hidden) {
            return new PasswordFormItem();
        }

        $result = null;
        switch ($type) {
            case Type::TEXT:
            case Type::TARRAY:
            case Type::SIMPLE_ARRAY:
            case Type::JSON_ARRAY:
            case Type::OBJECT:
            case Type::BLOB:
                $result = new TextAreaFormItem();
                break;
            case Type::INTEGER:
            case Type::SMALLINT:
            case Type::BIGINT:
                $result = new IntegerFormItem();
                break;
            case Type::DECIMAL:
            case Type::FLOAT:
                $result = new NumberFormItem();
                break;
            case Type::BOOLEAN:
                $result = new CheckboxFormItem();
                break;
            case Type::DATE:
                $result = new DateFormItem();
                break;
            case Type::DATETIME:
            case Type::DATETIMETZ:
                $result = new DateTimeFormItem();
                break;
            case Type::TIME:
                $result = new TimeFormItem();
                break;
            case Type::STRING:
            case Type::GUID:
            default:
                $result = new TextFormItem();
                break;
        }

        return $result;
    }
}