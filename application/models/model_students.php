<?php defined('BASEPATH') or exit('No direct script access allowed');

class Model_students extends MY_Model{
	
	protected $_table = 'students';
	protected $primary_key = 'student_id';
	protected $return_type = 'array';
	
	protected $after_get = array('remove_sensitive_data');
	protected $before_create = array('prep_data');
	
	protected function remove_sensitive_data($student){
		unset($student['password']);
		unset($student['ip_address']);
		return $student;
	}
	
	protected function prep_data($student){
		$student['password'] = md5($student['password']);
		$student['ip_address'] = $this->input->ip_address();
		$student['created_timestamp'] = date('Y-m-d H:i:s');
		return $student;
	}
}
