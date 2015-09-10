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
 * @package   ProcessRunner/ProcessRunners
 * @author    Stuart Herbert <stuherbert@ganbarodigital.com>
 * @copyright 2011-present MediaSift Ltd www.datasift.com
 * @copyright 2015-present Ganbaro Digital Ltd www.ganbarodigital.com
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link      http://code.ganbarodigital.com/php-process-runner
 */

namespace GanbaroDigital\ProcessRunner\ProcessRunners;

use GanbaroDigital\Filesystem\Requirements\RequireAbsoluteFolderOrNull;
use GanbaroDigital\ProcessRunner\Events\ProcessEnded;
use GanbaroDigital\ProcessRunner\Events\ProcessPartialOutput;
use GanbaroDigital\ProcessRunner\Events\ProcessStarted;
use GanbaroDigital\ProcessRunner\Exceptions\E4xx_UnsupportedType;
use GanbaroDigital\ProcessRunner\Exceptions\E5xx_ProcessFailedToStart;
use GanbaroDigital\ProcessRunner\Values\ProcessResult;
use GanbaroDigital\ProcessRunner\ValueBuilders\BuildEscapedCommandLine;
use GanbaroDigital\DateTime\Requirements\RequireTimeoutOrNull;
use GanbaroDigital\DateTime\ValueBuilders\BuildTimeoutAsFloat;
use GanbaroDigital\Reflection\Requirements\RequireTraversable;
use GanbaroDigital\EventStream\Streams\DispatchEvent;
use GanbaroDigital\EventStream\Streams\EventStream;
use GanbaroDigital\EventStream\ValueBuilders\GuaranteeEventStream;
use Traversable;

/**
 * based on the ProcessRunner code from Storyplayer
 */
class PopenProcessRunner implements ProcessRunner
{
    /**
     * how long do we wait when trying to shutdown a non-responsive process?
     */
    const SHUTDOWN_TIMEOUT = 5.0;

    /**
     * run a CLI command using the popen() interface
     *
     * @param  array|Traversable $command
     *         the command to execute
     * @param  int|null $timeout
     *         how long before we force the command to close?
     * @param  string|null $cwd
     *         the folder to run the command inside
     * @param  EventStream $eventStream
     *         helper to send events to
     * @return ProcessResult
     *         the result of executing the command
     */
    public static function run($command, $timeout = null, $cwd = null, EventStream $eventStream = null)
    {
        // robustness
        RequireTraversable::checkMixed($command, E4xx_UnsupportedType::class);
        RequireTimeoutOrNull::check($timeout);
        RequireAbsoluteFolderOrNull::check($cwd);
        $eventStream = GuaranteeEventStream::from($eventStream);

        return self::runCommand($command, $timeout, $cwd, $eventStream);
    }

    /**
     * run a CLI command using the popen() interface
     *
     * @param  array|Traversable $command
     *         the command to execute
     * @param  int|null $timeout
     *         how long before we force the command to close?
     * @param  string|null $cwd
     *         the folder to run the command inside
     * @param  EventStream $eventStream
     *         helper to send events to
     * @return ProcessResult
     *         the result of executing the command
     */
    private static function runCommand($command, $timeout, $cwd, EventStream $eventStream)
    {
        // when the command needs to stop
        $timeoutToUse = self::getTimeoutToUse($timeout);

        // start the process
        list($process, $pipes) = self::startProcess($command, $cwd);
        DispatchEvent::to($eventStream, new ProcessStarted($command, $timeout, $cwd));

        // drain the pipes
        try {
            list($output, $timedOut) = self::drainPipes($pipes, $timeoutToUse, $eventStream);
        }
        finally {
            // at this point, our pipes have been closed
            // we can assume that the child process has finished
            $retval = self::stopProcess($process, $pipes, $timedOut);
        }

        // all done
        $retval = new ProcessResult($command, $retval, $output);
        DispatchEvent::to($eventStream, new ProcessEnded($retval));
        return $retval;
    }

    /**
     * convert the timeout into something we can use
     *
     * @param  mixed $timeout
     *         the timeout value to convert
     * @return array
     *         0 - the overall timeout
     *         1 - tv_sec param to select()
     *         2 - tv_usec param to select()
     */
    private static function getTimeoutToUse($timeout = null)
    {
        // special case
        if ($timeout === null) {
            return [ 0, 1, 0 ];
        }

        // general case
        $tAsF = BuildTimeoutAsFloat::from($timeout);
        if ($tAsF >= 1.0) {
            return [ $tAsF, 1, 0 ];
        }

        return [ max($tAsF, 0.2), 0, max(intval($tAsF * 1000000), 200000) ];
    }

    /**
     * start the command
     *
     * @param  array|Traversable $command
     *         the command to execute
     * @param  string|null $cwd
     *         the folder to run the command inside
     * @return array
     *         0 - the process handle
     *         1 - the array of pipes to interact with the process
     */
    private static function startProcess($command, $cwd = null)
    {
        // create the command to execute
        $cmdToExecute = BuildEscapedCommandLine::from($command);

        // how we will talk to the process
        $pipes = [];

        // start the process
        $process = proc_open($cmdToExecute, self::buildPipesSpec(), $pipes, $cwd);
        if (!$process) {
            // fork failed?
            throw new E5xx_ProcessFailedToStart($command);
        }

        // we do not want to block whilst reading from the child process
        stream_set_blocking($pipes[1], 0);
        stream_set_blocking($pipes[2], 0);

        // all done
        return [$process, $pipes];
    }

    /**
     * stop a previously started process
     *
     * @param  resource $process
     *         the process handle
     * @param  array &$pipes
     *         the pipes that are open to the process
     * @param  boolean $timedOut
     *         did the process fail to exit in the time allotted?
     * @return int
     *         the return code from the command
     */
    private static function stopProcess($process, &$pipes, $timedOut)
    {
        // pipes must be closed first to avoid
        // a deadlock, according to the PHP Manual
        fclose($pipes[1]);
        fclose($pipes[2]);

        // make sure the process has terminated
        if ($timedOut) {
            self::shutdownProcess($process);
            self::terminateProcess($process);
        }
        $exitCode = proc_close($process);

        // all done
        return $exitCode;
    }

    /**
     * gracefully stop the child process, in the same manner that /sbin/init
     * would be expected to
     *
     * @param  resource $process
     *         the child process to shutdown
     * @return void
     */
    private static function shutdownProcess($process)
    {
        // we do not want to wait forever
        $startTime = $endTime = microtime(true);

        // what state is the process in?
        $status = proc_get_status($process);
        while ($status['running'] && ($endTime - $startTime < self::SHUTDOWN_TIMEOUT)) {
            proc_terminate($process);
            usleep(1000);
            $status = proc_get_status($process);
            $endTime = microtime(true);
        }
    }

    /**
     * forceably stop the child process
     *
     * @param  resource $process
     *         the process to stop
     * @return void
     */
    private static function terminateProcess($process)
    {
        $status = proc_get_status($process);
        if (!$status['running']) {
            return;
        }

        // if we get here, it's time to be heavy-handed!
        proc_terminate($process, 9);
    }

    /**
     * create a description of the pipes that we want for talking to
     * the process
     *
     * @return array
     */
    private static function buildPipesSpec()
    {
        return [
            [ 'file', 'php://stdin', 'r' ],
            [ 'pipe', 'w' ],
            [ 'pipe', 'w' ]
        ];
    }

    /**
     * capture the output from the process
     *
     * @param  array &$pipes
     *         the pipes that are connected to the process
     * @param  array $timeout
     *         the timeout to use whilst draining the pipes
     * @param  EventStream $eventStream
     *         helper to send events to
     * @return array
     *         [0] - the combined output from stdout and stderr
     *         [1] - TRUE if the command timed out, false otherwise
     */
    private static function drainPipes(&$pipes, $timeout, EventStream $eventStream)
    {
        // the output from the command will be captured here
        $output = '';

        // keep track of how long we have been doing this
        $startTime = $endTime = microtime(true);

        // grab whatever output we can
        while (self::checkPipesAreOpen($pipes) && !self::hasTimedout($startTime, $endTime, $timeout[0])) {
            self::waitForTimeout($pipes, $timeout[1], $timeout[2]);
            $output .= self::getOutputFromPipe($pipes[1], $eventStream);
            $output .= self::getOutputFromPipe($pipes[2], $eventStream);
            $endTime = microtime(true);
        }

        // did we timeout?
        $timedOut = self::hasTimedout($startTime, $endTime, $timeout[0]);

        // all done
        return [ $output , $timedOut ];
    }

    /**
     * has a timeout occurred?
     *
     * @param  float  $startTime
     *         when did the process start?
     * @param  float  $endTime
     *         when did the process end?
     * @param  float  $timeout
     *         how long is the process allowed to run for?
     * @return boolean
     *         TRUE if a timeout occurred
     *         FALSE otherwise
     */
    private static function hasTimedOut($startTime, $endTime, $timeout)
    {
        // special case
        if ($timeout === 0) {
            return false;
        }

        // general case
        return ($endTime - $startTime > $timeout);
    }

    /**
     * are the pipes connected to our child process still open?
     *
     * @param  array $pipes
     *         the pipes connected to our child process
     * @return boolean
     *         TRUE if at least one pipe is still open
     *         FALSE otherwise
     */
    private static function checkPipesAreOpen($pipes)
    {
        if (!feof($pipes[1]) || !feof($pipes[2])) {
            return true;
        }
        return false;
    }

    /**
     * wait for the connected process to write something to our pipes
     * @param  array $pipes
     *         the connected pipes
     * @param  int $tsSec
     *         the number of seconds to wait in select()
     * @param  int $tsUsec
     *         the number of milliseconds to wait in select()
     * @return void
     */
    private static function waitForTimeout($pipes, $tsSec, $tsUsec)
    {
        // block until there is something to read, or until the
        // timeout has happened
        //
        // this makes sure that we do not burn CPU for the sake of it
        $readable = [ $pipes[1], $pipes[2] ];
        $writeable = $except = [];
        stream_select($readable, $writeable, $except, $tsSec, $tsUsec);
    }

    /**
     * get any output that's waiting on a pipe
     *
     * @param  resource $pipe
     *         the pipe to check and read from
     * @param  EventStream $eventStream
     *         helper to send events to
     * @return string
     *         the returned output, or an empty string otherwise
     */
    private static function getOutputFromPipe($pipe, EventStream $eventStream)
    {
        if ($line = fgets($pipe)) {
            DispatchEvent::to($eventStream, new ProcessPartialOutput($line));
            return $line;
        }

        return '';
    }

    /**
     * run a CLI command using the popen() interface
     *
     * @param  array|Traversable $command
     *         the command to execute
     * @param  int|null $timeout
     *         how long before we force the command to close?
     * @param  string|null $cwd
     *         the folder to run the command inside
     * @return ProcessResult
     *         the result of executing the command
     */
    public function __invoke($command, $timeout = null, $cwd = null, EventStream $eventStream = null)
    {
        return self::run($command, $timeout, $cwd, $eventStream);
    }
}