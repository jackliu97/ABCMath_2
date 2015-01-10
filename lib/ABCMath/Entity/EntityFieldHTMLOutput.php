<?php
namespace ABCMath\Entity;

use ABCMath\Entity\EntityField;

class EntityFieldHTMLOutput extends EntityField
{
    protected static $options = array();

    public function __construct()
    {
        parent::__construct();
    }

    public function buildFieldHTML($value = null)
    {
        $div_class = 'form-group';
        $field = '';

        switch ($this->field_type_id) {
            case self::INPUT:
                $field = $this->_buildInput($value);
            break;

            case self::SELECT:
                $field = $this->_buildSelect($value);
            break;

            case self::CHECKBOX:
                $div_class = 'checkbox';
                $field = $this->_buildCheckbox($value);
            break;

            case self::DATE:
                $field = $this->_buildDate($value);
            break;

            case self::TIME:
                $field = $this->_buildTime($value);
            break;

            case self::DATETIME:
                $field = $this->_buildDateTime($value);
            break;

            case self::TEXTAREA:
                $field = $this->_buildTextarea($value);
            break;
        }

        return "<div class='{$div_class}'>".
                "<label>{$this->display_name}</label>".
                "$field</div>";
    }

    protected function _buildSelect($value, $options = null)
    {
        if (is_null($options)) {
            $options = $this->_buildOptions($this->join_table);
        }

        return form_dropdown($this->field_name,
                    $options,
                    $value,
                    " id='{$this->field_name}' class='form-control' ");
    }

    protected function _buildOptions()
    {
        $result = array();
        if (!$this->join_table) {
            return $result;
        }

        $qb = $this->_conn->createQueryBuilder();
        $qb->select('t.id as id', "t.{$this->join_display} as description")
            ->from($this->join_table, 't');
        $options = $qb->execute()->fetchAll();

        if (!$options) {
            return $result;
        }

        foreach ($options as $option) {
            $result[$option['id']] = $option['description'];
        }

        return $result;
    }

    protected function _buildCheckbox($value)
    {
        $data = array(
          'name' => $this->field_name,
          'id' => $this->field_name,
          'value' => '1',
          'checked' => (empty($value) ? '' : true),
      );

        return form_checkbox($data);
    }

    protected function _buildInputData($value)
    {
        $data = array(
            'name' => $this->field_name,
            'id' => $this->field_name,
            'value' => (isset($value) ? $value : ''),
            'class' => 'form-control',
        );

        if ($this->is_primary) {
            $data['disabled'] = 'disabled';
        }

        if ($this->field_size > 0) {
            $data['size'] = $this->field_size;
            $data['maxlength'] = $this->field_size;
        }

        return $data;
    }

    protected function _buildTextareaData($value)
    {
        $data = array(
            'name' => $this->field_name,
            'id' => $this->field_name,
            'value' => (isset($value) ? $value : ''),
            'class' => 'form-control',
        );

        return $data;
    }

    protected function _buildDateTime($value)
    {
        $data = $this->_buildInputData($value);
        $data['class'] = 'form-control validate-date';
        $data['placeholder'] = 'mm/dd/yyyy hh:mm am/pm';

        return form_input($data);
    }

    protected function _buildTime($value)
    {
        $options = time_dropdown_options(8, 20);

        return $this->_buildSelect($value, $options);

        $data = $this->_buildInputData($value);
        $data['class'] = 'form-control validate-time';
        $data['placeholder'] = 'hh:mm';

        return form_input($data);
    }

    protected function _buildDate($value)
    {
        $data = $this->_buildInputData($value);
        $data['class'] = 'form-control validate-datetime';
        $data['placeholder'] = 'yyyy-mm-dd';

        return form_input($data);
    }

    protected function _buildInput($value)
    {
        return form_input($this->_buildInputData($value));
    }

    protected function _buildTextarea($value)
    {
        return form_textarea($this->_buildTextareaData($value));
    }
}
