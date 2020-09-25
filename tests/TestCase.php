<?php

namespace Tests;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function hasMany_relation_test($related, $foreignKey, $relation)
    {
        $this->assertInstanceOf(HasMany::class, $relation);
        $this->assertInstanceOf($related, $relation->getRelated());
        $this->assertEquals($foreignKey, $relation->getForeignKeyName());
    }

    protected function belongsTo_relation_test($related, $foreignKey, $ownerKey, $relation)
    {
        $this->assertInstanceOf(BelongsTo::class, $relation);
        $this->assertInstanceOf($related, $relation->getRelated());
        $this->assertEquals($ownerKey, $relation->getOwnerKeyName());
        $this->assertEquals($foreignKey, $relation->getForeignKeyName());
    }

    protected function belongsToMany_relation_test($related, $foreignKey, $relatedKey, $relation)
    {
        $this->assertInstanceOf(BelongsToMany::class, $relation);
        $this->assertInstanceOf($related, $relation->getRelated());
        $this->assertEquals($relatedKey, $relation->getRelatedPivotKeyName());
        $this->assertEquals($foreignKey, $relation->getForeignPivotKeyName());
    }
}
