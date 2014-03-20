<?php namespace Orchestra\Tenanti\TestCase;

use Mockery as m;
use Orchestra\Tenanti\TenantiServiceProvider;

class TenantiServiceProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testIsDeferred()
    {
        $stub = new TenantiServiceProvider(null);
        $this->assertTrue($stub->isDeferred());
    }
}
