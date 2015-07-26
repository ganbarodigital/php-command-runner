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

use PHPUnit_Framework_TestCase;

/**
 * @coversDefaultClass GanbaroDigital\ProcessRunner\Values\ProcessResult
 */
class ProcessResultTest extends PHPUnit_Framework_TestCase
{
    /**
     * @coversNothing
     */
    public function testCanInstantiate()
    {
        // ----------------------------------------------------------------
        // setup your test



        // ----------------------------------------------------------------
        // perform the change

        $obj = new ProcessResult([], 0, '');

        // ----------------------------------------------------------------
        // test the results

        $this->assertTrue($obj instanceof ProcessResult);
    }

    /**
     * @covers ::__construct
     */
    public function testCanGetCommandDetails()
    {
        // ----------------------------------------------------------------
        // setup your test

        $expectedResult = [
            'php',
            '-i'
        ];
        $obj = new ProcessResult($expectedResult, 100, '');

        // ----------------------------------------------------------------
        // perform the change

        $actualResult = $obj->getCommand();

        // ----------------------------------------------------------------
        // test the results

        $this->assertEquals($expectedResult, $actualResult);
    }

    /**
     * @covers ::getCommandAsString
     */
    public function testCanGetCommandDetailsAsString()
    {
        // ----------------------------------------------------------------
        // setup your test

        $command = [
            'php',
            '-i'
        ];
        $expectedResult = "'php' '-i'";
        $obj = new ProcessResult($command, 100, '');

        // ----------------------------------------------------------------
        // perform the change

        $actualResult = $obj->getCommandAsString();

        // ----------------------------------------------------------------
        // test the results

        $this->assertEquals($expectedResult, $actualResult);
    }

    /**
     * @covers ::__construct
     * @expectedException GanbaroDigital\DataContainers\Exceptions\E4xx_NoSuchMethod
     */
    public function testIsReadOnly()
    {
        // ----------------------------------------------------------------
        // setup your test

        $expectedResult = 100;
        $obj = new ProcessResult([], $expectedResult, '');

        // ----------------------------------------------------------------
        // perform the change

        $actualResult = $obj->setReturnCode(101);
    }

    /**
     * @covers ::__construct
     */
    public function testCanGetResultCode()
    {
        // ----------------------------------------------------------------
        // setup your test

        $expectedResult = 100;
        $obj = new ProcessResult([], $expectedResult, '');

        // ----------------------------------------------------------------
        // perform the change

        $actualResult = $obj->getReturnCode();

        // ----------------------------------------------------------------
        // test the results

        $this->assertEquals($expectedResult, $actualResult);
    }

    /**
     * @covers ::__construct
     */
    public function testCanGetCommandOutput()
    {
        // ----------------------------------------------------------------
        // setup your test

        $expectedResult = <<<EOF
This is the fake output of running a command
which includes new lines

and blank lines

and anything, really
EOF;
        $obj = new ProcessResult([], 0, $expectedResult);

        // ----------------------------------------------------------------
        // perform the change

        $actualResult = $obj->getOutput();

        // ----------------------------------------------------------------
        // test the results

        $this->assertEquals($expectedResult, $actualResult);
    }

}