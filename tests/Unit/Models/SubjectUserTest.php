<?php

namespace Tests\Unit\Models;

use App\Models\Subject;
use App\Models\SubjectUser;
use App\Models\User;
use Tests\TestCase;

class SubjectUserTest extends TestCase
{
    protected $subjectUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subjectUser = new SubjectUser();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->subjectUser);
    }

    public function test_table_name()
    {
        $this->assertEquals('subject_user', $this->subjectUser->getTable());
    }

    public function test_fillable()
    {
        $this->assertEquals([
            'subject_id',
            'user_id',
            'start_time',
            'end_time',
            'status',
        ], $this->subjectUser->getFillable());
    }

    public function test_key_name()
    {
        $this->assertEquals('id', $this->subjectUser->getKeyName());
    }

    public function test_user_relation()
    {
        $this->belongsTo_relation_test(
            User::class,
            'user_id',
            'id',
            $this->subjectUser->user()
        );
    }

    public function test_subject_relation()
    {
        $this->belongsTo_relation_test(
            Subject::class,
            'subject_id',
            'id',
            $this->subjectUser->subject()
        );
    }
}
