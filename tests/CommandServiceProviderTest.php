<?php namespace Orchestra\Tenanti\TestCase;


use Orchestra\Tenanti\CommandServiceProvider;

class CommandServiceProviderTest extends \PHPUnit_Framework_TestCase {


    public function testServiceProviderIsDeferred()
    {
        $stub = new CommandServiceProvider(null);

        $this->assertTrue($stub->isDeferred());
    }

    public function testProvidesMethod()
    {
        $stub = new CommandServiceProvider(null);

        $expected = [
            'orchestra.commands.tenanti.migrate',
            'orchestra.commands.tenanti.setup',
        ];

        $this->assertEquals($expected, $stub->provides());
    }
}
