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

use Doctrine\DBAL\Types\Type;
use Illuminate\Support\Facades\Lang;
use Label305\Auja\Page\FormItem\CheckboxFormItem;
use Label305\Auja\Page\FormItem\DateFormItem;
use Label305\Auja\Page\FormItem\DateTimeFormItem;
use Label305\Auja\Page\FormItem\FormItem;
use Label305\Auja\Page\FormItem\IntegerFormItem;
use Label305\Auja\Page\FormItem\NumberFormItem;
use Label305\Auja\Page\FormItem\PasswordFormItem;
use Label305\Auja\Page\FormItem\SelectFormItem;
use Label305\Auja\Page\FormItem\SelectOption;
use Label305\Auja\Page\FormItem\TextAreaFormItem;
use Label305\Auja\Page\FormItem\TextFormItem;
use Label305\Auja\Page\FormItem\TimeFormItem;
use Label305\AujaLaravel\Config\AujaConfigurator;
use Label305\AujaLaravel\Config\Column;
use Label305\AujaLaravel\Config\Model;

/**
 * A factory class for creating PageComponents out of types.
 *
 * @package Label305\AujaLaravel\Factory
 */
class FormItemFactory {

    /**
     * @var AujaConfigurator
     */
    private $aujaConfigurator;

    public function __construct(AujaConfigurator $aujaConfigurator) {
        $this->aujaConfigurator = $aujaConfigurator;
    }

    /**
     * @param Model     $model  The `Model` which contains given `Column`.
     * @param Column    $column The `Column` to create a `FormItem` for.
     * @param \Eloquent $item   The instance to retrieve information from for filling the `FormItem`.
     *
     * @return FormItem The created `FormItem`.
     */
    public function getFormItem(Model $model, Column $column, $item) {
        $result = null;

        if (ends_with($column->getName(), '_id')) {
            $result = $this->createSelectAssociationFormItem($model, $column, $item);
        } else {
            $result = $this->createFromType($model, $column, $item);
        }

        return $result;
    }

    /**
     * Returns a `SelectFormItem` which is filled with instances of the model given `Column` represents.
     *
     * @param Model     $model  The `Model` which contains given `Column`.
     * @param Column    $column The `Column` which represents a related model.
     * @param \Eloquent $item   The instance to retrieve information from for filling the `SelectFormItem`.
     *
     * @return SelectFormItem The created `SelectFormItem`.
     */
    private function createSelectAssociationFormItem(Model $model, Column $column, $item) {
        $result = new SelectFormItem();

        $relations = $this->aujaConfigurator->getRelationsForModel($model);
        $relatedModel = null;
        foreach ($relations as $relation) {
            $rightModel = $relation->getRight();
            if (starts_with($column->getName(), camel_case($rightModel->getName()))) {
                $relatedModel = $rightModel;
            }
        }

        if ($relatedModel != null) {
            $displayName = $this->aujaConfigurator->getDisplayName($relatedModel);
            $result->setName($displayName);

            $result->setValue($item->id);

            $items = call_user_func(array($relatedModel->getName(), 'all'));
            $displayField = $this->aujaConfigurator->getDisplayField($relatedModel);
            foreach ($items as $item) {
                $label = isset($item->$displayField) ? $item->$displayField : '';
                $value = $item->id;

                $option = new SelectOption($label, $value);
                $result->addOption($option);
            }
        }

        return $result;
    }

    /**
     * Returns a `FormItem` based on the type of the `Column`.
     *
     * @param Model     $model  The `Model` which contains given `Column`.
     * @param Column    $column The `Column` to create a `FormItem` for.
     * @param \Eloquent $item   The instance to retrieve information from for filling the `FormItem`.
     *
     * @return FormItem The created `FormItem`.
     */
    private function createFromType(Model $model, Column $column, $item) {
        $result = null;
        switch ($column->getType()) {
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

        $columnName = $column->getName();
        $result->setName($columnName);
        $result->setLabel(Lang::trans($this->aujaConfigurator->getColumnDisplayName($model, $columnName)));

        if ($item != null && isset($item->$columnName)) {
            $result->setValue($item->$columnName);
        }

        return $result;
    }
}