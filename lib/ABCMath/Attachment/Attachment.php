<?php
namespace ABCMath\Attachment;

use ABCMath\Base;

class Attachment extends Base
{
    public $id;

    public function __construct()
    {
        parent::__construct();
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function load($data = array())
    {
        if (!count($data)) {
            $data = $this->_getFromDB();
        }

        foreach ($data as $k => $v) {
            $this->{$k} = $v;
        }
    }

    public function save()
    {
        if (!$this->id) {
            $this->id = $this->_insert();
        } else {
            $this->_update();
        }

        if (!$this->id) {
            $this->log('ID does not exist, save failed.');

            return false;
        }

        return true;
    }

    public function delete()
    {
        if (is_file($this->full_path) && unlink($this->full_path)) {
            $this->_conn->delete('attachments', array( 'id' => $this->id ));
        }
    }

    protected function _insert()
    {
        $this->_conn->insert('attachments',
            array(
                'description' => $this->description,
                'file_name' => $this->file_name,
                'file_type' => $this->file_type,
                'file_path' => $this->file_path,
                'full_path' => $this->full_path,
                'raw_name' => $this->raw_name,
                'orig_name' => $this->orig_name,
                'client_name' => $this->client_name,
                'file_ext' => $this->file_ext,
                'file_size' => $this->file_size,
                'is_image' => $this->is_image,
                'image_width' => $this->image_width,
                'image_height' => $this->image_height,
                'image_type' => $this->image_type,
                'image_size_str' => $this->image_size_str,
                )
        );
        $this->id = $this->_conn->lastInsertId();

        return $this->id;
    }

    protected function _update()
    {
        $this->_conn->update('attachments',
            array(
                'description' => $this->description,
                'file_name' => $this->file_name,
                'file_type' => $this->file_type,
                'file_path' => $this->file_path,
                'full_path' => $this->full_path,
                'raw_name' => $this->raw_name,
                'orig_name' => $this->orig_name,
                'client_name' => $this->client_name,
                'file_ext' => $this->file_ext,
                'file_size' => $this->file_size,
                'is_image' => $this->is_image,
                'image_width' => $this->image_width,
                'image_height' => $this->image_height,
                'image_type' => $this->image_type,
                'image_size_str' => $this->image_size_str,
                ),
            array(
                'id' => $this->id,
                ));
    }

    protected function _getFromDB()
    {
        $qb = $this->_conn->createQueryBuilder();
        $qb->select('*')
            ->from('attachments', 'a')
            ->where('a.id = ?')
            ->setParameter(0, $this->id);

        return $qb->execute()->fetch();
    }
}
