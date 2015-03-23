<?php
class Ctrl_statistics extends MY_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('mod_statistics');
    }

    private function check_logged_in() {
        // MODEL:
        if (!$this->mod_users->is_logged_in())
            throw new DataException('You must be logged in to access this function');
    }

	public function index() {
        $this->show_stat();
	}

	public function show_stat() {
        try {
            $this->check_logged_in();
            $this->load->library('db_config');

            $alltemplates = $this->mod_statistics->allTemplates($this->mod_users->my_id());
            $goodtemplates = array();
            foreach ($alltemplates as $templ) {
                $allquizzes = $this->mod_statistics->allQuizzes(intval($templ->qtid));
                if (count($allquizzes)>0) {
                    $this->db_config->init_config($templ->dbname,$templ->dbpropname, 'en');
                    $templ->localization = json_decode($this->db_config->localization_json);
                    $templ->obj2feat = $this->db_config->typeinfo->obj2feat;
                    $templ->quizzes = $allquizzes;
                    $templ->req_features = $this->mod_statistics->allReqFeatures(intval($templ->qtid));
                    $goodtemplates[] = $templ;
                }
            }

            
            $this->load->view('view_top1', array('title' => 'Statistics'));
            $this->load->view('view_top2');
            $this->load->view('view_menu_bar');
            $this->load->view('view_confirm_dialog');
            $center_text = $this->load->view('view_statistics', array('data' => $goodtemplates), true);
            $left_text = $this->load->view('view_statistics_left', array('name' => $this->mod_users->my_name()), true);
            $this->load->view('view_main_page', array('left' => $left_text,
                                                      'center' => $center_text));
            $this->load->view('view_bottom');
            
        }
        catch (DataException $e) {
            $this->error_view($e->getMessage(), 'Statistics');
        }
    }


    public function update_stat() {
        $this->mod_statistics->endQuiz();
    }
}
