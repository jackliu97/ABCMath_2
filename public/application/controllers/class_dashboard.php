<?php if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}
use \ABCMath\Course\ABCClassManager;
use \ABCMath\Course\ABCClass;
use \ABCMath\Teacher\Teacher;
use \ABCMath\Student\Student;
use \ABCMath\Student\StudentManager;
use \ABCMath\Course\Lesson;
use \ABCMath\Course\LessonManager;
use \ABCMath\Db\Datatable;
use \ABCMath\Template\Template;
use \ABCMath\Course\Assignment;
use \ABCMath\Course\AssignmentManager;
use \ABCMath\Attachment\Attachment;

class Class_Dashboard extends CI_Controller
{
    protected $_template;

    public function __construct()
    {
        parent::__construct();

        if ($this->User_Model->check_login() == false) {
            $this->session->sess_destroy();
            header('Location: /login');
        }

        $this->_template = new Template(Template::FILESYSTEM);
        if ($this->User_Model->check_permission('scaffolding') == false) {
            $this->editable = false;
        } else {
            $this->editable = true;
        }

        $this->semester_id = $this->session->userdata('semester_id');
    }

    public function info($class_id)
    {
        $lesson_id = $this->input->post('lesson_id');
        $data = array();
        $class_manager = new ABCClassManager();
        $class_manager->semester_id = $this->semester_id;

        $class = new ABCCLass();
        $class->setId($class_id);
        $class->load();

        $teacher = new Teacher();
        $teacher->setId($class->teacher_id);
        $result = $teacher->load();

        $lessonManager = new LessonManager();
        $lessonManager->class_id = $class_id;
        $lessonInfo = $lessonManager->getLessonsByClass();

        $assignment = new AssignmentManager();
        $data['assignment_types'] = $assignment->getAssignmentTypes();

        $data['class'] = $class;
        $data['teacher'] = $teacher;
        $data['editable'] = $this->editable;
        $data['start_time'] = new DateTime($class->start_time);
        $data['end_time'] = new DateTime($class->end_time);
        $data['lessons'] = $lessonInfo['lessons'];
        $data['lesson_id'] = $lesson_id;
        $data['class_options'] = $class_manager->getAllClasses('options');

        $this->load->view('header');
        $this->load->view('navbar');
        $this->load->view('dashboard/class', $data);
        $this->load->view('footer', array(
                                        'private_js' => array('dashboard/class.js'),
                                        'datatable' => true,
                                        ));
    }

    public function unregister()
    {
        $class_id = $this->input->post('class_id');
        $student_id = $this->input->post('student_id');

        $class = new ABCCLass();
        $class->setId($class_id);

        return $class->removeStudent($student_id);
    }

    public function register()
    {
        $class_id = $this->input->post('class_id');
        $student_id = $this->input->post('student_id');

        $class = new ABCCLass();
        $class->setId($class_id);

        return $class->addStudent($student_id);
    }

    public function get_students_for_add($class_id)
    {
        $student_manager = new StudentManager();
        $dt = new Datatable();
        $dt->sql = $student_manager->getAllStudentsSQL();
        $dt->columns = array(    'student_id',
                                'external_id',
                                'name',
                                'email',
                                'telephone',
                                'cellphone',
                                'class_id', );

        $result = $dt->processQuery();

        if (count($result['aaData'])) {
            foreach ($result['aaData'] as $key => $row) {
                foreach ($row as $k => $col) {
                    if ($k == 0) {
                        $student_id = $col;

                        $class_ids = explode(',', $row[6]);
                        if (in_array($class_id, $class_ids)) {
                            $result['aaData'][$key][$k] =
                            "<button type='button' class='btn btn-primary btn-sm'>Registered</button>";
                        } else {
                            $result['aaData'][$key][$k] =
                            "<button type='button' student_id='{$col}' class_id='{$class_id}' class='btn btn-success btn-sm register'>Add</button>";
                        }
                    } else {
                        $result['aaData'][$key][$k] =
                            "<a href='/student_dashboard/info/{$student_id}'>".
                            "{$result['aaData'][$key][$k]}</a>";
                    }
                }
            }
        }

        $this->load->view('response/datatable', array('json' => $result));
    }

    public function get_students_for_remove($class_id)
    {
        $student_manager = new StudentManager();
        $dt = new Datatable();
        $dt->sql = $student_manager->getAllStudentsByClassSQL($class_id);
        $dt->columns = array(    'student_id',
                                'external_id',
                                'name',
                                'email',
                                'telephone',
                                'cellphone', );

        $result = $dt->processQuery();

        if (count($result['aaData'])) {
            foreach ($result['aaData'] as $key => $row) {
                foreach ($row as $k => $col) {
                    if ($k == 0) {
                        $student_id = $col;
                        $result['aaData'][$key][$k] =
                        "<button type='button' student_id='{$col}' class_id='{$class_id}' class='btn btn-danger btn-sm unregister'>Remove</button>";
                    } else {
                        $result['aaData'][$key][$k] =
                            "<a href='/student_dashboard/info/{$student_id}'>".
                            "{$result['aaData'][$key][$k]}</a>";
                    }
                }
            }
        }

        $this->load->view('response/datatable', array('json' => $result));
    }

    public function build_class_navigation()
    {
        $class_id = $this->input->post('class_id');
        $data = array('class_id' => $class_id);

        $class_manager = new ABCClassManager();
        $class_manager->semester_id = $this->semester_id;
        $data['classes'] = $class_manager->getAllClasses();

        $result = array(
            'success' => true,
            'html' => $this->_template->render('Class/class_navigation.twig', $data),
            );

        $this->load->view('response/datatable', array('json' => $result));
    }

    public function open_attachment($attachment_id)
    {
        $attachment = new Attachment();
        $attachment->setId($attachment_id);
        $attachment->load();
        $this->output
            ->set_header('Content-Disposition: inline; filename="'.$attachment->file_name.'"')
            ->set_content_type($attachment->file_type)
            ->set_output(file_get_contents($attachment->full_path));
    }

    public function show_attachments()
    {
        $class_id = $this->input->post('class_id');
        $lesson_id = $this->input->post('lesson_id');

        $data = array();
        $data['lesson_id'] = $lesson_id;

        if ($lesson_id) {
            $currentLesson = new Lesson();
            $currentLesson->id = $lesson_id;
            $attachments = $currentLesson->getAllAttachments();
            $data['attachments'] = $attachments['attachments'];
        }

        $result = array(
            'success' => true,
            'html' => $this->_template->render('Class/attachment.twig', $data),
            );

        $this->load->view('response/datatable', array('json' => $result));
    }

    public function upload_attachment()
    {
        $result = array('success' => true, 'message' => '');
        $lesson_id = $this->input->post('lesson_id');
        $description = $this->input->post('attachment_description');

        if (!count($_FILES)) {
            //no files selected.
            $result['success'] = false;
            $result['message'] = 'No files selected.';
            $this->load->view('response/json', array('json' => $result));

            return false;
        }

        if (!$lesson_id) {
            $result['success'] = false;
            $result['message'] = 'No lesson id given.';
            $this->load->view('response/json', array('json' => $result));

            return false;
        }

        $upload_path = './uploads/lesson_attachments/'.$lesson_id;
        if (!is_dir($upload_path)) {
            mkdir($upload_path, 0777, true);
        }

        $this->load->library('upload',
            array(
                'upload_path' => $upload_path,
                'allowed_types' => 'xlsx|pdf|xls|csv|png|jpg|jpeg|gif|doc|docx',
                )
            );

        if (!$this->upload->do_upload('0')) {
            $result['success'] = false;
            $result['message'] = $this->upload->display_errors();
            $this->load->view('response/json', array('json' => $result));

            return false;
        }

        $upload_data = $this->upload->data();
        $attachment = new Attachment();
        $attachment->load($upload_data);
        $attachment->description = $description;
        $attachment->save();

        $lesson = new Lesson();
        $lesson->id = $lesson_id;
        $attachment_result = $lesson->addAttachment($attachment);
        if ($attachment_result !== true) {
            $result['success'] = false;
            $result['message'] = $attachment_result;
        }

        $this->load->view('response/json', array('json' => $result));
    }

    public function show_attendance()
    {
        $class_id = $this->input->post('class_id');
        $lesson_id = $this->input->post('lesson_id');
        $data = array();
        $data['lesson_id'] = $lesson_id;
        $data['single_batch_hw'] = true;

        $studentManager = new StudentManager();
        $studentManager->class_id = $class_id;
        $studentManager->loadStudentsByClass();
        $data['students'] = $studentManager->students;

        if ($lesson_id) {
            $currentLesson = new Lesson();
            $currentLesson->id = $lesson_id;
            $currentLesson->load();

            $class = ABCClass::get($currentLesson->class_id);
            if(in_array($class->subject_id, array(10, 15))){
                $data['single_batch_hw'] = false;
            }

            $data['currentLesson'] = $currentLesson;
            $attendanceData = $currentLesson->getAttendance();
            foreach ($data['students'] as $k => $student) {
                $data['students'][$k]->present =
                    isset($attendanceData[$student->id]['present']) ?
                    $attendanceData[$student->id]['present'] : null;
                $data['students'][$k]->tardy =
                    isset($attendanceData[$student->id]['tardy']) ?
                    $attendanceData[$student->id]['tardy'] : null;
            }
        }

        $result = array(
            'success' => true,
            'html' => $this->_template->render('Class/attendance.twig', $data),
            );

        $this->load->view('response/datatable', array('json' => $result));
    }

    public function print_attendance_view($class_id)
    {
        $class = new ABCCLass();
        $class->setId($class_id);
        $class->load();

        $lessonManager = new LessonManager();
        $lessonManager->class_id = $class_id;
        $lessons = $lessonManager->getLessonsByClass();

        //get all students for this class order by last name.
        $student_manager = new StudentManager();
        $student_manager->class_id = $class_id;
        $student_manager->loadStudentsByClass();
        $students = $student_manager->students;

        $this->load->library('pdf/AttendancePDF');
        $this->attendancepdf->SetClassData($class);
        $this->attendancepdf->SetLessons($lessons['lessons']);
        $this->attendancepdf->AddPage();
        if (!count($students)) {
            $this->attendancepdf->RotatedText(100, 50, 'There are no students in this class.', 270);
            $this->attendancepdf->Output();
        }

        if (!count($lessons)) {
            $this->attendancepdf->RotatedText(100, 50, 'There are no lessons in this class.', 270);
            $this->attendancepdf->Output();
        }

        $this->attendancepdf->DrawHeaderRow();
        $this->attendancepdf->SetFont('Arial', 'B', 8);
        $this->attendancepdf->SetLineWidth(0.1);
        $this->attendancepdf->SetDrawColor(0, 0, 0);
        $y = 170;
        $count = 0;
        foreach ($students as $student) {
            $this->attendancepdf->DrawStudentRow($student, $y);
            $y -= 8;
            $count ++;
            if ($count % 20 == 0) {
                $this->attendancepdf->AddPage();
                $this->attendancepdf->DrawHeaderRow();
                $y = 170;
            }
        }
        $this->attendancepdf->Output();
    }

    public function take_attendance()
    {
        $student_id = $this->input->post('student_id');
        $lesson_id = $this->input->post('lesson_id');

        $lesson = new Lesson();
        $lesson->id = $lesson_id;

        $return = $lesson->takeAttendance($student_id);
        $this->load->view('response/json', array('json' => $return));
    }

    public function mark_tardy()
    {
        $student_id = $this->input->post('student_id');
        $lesson_id = $this->input->post('lesson_id');

        $lesson = new Lesson();
        $lesson->id = $lesson_id;
        $return = $lesson->markTardy($student_id);
        $this->load->view('response/json', array('json' => $return));
    }

    public function mark_absent()
    {
        $student_id = $this->input->post('student_id');
        $lesson_id = $this->input->post('lesson_id');

        $lesson = new Lesson();
        $lesson->id = $lesson_id;
        $return = $lesson->markAbsent($student_id);
        $this->load->view('response/json', array('json' => $return));
    }

    public function attendance_data()
    {
        $student_id = $this->input->post('student_id');
        $lesson_id = $this->input->post('lesson_id');
        $data_type = $this->input->post('data_type');

        $lesson = new Lesson();
        $lesson->id = $lesson_id;
        $attendance_id = $lesson->touchAttendance($student_id);
        $result['success'] = true;
        $result['data_type'] = $data_type;
        $result['data_value'] = $lesson->toggleAttendanceData($attendance_id, $data_type);

        $this->load->view('response/json', array('json' => $result));
    }

    public function show_grades()
    {
        $class_id = $this->input->post('class_id');
        $lesson_id = $this->input->post('lesson_id');
        $data = array();
        $data['lesson_id'] = $lesson_id;

        if ($lesson_id) {
            $currentLesson = new Lesson();
            $currentLesson->id = $lesson_id;
            $currentLesson->load();
            $data['currentLesson'] = $currentLesson;
        }

        $result = array(
            'success' => true,
            'html' => $this->_template->render('Class/grades.twig', $data),
            );
        $this->load->view('response/datatable', array('json' => $result));
    }

    public function show_assignments()
    {
        $class_id = $this->input->post('class_id');
        $lesson_id = $this->input->post('lesson_id');
        $data = array();
        $data['lesson_id'] = $lesson_id;

        if ($lesson_id) {
            $currentLesson = new Lesson();
            $currentLesson->id = $lesson_id;
            $currentLesson->load();
            $data['currentLesson'] = $currentLesson;

            $assignmentManager = new AssignmentManager();
            $data['assignments'] = $assignmentManager->getAssignmentByLesson($lesson_id);
        }

        $result = array(
            'success' => true,
            'html' => $this->_template->render('Class/assignments.twig', $data),
            );
        $this->load->view('response/datatable', array('json' => $result));
    }

    public function get_assignment_info()
    {
        $assignment_id = $this->input->post('assignment_id');

        $assignment = new Assignment();
        $assignment->id = $assignment_id;
        $assignment->load();

        $info = array(
            'id' => $assignment->id,
            'name' => $assignment->name,
            'assignment_type_id' => $assignment->assignment_type_id,
            'description' => $assignment->description,
            'weight' => $assignment->weight,
            'maximum_score' => $assignment->maximum_score
            );

        $result = array(
            'success' => true,
            'info' => $info,
            );
        $this->load->view('response/datatable', array('json' => $result));
    }

    public function save_assignment()
    {
        $lesson_id = $this->input->post('lesson_id');
        $id = $this->input->post('id');
        $name = $this->input->post('name');
        $assignment_type_id = $this->input->post('assignment_type_id');
        $description = $this->input->post('description');
        $weight = $this->input->post('weight');
        $maximum_score = $this->input->post('maximum_score');
        $apply_to_all = $this->input->post('apply_to_all');

        $lesson_ids = array();

        //we're applying this to all lessons.
        if($apply_to_all){
            $lesson = Lesson::get($lesson_id);
            $lessonManager = new LessonManager();
            $lessons = $lessonManager->getLessonsByClass($lesson->class_id);
            foreach($lessons['lessons'] as $lessonObj){
                $lesson_ids []= $lessonObj->id;
            }
        }else{
            $lesson_ids []= $lesson_id;
        }

        foreach($lesson_ids as $this_id){

            $assignment = new Assignment();
            $assignment->id = $id;
            $assignment->name = $name;
            $assignment->assignment_type_id = $assignment_type_id;
            $assignment->description = $description;
            $assignment->weight = $weight;
            $assignment->maximum_score = $maximum_score;
            $assignment->lesson_id = $this_id;

            try {
                $assignment->save();
                $result = array(
                    'success' => true,
                    );
            } catch (Exception $e) {
                $result = array(
                    'success' => false,
                    'message' => $e->getMessage(),
                    );
            }

        }

        $this->load->view('response/datatable', array('json' => $result));
    }

    public function delete_attachment()
    {
        $attachment_id = $this->input->post('attachment_id');

        $attachment = new Attachment();
        $attachment->id = $attachment_id;
        $attachment->load();
        try {
            $attachment->delete();
            $result = array(
                'success' => true,
                );
        } catch (Exception $e) {
            $result = array(
                'success' => false,
                'message' => $e->getMessage(),
                );
        }

        $this->load->view('response/datatable', array('json' => $result));
    }

    public function delete_assignment()
    {
        $assignment_id = $this->input->post('assignment_id');

        $assignment = new Assignment();
        $assignment->id = $assignment_id;
        try {
            $assignment->delete();
            $result = array(
                'success' => true,
                );
        } catch (Exception $e) {
            $result = array(
                'success' => false,
                'message' => $e->getMessage(),
                );
        }

        $this->load->view('response/datatable', array('json' => $result));
    }
}
