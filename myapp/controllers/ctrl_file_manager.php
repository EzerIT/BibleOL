<?php
class Ctrl_file_manager extends MY_Controller {
    public $loc; // Localization

    public function __construct() {
        parent::__construct();
        $this->lang->load('file_manager', $this->language);
        $this->load->model('mod_quizpath');
        $this->load->helper('varset');
    }

	public function index() {
        $this->show_files();
	}


    private function show_files_2() {
        $dirlist = $this->mod_quizpath->dirlist(false);

        $this->load->model('mod_askemdros');
        $db_books = $this->mod_askemdros->db_and_books();

        // VIEW:
        $this->load->view('view_top1', array('title' => $this->lang->line('file_mgmt')));
        $this->load->view('view_top2');
        $this->load->view('view_menu_bar', array('langselect' => true));
        $this->load->view('view_confirm_dialog');
        $this->load->view('view_alert_dialog');

        $center_text = $this->load->view('view_file_manager',
                                         array('dirlist' => $dirlist,
                                               'is_top' => $this->mod_quizpath->is_top(),
                                               'databases' => $db_books,
                                               'copy_or_move' => $this->session->userdata('operation')),
                                         true);
        $this->load->view('view_main_page', array('left' => '<h1>'.$this->lang->line('exercise_file_mgmt').'</h1>'
                                                             .$this->lang->line('file_mgmt_description'),
                                                  'center' => $center_text));
        $this->load->view('view_bottom');
    }

	public function show_files() {
        try {
            $this->mod_users->check_admin();

            $this->mod_quizpath->init(set_or_default($_GET['dir'], ''), true, false);

            $this->show_files_2();
        }
        catch (DataException $e) {
            $this->error_view($e->getMessage(), $this->lang->line('file_mgmt'));
        }
    }

	public function create_folder() {
        try {
            $this->mod_users->check_admin();

            $this->mod_quizpath->init(set_or_default($_POST['dir'], ''), true, false);

            if (isset($_POST['create'])) {
                $create = trim($_POST['create']);

                if (preg_match('|[/?*:;{}\\\\]|',$create))
                    throw new DataException($this->lang->line('illegal_char_folder_name'));

                $this->mod_quizpath->mkdir($create);
            }

            $this->show_files_2();
        }
        catch (DataException $e) {
            $this->error_view($e->getMessage(), $this->lang->line('create_folder'));
        }
    }

	public function delete_folder() {
        try {
            $this->mod_users->check_admin();

            $this->mod_quizpath->init(set_or_default($_GET['dir'], ''), true, false);

            if (isset($_GET['delete']))
                $this->mod_quizpath->rmdir($_GET['delete']);

            $this->show_files_2();
        }
        catch (DataException $e) {
            $this->error_view($e->getMessage(), $this->lang->line('delete_folder'));
        }
    }

    public function copy_delete_files() {
        try {
            $this->mod_users->check_admin();

            $this->mod_quizpath->init(set_or_default($_POST['dir'], ''), true, false);

            if (isset($_POST['file']) && isset($_POST['operation'])) {
                switch ($_POST['operation']) {
                  case 'copy':
                  case 'move':
                        $this->session->set_userdata('files', $_POST['file']);
						$this->session->set_userdata('operation', $_POST['operation']);
						$this->session->set_userdata('from_dir', $_POST['dir']);
                        break;

                  case 'delete':
                        $this->mod_quizpath->delete_files($_POST['file']);
                        break;
                }
            }
            $this->show_files_2();
        }
        catch (DataException $e) {
            $this->error_view($e->getMessage(), $this->lang->line('copy_or_delete_files'));
        }
    }

    public function insert_files() {
        try {
            $this->mod_users->check_admin();

            if ($this->session->userdata('files')===false ||
                $this->session->userdata('operation')===false ||
                $this->session->userdata('from_dir')===false)
                throw new DataException($this->lang->line('missing_src_info'));
            if (!isset($_GET['dir']))
                throw new DataException($this->lang->line('missing_dest_info'));

            $this->mod_quizpath->init($_GET['dir'], true, false); // Destination

            $this->load->model('mod_quizpath','mod_source_quizpath');
            $this->mod_source_quizpath->init($this->session->userdata('from_dir'), true, false); // Source

            foreach ($this->session->userdata('files') as $f)
                if (file_exists($this->mod_quizpath->get_absolute() . '/' . $f))
                    throw new DataException(sprintf($this->lang->line('file_exists'), $f)
                                            .' '. ($this->session->userdata('operation')==='copy'
                                               ? $this->lang->line('file_exists_copied')
                                               : $this->lang->line('file_exists_moved')));

            foreach ($this->session->userdata('files') as $f)
                if (!@copy($this->mod_source_quizpath->get_absolute() . '/' . $f,
                          $this->mod_quizpath->get_absolute() . '/' . $f))
                    throw new DataException(sprintf($this->lang->line('cannot_copy'), $f));
                
            if ($this->session->userdata('operation') === 'move')
                $this->mod_source_quizpath->delete_files($this->session->userdata('files'));
                
            $this->session->unset_userdata(array('files'=>'', 'operation'=>'', 'from_dir'=>''));

            $this->show_files_2();
        }
        catch (DataException $e) {
            $this->error_view($e->getMessage(), $this->lang->line('insert_files'));
        }
    }

    public function cancel_copy() {
        try {
            $this->mod_users->check_admin();

            $this->session->unset_userdata(array('files'=>'', 'operation'=>'', 'from_dir'=>''));

            $this->show_files();
        }
        catch (DataException $e) {
            $this->error_view($e->getMessage(), $this->lang->line('cancel_copy_title'));
        }
    }

    public function upload_files() {
        try {
            $this->mod_users->check_admin();

            $this->mod_quizpath->init(set_or_default($_GET['dir'], ''), true, false);

            // VIEW:
            $this->load->view('view_top1', array('title' => $this->lang->line('upload_files'),
                                                 'css_list' => array('styles/fileuploader.css'),
                                                 'js_list'=>array('valums-file-uploader-b3b20b1/client/fileuploader.js')));

            $this->load->view('view_top2');
            $this->load->view('view_menu_bar', array('langselect' => true));

            $center_text = $this->load->view('view_upload_files',
                                             array('dir' => $this->mod_quizpath->get_relative()),
                                             true);
            $this->load->view('view_main_page', array('left' => '<h1>'.$this->lang->line('upload_exercise_files1').'</h1>'
                                                                .'<p>'.$this->lang->line('upload_exercise_files2').'</p>'
                                                                .($this->mod_quizpath->is_top()
                                                                  ? $this->lang->line('upload_exercise_files3_top')
                                                                  : sprintf($this->lang->line('upload_exercise_files3_other'),
                                                                            $this->mod_quizpath->get_relative()))
                                                                .'<p>'.$this->lang->line('upload_exercise_files4').'</p>',
                                                      'center' => $center_text));
            $this->load->view('view_bottom');
        }
        catch (DataException $e) {
            $this->error_view($e->getMessage(), $this->lang->line('upload_files'));
        }
    }

    public function edit_visibility() {
        try {
            $this->mod_users->check_admin();

            $this->mod_quizpath->init(set_or_default($_GET['dir'], ''), true, false);

            if ($this->mod_quizpath->is_top())
                throw new DataException($this->lang->line('cannot_change_visibility_top'));


            $this->load->model('mod_classes');
            $this->load->model('mod_classdir');
            $all_classes = $this->mod_classes->get_all_classes();
            $old_classes = $this->mod_classdir->get_classes_for_dir($this->mod_quizpath->get_relative());

            $this->load->helper('form');
            $this->load->library('form_validation');

            $this->form_validation->set_rules('inclass[]', '', '');
            
            if ($this->form_validation->run()) {
                $new_classes = $this->input->post('inclass');
                if (!$new_classes) // post() returns false when nothing is selected...
                    $new_classes = array(); // ...so we set the array to empty

                $this->mod_classdir->update_classes_for_dir($this->mod_quizpath->get_relative(), $old_classes, $new_classes);
                redirect("/file_manager?dir={$this->mod_quizpath->get_relative()}");
            }
            else {
                // VIEW:
                $this->load->view('view_top1', array('title' => $this->lang->line('edit_visibility')));
                $this->load->view('view_top2');
                $this->load->view('view_menu_bar', array('langselect' => true));
                
                $center_text = $this->load->view('view_edit_visibility',
                                                 array('dir' => $this->mod_quizpath->get_relative(),
                                                       'allclasses' => $all_classes,
                                                       'old_classes' => $old_classes),
                                                 true);
             
                $this->load->view('view_main_page', array('left' => '<h1>'.$this->lang->line('control_visibility').'</h1>'
                                                                     .'<p>'.sprintf($this->lang->line('visibility_folder_desc'),
                                                                                    $this->mod_quizpath->get_relative())
                                                                     .'</p>',
                                                          'center' => $center_text));
                $this->load->view('view_bottom');
                return;
            }
        }
        catch (DataException $e) {
            $this->error_view($e->getMessage(), $this->lang->line('edit_visibility'));
        }
    }

    public function download_ex() {
        try {
            $this->mod_users->check_admin();
        
            if (!isset($_GET['dir']))
                throw new DataException($this->lang->line('missing_folder_name'));
            if (!isset($_GET['file']))
                throw new DataException($this->lang->line('missing_quiz_filename'));

            $this->load->model('mod_quizpath');
            $this->mod_quizpath->init(rawurldecode($_GET['dir']) . '/' . rawurldecode($_GET['file']), false, false);


            header('Content-Type: application/octet-stream');
            header('Content-Length: ' . filesize($this->mod_quizpath->get_absolute()));
            header('Content-Disposition: attachment; filename="' . $_GET['file'] . '"');

            $this->load->helper('file');
            $contents = read_file($this->mod_quizpath->get_absolute());

            echo $contents;
        }
        catch (DataException $e) {
            $this->error_view($e->getMessage(), $this->lang->line('download_exercise'));
        }
    }

    public function rename_file() {
        try {
            $this->mod_users->check_admin();
        
            if (!isset($_POST['dir']))
                throw new DataException($this->lang->line('missing_folder_name'));
            if (!isset($_POST['oldname']))
                throw new DataException($this->lang->line('missing_old_filename'));
            if (!isset($_POST['newname']))
                throw new DataException($this->lang->line('missing_new_filename'));

            $newname = trim($_POST['newname']);

            if (preg_match('|[/?*:;{}\\\\]|',$newname))
                throw new DataException($this->lang->line('illegal_char_filename'));
            
            $this->load->model('mod_quizpath');
            $this->mod_quizpath->init(rawurldecode($_POST['dir']), true, false);

            $this->mod_quizpath->rename($_POST['oldname'], $newname);

            redirect("/file_manager?dir={$this->mod_quizpath->get_relative()}");
        }
        catch (DataException $e) {
            $this->error_view($e->getMessage(), $this->lang->line('rename_exercise'));
        }
    }
}
