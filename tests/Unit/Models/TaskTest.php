<?php

namespace Tests\Unit\Models;

use App\Models\Subject;
use App\Models\Task;
use App\Models\User;
use Tests\TestCase;

class TaskTest extends TestCase
{
    protected $task;

    protected function setUp(): void
    {
        parent::setUp();
        $this->task = new Task();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->task);
    }

    public function test_table_name()
    {
        $this->assertEquals('tasks', $this->task->getTable());
    }

    public function test_fillable()
    {
        $this->assertEquals([
            'subject_id',
            'user_id',
            'plan',
            'actual',
            'next_plan',
            'comment',
            'review',
            'status',
        ], $this->task->getFillable());
    }

    public function test_key_name()
    {
        $this->assertEquals('id', $this->task->getKeyName());
    }

    public function test_user_relation()
    {
        $this->belongsTo_relation_test(
            User::class,
            'user_id',
            'id',
            $this->task->user()
        );
    }

    public function test_subject_relation()
    {
        $this->belongsTo_relation_test(
            Subject::class,
            'subject_id',
            'id',
            $this->task->subject()
        );
    }
}
