<?php
namespace ABCMath\File;

use ABCMath\Base;

class FileManager extends Base
{
    protected $file_path;
    protected $file_id;

    public function __construct(){
        parent::__construct();
        $this->file_path = null;
    }

    public function set($file_path){
        $this->file_path = $file_path;
    }

    public function save(){

        $this->_conn->insert('files',
                    array('file_path' => $this->file_path)
                    );

        return $this->_conn->lastInsertId();

    }

    public function load( $file_id ){

        $qb = $this->_conn->createQueryBuilder();
        $qb->select('f.file_path')
            ->from('files', 'f')
            ->where('f.id = ?')
            ->setParameter(0, $file_id);

        $result = $qb->execute()->fetch();
        return array_get($result, 'file_path');

    }

}