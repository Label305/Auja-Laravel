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


class Relation {

    /**
     * A relation type which defines a 'belongs to' relationship:
     * "Left belongs to Right".
     */
    const BELONGS_TO = "belongs_to";

    /**
     * A relation type which defines a 'has many' relationship:
     * "Left has many Rights".
     */
    const HAS_MANY = "has_many";

    /**
     * A relation type which defines a 'has and belongs to' relationship:
     * "Left has and belongs to many Rights".
     */
    const HAS_AND_BELONGS_TO = "has_and_belongs_to";

    /**
     * @var Model the left hand side of the relationship.
     */
    private $left;

    /**
     * @var Model the right hand side of the relationship.
     */
    private $right;

    /**
     * The type of the relationship.
     *
     * @var String must be one of {BELONGS_TO HAS_AND_BELONGS_TO}.
     */
    private $type;

    /**
     * Creates a new Relation.
     *
     * @param $left  Model the left hand side of the relationship.
     * @param $right Model the right hand side of the relationship.
     * @param $type  String the type of the relationship. Must be one of {BELONGS_TO HAS_MANY, HAS_AND_BELONGS_TO}.
     */
    function __construct(Model $left, Model $right, $type) {
        $this->left = $left;
        $this->right = $right;
        $this->type = $type;
    }

    /**
     * @return Model the left hand side of the relationship.
     */
    public function getLeft() {
        return $this->left;
    }

    /**
     * @return Model the right hand side of the relationship.
     */
    public function getRight() {
        return $this->right;
    }

    /**
     * @return String the type of the relationship. One of {BELONGS_TO HAS_MANY, HAS_AND_BELONGS_TO}.
     */
    public function getType() {
        return $this->type;
    }

}