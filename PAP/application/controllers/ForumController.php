<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Forum class.
 * 
 * @extends CI_Controller
 */
class ForumController extends CI_Controller
{

	/**
	 * __construct function.
	 * 
	 * @access public
	 * @return void
	 */
	public function __construct()
	{

		parent::__construct();
		$this->load->library(array('session'));
		$this->load->helper(array('url'));
		$this->load->model('forum_model');
		$this->load->model('user_model');
	}






















	/**
	 * index function.
	 * 
	 * @access public
	 * @param mixed $slug (default: false)
	 * @return void
	 */
	public function index($slug = false)
	{

		// create the data object
		$data = new stdClass();

		/*
		$dados= array(2,4,5,6,1,23,45,76,10);
		var_dump($dados);
		var_dump(json_encode($dados));
		$jsondados=json_encode($dados);
		var_dump($jsondados);
		$dadosadd= json_decode($jsondados);
		$dadosadd[count($dadosadd)] = 44;
		var_dump($dadosadd);
	*/
		if ($slug === false) {

			// create objects
			$forums = $this->forum_model->getForums();

			foreach ($forums as $forum) {

				$forum->permalink    = base_url($forum->slug);
				$forum->topics       = $this->forum_model->getForumTopics($forum->id);
				$forum->count_topics = count($forum->topics);
				$forum->count_posts  = $this->forum_model->countForumPosts($forum->id);

				if ($forum->count_topics > 0) {

					// $forum has topics
					$forum->latest_topic            = $this->forum_model->getForumLatestTopic($forum->id);
					$forum->latest_topic->permalink = $forum->slug . '/' . $forum->latest_topic->slug;
					$forum->latest_topic->author    = $this->user_model->getUsernameById($forum->latest_topic->user_id);
				} else {

					// $forum doesn't have topics yet
					$forum->latest_topic = new stdClass();
					$forum->latest_topic->permalink = null;
					$forum->latest_topic->title = null;
					$forum->latest_topic->author = null;
					$forum->latest_topic->created_at = null;
				}
			}

			// create breadcrumb
			$breadcrumb  = '<ol class="breadcrumb">';
			$breadcrumb .= '<li class="active">Home</li>';
			$breadcrumb .= '</ol>';

			// assign created objects to the data object
			$data->forums     = $forums;
			$data->breadcrumb = $breadcrumb;

			// load views and send data
			$this->load->view('header');
			$this->load->view('forum/index', $data);
			$this->load->view('footer');
		} else {

			// get id from slug
			$forum_id = $this->forum_model->getForumIdFromForumSlug($slug);

			// create objects
			$forum    = $this->forum_model->getForum($forum_id);
			$topics   = $this->forum_model->getForumTopics($forum_id);

			// create breadcrumb
			$breadcrumb  = '<ol class="breadcrumb">';
			$breadcrumb .= '<li><a href="' . base_url() . '">Home</a>-></li>';
			$breadcrumb .= '<li class="active">' . $forum->title . '</li>';
			$breadcrumb .= '</ol>';

			foreach ($topics as $topic) {

				$topic->author                  = $this->user_model->getUsernameById($topic->user_id);
				$topic->permalink               = $slug . '/' . $topic->slug;
				$topic->posts                   = $this->forum_model->getPosts($topic->id);
				$topic->count_posts             = count($topic->posts);
				$topic->latest_post             = $this->forum_model->getTopicLatestPost($topic->id);
				$topic->latest_post->author     = $this->user_model->getUsernameById($topic->latest_post->user_id);
			}

			// assign created objects to the data object
			$data->forum      = $forum;
			$data->topics     = $topics;
			$data->breadcrumb = $breadcrumb;

			// load views and send data
			$this->load->view('header');
			$this->load->view('forum/single', $data);
			$this->load->view('footer');
		}
	}

	/**
	 * create function.
	 * 
	 * @access public
	 * @return void
	 */
	public function createForum()
	{


		$data = new stdClass();

		//criar forum obriga a estar confirmado e a ter 25 de fame
		if ($this->session->userdata('fame') > 25)
			$data->criarForum = true;
		else
			$data->criarForum = false;


		// create breadcrumb
		$breadcrumb  = '<ol class="breadcrumb">';
		$breadcrumb .= '<li><a href="' . base_url() . '">Home</a>-></li>';
		$breadcrumb .= '<li class="active">Create a new forum</li>';
		$breadcrumb .= '</ol>';

		$data->breadcrumb = $breadcrumb;


		$this->load->helper('form');
		$this->load->library('form_validation');

		// form validation
		$this->form_validation->set_rules('title', 'Forum Title', 'trim|required|alpha_numeric_spaces|min_length[3]|max_length[255]|is_unique[forums.title]', array('is_unique' => 'The forum title you entered already exists. Please choose another forum title.'));
		$this->form_validation->set_rules('description', 'Description', 'trim|alpha_numeric_spaces|max_length[80]');

		if ($this->form_validation->run() === false) {
			//algo correu mal na validacao
			// manter o que o user escreveu nos campos
			$data->title       = $this->input->post('title');
			$data->description = $this->input->post('description');
			$data->tags        = $this->input->post('tags');

			$this->load->view('header');
			$this->load->view('forum/create/create', $data);
			$this->load->view('footer');
		} else {

			//capturar variaveis dos form,
			$title       = $this->input->post('title');
			$description = $this->input->post('description');
			$tags = $this->input->post('tags');

			if ($this->forum_model->createForum($title, $description, $this->session->userdata('user_id'), str_replace(' ', '', $tags))) {

				// forum creation success
				$this->load->view('header');
				$this->load->view('forum/create/create_success', $data);
				$this->load->view('footer');
			} else {

				// forum creation failed
				$data->error = 'There was a problem creating the new forum. Please try again.';

				$this->load->view('header');
				$this->load->view('forum/create/create', $data);
				$this->load->view('footer');
			}
		}
	}

	public function report($tipo,$id)
	{
		if ($this->session->userdata('logged_in') == false) {
			redirect(base_url('login'));
			return;
		}
		$this->load->model('Contacto_model');
		if($this->Contacto_model->addReport($tipo,$id)){
				$this->load->view('header');
				$this->load->view('forum/success_report');
				$this->load->view('footer');
		}
	}



	public function delete($tipo,$id)
	{
		if ($this->session->userdata('logged_in') == false) {
			redirect(base_url('login'));
			return;
		}
		if($tipo=='forum'){
			$forum = $this->forum_model->getForum($id);
			if($forum)
			if($forum->id_user==$this->session->userdata('user_id') || $this->session->userdata('is_admin')==true){
				$this->forum_model->deleteForum($forum->id);
				redirect(base_url());
				return;
			}
				
		}	
		if($tipo=='topic'){
			$topic = $this->forum_model->getTopic($id);
			if($topic){
				$forumslug=$this->forum_model->getForum($topic->forum_id)->slug;
				if($topic->user_id==$this->session->userdata('user_id') || $this->session->userdata('is_admin')==true){
					$this->forum_model->deleteTopic($topic->id);
					redirect(base_url('forum/'.$forumslug));
					return;
			}
			
			}
		}
		if($tipo=='post'){
			$post = $this->forum_model->getPostById($id);

			if($post){

				$topic=$this->forum_model->getTopic($post->topic_id);
				$forumslug=$this->forum_model->getForum($topic->forum_id)->slug;
				if($post->user_id==$this->session->userdata('user_id') || $this->session->userdata('is_admin')==true){
			
					$this->forum_model->deletePost($post->id);
					redirect(base_url('forum/'.$forumslug.'/'.$topic->slug));
					return;
				}
			}
			
		}
	}

	/*
	 * createTopic function.
	 * 
	 * @access public
	 * @param string $forum_slug
	 * @return void
	 */
	public function createTopic($forum_slug)
	{


		$data = new stdClass();

		// obriga login para poder criar um topico
		if ($this->session->userdata('logged_in') !== true) {
			$data->login_needed = true;
		} else {
			$data->login_needed = false;
		}

		// capturar variaveis pelo url
		$forum_slug = $this->uri->segment(1);
		$forum_id   = $this->forum_model->getForumIdFromForumSlug($forum_slug);
		$forum      = $this->forum_model->getForum($forum_id);

		// criar breadcrumb
		$breadcrumb  = '<ol class="breadcrumb">';
		$breadcrumb .= '<li><a href="' . base_url() . '">Home</a>-></li>';
		$breadcrumb .= '<li><a href="' . base_url('forum/'.$forum->slug) . '">' . $forum->title . '</a>-></li>';
		$breadcrumb .= '<li class="active">Create a new topic</li>';
		$breadcrumb .= '</ol>';

		//guardar o breadcrumb para passar para a view
		$data->breadcrumb = $breadcrumb;


		$this->load->helper('form');
		$this->load->library('form_validation');

		//form validation
		$this->form_validation->set_rules('title', 'Topic Title', 'trim|required|alpha_numeric_spaces|min_length[3]|max_length[255]|is_unique[topics.title]', array('is_unique' => 'The topic title you entered already exists in our database. Please enter another topic title.'));
		$this->form_validation->set_rules('content', 'Content', 'required|min_length[3]');

		if ($this->form_validation->run() === false) {
			//erro na validacao
			// manter o que o user escreveu
			$data->title   = $this->input->post('title');
			$data->content = $this->input->post('content');

			// retornar para a view
			$this->load->view('header');
			$this->load->view('topic/create/create', $data);
			$this->load->view('footer');

		} else {

			// capturar variaveis
			$title   = $this->input->post('title');
			$content = $this->input->post('content');
			$img_content=false;
			$user_id = $_SESSION['user_id'];

			if (isset($_FILES['contentimg']['name']) && !empty($_FILES['contentimg']['name'])) {
				// setup upload configuration and load upload library
				$config['upload_path']      = './uploads/topiccontent/';
				$config['allowed_types']    = 'gif|jpg|png';
				$config['max_size']         = 16048;
				$config['max_width']        = 4024;
				$config['max_height']       = 4024;
				$config['file_name']		= $this->session->userdata('user_id').'-posts-'.time();
				$config['file_ext_tolower'] = true;

				$this->load->library('upload');
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('contentimg')) {
				
					$error = array('error' => $this->upload->display_errors());
					var_dump($error);
				} else {
					$img_content = $this->upload->data('file_name');
				}
			}
			
					
			if ($this->forum_model->createTopic($forum_id, $title, $content, $user_id,$img_content)) {

				// topic creation ok
				redirect(base_url('forum/' . $forum_slug . '/' . strtolower(url_title($title))));
			} else {

				// topic creation failed
				$data->error = 'There was a problem creating your new topic. Please try again.';


				$this->load->view('header');
				$this->load->view('topic/create/create', $data);
				$this->load->view('footer');
			}
		}
	}

	/**
	 * topic function.
	 * lista todos os posts dentro do topic
	 * @access public
	 * @param string $forum_slug
	 * @param string $topic_slug
	 * @return void
	 */
	public function topic($forum_slug, $topic_slug)
	{


		$data['posts'] = '';
		// get ids from slugs
		$forum_id = $this->forum_model->getForumIdFromForumSlug($forum_slug);
		$topic_id = $this->forum_model->getTopicIdFromTopicSlug($topic_slug);

		// create objects
		$forum = $this->forum_model->getForum($forum_id);
		$topic = $this->forum_model->getTopic($topic_id);
		$posts = $this->forum_model->getPosts($topic_id);
		$this->load->model('User_model');

		foreach ($posts as $post) {
			$user=$this->User_model->getUserById($post->user_id);
			if ($post->replying_to == null) {
				$data['posts'] .= "<div class='col-md-12' style='margin-left: 1em' id='post-'" . $post->id . "><header class='post-header'><img src='".base_url('uploads/avatars/'.$user->avatar)."' width='50px' style='margin:5px; border: 1px solid black' ><small><a href=" . base_url('user/' . $this->user_model->getUsernameById($post->user_id)) . ">" . $this->user_model->getUsernameById($post->user_id) . "</a> " . $post->created_at . "</small></header>";
				$data['posts'] .= "<div class='post-content'>" . $post->content . "</div>";
				if($post->img!=null){
					$data['posts'] .="<img style='margin-left:3em; max-width: 500px; height: auto;' class='img-fluid'  src='".base_url('uploads/topiccontent/'.$post->img )."' ><br>";
				}
				
				$data['posts'] .="<a class='btn btn-default' href=" . base_url('forum/' . $forum->slug . '/' . $topic->slug . '/reply/' . $post->id) . "> <button class='btn btn-info'>Reply</button></a><hr ></div>";

			} else {

				$data['posts'] .= "<div class='col-md-12' style=' margin:1em; border-left: 1px solid 	#cccccc;' id='post-" . $post->id . "'><header class='post-header'><img src='".base_url('uploads/avatars/'.$user->avatar)."' width='60px' height='60px' style='margin:5px; border: 1px solid black' ><small><a href=" . base_url('user/' . $this->user_model->getUsernameById($post->user_id)) . ">" . $this->user_model->getUsernameById($post->user_id) . "</a> " . $post->created_at . "</small>";


				//Delete
				if ($post->user_id == $this->session->userdata('user_id')) {
					$data['posts'] .= "<a href='".base_url('forum/delete/post/'.$post->id)."'><img style='margin-left: 1em;' src='" . base_url('uploads/icons/remove.png') . "' alt='delete' width='15px' height='15px'></a>";
				}
				//Report Icon

				$data['posts'] .= "<a href='".base_url('report/post/'.$post->id)."'><img style='margin-left: 1em;' src='" . base_url('uploads/icons/report.png') . "' alt='delete' width='15px' height='15px'></a></header><div class='post-content' style='margin-left: 1em;'>";

				//Post apagado?
				if ($post->deleted == 1) {
					$data['posts'] .= "<span ><strong>THIS POST WAS DELETED BY </strong></span><a href='" . base_url('user/' . $this->user_model->getUsernameById($post->updated_by)) . "'>" . $this->user_model->getUsernameById($post->updated_by) . "</a></div>
						</div>";
				} else {
					$data['posts'] .= $post->content .
						"</div>
						<a style='margin-felt:1em'  class='btn btn-default' href=" . base_url('forum/' . $forum->slug . '/' . $topic->slug . '/reply/' . $post->id) . "><button class='btn btn-info'>Reply</button></a>
						</div>";
					
				}
				$data['posts'] .= '<script>$("#post-' . $post->id . '").appendTo("#post-' . $post->replying_to . '")</script>';
			}
		}

		$data['posts'] .= "<div/>";
		// create breadcrumb
		$data['breadcrumb']  = '<ol class="breadcrumb">';
		$data['breadcrumb'] .= '<li><a href="' . base_url() . '">Home</a>-></li>';
		$data['breadcrumb'] .= '<li><a href="' . base_url('forum/' . $forum->slug) . '">' . $forum->title . '</a>-></li>';
		$data['breadcrumb'] .= '<li class="active">' . $topic->title . '</li>';
		$data['breadcrumb'] .= '</ol>';

	


		// load views and send data
		$this->load->view('header');
		$this->parser->parse('topic/single', $data);
		$this->load->view('footer');
	}

	/**
	 * createPost function.
	 * 
	 * @access public
	 * @param string $forum_slug
	 * @param string $topic_slug
	 * @return void
	 */
	public function createPost($forum_slug, $topic_slug)
	{

		// create the data object
		$data = [];

		// if the user is not logged in, he cannot reply to a topic
		if (!$this->session->userdata('logged_in')) {
			redirect(base_url('login'));
			return;
			
			
		}else{
		$data['login_needed'] = '';
		$data['error']='';
		$data['val_error']='';
		$data['posts']='';
		// get ids from slugs
		$forum_id = $this->forum_model->getForumIdFromForumSlug($forum_slug);
		$topic_id = $this->forum_model->getTopicIdFromTopicSlug($topic_slug);

		// create objects
		$forum = $this->forum_model->getForum($forum_id);
		$topic = $this->forum_model->getTopic($topic_id);
		$posts = $this->forum_model->getPosts($topic_id);
		$this->load->model('User_model');
		foreach ($posts as $post) {
			$user=$this->User_model->getUserById($post->user_id);
			if ($post->replying_to == null) {
				$data['posts'] .= "<div 
				class='col-md-12' style='margin-left: 1em' id='post-'" . $post->id . "><header class='post-header'><img src='".base_url('uploads/avatars/'.$user->avatar)."' width='60px' height='60px' style='margin:5px; border: 1px solid black' ><small><a href=" . base_url('user/' . $this->user_model->getUsernameById($post->user_id)) . ">" . $this->user_model->getUsernameById($post->user_id) . "</a> " . $post->created_at . "</small></header>";
				$data['posts'] .= "<div class='post-content'>" . $post->content . "</div>
				</div>";
				if($post->img!=null){
					$data['posts'] .="<img style='margin-left:3em; max-width: 500px; height: auto;' class='img-fluid'  src='".base_url('uploads/topiccontent/'.$post->img )."' ><hr >";
				
				}
				
				
			} else {
				
				$data['posts'] .= "<div class='col-md-12' style=' margin:1em; border-left: 1px solid 	#cccccc;' id='post-" . $post->id . "'><header class='post-header'><img src='".base_url('uploads/avatars/'.$user->avatar)."' width='60px' height='60px' style='margin:5px; border: 1px solid black' ><small><a href=" . base_url('user/' . $this->user_model->getUsernameById($post->user_id)) . ">" . $this->user_model->getUsernameById($post->user_id) . "</a> " . $post->created_at . "</small></header>";


				//Post apagado?
				if ($post->deleted == 1) {
					$data['posts'] .= "<div class='post-content'><span ><strong>THIS POST WAS DELETED BY </strong></span><a href='" . base_url('user/' . $this->user_model->getUsernameById($post->updated_by)) . "'>" . $this->user_model->getUsernameById($post->updated_by) . "</a></div>
						</div>";
				} else {
					$data['posts'] .="<div class='post-content'>" . $post->content .
						"</div>						</div>";
					
				}
				$data['posts'] .= '<script>$("#post-' . $post->id . '").appendTo("#post-' . $post->replying_to . '")</script>';
			}
		}

		// create breadcrumb
		$breadcrumb  = '<ol class="breadcrumb">';
		$breadcrumb .= '<li><a href="' . base_url() . '">Home</a>-></li>';
		$breadcrumb .= '<li><a href="' . base_url('forum/'.$forum->slug) . '">' . $forum->title . '</a>-></li>';
		$breadcrumb .= '<li class="active">' . $topic->title . '</li>';
		$breadcrumb .= '</ol>';

		// assign created objects to the data object

		$data['breadcrumb'] = $breadcrumb;

		// load form helper and validation library
		$this->load->helper('form');
		$this->load->library('form_validation');

		// set validation rules
		$this->form_validation->set_rules('reply', 'Reply', 'required|min_length[2]');

		if ($this->form_validation->run() === false) {

			// keep what the user has entered previously on fields
			$data['content'] = $this->input->post('reply');
			if(validation_errors()){
				$data['val_error'] = '<div class="col-md-12">
				<div class="alert alert-danger" role="alert">'. validation_errors().'</div></div>';
			}
			
			$this->load->view('header');
			$this->parser->parse('topic/reply', $data);
			$this->load->view('footer');
		} else {

			$user_id = $_SESSION['user_id'];
			$content = $this->input->post('reply');
			$replying = ($this->uri->segment(5) != null) ? $this->uri->segment(5) : false;
			if ($this->forum_model->replyPost($topic_id, $user_id, $content,$replying)) {

				// post creation ok
				redirect(base_url('forum/' . $forum_slug . '/' . $topic_slug));
			} else {

				// post creation failed, this should never happen
				$data['error'] = '<div class="col-md-12">
				<div class="alert alert-danger" role="alert">There was a problem creating your reply. Please try again.</div></div>';

				// send error to the view
				$this->load->view('header');
				$this->load->view('topic/reply', $data);
				$this->load->view('footer');
			}
		}
	}
	}
	public function searchTags(){
		$this->forum_model->getForumByTags('html');
	}

	
}


