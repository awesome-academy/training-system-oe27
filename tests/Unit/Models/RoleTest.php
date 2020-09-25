<?php

namespace Tests\Unit\Models;

use App\Models\Role;
use App\Models\User;
use Tests\TestCase;

class RoleTest extends TestCase
{
    protected $role;

    protected function setUp(): void
    {
        parent::setUp();
        $this->role = new Role();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->role);
    }

    public function test_table_name()
    {
        $this->assertEquals('roles', $this->role->getTable());
    }

    public function test_fillable()
    {
        $this->assertEquals([
            'name',
        ], $this->role->getFillable());
    }

    public function test_key_name()
    {
        $this->assertEquals('id', $this->role->getKeyName());
    }

    public function test_users_relation()
    {
        $this->hasMany_relation_test(
            User::class,
            'role_id',
            $this->role->users()
        );
    }
}
