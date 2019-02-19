<?php
class Ctrl_site extends MY_Controller {
    public function __construct() {
        parent::__construct();
    }

    public function site_text() {
        try {
            $this->mod_users->check_admin();

            $this->load->model('mod_translate');
            $langs = $this->mod_translate->get_all_if_languages();
            $variant = $this->config->item('url_variant');
            $use = 'intro_center';
            

            if (!isset($_GET['lang']) || !isset($langs[$_GET['lang']])) {

                // VIEW:
                $this->load->view('view_top1', array('title' => $this->lang->line('site_edit')));
                $this->load->view('view_top2');
                $this->load->view('view_menu_bar', array('langselect' => true));
                $center_text = $this->load->view('view_select_sitetext_lang', array('langs' => $langs), true);
                $this->load->view('view_main_page',
                                  array('left_title' => empty($variant)
                                                          ? "Edit Introduction Page for Main Site"
                                                          : sprintf("Edit Introduction Page for Site “%s”",$variant),
                                        'left' => 'Select language',
                                        'center' => $center_text));
                $this->load->view('view_bottom');
            }
            else {
                $targetlang = $_GET['lang'];

                $this->load->model('mod_sitetext');

                $this->load->helper('form');

                if (isset($_POST['edittext'])) {
                    $this->mod_sitetext->set_sitetext($use, $targetlang, $variant, $_POST['edittext']);
                    redirect("/");
                }
                
                $intro_text = $this->mod_sitetext->get_sitetext_strict($use, $targetlang, $variant);

                $javascripts = array('ckeditor/ckeditor.js',
                                     'ckeditor/adapters/jquery.js');

                $this->load->view('view_top1', array('title' => $this->lang->line('site_edit'),
                                                     'js_list' => $javascripts));

                $this->load->view('view_top2');
                $this->load->view('view_menu_bar', array('langselect' => true));
                $center_text = $this->load->view('view_edit_sitetext', array('text' => $intro_text, 'lang' => $targetlang), true);
                $this->load->view('view_main_page',
                                  array('left_title' => empty($variant)
                                                          ? "Edit Introduction Page for Main Site"
                                                          : sprintf("Edit Introduction Page for Site “%s”",$variant),
                                        'left' => 'xxx',
                                        'center' => $center_text));
                $this->load->view('view_bottom');
            }
        }
        catch (DataException $e) {
            $this->error_view($e->getMessage(), $this->lang->line('site_edit'));
        }
    }

    public function site_text2() {
        echo "<pre> GET: ",
            htmlspecialchars(print_r($_GET,true)),
            "\nPOST: ",
            htmlspecialchars(print_r($_POST,true)),
            "</pre>";
    }

    

    public function index() {
        if ($this->mod_users->is_logged_in_noaccept()) {
            if (isset($_SESSION['new_oauth2'])) {
                $authority = $_SESSION['new_oauth2'];

                $this->load->helper('form');
                $this->load->helper('myurl');
                $this->lang->load('login', $this->language);
                $this->lang->load('privacy', $this->language);

                $acceptance_code = $this->mod_users->generate_acceptance_code();

                // VIEW:
                $this->load->view('view_top1', array('title' => $this->lang->line("new_{$authority}_user")));
                $this->load->view('view_top2');
                $this->load->view('view_menu_bar', array('langselect' => true));
                $center_text = $this->load->view('view_new_oauth2_user',
                                                 array('authority' => $authority,
                                                       'user_info' => $this->mod_users->get_me(),
                                                       'acceptance_code' => $acceptance_code),
                                                 true);
                $this->lang->load('intro_text', $this->language); // For 'welcome' below
                $this->load->view('view_main_page',array('left_title' => $this->lang->line('welcome'),
                                                         'left' => $this->lang->line('first_you_must_accept_policy'),
                                                         'center' => $center_text));
                $this->load->view('view_bottom');
            }
            else {
                // User needs to accept new policy

                $this->lang->load('privacy', $this->language);

                $acceptance_code = $this->mod_users->generate_acceptance_code();

                $this->load->helper('form');

                // VIEW:
                $this->load->view('view_top1', array('title' => $this->lang->line('policy')));
                $this->load->view('view_top2');
                $this->load->view('view_menu_bar', array('langselect' => true));
                $center_text = $this->load->view('view_accept_policy', array('acceptance_code' => $acceptance_code,
                                                                             'user_id' => $this->mod_users->my_id()), true);

                $this->load->view('view_main_page', array('left_title' => $this->lang->line('new_privacy_header'),
                                                          'left' => $this->lang->line('new_privacy_intro'),
                                                          'center' => $center_text));
        
                $this->load->view('view_bottom');
            }
        }
        else {
            $this->load->model('mod_intro_text');
            $data['left_title'] = $this->mod_intro_text->left_text_title();

            $variant = $this->config->item('url_variant');
            $data['left'] = "<h1>VARIANT: $variant</h1>" . $this->mod_intro_text->left_text();

            $data['center'] = $this->mod_intro_text->center_text();
            $data['right_title'] = $this->mod_intro_text->select_site_title();
            $data['right'] = $this->mod_intro_text->select_site_text();
            $data['logos'] = true;
        
            $this->load->view('view_top1', array('title'=>'Bible Online Learner'));
            $this->load->view('view_top2');
            $this->load->view('view_menu_bar', array('langselect' => true));
            $this->load->view('view_main_page',$data);
            $this->load->view('view_bottom');
        }
	}
}
