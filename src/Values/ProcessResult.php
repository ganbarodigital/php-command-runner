<?php

/**
 * Copyright (c) 2011-present MediaSift Ltd
 * Copyright (c) 2015-present Ganbaro Digital Ltd
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the names of the copyright holders nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @category  Libraries
 * @package   ProcessRunner/Values
 * @author    Stuart Herbert <stuherbert@ganbarodigital.com>
 * @copyright 2011-present MediaSift Ltd www.datasift.com
 * @copyright 2015-present Ganbaro Digital Ltd www.ganbarodigital.com
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link      http://code.ganbarodigital.com/php-process-runner
 */

namespace GanbaroDigital\ProcessRunner\Values;

use GanbaroDigital\ProcessRunner\Exceptions\E4xx_UnsupportedType;
use GanbaroDigital\ProcessRunner\ValueBuilders\BuildEscapedCommandLine;
use GanbaroDigital\DataContainers\Containers\LazyValueObject;
use GanbaroDigital\Reflection\Requirements\RequireNumeric;
use GanbaroDigital\Reflection\Requirements\RequireStringy;
use GanbaroDigital\Reflection\Requirements\RequireTraversable;

/**
 * @method int getReturnCode()
 * @method void setReturnCode(int)
 * @method string getOutput()
 * @method void   setOutput(string)
 * @method array getCommand()
 * @method void  setCommand(array)
 */
class ProcessResult extends LazyValueObject
{
    public function __construct(array $command, $returnCode, $output)
    {
        // robustness!
        RequireTraversable::checkMixed($command, E4xx_UnsupportedType::class);
        RequireNumeric::check($returnCode, E4xx_UnsupportedType::class);
        RequireStringy::checkMixed($output, E4xx_UnsupportedType::class);

        $this->setCommand($command);
        $this->setReturnCode($returnCode);
        $this->setOutput($output);

        // all done
        $this->makeReadOnly();
    }

    public function getCommandAsString()
    {
        return BuildEscapedCommandLine::fromArray($this->getCommand());
    }
}