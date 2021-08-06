<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Forum_model class.
 *
 * @extends CI_Model
 */
class Forum_model extends CI_Model {

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {

		parent::__construct();
		$this->load->database();
		$this->load->helper(array('url'));

	}

	/**
	 * createForum function.
	 *
	 * @access public
	 * @param string $title
	 * @param string $description
	 * @return bool
	 */
	public function createForum($title, $description,$id_user, $tags) {
		if($tags){
			$arrayTags= json_encode(explode('-',strtolower($tags)));
			$data = array(
				'title'       => $title,
				'slug'        => strtolower(url_title($title)),
				'description' => $description,
				'created_at'  => date('Y-m-j H:i:s'),
				'id_user' 	  => $id_user,
				'tags'        => $arrayTags
			);
	
			
		}else
		$data = array(
			'title'       => $title,
			'slug'        => strtolower(url_title($title)),
			'description' => $description,
			'created_at'  => date('Y-m-j H:i:s'),
			'id_user' 	  => $id_user
		);
		
		
		return $this->db->insert('forums', $data);
	}


	public function getForumIdFromForumSlug($slug) {

		$this->db->select('id');
		$this->db->from('forums');
		$this->db->where('slug', $slug);
		return $this->db->get()->row('id');

	}






	/**
	 * getTopicIdFromTopicSlug function.
	 *
	 * @access public
	 * @param string $topic_slug
	 * @return int
	 */
	public function getTopicIdFromTopicSlug($topic_slug) {

		$this->db->select('id');
		$this->db->from('topics');
		$this->db->where('slug', $topic_slug);
		return $this->db->get()->row('id');

	}

	/**
	 * getForums function.
	 *
	 * @access public
	 * @return array of objects
	 */
	public function getForums() {

		return $this->db->get('forums')->result();

	}

	/**
	 * getForum function.
	 *
	 * @access public
	 * @param int $forum_id
	 * @return object
	 */
	public function getForum($forum_id) {

		$this->db->from('forums');
		$this->db->where('id', $forum_id);
		return $this->db->get()->row();

	}

	/**
	 * getTopic function.
	 *
	 * @access public
	 * @param int $topic_id
	 * @return object
	 */
	public function getTopic($topic_id) {

		$this->db->from('topics');
		$this->db->where('id', $topic_id);
		return $this->db->get()->row();

	}

	/**
	 * getForumTopics function.
	 *
	 * @access public
	 * @param int $forum_id
	 * @return array of objects
	 */
	public function getForumTopics($forum_id) {

		$this->db->from('topics');
		$this->db->where('forum_id', $forum_id);
		return $this->db->get()->result();

	}

	public function deleteForum($forum_id){
		$this->db->where('id',$forum_id);
		if($this->db->delete('forums')){
			$this->db->from('topics');
			$this->db->where('forum_id',$forum_id );	
			$ids= $this->db->get()->result();//percorrer os topic de um forum e apagar os posts
			foreach($ids as $id){
				$this->deletePostsFromTopics($id->id);
			}
			$this->db->where('forum_id',$forum_id );
			$this->db->delete('topics');
			return;
		}
		return;
	}




	
	public function deletePostsFromTopics($id_topic){
		$this->db->where('topic_id',$id_topic);
		return $this->db->delete('posts');
		
	}







	public function deleteTopic($topic_id){
		$this->db->where('id',$topic_id);
		$this->db->delete('topics');
		$this->deletePostsFromTopics($topic_id);
		return;
	}

	public function deletePost($post){
		$dadosPost=$this->getPostById($post);
		var_dump($dadosPost->user_id);
		var_dump($this->session->userdata('user_id'));
		if($dadosPost->user_id!=$this->session->userdata('user_id') ){
			if($this->session->userdata('is_admin')==false){
				return false;
			}
		}
		

		if(!$dadosPost->replying_to==null){
			$data=array(
				'deleted' => 1,
				'updated_by' => $this->session->userdata('user_id')
			);
			$this->db->where('id', $post);
			return $this->db->update('posts', $data);
	
		}else{
			return $this->deleteTopic($dadosPost->topic_id);
		}
		
	}




	public function getForumByTags($tags){
		$forumsComTag=[];
		$i=0;
			foreach($this->getForums() as $forum){

				if(array_search('php',json_decode($forum->tags),true));
				$forumsComTag[$i++]=$forum->id;
				var_dump($forumsComTag);
			}
			
		return $forumsComTag;
	
		
	}


	public function getReplyingPosts($post){
		
		$this->db->where('replying_to',$post);
		$this->db->from('posts');
		return $this->db->get()->result();
	}




	/**
	 * getPosts function.
	 *
	 * @access public
	 * @param int $topic_id
	 * @return array of objects
	 */
	public function getPosts($topic_id) {

		$this->db->from('posts');
		$this->db->where('topic_id', $topic_id);
		return $this->db->get()->result();

	}


	public function getPostById($post_id){
		$this->db->from('posts');
		$this->db->where('id',$post_id);
		return $this->db->get()->row();
	}

	/**
	 * getTopicLatestPost function.
	 *
	 * @access public
	 * @param int $topic_id
	 * @return object
	 */
	public function getTopicLatestPost($topic_id) {

		$this->db->from('posts');
		$this->db->where('topic_id', $topic_id);
		$this->db->order_by('created_at', 'DESC');
		$this->db->limit(1);
		return $this->db->get()->row();

	}

	/**
	 * createTopic function.
	 *
	 * @access public
	 * @param int $forum_id
	 * @param string $title
	 * @param string $content
	 * @param int $user_id
	 * @return bool
	 */
	public function createTopic($forum_id, $title, $content, $user_id,$img) {
		var_dump($img);
		$data = array(
			'title'      => $title,
			'slug'       => strtolower(url_title($title)),
			'user_id'    => $user_id,
			'forum_id'   => $forum_id,
			'created_at' => date('Y-m-j H:i:s'),
			'updated_at' => date('Y-m-j H:i:s'),
		);

		if ($this->db->insert('topics', $data)) {
			$topic_id = $this->db->insert_id();
			return $this->createPost($topic_id, $user_id, $content,$img);
		}
		return false;

	}

	/**
	 * createPost function.
	 *
	 * @access public
	 * @param int $topic_id
	 * @param int $user_id
	 * @param string $content
	 * @return bool
	 */
	public function createPost($topic_id, $user_id, $content, $img=false, $reply_to=false) {
		
		if($img)
		$data = array(
			'content'    => $content,
			'user_id'    => $user_id,
			'topic_id'   => $topic_id,
			'created_at' => date('Y-m-j H:i:s'),
			'img' =>$img
		);
		else
			$data = array(
				'content'    => $content,
				'user_id'    => $user_id,
				'topic_id'   => $topic_id,
				'created_at' => date('Y-m-j H:i:s'),
			);

		if ($this->db->insert('posts', $data)) {

			$data = array('updated_at' => date('Y-m-j H:i:s'));
			$this->db->where('id', $topic_id);
			return $this->db->update('topics', $data);

		}
		return false;

	}
	public function replyPost($topic_id, $user_id, $content, $reply_to) {

		$data = array(
			'content'    => $content,
			'user_id'    => $user_id,
			'topic_id'   => $topic_id,
			'created_at' => date('Y-m-j H:i:s'),
			'replying_to'=> $reply_to
		);

		if ($this->db->insert('posts', $data)) {

			$data = array('updated_at' => date('Y-m-j H:i:s'));
			$this->db->where('id', $topic_id);
			return $this->db->update('topics', $data);

		}
		return false;

	}



	/**
	 * countForumPosts function.
	 *
	 * @access public
	 * @param int $forum_id
	 * @return int
	 */
	public function countForumPosts($forum_id) {

		$this->db->select('posts.id');
		$this->db->from('posts');
		$this->db->join('topics', 'posts.topic_id = topics.id');
		$this->db->where('topics.forum_id', $forum_id);
		$this->db->group_by('posts.id');
		return count($this->db->get()->result());

	}

	/**
	 * getForumLatestTopic function.
	 *
	 * @access public
	 * @param int $forum_id
	 * @return object
	 */
	public function getForumLatestTopic($forum_id) {

		$this->db->from('topics');
		$this->db->where('forum_id', $forum_id);
		$this->db->order_by('created_at', 'DESC');
		$this->db->limit(1);
		return $this->db->get()->row();

	}

}
