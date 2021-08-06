<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Contacto_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'contacto';
    }



    public function addContacto($type, $msg, $idUser)
    {
        $data = array(
            'tipo'       => $type,
            'msg' => $msg,
            'idUser' => $idUser,
            'created_at'  => date('Y-m-j H:i:s'),
        );

        return $this->db->insert($this->table, $data);
    }


    public function deleteContacto($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete($this->table);
    }


    public function getAllContacts()
    {
        $this->db->from($this->table);
        return $this->db->get()->result();
    }
    public function getContact($id)
    {
        $this->db->where('id', $id);
        $this->db->from($this->table);
        return $this->db->get()->result();
    }

    public function addReport($tipo, $id)
    {

        if ($tipo == 'post') {
            $report = 'PostReport';
            $dados = array(
                'tipo' => $report,
                'msg' => 'Reported Content',
                'idUser' => $this->session->userdata('user_id'),
                'created_at'  => date('Y-m-j H:i:s'),
                'idPost' => $id

            );
        } elseif ($tipo == 'topic') {
            $report = 'TopicReport';
            $dados = array(
                'tipo' => $report,
                'msg' => 'Reported Content',
                'idUser' => $this->session->userdata('user_id'),
                'created_at'  => date('Y-m-j H:i:s'),
                'idTopic' => $id
            );
        } elseif ($tipo == 'forum') {
            $report = 'ForumReport';
            $dados = array(
                'tipo' => $report,
                'msg' => 'Reported Content',
                'idUser' => $this->session->userdata('user_id'),
                'created_at'  => date('Y-m-j H:i:s'),
                'idForum' => $id
            );
        }
        return $this->db->insert($this->table, $dados);
    }

    public function getAllUserContact($idUser)
    {
        $this->db->get('id');
        $this->db->from($this->table);
        $this->db->where('idUser', $idUser);
        return $this->db->get()->result();
    }

    public function sendNotificationToUser($to_user,$title,$msg){
        $dados = array(
            'tipo_notificacao' => $title,
            'msg' => $msg,
            'user_id' => $to_user,
            'sent_at'  => date('Y-m-j H:i:s'),

        );
        return $this->db->insert('feed', $dados);
    }
    public function contDelete($id){
        $this->db->where('id',$id);
        return $this->db->delete('contacto');
    }
}
