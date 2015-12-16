<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'/libraries/REST_Controller.php';

class Api extends REST_Controller{
	function __construct(){
		parent::__construct();
		$this->load->helper('my_api');
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
		$data = remove_unknown_fields($this->put(), $this->form_validation->get_field_names('student_put'));
		$this->form_validation->set_data($data);
		if($this->form_validation->run('student_put') != false){
			$this->load->model('Model_students');
			
			$exists = $this->Model_students->get_by(array('email_address'=>$this->put('email_address')));
			if($exists){
				$this->response(array('status'=>'failure','message'=>'The specified email address has been exists'),REST_Controller::HTTP_CONFLICT);
			}
			
			$student_id = $this->Model_students->insert($data);
			if (!$student_id){
				$this->response(array('status'=> 'failure', 'message'=> 'An unexpected error occurred while trying to create the student'), REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
			} else {
				$this->response(array('status'=> 'success', 'message'=> 'created'));
			}
		} else {
			$this->response(array('status' => 'failure', 'message' => $this->form_validation->get_errors_as_array()), REST_Controller::HTTP_BAD_REQUEST);
		}
	}
	
	function student_post(){
		$student_id = $this->uri->segment(3);
		$this->load->model('Model_students');
		$student = $this->Model_students->get_by(array('student_id'=> $student_id, 'status'=>'active'));
	
		if (isset($student['student_id'])){
			$this->load->library('form_validation');
			$data = remove_unknown_fields($this->post(), $this->form_validation->get_field_names('student_post'));
			$this->form_validation->set_data($data);
			if($this->form_validation->run('student_post') != false){
				$this->load->model('Model_students');
					
				$safe_email = !isset($data['email_address']) || $data['email_address'] == $student['email_address'] || !$this->Model_students->get_by(array('email_address'=> $data['email_address']));
				if(!$safe_email){
					$this->response(array('status'=>'failure','message'=>'The specified email address is already in use'),REST_Controller::HTTP_CONFLICT);
				}
					
				$updated = $this->Model_students->update($student_id, $data);
				if (!$updated){
					$this->response(array('status'=> 'failure', 'message'=> 'An unexpected error occurred while trying to update the student'), REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
				} else {
					$this->response(array('status'=> 'success', 'message'=> 'updated'));
				}
			} else {
				$this->response(array('status' => 'failure', 'message' => $this->form_validation->get_errors_as_array()), REST_Controller::HTTP_BAD_REQUEST);
			}
		} else {
			$this->response(array('status'=> 'failure', 'message'=> 'The specified student could not be found'), REST_Controller::HTTP_NOT_FOUND);
		}
	}
	
	function student_delete(){
		$student_id = $this->uri->segment(3);
		$this->load->model('Model_students');
		$student = $this->Model_students->get_by(array('student_id'=> $student_id, 'status'=>'active'));
	
		if (isset($student['student_id'])){
			$data['status'] = 'deleted';
			$deleted = $this->Model_students->update($student_id, $data);
			if (!$deleted){
				$this->response(array('status'=> 'failure', 'message'=> 'An unexpected error occurred while trying to delete the student'), REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
			}
			else {
				$this->response(array('status'=> 'success', 'message'=> 'deleted'));
			}
			
		} else {
			$this->response(array('status'=> 'failure', 'message'=> 'The specified student could not be found'), REST_Controller::HTTP_NOT_FOUND);
		}
	}
}

