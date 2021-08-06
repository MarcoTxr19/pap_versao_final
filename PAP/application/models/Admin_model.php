<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Admin_model class.
 *
 * @extends CI_Model
 */
class Admin_model extends CI_Model {



	public function __construct() {
		parent::__construct();
	}

	public function update_user_rights($user_id, $is_admin, $is_moderator) {

		$data = array(
			'is_admin'     => $is_admin,
			'is_moderator' => $is_moderator,
			'updated_at'   => date('Y-m-j H:i:s'),
			'updated_by'   => $this->session->userdata('user_id')
		);

		$this->db->where('id', $user_id);
		return $this->db->update('users', $data);

	}

}

