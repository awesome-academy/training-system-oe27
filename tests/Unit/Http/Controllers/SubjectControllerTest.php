<?php

namespace Tests\Unit\Http\Controllers;

use App\Http\Controllers\SubjectController;
use App\Http\Requests\SubjectRequest;
use App\Models\Course;
use App\Models\CourseUser;
use App\Models\Subject;
use App\Models\SubjectUser;
use App\Models\User;
use App\Repositories\Course\CourseRepositoryInterface;
use App\Repositories\CourseUser\CourseUserRepositoryInterface;
use App\Repositories\Subject\SubjectRepositoryInterface;
use App\Repositories\SubjectUser\SubjectUserRepositoryInterface;
use App\Repositories\Task\TaskRepositoryInterface;
use App\Repositories\User\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;
use Faker\Factory as Faker;
use Mockery;

class SubjectControllerTest extends TestCase
{
    protected $subjectMock;
    protected $courseMock;
    protected $subjectUserMock;
    protected $courseUserMock;
    protected $taskMock;
    protected $userMock;
    protected $faker;
    protected $subjectController;

    public function setUp(): void
    {
        $this->subjectMock = Mockery::mock(SubjectRepositoryInterface::class);
        $this->courseMock = Mockery::mock(CourseRepositoryInterface::class);
        $this->subjectUserMock = Mockery::mock(SubjectUserRepositoryInterface::class);
        $this->courseUserMock = Mockery::mock(CourseUserRepositoryInterface::class);
        $this->taskMock = Mockery::mock(TaskRepositoryInterface::class);
        $this->userMock = Mockery::mock(UserRepositoryInterface::class);
        $this->subjectController = new SubjectController(
            $this->subjectMock,
            $this->courseMock,
            $this->subjectUserMock,
            $this->courseUserMock,
            $this->taskMock,
            $this->userMock
        );
        parent::setUp();
    }

    public function tearDown(): void
    {
        unset($this->subjectController);
        Mockery::close();
        parent::tearDown();
    }

    public function test_index_function()
    {
        $this->courseMock
            ->shouldReceive('getAll')
            ->with([
                'subjects',
                'subjects.subjectUsers',
            ])
            ->once()
            ->andReturn(new Collection);
        $this->subjectMock
            ->shouldReceive('getAll')
            ->once()
            ->andReturn(new Collection);
        $result = $this->subjectController->index();
        $data = $result->getData();
        $this->assertIsArray($data);
        $this->assertEquals('supervisor.manage-subject.list-subjects', $result->getName());
        $this->assertArrayHasKey('courses', $data);
        $this->assertArrayHasKey('subjects', $data);
    }

    public function test_show_trainee_function()
    {
        $userActive = new User();
        $userActive->pivot = new Pivot();
        $userActive->pivot->start_time = config('number.date_test');
        $subject = new Subject();
        $subject->usersActive = [
            $userActive,
        ];
        $this->subjectMock
            ->shouldReceive('getById')
            ->with(config('number.init'), [
                'usersActive',
                'course',
            ])
            ->once()
            ->andReturn($subject);

        $subjectUser = new SubjectUser();
        $subjectUser->user_id = config('number.role.trainee');
        $collection = new Collection($subjectUser);
        $this->subjectMock
            ->shouldReceive('getSubjectUsersBySubject')
            ->with($subject)
            ->once()
            ->andReturn($collection);
        $user = new User([
            'id' => config('number.init'),
            'role_id' => config('number.role.trainee'),
        ]);
        $this->be($user);
        $this->subjectMock
            ->shouldReceive('getTasksBySubject')
            ->with($subject)
            ->once()
            ->andReturn($collection);
        $result = $this->subjectController->show(config('number.init'));
        $data = $result->getData();
        $this->assertIsArray($data);
        $this->assertEquals('trainee.detail-subject', $result->getName());
        $this->assertArrayHasKey('subject', $data);
        $this->assertArrayHasKey('tasks', $data);
        $this->assertArrayHasKey('subjectUser', $data);
    }

    public function test_show_trainee_function_fail()
    {
        $userActive = new User();
        $userActive->pivot = new Pivot();
        $userActive->pivot->start_time = config('number.date_test');
        $subject = new Subject();
        $subject->usersActive = [
            $userActive,
        ];
        $this->subjectMock
            ->shouldReceive('getById')
            ->with(config('number.init'), [
                'usersActive',
                'course',
            ])
            ->once()
            ->andReturn($subject);

        $subjectUser = new SubjectUser();
        $subjectUser->user_id = config('number.empty');
        $collection = new Collection($subjectUser);
        $this->subjectMock
            ->shouldReceive('getSubjectUsersBySubject')
            ->with($subject)
            ->once()
            ->andReturn($collection);
        $user = new User([
            'id' => config('number.init'),
            'role_id' => config('number.role.trainee'),
        ]);
        $this->be($user);
        $result = $this->subjectController->show(config('number.init'));
        $this->assertInstanceOf(RedirectResponse::class, $result);
    }

    public function test_show_supervisor_function()
    {
        $userActive = new User();
        $userActive->pivot = new Pivot();
        $userActive->pivot->start_time = config('number.date_test');
        $subject = new Subject();
        $subject->usersActive = [
            $userActive,
        ];
        $this->subjectMock
            ->shouldReceive('getById')
            ->with(config('number.init'), [
                'usersActive',
                'course',
            ])
            ->once()
            ->andReturn($subject);

        $subjectUser = new SubjectUser();
        $subjectUser->user_id = config('number.role.trainee');
        $collection = new Collection($subjectUser);
        $this->subjectMock
            ->shouldReceive('getTasksBySubject')
            ->with($subject)
            ->once()
            ->andReturn($collection);
        $user = new User([
            'id' => config('number.init'),
            'role_id' => config('number.role.supervisor'),
        ]);
        $this->be($user);
        $result = $this->subjectController->show(config('number.init'));
        $data = $result->getData();
        $this->assertIsArray($data);
        $this->assertEquals('supervisor.manage-subject.detail-subject', $result->getName());
        $this->assertArrayHasKey('subject', $data);
        $this->assertArrayHasKey('tasks', $data);
    }

    public function test_create_function()
    {
        $this->courseMock
            ->shouldReceive('getWhereEqual')
            ->with([
                'status' => config('number.course.active'),
            ])
            ->once()
            ->andReturn(true);
        $result = $this->subjectController->create();
        $this->assertEquals('supervisor.manage-subject.create-subject', $result->getName());
        $data = $result->getData();
        $this->assertArrayHasKey('courses', $data);
    }

    public function test_store_function()
    {
        $path = config('image.path_test');
        $mimeType = config('image.mime_type_test');
        $originalName = config('image.original_name_test');
        $error = config('number.empty');
        $this->faker = Faker::create();
        $data = [
            'title' => 'test',
            'image' => new UploadedFile($path, $originalName, $mimeType, $error),
            'content_description' => $this->faker->text,
            'time' => config('number.time_default'),
            'course_id' => config('number.init'),
        ];
        $subject = new Subject();
        $subject->id = config('number.init');
        $this->subjectMock
            ->shouldReceive('create')
            ->once()
            ->andReturn($subject);
        $request = new SubjectRequest($data);
        $result = $this->subjectController->store($request);
        $this->assertInstanceOf(RedirectResponse::class, $result);
        $this->assertEquals(
            route('subject.show', ['subject' => $subject->id]),
            $result->headers->get('Location')
        );
    }

    public function test_edit_function()
    {
        $this->subjectMock
            ->shouldReceive('getById')
            ->with(config('number.init'))
            ->once()
            ->andReturn(true);
        $this->courseMock
            ->shouldReceive('getWhereEqual')
            ->with([
                'status' => config('number.course.active'),
            ])
            ->once()
            ->andReturn(true);
        $result = $this->subjectController->edit(config('number.init'));
        $data = $result->getData();
        $this->assertEquals('supervisor.manage-subject.edit-subject', $result->getName());
        $this->assertArrayHasKey('subject', $data);
        $this->assertArrayHasKey('courses', $data);
    }

    public function test_update_function()
    {
        $path = 'public/images/download.png';
        $mimeType = 'image/png';
        $originalName = 'download.png';
        $error = config('number.empty');
        $this->faker = Faker::create();
        $id = config('number.init');
        $data = [
            'id' => config('number.init'),
            'title' => 'test',
            'image' => new UploadedFile($path, $originalName, $mimeType, $error),
            'content_description' => $this->faker->text,
            'time' => config('number.time_default'),
            'course_id' => config('number.init'),
        ];
        $subject = new Subject();
        $subject->id = config('number.init');
        $this->subjectMock
            ->shouldReceive('update')
            ->once()
            ->andReturn(true);
        $request = new SubjectRequest($data);
        $result = $this->subjectController->update($request, $id);
        $this->assertInstanceOf(RedirectResponse::class, $result);
        $this->assertEquals(
            route('subject.show', ['subject' => $subject->id]),
            $result->headers->get('Location')
        );
    }

    public function test_update_function_fail()
    {
        $path = 'public/images/download.png';
        $mimeType = 'image/png';
        $originalName = 'download.png';
        $error = config('number.empty');
        $this->faker = Faker::create();
        $id = config('number.init');
        $data = [
            'id' => config('number.init'),
            'title' => 'test',
            'image' => new UploadedFile($path, $originalName, $mimeType, $error),
            'content_description' => $this->faker->text,
            'time' => config('number.time_default'),
            'course_id' => config('number.init'),
        ];
        $subject = new Subject();
        $subject->id = config('number.init');
        $this->subjectMock
            ->shouldReceive('update')
            ->once()
            ->andThrow(new ModelNotFoundException);
        $request = new SubjectRequest($data);
        $result = $this->subjectController->update($request, $id);
        $this->assertInstanceOf(RedirectResponse::class, $result);
        $this->assertEquals(
            route('subject.show', ['subject' => $subject->id]),
            $result->headers->get('Location')
        );
        $this->assertArrayHasKey('error', $result->getSession()->all());
    }

    public function test_destroy_function()
    {
        $subject = new Subject();
        $course = new Course();
        $course->id = config('number.test.id');
        $this->subjectMock
            ->shouldReceive('getById')
            ->with(config('number.test.id'))
            ->once()
            ->andReturn($subject);
        $this->subjectMock
            ->shouldReceive('getCourseBySubject')
            ->with($subject)
            ->once()
            ->andReturn($course);

        $this->subjectUserMock
            ->shouldReceive('deleteWhereEqual')
            ->with(['subject_id' => config('number.test.id')])
            ->once()
            ->andReturn(true);

        $this->taskMock
            ->shouldReceive('deleteWhereEqual')
            ->with(['subject_id' => config('number.test.id')])
            ->once()
            ->andReturn(true);

        $this->subjectMock
            ->shouldReceive('delete')
            ->with(config('number.test.id'))
            ->once()
            ->andReturn(true);

        $courseUser = new CourseUser();
        $courseUser->id  = config('number.init');
        $courseUser->status = config('number.active');
        $courseUsers = new Collection([
            $courseUser,
            $courseUser,
        ]);
        $this->courseUserMock
            ->shouldReceive('getWhereEqual')
            ->with([
                'course_id' => config('number.test.id'),
            ], 'user')
            ->once()
            ->andReturn($courseUsers);
        $subject = new Subject();
        $subject->id = config('number.init');
        $subjects = new Collection([
            $subject,
        ]);
        $this->courseMock
            ->shouldReceive('getSubjectsByCourse')
            ->with($course)
            ->once()
            ->andReturn($subjects);

        $user = new User();
        $user->id = config('number.init');
        $this->courseUserMock
            ->shouldReceive('getUserByCourseUser')
            ->with($courseUser)
            ->twice()
            ->andReturn($user);
        $subjectUser = new SubjectUser([
            'status' => config('number.inactive'),
            'subject_id' => config('number.init'),
        ]);
        $subjectUser->subject = new Subject([
            'time' => config('number.time_test'),
        ]);

        $this->userMock
            ->shouldReceive('getSubjectUsersByUser')
            ->twice()
            ->andReturn(
                new Collection([
                    $subjectUser,
                    new SubjectUser()
                ]),
                new Collection([
                    new SubjectUser()
                ])
            );

        $this->subjectUserMock
            ->shouldReceive('update')
            ->once()
            ->andReturn(true);
        $this->courseUserMock
            ->shouldReceive('getWhereEqual')
            ->once()
            ->andReturn(new Collection([
                $courseUser,
                $courseUser,
            ]));
        $this->courseUserMock
            ->shouldReceive('update')
            ->once()
            ->andReturn(true);
        $result = $this->subjectController->destroy(config('number.test.id'));
        $this->assertInstanceOf(RedirectResponse::class, $result);
        $this->assertEquals(
            route('subject.index'),
            $result->headers->get('Location')
        );
        $this->assertArrayHasKey('messenger', $result->getSession()->all());
    }

    public function test_destroy_function_fail()
    {
        $this->subjectMock
            ->shouldReceive('getById')
            ->with(config('number.test.id'))
            ->once()
            ->andThrow(new ModelNotFoundException);
        $result = $this->subjectController->destroy(config('number.test.id'));
        $this->assertInstanceOf(RedirectResponse::class, $result);
        $this->assertEquals(
            route('subject.index'),
            $result->headers->get('Location')
        );
        $this->assertArrayHasKey('error', $result->getSession()->all());
    }
}
