<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubjectRequest;
use App\Repositories\Course\CourseRepositoryInterface;
use App\Repositories\CourseUser\CourseUserRepositoryInterface;
use App\Repositories\Subject\SubjectRepositoryInterface;
use App\Repositories\SubjectUser\SubjectUserRepositoryInterface;
use App\Repositories\Task\TaskRepositoryInterface;
use App\Repositories\User\UserRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class SubjectController extends Controller
{
    protected $subjectRepo;
    protected $courseRepo;
    protected $subjectUserRepo;
    protected $courseUserRepo;
    protected $taskRepo;
    protected $userRepo;

    public function __construct(
        SubjectRepositoryInterface $subjectRepo,
        CourseRepositoryInterface $courseRepo,
        SubjectUserRepositoryInterface $subjectUserRepo,
        CourseUserRepositoryInterface $courseUserRepository,
        TaskRepositoryInterface $taskRepo,
        UserRepositoryInterface $userRepo
    ) {
        $this->subjectRepo = $subjectRepo;
        $this->courseRepo = $courseRepo;
        $this->subjectUserRepo = $subjectUserRepo;
        $this->courseUserRepo = $courseUserRepository;
        $this->taskRepo = $taskRepo;
        $this->userRepo = $userRepo;
        $this->middleware('supervisor')
            ->except('show');
    }

    public function index()
    {
        $relation = [
            'subjects',
            'subjects.subjectUsers',
        ];
        $courses = $this->courseRepo->getAll($relation);
        $subjects = $this->subjectRepo->getAll();

        return view('supervisor.manage-subject.list-subjects',
            compact([
                'courses',
                'subjects',
            ]));
    }

    public function create()
    {
        $courses = $this->courseRepo
            ->getWhereEqual([
                'status' => config('number.course.active'),
            ]);

        return view('supervisor.manage-subject.create-subject',
            compact('courses'));
    }

    public function store(SubjectRequest $request)
    {
        $attribute = [
            'title' => $request->title,
            'image' => $request->image->getClientOriginalName(),
            'description' => $request->content_description,
            'course_id' => $request->course_id,
            'time' => $request->time,
            'created_at' => now()->format(config('view.format_date.datetime')),
            'status' => config('number.subject.active'),
        ];
        $subject = $this->subjectRepo->create($attribute);

        return redirect()->route('subject.show', ['subject' => $subject->id]);
    }

    public function show($id)
    {
        $user = auth()->user();
        $relation = [
            'usersActive',
            'course',
        ];
        $subject = $this->subjectRepo->getById($id, $relation);
        $today = now()->format(config('view.format_date.date'));

        foreach ($subject->usersActive as $userActive) {
            $todayParse = Carbon::parse($today);
            $startTimeParse = Carbon::parse($userActive->pivot->start_time);
            $userActive->time = $todayParse->diffInWeekdays($startTimeParse);
        }

        if ($user->role_id == config('number.role.supervisor')) {
            $tasks = $this->subjectRepo
                ->getTasksBySubject($subject)
                ->where('created_at', '>=', $today);

            return view('supervisor.manage-subject.detail-subject',
                compact('subject', 'tasks'));
        } else {
            $subjectUser = $this->subjectRepo->getSubjectUsersBySubject($subject)
                ->firstWhere('user_id', $user->id);
            if ($subjectUser) {
                $data['subject'] = $subject;
                $data['tasks'] = $this->subjectRepo
                    ->getTasksBySubject($subject)
                    ->where('user_id', auth()->user()->id);
                $data['subjectUser'] = $subjectUser;

                return view('trainee.detail-subject', $data);
            } else {
                return redirect()->back();
            }
        }
    }

    public function edit($id)
    {
        $subject = $this->subjectRepo->getById($id);
        $courses = $this->courseRepo->getWhereEqual([
            'status' => config('number.course.active'),
        ]);

        return view('supervisor.manage-subject.edit-subject',
            compact('subject', 'courses'));
    }

    public function update(SubjectRequest $request, $id)
    {
        $data = [
            'title' => $request->title,
            'image' => $request->image->getClientOriginalName(),
            'description' => $request->content_description,
            'course_id' => $request->course_id,
            'time' => $request->time,
        ];

        try {
            $this->subjectRepo->update($id, $data);

            return redirect()->route('subject.show', ['subject' => $id]);
        } catch (ModelNotFoundException $e) {
            return redirect()->route('subject.show', ['subject' => $id])
                ->with('error', trans('both.message.update_not_success'));
        }
    }

    public function destroy($id)
    {
        try {
            $subject = $this->subjectRepo->getById($id);
            $course = $this->subjectRepo->getCourseBySubject($subject);
            $this->handelDeleteSubjectUsers($id);
            $this->handelDeleteTasks($id);
            $this->handelDeleteSubject($id);
            $this->handelStatus($course);

            return redirect()->route('subject.index')
                ->with('messenger', trans('both.message.delete_subject_success'));
        } catch (ModelNotFoundException $e) {
            return redirect()->route('subject.index')
                ->with('error', trans('both.message.update_not_success'));
        }
    }

    public function handelDeleteSubject($subject_id)
    {
        $this->subjectRepo->delete($subject_id);
    }

    public function handelDeleteSubjectUsers($subject_id)
    {
        $this->subjectUserRepo->deleteWhereEqual(['subject_id' => $subject_id]);
    }

    public function handelDeleteTasks($subject_id)
    {
        $this->taskRepo->deleteWhereEqual(['subject_id' => $subject_id]);
    }

    public function handelStatus($course)
    {
        $courseUsersActive = $this->courseUserRepo
            ->getWhereEqual([
                'course_id' => $course->id,
            ], 'user')
            ->where('status', config('number.active'));
        $subjects = $this->courseRepo
            ->getSubjectsByCourse($course);
        $subjectsId = $subjects->modelKeys();

        foreach ($courseUsersActive as $courseUserActive) {
            $user = $this->courseUserRepo->getUserByCourseUser($courseUserActive);
            $subjectUsers = $this->userRepo->getSubjectUsersByUser($user);
            $subjectUserActive = $subjectUsers
                ->where('status', config('number.active'))
                ->whereIn('subject_id', $subjectsId)
                ->first();
            $today = now()->format(config('view.format_date.date'));

            if (!$subjectUserActive) {
                $subjectUserInactive = $subjectUsers
                    ->where('status', config('number.inactive'))
                    ->whereIn('subject_id', $subjectsId)
                    ->first();

                if ($subjectUserInactive) {
                    $attributes = [
                        'status' => config('number.active'),
                        'star_time' => $today,
                        'end_time' => $this->calculatorEndTime($subjectUserInactive->subject->time),
                    ];
                    $this->subjectUserRepo->update($subjectUserInactive->id, $attributes);
                } else {
                    $attributes = [
                        'status' => config('number.passed'),
                        'end_time' => $today,
                    ];
                    $courseUser = $this->courseUserRepo->getWhereEqual([
                        'course_id' => $course->id,
                        'user_id' => $user->id,
                    ]);
                    $this->courseUserRepo->update($courseUser->first()->id, $attributes);
                }
            }
        }
    }

    public function calculatorEndTime($time)
    {
        $startTime = Carbon::parse(now()->format(config('view.format_date.date')));
        $guessEndTime = Carbon::parse(now()->addDays($time)->format(config('view.format_date.date')));

        $rangeWithoutWeekend = $guessEndTime->diffInWeekdays($startTime);
        $rangeWeekend = $time - $rangeWithoutWeekend;
        $rangeActual = $time + $rangeWeekend;

        $endTime = now()->addDays($rangeActual)->format(config('view.format_date.date'));

        return $endTime;
    }
}
