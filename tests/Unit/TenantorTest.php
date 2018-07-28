<?php

namespace Orchestra\Tenanti\Tests\Unit;

use Orchestra\Tenanti\Tenantor;
use PHPUnit\Framework\TestCase;
use Illuminate\Database\Eloquent\Model;

class TenantorTest extends TestCase
{
    /** @test */
    public function it_can_be_initiated()
    {
        $stub = Tenantor::make('company', 5, 'primary');

        $this->assertSame('company', $stub->getTenantName());
        $this->assertSame(5, $stub->getTenantKey());
        $this->assertSame('primary', $stub->getTenantConnectionName());
        $this->assertNull($stub->getTenantModel());
    }

    /** @test */
    public function it_can_be_initiated_from_eloquent()
    {
        $model = new class() extends Model {
            protected $connection = 'primary';
        };

        $model->id = 5;

        $stub = Tenantor::fromEloquent('company', $model);

        $this->assertSame('company', $stub->getTenantName());
        $this->assertSame(5, $stub->getTenantKey());
        $this->assertSame('primary', $stub->getTenantConnectionName());
        $this->assertSame($model, $stub->getTenantModel());
    }
}
