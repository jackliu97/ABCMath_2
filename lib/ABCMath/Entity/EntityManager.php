<?php
namespace ABCMath\Entity;

use ABCMath\Meta\Implement\ElementList;
use ABCMath\Base;

class EntityManager extends Base implements ElementList
{
    public $id;
    public $table_name;
    public $display_name;

    private $_black_list;

    public function __construct()
    {
        parent::__construct();
        $this->_black_list = array(
            '\'ci_sessions\'',
            '\'entity\'',
            '\'entity_fields\'',
            '\'grades\'',
            '\'groups\'',
            '\'invoice_sequence\'',
            '\'migrations\'',
            '\'throttle\'',
            '\'users\'',
            '\'users_groups\'',
            '\'users2\'',
            );
    }

    public function allEntitySQL()
    {
        $qb = $this->_conn->createQueryBuilder();
        $qb->select('e.id', 'e.table_name', 'e.display_name', 'e.last_updated')
            ->from('entity', 'e');

        return $qb->getSQL();
    }

    public function allEntityFieldsSQL($id)
    {
        $sql = "SELECT 	ef.id,
						ef.entity_table_id,
						e.table_name AS table_name,
						ef.field_name,
						ef.display_name,
						ef.field_type_id,
						fld.name AS field_type,
						ef.is_primary,
						ef.display_in_list,
						ef.field_order,
						ef.last_updated
				FROM entity_fields ef
				INNER JOIN entity e ON ef.entity_table_id = e.id
				INNER JOIN entity_field_types fld ON ef.field_type_id = fld.id
				AND ef.entity_table_id = {$id}";

        return $sql;
    }

    public function getAllFieldsByEntity($entity_id)
    {
        $qb = $this->_conn->createQueryBuilder();

        $qb->select('ef.id')
            ->from('entity_fields', 'ef')
            ->where('ef.entity_table_id = ?')
            ->setParameter(0, $entity_id)
            ->orderBy('ef.field_order, ef.field_name');

        return $qb->execute()->fetchAll();
    }

    public function getAllTables()
    {
        $qb = $this->_conn->createQueryBuilder();

        $qb->select('t.TABLE_NAME')
            ->from('information_schema.TABLES', 't')
            ->where("t.TABLE_SCHEMA = ? AND t.TABLE_SCHEMA = '{$this->_db_name}'"
                    ."AND t.TABLE_NAME NOT IN (".implode(',', $this->_black_list).")")
            ->setParameter(0, $this->_db_name);

        return $qb->execute()->fetchAll();
    }

    public function getAllUnusedTables()
    {
        $q = "SELECT t.TABLE_NAME
				FROM information_schema.TABLES t
				LEFT JOIN {$this->_db_name}.entity e
					ON e.table_name = t.TABLE_NAME
				WHERE e.table_name IS NULL
				AND t.TABLE_SCHEMA = '{$this->_db_name}'
				AND t.TABLE_NAME NOT IN (".implode(',', $this->_black_list).")
				ORDER BY t.table_name";

        $stmt = $this->_conn->prepare($q);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function getAllTableColumns($table_name)
    {
        $qb = $this->_conn->createQueryBuilder();

        $qb->select('c.COLUMN_NAME')
            ->from('information_schema.COLUMNS', 'c')
            ->where('c.TABLE_SCHEMA = ? AND c.TABLE_NAME = ?')
            ->setParameter(0, $this->_db_name)
            ->setParameter(1, $table_name);

        return $qb->execute()->fetchAll();
    }

    public function getAllUnusedTableColumns($table_name)
    {
        $q = "SELECT c.COLUMN_NAME
				FROM information_schema.COLUMNS C
				WHERE c.TABLE_SCHEMA = '{$this->_db_name}'
				AND c.TABLE_NAME = '{$table_name}'
				AND c.COLUMN_NAME not in (select f.field_name
				from {$this->_db_name}.entity_fields f
				inner join {$this->_db_name}.entity e ON e.id = f.entity_table_id
				where e.table_name = '{$table_name}')
				ORDER BY c.COLUMN_NAME";

        $stmt = $this->_conn->prepare($q);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function getAllFieldTypes()
    {
        $qb = $this->_conn->createQueryBuilder();
        $qb->select('eft.id', 'eft.name', 'eft.type')
            ->from('entity_field_types', 'eft');

        return $qb->execute()->fetchAll();
    }
}
