<?php
/**
 * Created by PhpStorm.
 * User: Alexander Wegner
 * Date: 10.12.2018
 * Time: 15:32
 * forked from https://github.com/sempro/phpunit-pretty-print
 */

namespace Oforge\Engine\Tests;

use Oforge\Engine\Modules\Core\Helper\StringHelper;
use PHPUnit\Framework\Test;
use PHPUnit\Framework\TestFailure;
use PHPUnit\Framework\TestListener;
use PHPUnit\Framework\TestResult;
use PHPUnit\Framework\TestSuite;
use PHPUnit\TextUI\ResultPrinter;
use PHPUnit\Util\Filter;

class OforgePrinter extends ResultPrinter implements TestListener
{
    protected $className;
    protected $previousClassName;
    
    public function startTestSuite(TestSuite $suite): void
    {
        parent::startTestSuite($suite);
    }
    
    public function startTest(Test $test): void
    {
        $this->className = get_class($test);
    }
    
    public function endTest(Test $test, float $time): void
    {
        parent::endTest($test, $time);
        
        $testMethodName = \PHPUnit\Util\Test::describe($test);
        
        // convert snakeCase method name to camelCase
        $testMethodName[1] = str_replace('_', '', ucwords($testMethodName[1], '_'));
        
        preg_match_all('/((?:^|[A-Z])[a-z]+)/', $testMethodName[1], $matches);
        $testNameArray = array_map('strtolower', $matches[0]);
        
        // check if prefix is test remove it
        if ($testNameArray[0] === 'test') {
            array_shift($testNameArray);
        }
        
        $name = implode(' ', $testNameArray);
        
        // get the data set name
        $name = $this->handleDataSetName($name, $testMethodName[1]);
        
        $color = 'fg-green';
        
        switch ($test->getStatus()) {
            case 0:
                $color = 'fg-green';
                break;
            case 1:
                $color = 'fg-yellow';
                break;
            case 2:
                $color = 'fg-yellow';
                break;
            case 3:
                $color = 'fg-red';
                break;
            case 4:
                $color = 'fg-red';
                break;
            case 5:
                $color = 'fg-yellow';
                break;
            case 6:
                $color = 'fg-yellow';
                break;
            default:
                break;
        }
        $this->write(' ');
        $this->writeWithColor($color, $name, false);
        $this->write(' ');
        
        $timeColor = $time > 0.5 ? 'fg-yellow' : 'fg-white';
        $this->writeWithColor($timeColor, '[' . number_format($time, 3) . 's]', true);
    }
    
    protected function writeProgress($progress): void
    {
        $lines = implode("",array_fill(0, strlen($this->className), '-'));
    
        if ($this->previousClassName !== $this->className) {
            $this->writeNewLine();
            $this->writeWithColor('fg-cyan, bold', $this->className, false);
            $this->writeWithColor('fg-cyan, bold', "\n" . $lines, true);
            $this->writeNewLine();
        }
        
        $this->previousClassName = $this->className;
        
        if (StringHelper::contains($progress, '.')) {
            $this->writeWithColor('fg-green, bold', '  ✔ ', false);
        } else if (StringHelper::contains($progress, 'I') ||
                   StringHelper::contains($progress, 'S')) {
            $this->writeWithColor('fg-yellow, bold', '  ⏻ ', false);
        }
        else {
            $this->writeWithColor('fg-red, bold', '  ✖ ', false);
        }
    }
    
    protected function printDefectTrace(TestFailure $defect): void
    {
        $this->write($this->formatExceptionMsg($defect->getExceptionAsString()));
        $trace = Filter::getFilteredStacktrace(
            $defect->thrownException()
        );
        if (!empty($trace)) {
            $this->write("\n" . $trace);
        }
        $exception = $defect->thrownException()->getPrevious();
        while ($exception) {
            $this->write(
                "\nCaused by\n" .
                TestFailure::exceptionToString($exception) . "\n" .
                Filter::getFilteredStacktrace($exception)
            );
            $exception = $exception->getPrevious();
        }
    }
    
    protected function formatExceptionMsg($exceptionMessage): string
    {
        $exceptionMessage = str_replace("+++ Actual\n", '', $exceptionMessage);
        $exceptionMessage = str_replace("--- Expected\n", '', $exceptionMessage);
        $exceptionMessage = str_replace('@@ @@', '', $exceptionMessage);
        
        if ($this->colors) {
            $exceptionMessage = preg_replace('/^(Exception.*)$/m', "\033[01;31m$1\033[0m", $exceptionMessage);
            $exceptionMessage = preg_replace('/(Failed.*)$/m', "\033[01;31m$1\033[0m", $exceptionMessage);
            $exceptionMessage = preg_replace("/(\-+.*)$/m", "\033[01;32m$1\033[0m", $exceptionMessage);
            $exceptionMessage = preg_replace("/(\++.*)$/m", "\033[01;31m$1\033[0m", $exceptionMessage);
        }
        
        return $exceptionMessage;
    }
    
    private function handleDataSetName($name, $testMethodName): string
    {
        preg_match('/\bwith data set "([^"]+)"/', $testMethodName, $dataSetMatch);
        
        if (empty($dataSetMatch)) {
            return $name;
        }
        
        return $name . ' [' . $dataSetMatch[1] . ']';
    }
    
    protected function printHeader(): void
    {
        $this->writeNewLine();
        $this->writeWithColor('fg-cyan, bold', '----------------------------------------------------------', true);
        $this->writeWithColor('fg-cyan, bold', '----------------------------------------------------------', false);
        parent::printHeader();
    }
    
    protected function printFooter(TestResult $result): void
    {
        if (\count($result) === 0) {
            $this->writeWithColor(
                'fg-black, bg-yellow',
                'No tests executed!'
            );
            
            return;
        }
        
        if ($result->wasSuccessful() &&
            $result->allHarmless() &&
            $result->allCompletelyImplemented() &&
            $result->noneSkipped()) {
            $this->writeWithColor(
                'bold, fg-black, bg-green',
                \sprintf(
                    'OK (%d test%s, %d assertion%s)',
                    \count($result),
                    (\count($result) == 1) ? '' : 's',
                    $this->numAssertions,
                    ($this->numAssertions == 1) ? '' : 's'
                )
            );
        } else {
            if ($result->wasSuccessful()) {
                $color = 'bold, fg-black, bg-yellow';
                
                if ($this->verbose || !$result->allHarmless()) {
                    $this->write("\n");
                }
                
                $this->writeWithColor(
                    $color,
                    'OK, but incomplete, skipped, or risky tests!'
                );
            } else {
                $this->write("\n");
                
                if ($result->errorCount()) {
                    $color = 'bold, fg-black, bg-red';
                    
                    $this->writeWithColor(
                        $color,
                        'ERRORS!'
                    );
                } elseif ($result->failureCount()) {
                    $color = 'bold, fg-white, bg-red';
                    
                    $this->writeWithColor(
                        $color,
                        'FAILURES!'
                    );
                } elseif ($result->warningCount()) {
                    $color = 'bold, fg-black, bg-yellow';
                    
                    $this->writeWithColor(
                        $color,
                        'WARNINGS!'
                    );
                }
            }
            
            $this->writeCountString(\count($result), 'Tests', $color, true);
            $this->writeCountString($this->numAssertions, 'Assertions', $color, true);
            $this->writeCountString($result->errorCount(), 'Errors', $color);
            $this->writeCountString($result->failureCount(), 'Failures', $color);
            $this->writeCountString($result->warningCount(), 'Warnings', $color);
            $this->writeCountString($result->skippedCount(), 'Skipped', $color);
            $this->writeCountString($result->notImplementedCount(), 'Incomplete', $color);
            $this->writeCountString($result->riskyCount(), 'Risky', $color);
            $this->writeWithColor($color, '.');
        }
    }
    
    /**
     * @param int    $count
     * @param string $name
     * @param string $color
     * @param bool   $always
     */
    private function writeCountString($count, $name, $color, $always = false): void
    {
        static $first = true;
        
        if ($always || $count > 0) {
            $this->writeWithColor(
                $color,
                \sprintf(
                    '%s%s: %d',
                    !$first ? ', ' : '',
                    $name,
                    $count
                ),
                false
            );
            
            $first = false;
        }
    }
}