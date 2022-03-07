<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Dashboard extends CI_Controller {
	public function __construct() {
		parent::__construct();
		$this->load->model('Api_model');
	}
	public function manage() {
		$user_data = $this->be_model->logged_in();
		if(!$user_data) {
			redirect('/');
			return false;
		} else {
			$this->be_page->generate(true, 'dashboard/'.$user_data['user_role_name']);
		}
	}
	public function add_song() {
		$requests = $this->input->post();
		$page_param = 'dashboard/add_song';
		$response = array();
		$data_param = array();
		if(isset($requests['tag'])) {
			if($requests['tag'] == 'add_song1') {
				$request_fields = array('user_id', 'title', 'guest', 'type_beat', 'genre_ids', 'languages', 'is_live', 'inspirations', 'lyrics');
				$input_total_data = array(
					array('name' => 'title', 'label' => 'Title', 'maxlength' => 100, 'required' => true),
					array('name' => 'guest', 'label' => 'Guest'),
					array('name' => 'type_beat', 'label' => 'Type Beat'),
					array('name' => 'genre_ids', 'label' => 'Genre(s)', 'required' => true),
					array('name' => 'languages', 'label' => 'Language(s)', 'required' => true),
					array('name' => 'is_live', 'label' => 'Studio or Live', 'required' => true),
					array('name' => 'inspirations', 'label' => 'Inspirations'),
					array('name' => 'lyrics', 'label' => 'Lyrics')
				);
				// validate form input
				foreach($input_total_data as $item) {
					$this->form_validation->set_rules($item['name'], $item['label'], $this->be_model->get_form_validation_rule($item));
				}
				if($this->form_validation->run() == true) {
					$page_param = 'dashboard/add_song1';
					if(isset($requests['genre_ids']) && count($requests['genre_ids']) > 0) {
						$requests['genre_ids'] = $this->be_model->convert_array_to_string($requests['genre_ids']);
					}
					if(isset($requests['languages']) && count($requests['languages']) > 0) {
						$requests['languages'] = $this->be_model->convert_array_to_string($requests['languages']);
					}
				} else {
					$response['result'] = 0;
					$response['report']['status'] = 0;
					$response['report']['msg'] = validation_errors();
				}
				$data_param['requests'] = elements($request_fields, $requests);
			} else if($requests['tag'] == 'add_song2') {
				$request_fields = array('user_id', 'title', 'guest', 'type_beat', 'genre_ids', 'languages', 'is_live', 'inspirations', 'lyrics');
				/* $input_total_data = array(
					array('name' => 'link', 'label' => 'File Upload', 'required' => true),
					array('name' => 'terms_conditions', 'label' => 'Terms and Conditions', 'required' => true)
				);
				// validate form input
				foreach($input_total_data as $item) {
					$this->form_validation->set_rules($item['name'], $item['label'], $this->be_model->get_form_validation_rule($item));
				} */
				if(!isset($_FILES['link']) || (isset($_FILES['link']) && empty($_FILES['link']))) {
					$response['result'] = 0;
					$response['report']['status'] = 0;
					$response['report']['msg'] = 'Please select a file to upload.';
					$page_param = 'dashboard/add_song1';
				} else {
					$result = $this->Api_model->add_song($requests);
					if ($result['result'] == 0) {
						$response['result'] = 0;
						$response['report']['status'] = 2;
						$response['report']['msg'] = $result['report']['msg'];
						$page_param = 'dashboard/add_song1';
					} else {
						redirect('/');
					}
				}
				$data_param['requests'] = elements($request_fields, $requests);
			} else {
				$response['result'] = 0;
				$response['report']['status'] = 0;
				$response['report']['msg'] = 'Invalid Request';
			}
		}
		$this->be_page->generate(true, $page_param, $data_param, $response);
		return;
	}
}