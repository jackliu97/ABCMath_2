<?php
namespace ABCMath\Student;

use ABCMath\Base;
use ABCMath\Course\ABCClassManager;

/**
 * This class imports a group of students.
 *
 */

class StudentImporter extends Base
{
    protected $_file;
    protected $_all_classes;
    protected $_mapper;
    protected $_message;
    protected $_insert_count;
    protected $_update_count;
    protected $_importer;

    public function __construct()
    {
        parent::__construct();

        $this->_mapper = StudentMapper::getMapper();
        $this->_message = array();
        $this->_all_classes = array();
        $this->_file = array();
        $this->_insert_count = 0;
        $this->_update_count = 0;
        $this->_importer = false;
    }

    public function getType($mime)
    {
        $types = array(
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
            'text/csv' => 'csv',
            'application/vnd.oasis.opendocument.spreadsheet' => 'xlsx',
            );

        return isset($types[$mime]) ? $types[$mime] : '';
    }

    // Array format accepted.
    // (
    //     [file_name] => 11-29-144-50_PM2.xlsx
    //     [file_type] => application/vnd.openxmlformats-officedocument.spreadsheetml.sheet
    //     [file_path] => /Users/jackliu/Sites/problem_bank/public/uploads/student_uploads/
    //     [full_path] => /Users/jackliu/Sites/problem_bank/public/uploads/student_uploads/11-29-144-50_PM2.xlsx
    //     [raw_name] => 11-29-144-50_PM2
    //     [orig_name] => 11-29-144-50_PM.xlsx
    //     [client_name] => 11-29-144-50_PM.xlsx
    //     [file_ext] => .xlsx
    //     [file_size] => 50.02
    //     [is_image] =>
    //     [image_width] =>
    //     [image_height] =>
    //     [image_type] =>
    //     [image_size_str] =>
    // )

    public function setFile(array $file)
    {
        $this->_file = $file;
    }

    public function import()
    {
        $type = $this->getType($this->_file['file_type']);

        $set_method = "set_{$type}_importer";
        $this->$set_method($this->_file['full_path']);

        $import_method = "import_{$type}";

        return $this->$import_method();
    }

    public function set_xlsx_importer($full_path)
    {
        $this->_importer = \PHPExcel_IOFactory::load($full_path);
    }

    public function import_xlsx()
    {
        $success = true;
        $header = array();
        $student_number = 1;
        $class_columns = array();
        $note_columns = array();
        $this->_failed_count = 0;
        $this->_update_count = 0;
        $this->_insert_count = 0;

        $row_iterator = $this->_importer->getActiveSheet()->getRowIterator();

        $this->_conn->beginTransaction();
        $this->_all_classes = $this->getClasses();

        $get_count_stmt = $this->_conn->prepare(
            "SELECT count(*) c FROM students WHERE external_id = ?"
            );

        foreach ($row_iterator as $row_number => $row) {
            // work on each row individually.
            $col_iterator = $row->getCellIterator();
            $student = array();
            $classes = array();
            $notes = array();

            // if we're on the first row... build header.
            if ($row->getRowIndex() == 1) {
                foreach ($col_iterator as $header_col) {
                    //if class header is matched, we load class array.
                    preg_match('/class[\d+]*/', $header_col->getValue(), $matches);
                    if (count($matches)) {
                        $class_columns[] = $header_col->getColumn();
                    }

                    //if notes header is matched, we load notes array.
                    preg_match('/note[\d+]*/', $header_col->getValue(), $matches);
                    if (count($matches)) {
                        $note_columns[] = $header_col->getColumn();
                    }

                    if (!array_key_exists($header_col->getValue(), $this->_mapper)) {
                        continue;
                    }
                    $header[$header_col->getColumn()] = $header_col->getValue();
                }
                continue;
            }

            //$this->_message []= "---PROCESSING RECORD [{$student_number}]---";

            //build the rest of the columns.
            foreach ($col_iterator as $k => $col) {
                //stuff like 'A', 'B', 'C', 'D'...
                $column_key = $col->getColumn();

                //if this column is a class column, we append the value as a selected class.
                if (in_array($column_key, $class_columns)) {
                    $class_ext_id = trim($col->getValue());
                    if ($class_ext_id) {
                        $classes[] = $class_ext_id;
                    }
                }

                //if this column is a note column, we append the value as a note.
                if (in_array($column_key, $note_columns)) {
                    $notes[] = $col->getValue();
                }

                if (!array_key_exists($column_key, $header)) {
                    continue;
                }

                //stuff like 'external_id', 'first_name', 'class_1', 'class_2' ...
                $header_name = $header[$column_key];

                //convert specific xls data to sql friendly data.
                $value = $col->getValue();
                $data_type = strtolower($this->_mapper[$header_name]['type']);
                $student[$header_name] = $this->xlsx2PHP($value, $data_type);
            }

            if (!isset($student['external_id']) || intval($student['external_id']) === 0) {
                $this->_message [] =
                    "RECORD [{$student_number}] have an invalid external_id (skipping)";
                $this->_failed_count += 1;
                $student_number += 1;
                $this->_message [] = '';
                continue;
            }

            $get_count_stmt->bindValue(1, $student['external_id']);
            $get_count_stmt->execute();
            $count_result = $get_count_stmt->fetch();

            if ($count_result['c'] == 0) {
                if ($this->_insertStudent($student)) {
                    $this->_insert_count += 1;
                    $student_id = $this->_conn->lastInsertId();
                } else {
                    $this->_conn->rollback();
                    $success = false;
                    break;
                }
            } else {
                if ($this->_updateStudent($student, $student['external_id'])) {
                    $this->_update_count += 1;
                } else {
                    $this->_conn->rollback();
                    $success = false;
                    break;
                }
            }

            //update student with class.
            if ($this->_registerStudentToClass($student['external_id'], $classes) === false) {
                $this->_conn->rollback();
                $success = false;
                break;
            }

            //update student with note.
            if ($this->_addNotesToStudent($student['external_id'], $notes) === false) {
                $this->_conn->rollback();
                $success = false;
                break;
            }
            $student_number += 1;
            //$this->_message []= '';
        }

        if ($success === true) {
            $this->_message [] = 'All records processed without any fatal errors. ';
            $this->_message [] = "{$this->_insert_count} successful insert(s). ";
            $this->_message [] = "{$this->_update_count} successful update(s). ";
            $this->_message [] = "{$this->_failed_count} failed record(s). ";
            $this->_conn->commit();
        } else {
            $this->_message [] = 'No changes has been made.';
        }

        return array('message' => $this->_message, 'success' => $success);
    }

    public function xlsx2PHP($xls_value, $type)
    {
        $php_value = $xls_value;
        switch ($type) {
            case 'date';

                //convert xlsx date to php date.
                //https://phpexcel.codeplex.com/discussions/70463
                $php_value = \DateTime::createFromFormat(
                    'U',
                    \PHPExcel_Shared_Date::ExcelToPHP($xls_value)
                )
                ->format('Y-m-d')
                ;

            break;
        }

        return $php_value;
    }

    public function getClasses()
    {
        $cm = new ABCClassManager();

        return $cm->getAllClasses();
    }

    protected function _insertStudent(array $data)
    {
        //make sure no primary keys exists.
        if (isset($data['id'])) {
            unset($data['id']);
        }

        try {
            $this->_conn->insert(
                'students',
                $data
                );
        } catch (\Doctrine\DBAL\DBALException $e) {
            $this->_message [] = '[Failed] on INSERT Column: '.print_r($data, true).
                            ' Reason'.$e->getMessage();

            return false;
        }

        //$this->_message []= "[{$data['external_id']}] INSERT: successful";
        return true;
    }

    protected function _updateStudent(array $data, $external_id)
    {
        try {
            $this->_conn->update(
                'students',
                $data,
                array('external_id' => $external_id)
                );
        } catch (\Doctrine\DBAL\DBALException $e) {
            $this->_message [] = '[Failed] on UPDATE Column: '.print_r($data, true).
                            " External ID: {$external_id}".
                            ' Reason'.$e->getMessage();

            return false;
        }

        //$this->_message []= "[{$data['external_id']}] UPDATE: successful";
        return true;
    }

    protected function _addNotesToStudent($student_external_id, array $notes)
    {
        if (count($notes) === 0) {
            return true;
        }
        $stmt = $this->_conn->prepare(
                'INSERT IGNORE INTO notes (student_id, notes, creation_datetime)
				SELECT s.id AS student_id, ?, NOW()
				FROM students s
				WHERE s.external_id = ?'
                );

        foreach ($notes as $note) {
            if (trim($note) === '') {
                continue;
            }

            $stmt->bindValue(1, $note);
            $stmt->bindValue(2, $student_external_id);

            try {
                $stmt->execute();
            } catch (\Doctrine\DBAL\DBALException $e) {
                $this->_message [] = '[Failed] on ADDING NOTES '.
                                    "student: [{$student_external_id}] ".
                                    'note: '.print_r($notes, true).
                                    ' Reason'.$e->getMessage();

                return false;
            }

            //$this->_message []= "[{$student_external_id}] ADDED NOTE:" .
            //	substr($note, 0, 15) . '...';
        }
    }

    protected function _registerStudentToClass($student_external_id, array $classes)
    {
        if (count($classes) === 0) {
            return true;
        }

        $params = array();
        $params_val = array();

        //$this->_message []= "REGISTERING classes [" .
        //	implode( ',', $classes ) . "]";

        foreach ($classes as $ext_id) {
            $params[] = '?';
            $params_val[] = $ext_id;
        }

        $params_val[] = $student_external_id;

        $stmt = $this->_conn->prepare(
            'INSERT IGNORE INTO student_class (student_id, class_id)
					SELECT  s.id AS student_id,
							c.id AS class_id
					FROM students s
						LEFT JOIN classes c ON c.external_id IN ( '.implode(',', $params).' )
					WHERE s.external_id = ?'
            );

        foreach ($params_val as $k => $val) {
            $stmt->bindValue(++$k, $val);
        }

        try {
            $stmt->execute();
        } catch (\Doctrine\DBAL\DBALException $e) {
            $this->_message [] = "[Failed] on REGISTER student: [{$student_external_id}] class: ".
                            print_r($classes, true).' Reason'.$e->getMessage();

            return false;
        }
        //$this->_message []= "[{$student_external_id}] REGISTERED to classes [" .
        //	implode( ',', $classes ) . "]";
        return true;
    }
}
