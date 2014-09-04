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
use Label305\Auja\Page\CheckboxFormItem;
use Label305\Auja\Page\DateFormItem;
use Label305\Auja\Page\DateTimeFormItem;
use Label305\Auja\Page\IntegerFormItem;
use Label305\Auja\Page\NumberFormItem;
use Label305\Auja\Page\PageFormItem;
use Label305\Auja\Page\PasswordFormItem;
use Label305\Auja\Page\TextAreaFormItem;
use Label305\Auja\Page\TextFormItem;
use Label305\Auja\Page\TimeFormItem;

/**
 * A factory class for creating PageComponents out of types.
 *
 * @package Label305\AujaLaravel
 */
class PageFormItemFactory {

    /**
     * @param $type   String the type of the column to create a PageFormItem for. See Doctrine\DBAL\Types\Type for the supported types.
     * @param $hidden bool whether the column is hidden.
     *
     * @return PageFormItem the created PageFormItem.
     */
    public static function getPageFormItem($type, $hidden) {
        if ($hidden) {
            return new PasswordFormItem();
        }

        $result = null;
        switch ($type) {
            case Type::TEXT:
            case TYPE::TARRAY:
            case TYPE::SIMPLE_ARRAY:
            case TYPE::JSON_ARRAY:
            case TYPE::OBJECT:
            case TYPE::BLOB:
                $result = new TextAreaFormItem();
                break;
            case TYPE::INTEGER:
            case TYPE::SMALLINT:
            case TYPE::BIGINT:
                $result = new IntegerFormItem();
                break;
            case TYPE::DECIMAL:
            case TYPE::FLOAT:
                $result = new NumberFormItem();
                break;
            case TYPE::BOOLEAN:
                $result = new CheckboxFormItem();
                break;
            case TYPE::DATE:
                $result = new DateFormItem();
                break;
            case TYPE::DATETIME:
            case TYPE::DATETIMETZ:
                $result = new DateTimeFormItem();
                break;
            case TYPE::TIME:
                $result = new TimeFormItem();
                break;
            case Type::STRING:
            case TYPE::GUID:
            default:
                $result = new TextFormItem();
                break;
        }

        return $result;
    }
}