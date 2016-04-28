<?php
namespace PhpBrew\Testing;
use CLIFramework\Testing\CommandTestCase as BaseCommandTestCase;
use PhpBrew\Console;
use GetOptionKit\Option;
use Exception;

abstract class CommandTestCase extends BaseCommandTestCase
{

    private $previousPhpBrewRoot;

    private $previousPhpBrewHome;

    public $primaryVersion = '5.5.22';

    public function getPrimaryVersion()
    {
        return $this->primaryVersion;
    }



    public function setupApplication()
    {
        $console = Console::getInstance();
        $console->getLogger()->setQuiet();
        $console->getFormatter()->preferRawOutput();
        return $console;
    }

    public function setUp()
    {
        parent::setUp();
        $this->previousPhpBrewRoot = getenv('PHPBREW_ROOT');
        $this->previousPhpBrewHome = getenv('PHPBREW_HOME');

        // <env name="PHPBREW_ROOT" value=".phpbrew"/>
        // <env name="PHPBREW_HOME" value=".phpbrew"/>

        // already setup in phpunit.xml, but it seems don't work.
        putenv('PHPBREW_ROOT=' . getcwd() . '/.phpbrew');
        putenv('PHPBREW_HOME=' . getcwd() . '/.phpbrew');

        if($options = \PhpBrew\Console::getInstance()->options) {
            $option = new Option('no-progress');
            $option->setValue(true);
            $options->set('no-progress', $option);
        }
    }

    /*
     * we don't have to restore it back. the parent environment variables
     * won't change if the they are changed inside a process.
     * but we might want to change it back if there is a test changed the environment variable.
     */
    public function tearDown()
    {
        if ($this->previousPhpBrewRoot !== null) {
            putenv('PHPBREW_ROOT=' . $this->previousPhpBrewRoot);
        }
        if ($this->previousPhpBrewHome !== null) {
            putenv('PHPBREW_HOME=' . $this->previousPhpBrewHome);
        }
    }

    public function assertCommandSuccess($args)
    {
        try {
            $this->assertTrue($this->runCommand($args));
        } catch (\CurlKit\CurlException $e) {
            $this->markTestIncomplete($e->getMessage());
        }
    }

    public function runCommand($args)
    {
        ob_start();
            $status = parent::runCommand($args);
        ob_end_clean();
        return $status;
    }

    public function runCommandWithStdout($args)
    {
        return parent::runCommand($args);
    }

}
