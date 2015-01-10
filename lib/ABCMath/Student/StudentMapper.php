<?php
namespace ABCMath\Student;

use ABCMath\Base;

class StudentMapper extends Base
{
    public static function getMapper()
    {
        $stumap = new StudentMapper();
        $table_data = $stumap->getTableData();
        $mapper_data = array();
        foreach ($table_data as $data) {
            $mapper_data[$data['field_name']] = array(
                                'display_name' => $data['display_name'],
                                'type' => $data['type'], );
        }

        return $mapper_data;
    }

    public function getTableData()
    {
        $sql = "SELECT
					ef.field_name,
					ef.display_name,
					eft.type
				FROM entity_fields ef
				LEFT JOIN entity_field_types eft ON ef.field_type_id = eft.id
				WHERE entity_table_id = ?
				ORDER BY ef.id";

        $stmt = $this->_conn->prepare($sql);
        $stmt->bindValue(1, 1);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
