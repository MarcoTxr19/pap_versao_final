<?php
defined('BASEPATH') or exit('No direct script access allowed');

class UserController extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('User_model');
	}

	public function index($username = false)
	{

		if ($username === false) {
			redirect(base_url());
			return;
		}


		// create the data object
		$data = new stdClass();


		$this->load->model('Forum_model');

		// get user id from username
		$user_id = $this->User_model->getIdByUsername($username);


		$user               = $this->User_model->getUserById($user_id);
		$user->count_topics = $this->User_model->count_user_topics($user_id);
		$user->count_posts  = $this->User_model->count_user_posts($user_id);
		$user->latest_post  = $this->User_model->get_user_last_post($user_id);
		if ($user->latest_post !== null) {
			$user->latest_post->topic            = $this->Forum_model->getTopic($user->latest_post->topic_id);
			$user->latest_post->topic->forum     = $this->Forum_model->getForum($user->latest_post->topic->forum_id);
			$user->latest_post->topic->permalink = base_url('forum/' . $user->latest_post->topic->forum->slug . '/' . $user->latest_post->topic->slug);
		} else {
			$user->latest_post = new stdClass();
			$user->latest_post->created_at = $user->username . ' has not posted yet';
		}
		$user->latest_topic = $this->User_model->get_user_last_topic($user_id);
		if ($user->latest_topic != null) {
			$user->latest_topic->forum     = $this->Forum_model->getForum($user->latest_topic->forum_id);
			$user->latest_topic->permalink = base_url('forum/' . $user->latest_topic->forum->slug . '/' . $user->latest_topic->slug);
		} else {
			$user->latest_topic        = new stdClass();
			$user->latest_topic->title = $user->username . ' has not started a topic yet';
		}

		// create breadcrumb
		$breadcrumb  = '<ol class="breadcrumb">';
		$breadcrumb .= '<li><a href="' . base_url() . '">Home</a>-></li>';
		$breadcrumb .= '<li class="active">' . $username . '</li>';
		$breadcrumb .= '</ol>';

		// create a button to permit profile edition
		$edit_button = '<a href="' . base_url('user/' . $user->username . '/edit') . '" class="btn btn-xs btn-success">Edit your profile</a>';

		// assign created objects to the data object
		$data->user       = $user;

		$data->breadcrumb = $breadcrumb;
		if (isset($_SESSION['username']) && $_SESSION['username'] === $username) {
			// user is on his own profile
			$data->edit_button = $edit_button;
		} else {
			// user is not on his own profile
			$data->edit_button = null;
		}

		$this->load->view('header');
		$this->load->view('user/profile/profile', $data);
		$this->load->view('footer');
	}

	public function login()
	{
		if (isset($_SESSION['logged_in'])) { //verifica se tem algum login existente
			redirect(base_url());
			return;
		} else {
			$data['val_error'] = '';
			$data['error'] = '';


			$this->form_validation->set_rules('username', 'Username', 'required|alpha_numeric');
			$this->form_validation->set_rules('password', 'Password', 'required');
			if (!$this->form_validation->run()) {
				$data['val_error']=(validation_errors())?'<div class="col-md-12"><div class="alert alert-danger" role="alert">'. validation_errors().'</div></div>':'';
				$this->load->view('header');
				$this->parser->parse('user/login/login',$data);
				$this->load->view('footer');
			} else {
				$username = $this->input->post('username');
				$password = $this->input->post('password');
				if (isset($username) && isset($password)) {
					if ($this->User_model->resolveLogin($username, $password)) {
						$user_id = $this->User_model->getIdByUsername($username);
						$user = $this->User_model->getUserById($user_id);

						// guardas dados do user em variaveis de sessao
						$userData = array(
							'user_id' => $user->id,
							'username' => $user->username,
							'logged_in' => true,
							'is_confirmed' => $user->is_confirmed,
							'is_admin' => $user->is_admin,
							'fame' => $user->fame
						);

						$this->session->set_userdata($userData);
						redirect(base_url());
					}else{
						$data['error']='<div class="col-md-12">
						<div class="alert alert-danger" role="alert">Wrong username or password</div></div>';
						$this->load->view('header');
						$this->parser->parse('user/login/login',$data);
						$this->load->view('footer');
					}
				}
			}
		}
	}





	public function logout()
	{

		if ($_SESSION['logged_in'] == true) {
			session_destroy();
			redirect(base_url());
		} else {

			// nao existe nenhum login
			// redirect para home
			redirect(base_url());
		}
	}



	public function register()
	{
		$data = new stdClass();

		$this->load->helper('form');
		$this->load->library('form_validation');

		// form validation
		$this->form_validation->set_rules('username', 'Username', 'trim|required|alpha_numeric|min_length[4]|max_length[20]|is_unique[users.username]', array('is_unique' => 'This username already exists. Please choose another one.'));
		$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|is_unique[users.email]');
		$this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[6]');
		$this->form_validation->set_rules('password_confirm', 'Confirm Password', 'trim|required|min_length[6]|matches[password]');

		if ($this->form_validation->run() === false) {

			// problema com validacao
			$this->load->view('header');
			$this->load->view('user/register/register', $data);
			$this->load->view('footer');
		} else {

			// capturar variaveis
			$username = $this->input->post('username');
			$email    = $this->input->post('email');
			$password = $this->input->post('password');

			if ($this->User_model->createUser($username,  $password, $email)) {

				// user criado com sucesso
				return redirect(base_url('login'));
			} else {

				// problema a criar user
				$data->error = 'There was a problem creating your new account. Please try again.';

				$this->load->view('header');
				$this->load->view('user/register/register', $data);
				$this->load->view('footer');
			}
		}
	}








	

	/**
	 * edit function.
	 * editar o perfil do user
	 * @access public
	 * @param mixed $username (default: false)
	 * @return void
	 */
	public function edit($username = false)
	{

		if ($username === false || $username !== $this->session->userdata('username')) {
			redirect(base_url());
			return;
		}
		$data = new stdClass();

		// load form helper and form validation library
		$this->load->helper('form');
		$this->load->library('form_validation');



		// form validation
		$password_required_if = $this->input->post('password') ? '|required' : ''; // se alguma coisa estar escrito no input password exige a passe atual 
		$this->form_validation->set_rules('username', 'Username', 'trim|min_length[4]|max_length[20]|alpha_numeric|is_unique[users.username]', array('is_unique' => 'This username already exists. Please choose another username.'));
		$this->form_validation->set_rules('email', 'Email', 'trim|valid_email|is_unique[users.email]', array('is_unique' => 'The email you entered already exists in our database.'));
		$this->form_validation->set_rules('current_password', 'Current Password', 'trim' . $password_required_if . '|callback_verify_current_password');
		$this->form_validation->set_rules('password', 'New Password', 'trim|min_length[6]|matches[password_confirm]');
		$this->form_validation->set_rules('password_confirm', 'Password Confirmation', 'trim|min_length[6]');

		// get the user object
		$user_id = $this->User_model->getIdByUsername($username);
		$user    = $this->User_model->getUserById($user_id);

		// create breadcrumb
		$breadcrumb  = '<ol class="breadcrumb">';
		$breadcrumb .= '<li><a href="' . base_url() . '">Home</a>-></li>';
		$breadcrumb .= '<li><a href="' . base_url('user/' . $username) . '">' . $username . '</a>-></li>';
		$breadcrumb .= '<li class="active">Edit</li>';
		$breadcrumb .= '</ol>';

		// assign objects to the data object
		$data->user       = $user;
		$data->breadcrumb = $breadcrumb;

		if ($this->form_validation->run() == false) {

			// validation not ok, send validation errors to the view
			$this->load->view('header');
			$this->load->view('user/profile/edit', $data);
			$this->load->view('footer');
		} else {

			$user_id = $_SESSION['user_id'];
			$update_data = [];

			if ($this->input->post('username') != '')
				$update_data['username'] = $this->input->post('username');

			if ($this->input->post('email') != '')
				$update_data['email'] = $this->input->post('email');

			if ($this->input->post('password') != '')
				$update_data['password'] = $this->input->post('password');


			// avatar upload
			if (isset($_FILES['userfile']['name']) && !empty($_FILES['userfile']['name'])) {
				// setup upload configuration and load upload library
				$config['upload_path']      = './uploads/avatars/';
				$config['allowed_types']    = 'gif|jpg|png';
				$config['max_size']         = 8048;
				$config['max_width']        = 1024;
				$config['max_height']       = 1024;
				$config['file_name']		= $this->session->userdata('user_id') . '-avatar' . time();
				$config['file_ext_tolower'] = true;

				$this->load->library('upload');
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('userfile')) {

					$error = array('error' => $this->upload->display_errors());
				} else {

					$update_data['avatar'] = $this->upload->data('file_name');
				}
			}

			if ($this->User_model->updateUser($user_id, $update_data)) {
				if (isset($update_data['username'])) {
					$_SESSION['username'] = $update_data['username'];
					if ($this->input->post('username') != '') {
						$_SESSION['flash']    = 'Your profile has been successfully updated!';
					}
				}

				if (isset($update_data['avatar'])) {
					$data->user->avatar = $update_data['avatar'];
				}

				if ($this->input->post('username') != '') {

					redirect(base_url('user/' . $update_data['username'] . '/edit'));
				} else {

					$data->success = 'Your profile has been successfully updated!';
					$this->load->view('header');
					$this->load->view('user/profile/edit', $data);
					$this->load->view('footer');
				}
			} else {

				$data->error = 'There was a problem updating your account. Please try again.';
				$this->load->view('header');
				$this->load->view('user/profile/edit', $data);
				$this->load->view('footer');
			}
		}
	}

	/**
	 * delete function.
	 *
	 * @access public
	 * @param mixed $username (default: false)
	 * @return void
	 */
	public function delete($username = false)
	{


		if ($username == false || !isset($_SESSION['username']) || $username !== $_SESSION['username']) {
			redirect(base_url());
			return;
		}


		$data = new stdClass();

		if ($this->session->userdata('username') === $username) {


			$breadcrumb  = '<ol class="breadcrumb">';
			$breadcrumb .= '<li><a href="' . base_url() . '">Home</a>-></li>';
			$breadcrumb .= '<li><a href="' . base_url('user/' . $username) . '">' . $username . '</a>-></li>';
			$breadcrumb .= '<li class="active">Delete</li>';
			$breadcrumb .= '</ol>';

			$user_id          = $this->User_model->getIdByUsername($username);
			$data->user       = $this->User_model->getUserById($user_id);
			$data->breadcrumb = $breadcrumb;

			if ($this->User_model->deleteUser($user_id)) {

				$data->success = 'Your user account has been successfully deleted. Bye bye :(';


				$this->load->view('header');
				$this->load->view('user/profile/delete', $data);
				$this->load->view('footer');
			} else {


				$data->error = 'There was a problem deleting your user account. Please contact an administrator.';


				$this->load->view('header');
				$this->load->view('user/profile/edit', $data);
				$this->load->view('footer');
			}
		} else {


			redirect(base_url());
			return;
		}
	}

	/**
	 * verify_current_password function.
	 *
	 * @access public
	 * @param string $str
	 * @return bool
	 */
	public function verify_current_password($str)
	{

		if ($str != '') {

			if ($this->User_model->resolveLogin($_SESSION['username'], $str) === true) {
				return true;
			}
			$this->form_validation->set_message('verify_current_password', 'The {field} field does not match your password.');
			return false;
		}
		return true;
	}




	public function voteUp()
	{
		if ($this->session->userdata('logged_in') == true) {
			$idUserVoting = $this->session->userdata('user_id');
			$idUserVoted = $this->uri->segment(2);
			$this->User_model->addFame($idUserVoted, $idUserVoting);
			redirect(base_url('user/' . $this->User_model->getUsernameById($idUserVoted)));
			return;
		} else {
			redirect(base_url('login'));
			return;
		}
	}

	public function feed(){
		if ($this->session->userdata('logged_in') == true) {
		$data['breadcrumb']  = '<ol class="breadcrumb">';
		$data['breadcrumb']  .= '<li><a href="' . base_url() . '">Home</a>-></li>';
		$data['breadcrumb']  .= '<li class="active">Feed</li>';
		$data['breadcrumb']  .= '</ol>';
			$notification= $this->User_model->feed();
			$data['nots']='';
			foreach($notification as $not){
				$data['nots'].='<hr><div class="col-sm-12"><h3>'.$not->tipo_notificacao.'<a style="margin-left:1em" href="'.base_url('UserController/delFeed/'.$not->id).'"><img src="'.base_url('uploads/icons/remove.png').'"  height="15px" ></a></h3>
				<p>'.$not->msg.'</p>
				<span><small> Received at:'.$not->sent_at.'</small></span>
				</div><hr>';
			}
			$this->load->view('header');
			$this->parser->parse('user/feed/feed.php',$data);
			$this->load->view('footer');
		} else {
			redirect(base_url('login'));
			return;
		}
	}

	public function delFeed($id){
		$feed = $this->User_model->feedById($id);
		if ($this->session->userdata('logged_in') == true || $feed->user_id == $this->session->userdata('user_id')) {
			$this->User_model->deleteFeed($id);
			redirect(base_url('feed'));
			return;
		} else {
			redirect(base_url());
			return;
		}
	}
}
