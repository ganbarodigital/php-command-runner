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
 * @package   ProcessRunner/Checks
 * @author    Stuart Herbert <stuherbert@ganbarodigital.com>
 * @copyright 2011-present MediaSift Ltd www.datasift.com
 * @copyright 2015-present Ganbaro Digital Ltd www.ganbarodigital.com
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link      http://code.ganbarodigital.com/php-process-runner
 */

namespace GanbaroDigital\ProcessRunner\Checks;

use GanbaroDigital\ProcessRunner\Values\ProcessResult;
use PHPUnit_Framework_TestCase;

/**
 * @coversDefaultClass GanbaroDigital\ProcessRunner\Checks\DidProcessFail
 */
class DidProcessFailTest extends PHPUnit_Framework_TestCase
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

        $obj = new DidProcessFail;

        // ----------------------------------------------------------------
        // test the results

        $this->assertTrue($obj instanceof DidProcessFail);
    }

    /**
     * @covers ::__invoke
     * @dataProvider provideResultsToTest
     */
    public function testCanUseAsObject($resultObj, $expectedResult)
    {
        // ----------------------------------------------------------------
        // setup your test

        $obj = new DidProcessFail;

        // ----------------------------------------------------------------
        // perform the change

        $actualResult = $obj($resultObj);

        // ----------------------------------------------------------------
        // test the results

        $this->assertEquals($expectedResult, $actualResult);
    }

    /**
     * @covers ::check
     * @covers ::checkProcessResult
     * @dataProvider provideResultsToTest
     */
    public function testCanCallStatically($resultObj, $expectedResult)
    {
        // ----------------------------------------------------------------
        // setup your test

        // ----------------------------------------------------------------
        // perform the change

        $actualResult1 = DidProcessFail::check($resultObj);
        $actualResult2 = DidProcessFail::checkProcessResult($resultObj);

        // ----------------------------------------------------------------
        // test the results

        $this->assertEquals($expectedResult, $actualResult1);
        $this->assertEquals($expectedResult, $actualResult2);
    }

    /**
     * @covers ::checkProcessResult
     * @dataProvider provideResultsThatSucceed
     */
    public function testReturnsFalseIfResultCodeIsZero($resultObj)
    {
        // ----------------------------------------------------------------
        // setup your test



        // ----------------------------------------------------------------
        // perform the change

        $actualResult = DidProcessFail::check($resultObj);

        // ----------------------------------------------------------------
        // test the results

        $this->assertFalse($actualResult);
    }

    /**
     * @covers ::checkProcessResult
     * @dataProvider provideResultsThatFailed
     */
    public function testReturnsTrueIfResultCodeIsNotZero($resultObj)
    {
        // ----------------------------------------------------------------
        // setup your test



        // ----------------------------------------------------------------
        // perform the change

        $actualResult = DidProcessFail::check($resultObj);

        // ----------------------------------------------------------------
        // test the results

        $this->assertTrue($actualResult);
    }


    public function provideResultsToTest()
    {
        $retval=[];
        $retval[] = [ new ProcessResult([], 0, ''), false ];
        for ($i = 1; $i <256; $i++) {
            $retval[] = [ new ProcessResult([], $i, ''), true ];
        }

        return $retval;
    }

    public function provideResultsThatSucceed()
    {
        return [ [ new ProcessResult([], 0, '') ] ];
    }

    public function provideResultsThatFailed()
    {
        $retval = [];
        for ($i = -255; $i <0; $i++) {
            $retval[] = [ new ProcessResult([], $i, '') ];
        }
        for ($i = 1; $i <256; $i++) {
            $retval[] = [ new ProcessResult([], $i, '') ];
        }

        return $retval;
    }
}