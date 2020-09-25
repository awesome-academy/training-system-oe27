<?php

namespace Tests\Unit\Models;

use App\Models\Course;
use App\Models\CourseUser;
use App\Models\User;
use Tests\TestCase;

class CourseUserTest extends TestCase
{
    protected $courseUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->courseUser = new CourseUser();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->courseUser);
    }

    public function test_table_name()
    {
        $this->assertEquals('course_user', $this->courseUser->getTable());
    }

    public function test_fillable()
    {
        $this->assertEquals([
            'course_id',
            'user_id',
            'status',
            'start_time',
            'end_time',
        ], $this->courseUser->getFillable());
    }

    public function test_key_name()
    {
        $this->assertEquals('id', $this->courseUser->getKeyName());
    }

    public function test_user_relation()
    {
        $this->belongsTo_relation_test(
            User::class,
            'user_id',
            'id',
            $this->courseUser->user()
        );
    }

    public function test_course_relation()
    {
        $this->belongsTo_relation_test(
            Course::class,
            'course_id',
            'id',
            $this->courseUser->course()
        );
    }
}
