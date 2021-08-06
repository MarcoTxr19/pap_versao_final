<?php
class AdminController extends CI_Controller
{

	public function __construct()
	{

		parent::__construct();
		$this->load->model('Forum_model');
		$this->load->model('User_model');
		$this->load->model('Admin_model');
		$this->load->model('Contacto_model');
	}

	public function index()
	{

		// if the user is not admin, redirect to base url
		if (!$this->session->userdata('is_admin')) {
			redirect(base_url());
			return;
		}
		$data = new stdClass();

		$this->load->view('header');
		$this->load->view('admin/home/index', $data);
		$this->load->view('footer');
	}
	public function contactos()
	{
		if (!$this->session->userdata('is_admin')) {
			redirect(base_url());
			return;
		}



		$contactos = $this->Contacto_model->getAllContacts();

		$data['contactos'] = '';


		foreach ($contactos as $supportMsg) {
			$data['contactos'] .= '<tr>';
			$data['contactos'] .= '<td> <p>' . $supportMsg->id . '</p> </td>';
			$data['contactos'] .= '<td> <p>' . $supportMsg->tipo . '</p></td>';
			$data['contactos'] .= '<td> <p>' . $supportMsg->msg . '</p></td>';
			$data['contactos'] .= '<td> <p>' . $supportMsg->idUser . '</p></td>';

			if ($supportMsg->tipo == "TopicReport")
				$data['contactos'] .= '<td> <a class="btn btn-xs btn-primary" href="' . base_url('admin/preview/topic/' . $supportMsg->idTopic . '/' . $supportMsg->idUser) . '">Details</a></td>';
			elseif ($supportMsg->tipo == "ForumReport")
				$data['contactos'] .= '<td><a class="btn btn-xs btn-primary" href="' . base_url('admin/preview/forum/' . $supportMsg->idForum . '/' . $supportMsg->idUser) . '">Details</a></td>';
			elseif ($supportMsg->tipo == "PostReport")
				$data['contactos'] .= '<td><a class="btn btn-xs btn-primary" href="' . base_url('admin/preview/post/' . $supportMsg->idPost . '/' . $supportMsg->idUser) . '">Details</a></td>';
			else
				$data['contactos'] .= '<td><a class="btn btn-xs btn-primary" href="' . base_url('admin/answer/' . $supportMsg->id) . '">Details</a>';
			$data['contactos'] .= '<a class="btn btn-xs btn-danger" href="' . base_url('AdminController/delCont/' . $supportMsg->id) . '">Delete</a></td>';
			$data['contactos'] .= '</tr>';
		}

		$data['preview'] = $this->preview();


		$this->load->view('header');
		$this->parser->parse('admin/contactos/lista_contacto', $data);
		$this->load->view('footer');
	}

	public function delCont($id){
		if($this->session->userdata('is_admin')==true){
			$this->Contacto_model->contDelete($id);
			redirect(base_url('admin/contactos'));
			return;
		}else{
			redirect(base_url());
			return;
		}
		
	}

	public function answer()
	{
		if (!$this->session->userdata('is_admin')) {
			redirect(base_url());
			return;
		}
		if (!$this->uri->segment(2) == "answer" || !$this->uri->segment(3)) {
			redirect(base_url());
			return;
		}
		$data['ok']='';
		$data['info'] = '';
		$data['val_error']='';
		$contact = $this->Contacto_model->getContact($this->uri->segment(3));
		foreach ($contact as $question) {
			$id=$question->idUser;
			$data['info'] .= "<div id='question-'" . $question->id . "> # : " . $question->id;
			$data['info'] .= "<br/> Type : " . $question->tipo;
			$data['info'] .= "<br/>Content : " . $question->msg;
			$data['info'] .= "<br/>Sent by: " . $question->idUser . " - <a href='" . base_url('user/' . $this->User_model->getUsernameById($question->idUser)) . "'>" . $this->User_model->getUsernameById($question->idUser) . "</a></div>";
		}
		$this->form_validation->set_rules('replysupport', 'Support Reply', 'required|min_length[15]');
		if (!$this->form_validation->run()) {
			if(validation_errors()){
				$data['val_error']='<div class="col-md-12">
				<div class="alert alert-danger" role="alert">'. validation_errors().'</div></div>';
			}
			$this->load->view('header');
			$this->parser->parse('admin/contactos/reply', $data);
			$this->load->view('footer');
		}else{
			if($this->Contacto_model->sendNotificationToUser($id,'Support Reply',$this->input->post('replysupport'))){
				$data['val_error']='<div class="col-md-12">
				<div class="alert alert-success" role="alert"> Your message was successfully sent to user!<a href="'.base_url('admin').'">Back to Admin Interface</a></div></div>';
				$this->load->view('header');
				$this->parser->parse('admin/contactos/reply', $data);
				$this->load->view('footer');
			}
			
		}
	}

	public function preview()
	{
		$data['preview'] = '';


		// Crregar Dados Topico
		if ($this->uri->segment(2) == "preview" && $this->uri->segment(3) == "topic") {
			$topico = $this->Forum_model->getTopic($this->uri->segment(4));
			$data['preview'] .= '<p> ID Topico => ' . $topico->id . '</p>';
			$data['preview'] .= '<p> Title => ' . $topico->title . '</p>';
			$data['preview'] .= '<p> Created at => ' . $topico->created_at . '</p>';

			$data['preview'] .= '<p> Created by => ' . $this->listarDadosUserPreview($topico->user_id) . '</p>';

			$data['preview'] .= '<p> Forum  => ' . $this->listarDadosForumPreview($topico->forum_id) . ' </p>';
			$data['preview'] .= '<p> Report sent by => ' . $this->listarDadosUserPreview($this->uri->segment(5)) . '</p>';
		}

		//Carregar dados  Post
		if ($this->uri->segment(2) == "preview" && $this->uri->segment(3) == "post") {
			$post = $this->Forum_model->getPostById($this->uri->segment(4));
			$data['preview'] .= '<p> ID Post => ' . $post->id . '</p>';
			$data['preview'] .= '<p>  Content => ' . $post->content . '</p>';



			$data['preview'] .= '<p> Created by User => ' . $this->listarDadosUserPreview($post->user_id) . '</p>';


			$topico =  $this->Forum_model->getTopic($post->topic_id);
			$data['preview'] .= '<p> Topic ID => ' . $topico->id . '| Topic Title: ' . $topico->title . ' | Created at: ' . $topico->created_at . ' | Created By: ' . $topico->user_id . ' </p>';
			$data['preview'] .= '<p> <bold> Created at </bold> => ' . $post->created_at . '</p>';
		}


		//Carregar dados  Forum
		if ($this->uri->segment(2) == "preview" && $this->uri->segment(3) == "forum") {
			$forum = $this->Forum_model->getForum($this->uri->segment(4));
			$data['preview'] .= '<p> ID Forum => ' . $forum->id . '</p>';
			$data['preview'] .= '<p> Title => ' . $forum->title . '</p>';
			$data['preview'] .= '<p> Description => ' . $forum->description . '</p>';
			$data['preview'] .= '<p> Created At => ' . $forum->created_at . '</p>';
			$data['preview'] .= '<p> Forum created by => ' . $this->listarDadosUserPreview($forum->id_user) . '</p>';
			$data['preview'] .= '<p> Report sent by => ' . $this->listarDadosUserPreview($this->uri->segment(5)) . '</p>';
		}
		return $data['preview'];
	}



	public function listarDadosUserPreview($id)
	{
		$user = $this->User_model->getUserById($id);
		$data['preview'] = '   ID: ' . $user->id  . ' || Username: ' . $user->username . '  || Email:  ' . $user->email . ' || Joined at: ' . $user->created_at . '   || Reputation: ' . $user->fame . '   || Is Confirmed: ' . $user->is_confirmed;
		return $data['preview'];
	}


	public function listarDadosForumPreview($id)
	{
		$forum = $this->Forum_model->getForum($id);
		$data['preview'] = '  ID: ' . $forum->id  . '|| Title : ' . $forum->title . ' || Description:  ' . $forum->description . ' || Created at: ' . $forum->created_at . ' || Created By: ' . $forum->id_user . ' - ' . $this->User_model->getUsernameById($forum->id_user);
		return $data['preview'];
	}






	public function deleteUser($username = false)
	{
		if (!$this->session->userdata('is_admin')) {
			redirect(base_url());
			return;
		}
		if ($username === false) {
			redirect(base_url('admin/users'));
			return;
		}
		$user_id = $this->User_model->getIdByUsername($username);
		$this->User_model->deleteUser($user_id);
		redirect(base_url('admin/users'));
		return;
	}




	public function users()
	{
		if (!$this->session->userdata('is_admin')) {
			redirect(base_url());
			return;
		}
		$data = new stdClass();
		$data->users = $this->User_model->getAllUsers();
		$this->load->view('header');
		$this->load->view('admin/users/users', $data);
		$this->load->view('footer');
	}

	public function editUser($username = false)
	{

		if (!$this->session->userdata('is_admin')) {
			redirect(base_url());
			return;
		}
		if ($username === false) {
			redirect(base_url('admin/users'));
			return;
		}

		$data = new stdClass();

		// create the user object
		$user_id = $this->User_model->getIdByUsername($username);
		$user    = $this->User_model->getUserById($user_id);

		// set options
		if ($user->is_admin === '1') {
			$options  = '<option value="administrator" selected>Administrator</option>';
			$options .= '<option value="user">User</option>';
		} else {
			$options  = '<option value="administrator">Administrator</option>';
			$options .= '<option value="user" selected>User</option>';
		}

		// assign values to the data object
		$data->user    = $user;
		$data->options = $options;
		if ($user->updated_by !== null) {
			$data->user->updated_by = $this->User_model->getUsernameById($user->updated_by);
		}
		$this->form_validation->set_rules('user_rights', 'User Rights', 'required|in_list[administrator,moderator,user]', array('in_list' => 'Don\'t try to hack the form!'));

		if ($this->form_validation->run() == false) {

			$this->load->view('header');
			$this->load->view('admin/users/edit_user', $data);
			$this->load->view('footer');
		} else {

			// assign rights to variables
			if ($this->input->post('user_rights') === 'administrator') {
				$is_admin     = '1';
				$is_moderator = '0';
			} elseif ($this->input->post('user_rights') === 'moderator') {
				$is_admin     = '0';
				$is_moderator = '1';
			} else {
				$is_admin     = '0';
				$is_moderator = '0';
			}

			if ($this->Admin_model->update_user_rights($user_id, $is_admin, $is_moderator)) {
				// update user success
				$data->success = $user->username . ' has successfully been updated.';
			} else {
				// error while updating user rights, this should never happen
				$data->error = 'There was an error while trying to update this user. Please try again';
			}

			$this->load->view('header');
			$this->load->view('admin/users/edit_user', $data);
			$this->load->view('footer');
		}
	}




	public function forums_and_topics()
	{
		if (!isset($_SESSION['is_admin'])) {
			redirect(base_url());
			return;
		}



		$forums = $this->Forum_model->getForums();
		$data['listForum'] = $this->listForums($forums);
		$data['topic'] = $this->previewTopic();
		$data['post'] = $this->previewPosts();
		$this->load->view('header');
		$this->parser->parse('admin/forums_and_topics/forums_and_topics', $data);
		$this->load->view('footer');
	}


	public function listForums($forums)
	{
		$data['forums'] = '';
		foreach ($forums as $key) {
			$data['forums'] .= '<tr>';
			$data['forums'] .= '<td><p>' . $key->id . '</p></td>';
			$data['forums'] .= '<td><p>' . $key->title . '</p></td>';
			$data['forums'] .= '<td><p>' . $key->slug . '</p></td>';
			$data['forums'] .= '<td><p>' . $key->description . '</p></td>';
			$data['forums'] .= '<td><p>' . $key->created_at . '</p></td>';
			$data['forums'] .= '<td><p> # ' . $key->id_user . '  -  <a href=' . base_url('user/' . $this->User_model->getUsernameById($key->id_user)) . '>' . $this->User_model->getUsernameById($key->id_user) . '</a></p></td>';
			$data['forums'] .= '<td><p>';
			$forumTags = json_decode($key->tags);
			foreach ($forumTags as $tags) {
				$data['forums'] .= $tags . ' - ';
			}
			$data['forums'] .= '</td></p>';
			$data['forums'] .= '<td><p><a class="btn btn-xs btn-primary" href=' . base_url('admin/preview/forum_topics/' . $key->id) . '>View Topics</a><a class="btn btn-xs btn-danger" href=' . base_url('admin/delete/forum/' . $key->id) . '>Delete</a></p></td>';
		}

		return $data['forums'];
	}



	public function previewTopic()
	{
		$data['topic'] = '';
		if ($_SESSION['is_admin'] == false) {
			redirect(base_url());
			return;
		}
		if (!$this->uri->segment(4) || !$this->uri->segment(2) == 'preview')
			return;
		$topics = $this->Forum_model->getForumTopics($this->uri->segment(4));
		return $this->createTableTopics($topics);;
	}

	public function createTableTopics($topics)
	{
		$data['topic'] = '';
		$data['topic'] .= '<h3>Topic</h3><table class="table table-striped"><thead>
		<tr><th>#</th><th>Title</th><th>Create at</th><th>Updated at</th><th>Created by</th><th>Actions</th></tr></thead>';
		foreach ($topics as $topic) {
			$data['topic'] .= '<tr><td><p>' . $topic->id . '</p></td>';
			$data['topic'] .= '<td><p>' . $topic->title . '</p></td>';
			$data['topic'] .= '<td><p>' . $topic->created_at . '</p></td>';
			$data['topic'] .= '<td><p>' . $topic->updated_at . '</p></td>';
			$data['topic'] .= '<td><p> # ' . $topic->user_id . '<a href="' . base_url('user/' . $this->User_model->getUsernameById($topic->user_id)) . '"> - ' . $this->User_model->getUsernameById($topic->user_id) . '</p></td>';
			$data['topic'] .= '<td><p><a class="btn btn-xs btn-primary" href=' . base_url('admin/preview/forum_topics/' . $this->uri->segment(4) . '/posts/' . $topic->id) . '>View Posts</a></p>
			<a class="btn btn-xs btn-danger" href=' . base_url('admin/delete/topic/' . $topic->id) . '>Delete</a></p></td>';
		}
		return $data['topic'] .= '</table>';
	}






	public function previewPosts()
	{
		$data['post'] = '';
		if ($_SESSION['is_admin'] == false) {
			redirect(base_url());
			return;
		}
		if (!$this->uri->segment(6) || !$this->uri->segment(2) == "preview")
			return;
		$posts = $this->Forum_model->getPosts($this->uri->segment(6));
		return $this->createTablePosts($posts);
	}





	public function createTablePosts($posts)
	{
		$data['post'] = '';
		$data['post'] .= '<h3>Posts</h3><table class="table table-striped"><thead>
		<tr><th>#</th><th>Content</th><th>Posted by</th><th>Created by</th><th>Actions</th></tr></thead>';
		foreach ($posts as $post) {
			$data['post'] .= '<tr><td><p>' . $post->id . '</p></td>';
			$data['post'] .= '<td><p>' . $post->content . '</p></td>';
			$data['post'] .= '<td><p> # ' . $post->user_id . '<a href="' . base_url('user/' . $this->User_model->getUsernameById($post->user_id)) . '"> - ' . $this->User_model->getUsernameById($post->user_id) . '</a></p></td>';
			$data['post'] .= '<td><p>' . $post->created_at . '</p></td>';
			$data['post'] .= '<td><p><a class="btn btn-xs btn-danger" href=' . base_url('admin/delete/post/' . $post->id) . '>Delete</a></p></td>';
		}
		return $data['post'] .= '</table>';
	}




	public function deleteForum($forum = false)
	{
		if ($forum == false || $this->session->userdata('is_admin') == false) {
			redirect(base_url());
			return;
		}
		$this->Forum_model->deleteForum($forum);
		redirect(base_url('admin/forums_and_topics'));
		return;
	}


	public function deleteTopic($topic = false)
	{
		if ($topic == false || $this->session->userdata('is_admin') == false) {
			redirect(base_url());
			return;
		}
		$this->Forum_model->deleteTopic($topic);
		redirect(base_url('admin/forums_and_topics'));
		return;
	}

	public function deletePost($post = false)
	{
		if ($post == false || $this->session->userdata('is_admin') == false) {
			redirect(base_url());
			return;
		}
		$this->Forum_model->deletePost($post);
		redirect(base_url('admin/forums_and_topics'));
		return;
	}
}
