<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'/libraries/REST_Controller.php';

class Api extends REST_Controller{
	function __construct(){
		parent::__construct();
	}
	
	function student_get(){
		$student_id = $this->uri->segment(3);
		$this->load->model('Model_students');
		$student = $this->Model_students->get_by(array('student_id'=> $student_id, 'status'=>'active'));
		
		if (isset($student['student_id'])){
			$this->response(array('status'=> 'success', 'message'=> $student));
		} else {
			$this->response(array('status'=> 'failure', 'message'=> 'The specified student could not be found'), REST_Controller::HTTP_NOT_FOUND);
		}
	}
	
	function student_put(){
		$this->load->library('form_validation');
		$this->form_validation->set_data($this->put());
		if($this->form_validation->run('student_put') != false){
			$this->load->model('Model_students');
			$student = $this->put();
			$student_id = $this->Model_students->insert($student);
			if (!$student_id){
				$this->response(array('status'=> 'failure', 'message'=> 'An unexpected error occurred while trying to create the student'), REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
			} else {
				$this->response(array('status'=> 'success', 'message'=> 'created'));
			}
		} else {
			$this->response(array('status' => 'failure', 'message' => $this->form_validation->get_errors_as_array()), REST_Controller::HTTP_BAD_REQUEST);
		}
	}
}

