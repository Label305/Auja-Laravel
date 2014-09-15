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

namespace Label305\AujaLaravel\Logging;

/**
 * An interface for logging.
 *
 * @author  Niek Haarman - <niek@label305.com>
 *
 * @package Label305\AujaLaravel\Logging
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
interface Logger {

    /**
     * Adds a log record at the ERROR level.
     *
     * @param  string $message The log message
     * @param  array  $context The log context
     *
     * @return Boolean Whether the record has been processed
     */
    public function err($message, array $context = array());

    /**
     * Adds a log record at the WARNING level.
     *
     * @param  string $message The log message
     * @param  array  $context The log context
     *
     * @return Boolean Whether the record has been processed
     */
    public function warn($message, array $context = array());

    /**
     * Adds a log record at the INFO level.
     *
     * @param  string $message The log message
     * @param  array  $context The log context
     *
     * @return Boolean Whether the record has been processed
     */
    public function info($message, array $context = array());

    /**
     * Adds a log record at the DEBUG level.
     *
     * @param  string $message The log message
     * @param  array  $context The log context
     *
     * @return Boolean Whether the record has been processed
     */
    public function debug($message, array $context = array());

} 