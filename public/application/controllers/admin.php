<?php if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}
use \ABCMath\Entity\EntityManager;
use \ABCMath\Entity\Entity;
use \ABCMath\Entity\EntityField;
use \ABCMath\Db\Datatable;

class admin extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if ($this->User_Model->check_permission('admin') == false) {
            $this->session->sess_destroy();
            header('Location: /login');
        }

        $this->session->set_userdata(
            array(
                'section' => 'admin',
                )
            );
    }

    public function index()
    {
        $this->load->view('header');
        $this->load->view('navbar');

        $this->load->view('admin/entity_list');
        $this->load->view('footer', array(
                                        'private_js' => array('admin/entity_list.js'),
                                        'datatable' => true,
                                        ));
    }

    public function entity_detail($id = null)
    {
        $data = array();

        if (!is_null($id)) {
            $entity = new Entity();
            $entity->id = $id;
            $entity->load();
            $data['entity'] = $entity;
        }

        $entity_manager = new EntityManager();
        $tables = $id ? $entity_manager->getAllTables() : $entity_manager->getAllUnusedTables();
        $data['table_options'] = array();

        foreach ($tables as $table) {
            $data['table_options'][$table['TABLE_NAME']] = $table['TABLE_NAME'];
        }

        $this->load->view('header');
        $this->load->view('navbar');
        $this->load->view('admin/entity_detail', $data);
        $this->load->view('footer', array(
                                        'private_js' => array('admin/entity_detail.js'),
                                        ));
    }

    public function save_entity()
    {
        $id = $this->input->post('id');
        $table_name = $this->input->post('table_name');
        $display_name = $this->input->post('display_name');
        $before_hook = $this->input->post('before_hook');
        $after_hook = $this->input->post('after_hook');

        if (!is_numeric($id) || $id <= 0) {
            $id = null;
        }

        $entity = new Entity();
        $entity->id = $id;
        $entity->table_name = $table_name;
        $entity->display_name = $display_name;
        $entity->before_hook = $before_hook;
        $entity->after_hook = $after_hook;
        $result = $entity->save();

        if ($result['success']) {
            $result['entity_id'] = $entity->id;
        }

        $this->load->view('response/json', array('json' => $result));

        return true;
    }

    public function delete_entity()
    {
        $entity_id = $this->input->post('entity_id');
        $entity = new Entity();
        $entity->id = $entity_id;
        $result = $entity->delete();

        $this->load->view('response/json', array('json' => $result));
    }

    public function entity_fields($id)
    {
        $data = array(
            'entity_id' => $id,
            );
        $entity = new Entity();
        $entity->id = $id;
        $entity->load();

        $data['table_name'] = $entity->display_name;
        $data['table_id'] = $entity->id;

        $this->load->view('header');
        $this->load->view('navbar');
        $this->load->view('admin/entity_field_list', $data);
        $this->load->view('footer', array(
                                        'private_js' => array('admin/entity_field_list.js'),
                                        'datatable' => true,
                                        ));
    }

    public function delete_entity_field()
    {
        $field_id = $this->input->post('field_id');
        $field = new EntityField();
        $field->id = $field_id;
        $result = $field->delete();

        $this->load->view('response/json', array('json' => $result));
    }

    public function entity_field_detail($table_id, $id = null)
    {
        $data = array();

        $entity = new Entity();
        $entity->id = $table_id;
        $entity->load();
        $data['entity'] = $entity;

        if (!is_null($id)) {
            $field = new EntityField();
            $field->id = $id;
            $field->load();
            $data['field'] = $field;
        }

        $entity_manager = new EntityManager();
        $fields = $entity_manager->getAllUnusedTableColumns($entity->table_name);
        $data['field_options'] = array();

        foreach ($fields as $field) {
            $data['field_options'][$field['COLUMN_NAME']] = $field['COLUMN_NAME'];
        }

        $field_types = $entity_manager->getAllFieldTypes();
        foreach ($field_types as $type) {
            $data['field_type_options'][$type['id']] = $type['name'];
        }
        $entity_manager = new EntityManager();
        $tables = $entity_manager->getAllTables();
        $data['join_table_options'] = array('' => 'None');

        foreach ($tables as $table) {
            $data['join_table_options'][$table['TABLE_NAME']] = $table['TABLE_NAME'];
        }

        $this->load->view('header');
        $this->load->view('navbar');
        $this->load->view('admin/entity_field_detail', $data);
        $this->load->view('footer', array(
                                        'private_js' => array('admin/entity_field_detail.js'),
                                        ));
    }

    public function save_entity_field()
    {
        $entityField = new EntityField();
        $entityField->id = $this->input->post('field_id');

        foreach ($entityField->allFields() as $field) {
            $entityField->{$field} = $this->input->post($field);
        }

        $result = $entityField->save();

        if ($result['success']) {
            $result['field_id'] = $entityField->id;
        }
        $this->load->view('response/json', array('json' => $result));

        return true;
    }

    public function get_all_entity_fields($id)
    {
        $result = array('success' => false, 'message' => '');
        $list = new EntityManager();
        $dt = new Datatable();
        $dt->sql = $list->allEntityFieldsSQL($id);
        $dt->columns = array(    'id',
                                'field_name',
                                'display_name',
                                'field_type',
                                'display_in_list',
                                'is_primary',
                                'field_order',
                                'last_updated', );

        $result = $dt->processQuery();

        if (count($result['aaData'])) {
            foreach ($result['aaData'] as $key => $row) {
                foreach ($row as $k => $col) {
                    if ($k == 0) {
                        $field_id = $col;
                        $result['aaData'][$key][$k] =
                            "<button entity_id='{$id}' field_id='{$field_id}' ".
                            "class='btn edit_mode glyphicon glyphicon-pencil' style='width:45px;'></button>&nbsp;".
                            "<button field_id='{$field_id}' ".
                            "class='btn remove glyphicon glyphicon-remove' style='width:45px;'></button>";
                    }
                }
            }
        }

        $this->load->view('response/datatable', array('json' => $result));
    }

    public function get_all_entities()
    {
        $result = array('success' => false, 'message' => '');
        $list = new EntityManager();
        $dt = new Datatable();
        $dt->sql = $list->allEntitySQL();
        $dt->columns = array( 'id', 'display_name', 'table_name', 'last_updated');
        $result = $dt->processQuery();

        if (count($result['aaData'])) {
            foreach ($result['aaData'] as $key => $row) {
                foreach ($row as $k => $col) {
                    if ($k == 0) {
                        $field_id = $col;
                        $result['aaData'][$key][$k] =
                            "<button entity_id='{$field_id}' class='btn edit_mode glyphicon glyphicon-pencil' style='width:45px;'>".
                            "</button>&nbsp;".
                            "<button entity_id='{$field_id}' class='btn remove glyphicon glyphicon-remove' style='width:45px;'>".
                            "</button>";
                    } else {
                        $result['aaData'][$key][$k] =
                        "<a entity_id='{$field_id}' class='list_mode'>".
                        "{$result['aaData'][$key][$k]}</a>";
                    }
                }
            }
        }

        $this->load->view('response/datatable', array('json' => $result));
    }
}
