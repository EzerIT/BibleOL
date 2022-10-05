<?php
class Ctrl_help extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->lang->load('text', $this->language);
    }

	public function index() {
        $this->show_help();
	}

	public function show_help() {
        try {
            $article = $this->uri->segment(3);

            if (is_null($article))
                $article = 'intro';
            
            $sub_article = $this->uri->segment(4);
            $full_article = $article . (!is_null($sub_article) ? "/$sub_article" : '');

            $dir = "usersguide/{$this->language}";
            $filename = "$dir/{$article}.php";

            if (!file_exists($filename)) {
                $dir = "usersguide/en";
                $filename = "$dir/{$article}.php";
                if (!file_exists($filename))
                    throw new DataException("There is no help article named '$article'");
            }

            // VIEW:
            $this->load->helper('help_dir_helper');

            $this->load->vars(array('in_help' => true,
                                    'hdir' => new HelpDir($dir, $sub_article),
                                    'sub_article' => $sub_article));
            
            $this->load->view('view_top1', array('title' => $this->lang->line('show_help')));
            $this->load->view('view_top2');
            $this->load->view('view_menu_bar', array('langselect' => true));


            $left_text = $this->load->view('view_help_navigator',array('current'=>$full_article),true);
            $center_text = $this->load->file(FCPATH.$filename,true);
            
            $this->load->view('view_main_page', array('left_title' => 'Help pages',
													  'left' => $left_text,
                                                      'center' => $center_text));

            $this->load->view('view_bottom');
        }
        catch (DataException $e) {
            $this->load->vars(array('in_help' => true));
            $this->error_view($e->getMessage(), $this->lang->line('select_text'));
        }
    }
}
