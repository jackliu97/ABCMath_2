<?php
namespace ABCMath\Attachment;

use \ABCMath\Base,
\ABCMath\Attachment\Attachment;


class AttachmentManager extends Base {
	public $attachments;

	public function __construct(){
		$this->attachments = array();
	}

	public function getAttachmentsByAssignment($attachment_id){

		$data = $this->_getAttachmentsByAssignmentData($attachment_id);
		if(!count($data)){
			return array();
		}

		foreach($data as $attachmentData){
			$attachmentObject = new Attachment();
			$attachmentObject->load($attachmentData);
			$this->attachments[]= $attachmentObject;
		}

		return $this->attachments;
	}

	protected function _getAttachmentsByAssignmentData($attachment_id){
		$q = "SELECT * FROM assignments_attachments aa
				INNER JOIN attachments a ON a.id = aa.attachment_id
				WHERE aa.assignment_id = ?";

		$stmt = $this->_conn->prepare($q);
		$stmt->bindValue(1, $attachment_id);
		$stmt->execute();
		return $stmt->fetchAll();
	}

	public function getAllAttachments(){
		$allData = $this->getAllAttachmentsData();
	}

	public function getAllAttachmentsData(){
		$qb = $this->_conn->createQueryBuilder();
		$qb->select('*')
			->from('attachments', 'a')
			->where('a.id = ?')
			->setParameter(0, $this->id);
		return $qb->execute()->fetch();
	}
}