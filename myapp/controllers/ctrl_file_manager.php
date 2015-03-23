<?php
class Ctrl_file_manager extends MY_Controller {
    public $loc; // Localization

    public function __construct() {
        parent::__construct();
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
        $this->load->view('view_top1', array('title' => 'File Management'));
        $this->load->view('view_top2');
        $this->load->view('view_menu_bar');
        $this->load->view('view_confirm_dialog');
        $this->load->view('view_alert_dialog');

        $center_text = $this->load->view('view_file_manager',
                                         array('dirlist' => $dirlist,
                                               'is_top' => $this->mod_quizpath->is_top(),
                                               'databases' => $db_books,
                                               'copy_or_move' => $this->session->userdata('operation')),
                                         true);
        $this->load->view('view_main_page', array('left' => '<h1>Exercise File Management</h1>
     														<p>Here you can upload or delete exercise files, or you can
															create or delete folders for the files.</p>
															<p>Note: You can only delete a folder if it is empty.</p>
                                                            <p>The &ldquo;Edit visibility&rdquo; button allows you
                                                            to control who can see the exercises</p>
															<p>Exercise files can be created with the stand-alone
															Windows-based version of PLOTLearner. The exercise files
															have file type &ldquo;.3et&rdquo;.</p>',
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
            $this->error_view($e->getMessage(), 'File Management');
        }
    }

	public function create_folder() {
        try {
            $this->mod_users->check_admin();

            $this->mod_quizpath->init(set_or_default($_POST['dir'], ''), true, false);

            if (isset($_POST['create'])) {
                $create = trim($_POST['create']);

                if (preg_match('|[/?*:;{}\\\\]|',$create))
                    throw new DataException("Illegal character in folder name");

                $this->mod_quizpath->mkdir($create);
            }

            $this->show_files_2();
        }
        catch (DataException $e) {
            $this->error_view($e->getMessage(), 'Create Folder');
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
            $this->error_view($e->getMessage(), 'Delete Folder');
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
            $this->error_view($e->getMessage(), 'Copy or Delete Files');
        }
    }

    public function insert_files() {
        try {
            $this->mod_users->check_admin();

            if ($this->session->userdata('files')===false ||
                $this->session->userdata('operation')===false ||
                $this->session->userdata('from_dir')===false)
                throw new DataException("Missing source information");
            if (!isset($_GET['dir']))
                throw new DataException("Missing destination information");

            $this->mod_quizpath->init($_GET['dir'], true, false); // Destination

            $this->load->model('mod_quizpath','mod_source_quizpath');
            $this->mod_source_quizpath->init($this->session->userdata('from_dir'), true, false); // Source

            foreach ($this->session->userdata('files') as $f)
                if (file_exists($this->mod_quizpath->get_absolute() . '/' . $f))
                    throw new DataException("Destination file '$f' already exists. Delete or rename it. "
                                            . 'Then try to insert the '
                                            . ($this->session->userdata('operation')==='copy' ? 'copied' : 'moved')
                                            . ' files again.');

            foreach ($this->session->userdata('files') as $f)
                if (!@copy($this->mod_source_quizpath->get_absolute() . '/' . $f,
                          $this->mod_quizpath->get_absolute() . '/' . $f))
                    throw new DataException("Cannot copy file '$f'");
                
            if ($this->session->userdata('operation') === 'move')
                $this->mod_source_quizpath->delete_files($this->session->userdata('files'));
                
            $this->session->unset_userdata(array('files'=>'', 'operation'=>'', 'from_dir'=>''));

            $this->show_files_2();
        }
        catch (DataException $e) {
            $this->error_view($e->getMessage(), 'Insert Files');
        }
    }

    public function cancel_copy() {
        try {
            $this->mod_users->check_admin();

            $this->session->unset_userdata(array('files'=>'', 'operation'=>'', 'from_dir'=>''));

            $this->show_files();
        }
        catch (DataException $e) {
            $this->error_view($e->getMessage(), 'Cancel Copy');
        }
    }

    public function upload_files() {
        try {
            $this->mod_users->check_admin();

            $this->mod_quizpath->init(set_or_default($_GET['dir'], ''), true, false);

            // VIEW:
            $this->load->view('view_top1', array('title' => 'Upload Files',
                                                 'css_list' => array('styles/fileuploader.css'),
                                                 'js_list'=>array('valums-file-uploader-b3b20b1/client/fileuploader.js')));

            $this->load->view('view_top2');
            $this->load->view('view_menu_bar');

            $center_text = $this->load->view('view_upload_files',
                                             array('dir' => $this->mod_quizpath->get_relative()),
                                             true);
            $this->load->view('view_main_page', array('left' => '<h1>Upload Exercise Files</h1>
                                                                <p>You can generate exercise template files with
                                                                the stand-alone Windows-based version of PLOTLearner.
                                                                The exercise files have file type &ldquo;.3et&rdquo;.</p>
                                                                <p>Here, you can upload the exercise template files to the '
                                                                . ($this->mod_quizpath->is_top()
                                                                   ? 'top'
                                                                   : "<i>'{$this->mod_quizpath->get_relative()}'</i>") .
                                                                ' folder of this web site.</p>
                                                                <p>Click the &ldquo;Upload files&rdquo; button to the right
                                                                to select files to upload. (In some browsers, you can also
                                                                drag and drop files into the button.)</p>',
                                                      'center' => $center_text));
            $this->load->view('view_bottom');
        }
        catch (DataException $e) {
            $this->error_view($e->getMessage(), 'Upload Files');
        }
    }

    public function edit_visibility() {
        try {
            $this->mod_users->check_admin();

            $this->mod_quizpath->init(set_or_default($_GET['dir'], ''), true, false);

            if ($this->mod_quizpath->is_top())
                throw new DataException('You cannot change the visibility of the top directory');


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
                $this->load->view('view_top1', array('title' => 'Edit Visibility'));
                $this->load->view('view_top2');
                $this->load->view('view_menu_bar');
                
                $friendly_name = $this->mod_quizpath->is_top() ? 'the top folder' : "the folder <i>{$this->mod_quizpath->get_relative()}</i>";
                $center_text = $this->load->view('view_edit_visibility',
                                                 array('dir' => $this->mod_quizpath->get_relative(),
                                                       'friendly_name' => $friendly_name,
                                                       'allclasses' => $all_classes,
                                                       'old_classes' => $old_classes),
                                                 true);
             
                $this->load->view('view_main_page', array('left' => "<h1>Control Exercise Visibility</h1>
                                                                     <p>Here you can select which classes
                                                                     can see the exercises in $friendly_name.</p>",
                                                          'center' => $center_text));
                $this->load->view('view_bottom');
                return;
            }
        }
        catch (DataException $e) {
            $this->error_view($e->getMessage(), 'Edit Visibility');
        }
    }

    public function download_ex() {
        try {
            $this->mod_users->check_admin();
        
            if (!isset($_GET['dir']))
                throw new DataException("Missing folder name");
            if (!isset($_GET['file']))
                throw new DataException("Missing quiz filename");

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
            $this->error_view($e->getMessage(), 'Download Exercise');
        }
    }

    public function rename_file() {
        try {
            $this->mod_users->check_admin();
        
            if (!isset($_POST['dir']))
                throw new DataException("Missing folder name");
            if (!isset($_POST['oldname']))
                throw new DataException("Missing old filename");
            if (!isset($_POST['newname']))
                throw new DataException("Missing new filename");

            $newname = trim($_POST['newname']);

            if (preg_match('|[/?*:;{}\\\\]|',$newname))
                throw new DataException("Illegal character in new filename");
            
            $this->load->model('mod_quizpath');
            $this->mod_quizpath->init(rawurldecode($_POST['dir']), true, false);

            $this->mod_quizpath->rename($_POST['oldname'], $newname);

            redirect("/file_manager?dir={$this->mod_quizpath->get_relative()}");
        }
        catch (DataException $e) {
            $this->error_view($e->getMessage(), 'Rename Exercise');
        }
    }
}
