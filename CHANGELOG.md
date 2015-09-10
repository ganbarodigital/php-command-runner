# CHANGELOG

## develop branch

Nothing yet.

## 0.3.0 - Thu 10 Sep 2015

### New

* PopenProcessRunner - now emits events
  * ProcessStarted
  * ProcessPartialLine
  * ProcessEnded

## 0.2.2 - Fri 4 Sep 2015

### Fixes

* PopenProcessRunner - only force-terminate a process if it times out

## 0.2.1 - Wed 2 Sep 2015

### Fixes

* Depend on a tagged release of php-file-system

## 0.2.0 - Tue 1 Sep 2015

### New

* ProcessRunners\PopenProcessRunner - can now be told which folder to run the child process in

## 0.1.1 - Sun 25 Jul 2015

### Fixes

* ProcessRunners\PopenProcessRunner - more reliable detection of a process's return code

## 0.1.0 - Sun 25 July 2015

### New

* Checks\DidProcessFail added
* Checks\DidProcessSucceed added
* Exceptions\E4xx_ProcessFailed added
* Exceptions\E4xx_ProcessSucceeded added
* Exceptions\E5xx_ProcessRunnerException added
* Exceptions\E5xx_ProcessFailedToStart added
* ProcessRunners\ProcessRunner added
* ProcessRunners\PopenProcessRunner added
* Requirements\RequireProcessFailed added
* Requirements\RequireProcessSucceeded added
* Values\ProcessResult added
* ValueBuilders\BuildEscapedCommandLine added
