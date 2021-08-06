<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/*
 * User_model CRUD da tabela users
 * 
 * @author Marco Teixeira
 */
class User_model extends CI_Model{
	public function __construct(){
		parent::__construct();
		$this->table='users';
	}

	/**
	 * createUser function Insere um novo utilizador na base de dados
	 * 
	 * @access public
	 * @param String $username nome do utilizador
	 * @param String $password password do utilizador
	 * @param String $email email do utilizador
	 */
	public function createUser($username,$password,$email){
		$dadosUser = array(
			'username'=>$username,
			'email'=>$email,
			'password'=>$this->hash_password($password),
			'users_helped' => '[]',
			'created_at'=>date('Y-m-j H:i:s')
		);
		if($this->db->insert($this->table,$dadosUser))

			return "Conta Criada com Sucesso";
		else
			return  "Algo correu mal";

	}



	/**
	 * function resolveLogin 
	 * Seleciona da db o field password na row que  o field username seja igual ao $username
	 * e compara a password inserida com a da db utilizando o metodo nativo do PHP password_verify()
	 * esse metodo retorna um boolean.
	 * 
	 * @access public
	 * @param String $username Nome de utilizador inserido
	 * @param String $password Password inserida
	 * @return Boolean  
	 */
	public function resolveLogin($username,$password){
		$this->db->select('password');
		$this->db->from($this->table);
		$this->db->where('username', $username);
		$hash = $this->db->get()->row('password');

		return $this->verify_password_hash($password, $hash);
	}

	private function verify_password_hash($password, $hash){
		return password_verify($password,$hash);
	}


	/**
	 * 
	 */
	public function deleteUser($user_id) {

		// delete all user topics, posts and delete user account
		$this->db->where('id', $user_id);
		if ($this->db->delete('users')) {
			$this->db->where('id_user', $user_id);
			if($this->db->delete('forums')){
				$this->db->where('user_id', $user_id);
				if ($this->db->delete('topics')) {
					$this->db->where('user_id', $user_id);
					return $this->db->delete('posts');
				}
				return;
			}
			return;
		}
		return;
	}

	public  function  getIdByUsername($username){

		$query=$this->db->get_where($this->table,array('username'=>$username));
		$dadosUser=$query->result();
		return $dadosUser[0]->id;
}



	public function getUsernameById($id){
		$this->db->select('username');
		$this->db->from($this->table);
		$this->db->where('id', $id);

		return $this->db->get()->row('username');


	}


	public function feed(){
		$id=$this->session->userdata('user_id');
		$this->db->where('user_id',$id);
		$this->db->from('feed');
		return $this->db->get()->result();
	}
	public function feedById($id){
		$this->db->where('id',$id);
		$this->db->from('feed');
		return $this->db->get()->row();
	}

	public function deleteFeed($id){
		$this->db->where('id',$id);
		return $this->db->delete('feed');
	}


	public function getUserById($user_id) {

		$this->db->from($this->table);
		$this->db->where('id', $user_id);
		return $this->db->get()->row();

	}
	public function getAllUsers() {

		$this->db->from($this->table);
		return $this->db->get()->result();

	}


	public function count_user_posts($user_id) {

		$this->db->select('id');
		$this->db->from('posts');
		$this->db->where('user_id', $user_id);
		return $this->db->get()->num_rows();

	}


	public function count_user_topics($user_id) {

		$this->db->select('id');
		$this->db->from('topics');
		$this->db->where('user_id', $user_id);
		return $this->db->get()->num_rows();

	}




	public function get_user_last_post($user_id) {

		$this->db->from('posts');
		$this->db->where('user_id', $user_id);
		$this->db->order_by('created_at', 'DESC');
		$this->db->limit(1);
		return $this->db->get()->row();

	}


	public function get_user_last_topic($user_id) {

		$this->db->from('topics');
		$this->db->where('user_id', $user_id);
		$this->db->order_by('created_at', 'DESC');
		$this->db->limit(1);
		return $this->db->get()->row();

	}

	private function hash_password($password)
	{

		return password_hash($password, PASSWORD_BCRYPT);
	}

	private function confirmarEmail($username, $email) {

		// load email library and url helper
		$this->load->library('email');

		// get the site email address
		$email_address = $this->config->item('site_email');

		// initialize the email configuration
		$this->email->initialize(array(
			'protocol' => 'smtp',
			'smtp_host' => 'ssl://smtp.gmail.com',
			'smtp_port' => 465,
			'smtp_user' => 'markinhot2003@gmail.com',
			'smtp_pass' => 'Marquinho2003',
			'mailtype' => 'html',
			'charset' => 'utf-8'
		));

		// get user registration date
		$registration_date = $this->db->select('created_at')->from('users')->where('username', $username)->get()->row('created_at');

		// create a user email hash with user email and user registration date
		$hash = sha1($email . $registration_date);

		// prepare the email
		$this->email->from($email_address, $email_address);
		$this->email->to($email);
		$this->email->subject('Please confirm your email to validate your new user account.');
		$message  = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional //EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><body>';
		$message .= "Hi " . $username . ",<br><br>";
		$message .= "Please click the link below to confirm your account on " . base_url() . "<br><br>";
		$message .= "Click this link: <a href=\"" . base_url() . "user/email_validation/" . $username . "/" . $hash . "\">Confirm your email and validate your account</a>";
		$message .= "</body></html>";
		$this->email->message($message);

		// send the email and return status
		return $this->email->send();

	}


	public function confirm_account($username, $hash) {

		// find the email for the given user
		$email = $this->db->select('email')
			->from('users')
			->where('username', $username)
			->get()
			->row('email');

		// find the registration date for the given user
		$registration_date = $this->db->select('created_at')
			->from('users')
			->where('username', $username)
			->get()
			->row('created_at');

		// if the user from the url exists
		if ($email && $registration_date) {

			if (sha1($email . $registration_date) === $hash) {

				// values from the url are good, we can validate the account
				$data = array('is_confirmed' => '1');
				$this->db->where('username', $username);
				return $this->db->update('users', $data);

			}
			return false;

		}
		return false;

	}





	/**
	 * updateUser function.
	 * 
	 * @access public
	 * @param int $user_id
	 * @param array $update_data
	 * @return bool
	 */
	public function updateUser($user_id, $update_data) {
		
		// if user wants to update its password, hash the given password
		if (array_key_exists('password', $update_data)) {
			$update_data['password'] = $this->hash_password($update_data['password']);
		}
		
		if (!empty($update_data)) {
			
			$this->db->where('id', $user_id);
			return $this->db->update('users', $update_data);
			
		}
		return false;
		
	}





	/**
	 * 
	 * 
	 */
	
	public function addFame($user_id,$user_voting){
		$listaUsersHelped= $this->getUsersHelped($user_id);
		if(!in_array($user_voting,$listaUsersHelped)){
			$this->addUsersHelped($user_id,$user_voting);
			$this->db->set('fame',$this->getFame($user_id)+1);
			$this->db->where('id',$user_id);
			$this->db->update($this->table);
			return true;
		}	
		return false;
		
	}


	/**
	 * 
	*/
	public function getFame($user_id)
	{
		$this->db->select('fame');
		$this->db->where('id',$user_id);
		$this->db->from($this->table);
		$result = $this->db->get()->row()->fame;
		var_dump($result);
		return $result;
	}
/**
 * 
 */
	public function getUsersHelped($user_id)
	{
		$this->db->select('users_helped');
		$this->db->where('id',$user_id);
		$this->db->from($this->table);
		$result =$this->db->get()->row('users_helped');
		//var_dump(json_decode($result));
		return json_decode($result);
	}


	/**
	 * addUsersHelped function
	 * @access public
	 * @param int $user_id (id do user votado)
	 * @param int $user_helped_id (id do user que esta a votar nesse utilizador)
	 */
	public function addUsersHelped($user_id,$user_helped_id){
		$listaUsers=$this->getUsersHelped($user_id);
		$listaUsers[count($listaUsers)]=$user_helped_id;
		if(in_array($user_helped_id, $listaUsers)){
			$this->db->set('users_helped',json_encode($listaUsers));
			$this->db->where('id',$user_id);
			$this->db->update($this->table);
		}
		return;
	}


}
