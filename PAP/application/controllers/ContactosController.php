<?php
defined('BASEPATH') or exit('No direct script access allowed');

class ContactosController extends CI_Controller
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
        $this->load->model('Contacto_model');
    }

    public function index()
    {
        $data['breadcrumb']  = '<ol class="breadcrumb">';
		$data['breadcrumb']  .= '<li><a href="' . base_url() . '">Home</a>-></li>';
		$data['breadcrumb']  .= '<li class="active">Help</li>';
		$data['breadcrumb']  .= '</ol>';
        $this->load->view('header');
        $this->parser->parse('contacto/contacto',$data);
        $this->load->view('footer');
    }

    public function Contactar()
    {
        if ($this->session->userdata['logged_in']) {
            $data['breadcrumb']  = '<ol class="breadcrumb">';
		    $data['breadcrumb']  .= '<li><a href="' . base_url() . '">Home</a>-></li>';
		    $data['breadcrumb']  .= '<li ><a href="' . base_url('info') . '">Help</a>-></li>';
            $data['breadcrumb']  .= '<li >Support Message</li>';
		    $data['breadcrumb']  .= '</ol>';
            $data['form_val'] = '';
            $this->form_validation->set_rules('msgContacto', 'Description', 'required');
            if ($this->form_validation->run() === false) {
                if (validation_errors())
                    $data['form_val'] = '<div class="col-md-12">
				    <div class="alert alert-danger" role="alert">' . validation_errors() . '</div></div>';;
                $this->load->view('header');
                $this->parser->parse('contacto/contact_form', $data);
                $this->load->view('footer');
            } else {
                $idUser = $this->session->userdata('user_id');
                $tipoProblema = $_POST['tipoContacto'];
                $msg = $_POST['msgContacto'];
                $this->Contacto_model->addContacto($tipoProblema,$msg,$idUser);
                $this->load->view('header');
                $this->load->view('contacto/contact_form_success');
                $this->load->view('footer');
                
        

            }
        } else {
            redirect(base_url());
        }
    }
    
   
}
