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
 * @package   CommandRunner/Requirements
 * @author    Stuart Herbert <stuherbert@ganbarodigital.com>
 * @copyright 2011-present MediaSift Ltd www.datasift.com
 * @copyright 2015-present Ganbaro Digital Ltd www.ganbarodigital.com
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link      http://code.ganbarodigital.com/php-command-runner
 */

namespace GanbaroDigital\CommandRunner\Requirements;

use GanbaroDigital\CommandRunner\Values\CommandResult;
use PHPUnit_Framework_TestCase;

/**
 * @coversDefaultClass GanbaroDigital\CommandRunner\Requirements\RequireCommandFailed
 */
class RequireCommandFailedTest extends PHPUnit_Framework_TestCase
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

        $obj = new RequireCommandFailed;

        // ----------------------------------------------------------------
        // test the results

        $this->assertTrue($obj instanceof RequireCommandFailed);
    }

    /**
     * @covers ::__invoke
     * @dataProvider provideResultThatFailed
     */
    public function testCanUseAsObject($data)
    {
        // ----------------------------------------------------------------
        // setup your test

        $obj = new RequireCommandFailed;

        // ----------------------------------------------------------------
        // perform the change

        $obj($data);

        // ----------------------------------------------------------------
        // test the results
        //
        // if we get here, then all is well
    }

    /**
     * @covers ::check
     * @dataProvider provideResultThatFailed
     */
    public function testCanCallStatically($data)
    {
        // ----------------------------------------------------------------
        // setup your test

        // ----------------------------------------------------------------
        // perform the change

        RequireCommandFailed::check($data);

        // ----------------------------------------------------------------
        // test the results
        //
        // if we get here, then all is well
    }

    /**
     * @covers ::check
     * @covers ::checkCommandResult
     * @dataProvider provideResultsThatSucceed
     * @expectedException GanbaroDigital\CommandRunner\Exceptions\E4xx_CommandSucceeded
     */
    public function testThrowsExceptionIfACommandSucceeded($data)
    {
        // ----------------------------------------------------------------
        // setup your test

        // ----------------------------------------------------------------
        // perform the change

        RequireCommandFailed::check($data);
    }

    /**
     * @covers ::check
     * @covers ::checkCommandResult
     * @dataProvider provideResultsThatFailed
     */
    public function testDoesNotThrowExceptionIfCommandFailed($data)
    {
        // ----------------------------------------------------------------
        // setup your test

        // ----------------------------------------------------------------
        // perform the change

        RequireCommandFailed::check($data);

        // ----------------------------------------------------------------
        // test the results
        //
        // if we get here, then all is well
    }

    public function provideResultsThatSucceed()
    {
        return [ [ new CommandResult([], 0, '') ] ];
    }

    public function provideResultThatFailed()
    {
        return [ [new CommandResult([], 1, '') ] ];
    }

    public function provideResultsThatFailed()
    {
        $retval = [];
        for ($i = -255; $i <0; $i++) {
            $retval[] = [ new CommandResult([], $i, '') ];
        }
        for ($i = 1; $i <256; $i++) {
            $retval[] = [ new CommandResult([], $i, '') ];
        }

        return $retval;
    }
}