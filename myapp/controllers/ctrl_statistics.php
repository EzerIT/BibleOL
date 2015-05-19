<?php
class Ctrl_statistics extends MY_Controller {
    public function __construct() {
        parent::__construct();
        $this->lang->load('statistics', $this->language);
        $this->load->model('mod_statistics');
    }

    private function check_logged_in() {
        // MODEL:
        if (!$this->mod_users->is_logged_in())
            throw new DataException($this->lang->line('must_be_logged_in'));
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
                    $this->db_config->init_config($templ->dbname,$templ->dbpropname, $this->language_short);
                    $templ->l10n = json_decode($this->db_config->l10n_json);
                    $templ->obj2feat = $this->db_config->typeinfo->obj2feat;
                    $templ->quizzes = $allquizzes;
                    $templ->req_features = $this->mod_statistics->allReqFeatures(intval($templ->qtid));
                    $goodtemplates[] = $templ;
                }
            }

            
            $this->load->view('view_top1', array('title' => $this->lang->line('statistics_title')));
            $this->load->view('view_top2');
            $this->load->view('view_menu_bar', array('langselect' => true));
            $this->load->view('view_confirm_dialog');
            $center_text = $this->load->view('view_statistics', array('data' => $goodtemplates), true);
            $left_text = $this->load->view('view_statistics_left', array('name' => $this->mod_users->my_name()), true);
            $this->load->view('view_main_page', array('left' => $left_text,
                                                      'center' => $center_text));
            $this->load->view('view_bottom');
            
        }
        catch (DataException $e) {
            $this->error_view($e->getMessage(), $this->lang->line('statistics_title'));
        }
    }


    public function update_stat() {
        $this->mod_statistics->endQuiz();
    }
}
