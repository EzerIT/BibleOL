<?php
class Ctrl_exams extends MY_Controller {
    public $loc; // Localization

    public function __construct() {
        parent::__construct();
        
        $this->lang->load('exams', $this->language);
        $this->load->model('mod_quizpath');
        $this->load->helper('varset');
        
    }

    public function index() {
    $this->show_files();
    //print_r($this->getDirContents(__DIR__."/../../quizzes"));
    //exit();
    }
    
    public function build() {
        $exam = $_POST["exam"];
        $ename = $_POST["name"];
        $path = __DIR__."/../../exam/" . $ename;
        if(!file_exists($path)){
            mkdir($path);
        }
        foreach($exam as $url){
            copy($url, $path . "/" . basename($url));
        }
        print($ename);
        exit();
    }
    
    public function getDirFolders($dir, &$results = array()){
        $files = scandir($dir);

        foreach($files as $key => $value){
            $path = realpath($dir.DIRECTORY_SEPARATOR.$value);
            if(!is_dir($path)) {
                $results[] = $path;
            } else if($value != "." && $value != "..") {
                $results[] = $path;
            }
        }

        return $results;
    }
    
    /**
     * First checks if the user is a teacher
     * Creates a new directory with the same name as the exam
     * Copies exercises that are currently in exercise_list to the new directory
     */
    public function create_exam (){
    	try {
            $this->mod_users->check_teacher();

    		
    		
            if (isset($_POST['create_exam'])) {
                $create = trim($_POST['create_exam']);

                if (preg_match('|[/?*;{}"\'\\\\]|',$create))
                    throw new DataException($this->lang->line('illegal_char_folder_name'));

					 $exercise_lst = $_POST['exercise_list'];				
					 
					 // chmod 7-7 required					 
                mkdir('/var/www/BibleOL/exam/'.$create);
                
					 //echo '<span>alert('.$exercise_lst.')</span>';
					 				 
					 $ex_ar = explode(',', $exercise_lst);
					 
					 $base_pth = '/var/www/BibleOL/';					 
					 $ex_pth = $base_pth . 'quizzes';             
                
                foreach ( $ex_ar as $key => $exrcs){
                	 $exrcs = str_replace('"', '', $exrcs);
                	 $org_pth = $ex_pth . "/" . $exrcs;
                	 if(($exrcs) != 'undefined') {
						 	 $new_pth = $base_pth . "exam/" . $create . "/" . basename($exrcs);
						 	 copy($org_pth, $new_pth);          
						 }                
                }
					 
					 $this -> create_config_file($create, $ex_ar); 
					 
					 header("Location: http://127.0.0.1/exams/edit_exam?exam=$create");  
            }

        }
        catch (DataException $e) {
            $this->error_view($e->getMessage(), $this->lang->line('illegal_char_folder_name'));
        }
    }

	 // Create xml configuration file
	 public function create_config_file($examname, array $exercises){		
	 		
	 		// Set up XML file
	 		$dom = new DOMDocument();
	 		$dom->encoding = 'utf-8';
	 		$dom->xmlVersion = '1.0';
	 		$dom->formatOutput = true;
	 		$dom->preserveWhiteSpace = false;
	 		
	 		// Add root tags to XML file
	 		$root = $dom->createElement('exam');
	 		$dom->appendChild($root);
	 		$examname_node = $dom->createElement('examname', $examname);
	 		$root->appendChild($examname_node);
	 		
	 		
	 		// Add exercise tags to XML file
	 		// <exercise numq="10" time="0">exercisename.3et</exercise>
	 		$order = 1;
	 		foreach($exercises as $key => $value){
	 			 if ($key != 0){			 
	 			     $value = str_replace('"', '', $value);
	 			     print($value);
				     $exercise_node = $dom->createElement('exercise');
				     $root->appendChild($exercise_node);
				     $exercise_name = $dom->createElement('exercisename', $value);
				     $exercise_node->appendChild($exercise_name);
				     $numq_node = $dom->createElement('numq', '0');
				     $exercise_node->appendChild($numq_node);
				     $time_node = $dom->createElement('time', '0');
				     $exercise_node->appendChild($time_node);
				     $weight_node = $dom->createElement('weight', '1');
				     $exercise_node->appendChild($weight_node);
				     $order_node = $dom->createElement('order', $order);
				     $exercise_node->appendChild($order_node);
				     
				     $order = $order + 1;
				     /*
				     $exercise_node->setAttributeNode(new DOMAttr('numq', '10'));
				     $exercise_node->setAttributeNode(new DOMAttr('time', '0'));
				     $exercise_node->setAttributeNode(new DOMAttr('weight', 1));
					  */				 
				 }
	 		}
	 		
	 		$dom->save('/var/www/BibleOL/exam/'.$examname.'/config.xml');
			 
	 }

    /**
     * Stores all elements of a directory in an array
     * First element of all arrays is always the name of the directory itself
     * @param $dir Name of the directory
     * @return $results Array which contains all sub-directories and items in the main
     *		directory and all sub-directories
     */
    public function getDirFiles($dir){
		$files = scandir($dir);
		
		$results = array();		
		$results[] = realpath($dir);				
		
		foreach($files as $key => $value){
			$path = realpath($dir.DIRECTORY_SEPARATOR.$value);
			if(substr(basename($path),0,1)=='.') continue;
 			if(!is_dir($path)){
				$results[] = $path;
			} else if($value != "." && $value != "..") {
				$results[] = $this -> getDirFiles($path);			
			}		
		}
		
		return $results;    
    }
 
    
    public function getDirContents($dir, &$results = array()){
        $files = scandir($dir);

        foreach($files as $key => $value){
            $path = realpath($dir.DIRECTORY_SEPARATOR.$value);
            if(substr(basename($path),0,1)=='.') continue;
            if(!is_dir($path)) {
                $results[] = $path;
            } else if($value != "." && $value != "..") {
                $this -> getDirContents($path, $results);
                $results[] = $path;
            }
        }

        return $results;
    }
    
    
    
    
    //Get all elements of a folder only one level down
    public function getChildren($dir) {
    	$results = array();
    	$files = scandir($dir);
    	
    	foreach($files as $key => $value){
			$path = realpath();    	
		}
    	
    	return $results;
    }



    private function show_files_2() {
        $this->lang->load('owner', $this->language);

        $dirlist = $this->mod_quizpath->dirlist(false);

        $this->load->model('mod_askemdros');
        $db_books = $this->mod_askemdros->db_and_books();

        if ($this->mod_users->is_admin())
            $teachers = $this->mod_users->get_teachers();
        else
            $teachers = array();

        // VIEW:
        $this->load->view('view_top1', array('title' => $this->lang->line('file_mgmt')));
        $this->load->view('view_top2');
        $this->load->view('view_menu_bar', array('langselect' => true));
        $this->load->view('view_confirm_dialog');
        $this->load->view('view_alert_dialog');

        $center_text = $this->load->view('view_exams',
                                         array('dirlist' => $dirlist,
                                               'examlist' => $this->getDirFolders(__DIR__."/../../exam"),
                                               'exerciselist' => $this->getDirContents(__DIR__."/../../quizzes"),
                                               'dir_files' => $this->getDirFiles(__DIR__."/../../quizzes"),
                                               //'show_contents' => $this->showContents('dir_files'),
                                               'is_top' => $this->mod_quizpath->is_top(),
                                               'databases' => $db_books,
                                               'isadmin' => $this->mod_users->is_admin(),
                                               'teachers' => $teachers,
                                               'copy_or_move' => $this->session->userdata('operation')),
                                         true);
        $this->load->view('view_main_page', array('left_title' => $this->lang->line('exercise_file_mgmt'),
                                                  'left' => $this->lang->line('file_mgmt_description'),
                                                  'center' => $center_text));
        $this->load->view('view_bottom');
    }

	public function show_files() {
        try {
            $this->mod_users->check_teacher();

            $this->mod_quizpath->init(set_or_default($_GET['dir'], ''), true, false);

            $this->show_files_2();
        }
        catch (DataException $e) {
            $this->error_view($e->getMessage(), $this->lang->line('file_mgmt'));
        }
    }

	public function create_folder() {
        try {
            $this->mod_users->check_teacher();

            $this->mod_quizpath->init(set_or_default($_POST['exam'], ''), true, false);

            if (isset($_POST['create_exam'])) {
                $create = trim($_POST['create_exam']);

                if (preg_match('|[/?*;{}"\'\\\\]|',$create))
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
            $this->mod_users->check_teacher();

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

            $this->mod_quizpath->init(set_or_default($_POST['dir'], ''), true, false);

            if (isset($_POST['file']) && isset($_POST['operation'])) {
                switch ($_POST['operation']) {
                  case 'copy':
                  case 'move':
                        $this->mod_users->check_teacher();

                        if ($_POST['operation']==='move')
                            $this->mod_quizpath->check_delete_files($_POST['file']);

                        $this->session->set_userdata('files', $_POST['file']);
						$this->session->set_userdata('operation', $_POST['operation']);
						$this->session->set_userdata('from_dir', $_POST['dir']);
                        break;

                  case 'chown':
                        $this->lang->load('owner', $this->language);
                        $this->mod_users->check_admin();
                        if (isset($_POST['newowner']) && is_numeric($_POST['newowner']))
                            $this->mod_quizpath->chown_files($_POST['file'], intval($_POST['newowner']));
                        break;
                        
                  case 'delete':
                        $this->mod_users->check_teacher();
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
            $this->mod_users->check_teacher();

            if ($this->session->userdata('files')===null ||
                $this->session->userdata('operation')===null ||
                $this->session->userdata('from_dir')===null)
                throw new DataException($this->lang->line('missing_src_info'));
            if (!isset($_GET['dir']))
                throw new DataException($this->lang->line('missing_dest_info'));

            $this->mod_quizpath->init($_GET['dir'], true, false); // Destination

            $this->load->model('mod_quizpath','mod_source_quizpath');
            $this->mod_source_quizpath->init($this->session->userdata('from_dir'), true, false); // Source

            $fileowner = array();

            foreach ($this->session->userdata('files') as $f) {
                if (file_exists($this->mod_quizpath->get_absolute() . '/' . $f))
                    throw new DataException(sprintf($this->lang->line('file_exists'), $f)
                                            .' '. ($this->session->userdata('operation')==='copy'
                                               ? $this->lang->line('file_exists_copied')
                                               : $this->lang->line('file_exists_moved')));

                // $fileowner will hold the original owner of moved files.
                // This is used to set the owner at the destination, because if an administrator
                // is moving files, the ownership should not change
                if ($this->session->userdata('operation') === 'move') {
                    $owner = $this->mod_source_quizpath->get_excercise_owner($f);
                    $fileowner[$f] = $owner;
                    if ($owner!=$this->mod_users->my_id() && !$this->mod_users->is_admin()) {
                        if (count($this->session->userdata('files'))==1)
                            throw new DataException($this->lang->line('not_owner'));
                        else
                            throw new DataException($this->lang->line('not_owner_all'));
                    }
                }
            }

            foreach ($this->session->userdata('files') as $f)
                if (!@copy($this->mod_source_quizpath->get_absolute() . '/' . $f,
                          $this->mod_quizpath->get_absolute() . '/' . $f))
                    throw new DataException(sprintf($this->lang->line('cannot_copy'), $f));
                else {
                    if ($this->session->userdata('operation') === 'move')
                        $this->mod_quizpath->set_owner($fileowner[$f], $f);
                    else
                        $this->mod_quizpath->set_owner($this->mod_users->my_id(), $f);
                }

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
            $this->mod_users->check_teacher();

            $this->session->unset_userdata(array('files'=>'', 'operation'=>'', 'from_dir'=>''));

            $this->show_files();
        }
        catch (DataException $e) {
            $this->error_view($e->getMessage(), $this->lang->line('cancel_copy_title'));
        }
    }

    public function upload_files() {
        try {
            $this->mod_users->check_teacher();

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
            $this->load->view('view_main_page', array('left_title' => $this->lang->line('upload_exercise_files1'),
                                                      'left' => '<p>'.$this->lang->line('upload_exercise_files2').'</p>'
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

    // Dummy validation function
    public function always_true($field) {
        return true;
    }

    public function edit_visibility() {
        try {
            $this->mod_users->check_teacher();

            $this->mod_quizpath->init(set_or_default($_GET['dir'], ''), true, false);

            if ($this->mod_quizpath->is_top())
                throw new DataException($this->lang->line('cannot_change_visibility_top'));


            $this->load->model('mod_classes');
            $this->load->model('mod_classdir');
            $all_classes = $this->mod_classes->get_all_classes();
            $old_classes = $this->mod_classdir->get_classes_for_dir($this->mod_quizpath->get_relative());

            $this->load->helper('form');
            $this->load->library('form_validation');

            $this->form_validation->set_rules('inclass[]', '', 'callback_always_true');  // Dummy rule. At least one rule is required
            
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
             
                $this->load->view('view_main_page', array('left_title' => $this->lang->line('control_visibility'),
                                                          'left' => sprintf($this->lang->line('visibility_folder_desc'),
                                                                            $this->mod_quizpath->get_relative()),
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
            $this->mod_users->check_teacher();
        
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
            $contents = file_get_contents($this->mod_quizpath->get_absolute());

            echo $contents;
        }
        catch (DataException $e) {
            $this->error_view($e->getMessage(), $this->lang->line('download_exercise'));
        }
    }

    public function rename_file() {
        try {
            $this->mod_users->check_teacher();
        
            if (!isset($_POST['dir']))
                throw new DataException($this->lang->line('missing_folder_name'));
            if (!isset($_POST['oldname']))
                throw new DataException($this->lang->line('missing_old_filename'));
            if (!isset($_POST['newname']))
                throw new DataException($this->lang->line('missing_new_filename'));

            $newname = trim($_POST['newname']);

            if (preg_match('|[/?*;{}"\'\\\\]|',$newname))
                throw new DataException($this->lang->line('illegal_char_filename'));
            
            $this->load->model('mod_quizpath');
            $this->mod_quizpath->init(rawurldecode($_POST['dir']), true, false);
            $owner = $this->mod_quizpath->get_excercise_owner($_POST['oldname'] . '.3et');

            if ($owner!=$this->mod_users->my_id() && !$this->mod_users->is_admin())
                throw new DataException($this->lang->line('not_owner'));

            $this->mod_quizpath->rename($_POST['oldname'], $newname);

            redirect("/file_manager?dir={$this->mod_quizpath->get_relative()}");
        }
        catch (DataException $e) {
            $this->error_view($e->getMessage(), $this->lang->line('rename_exercise'));
        }
    }

    public function update_ownership() {
        try {
            $this->mod_users->check_admin();

            $this->load->model('mod_quizpath');

            $added = array();
            $deleted = array();
            $this->mod_quizpath->fix_exerciseowner($added, $deleted);

            // VIEW:
            $this->load->view('view_top1', array('title' => $this->lang->line('file_mgmt')));
            $this->load->view('view_top2');
            $this->load->view('view_menu_bar', array('langselect' => true));
                
            $center_text = $this->load->view('view_update_ownership',
                                             array('added' => $added,
                                                   'deleted' => $deleted),
                                             true);
             
            $this->load->view('view_main_page', array('left_title' => $this->lang->line('ownership_updated'),
                                                      'center' => $center_text));
            $this->load->view('view_bottom');
        }
        catch (DataException $e) {
            $this->error_view($e->getMessage(), $this->lang->line('file_mgmt'));
        }
    }
    
    
    public function edit_exam() {
			$this->mod_users->check_teacher();
    	
         $this->load->model('mod_askemdros');
         $this->load->model('mod_localize');
    	 	
    	 	$javascripts = array('jstree/jquery.jstree.js',
                                 'ckeditor/ckeditor.js',
                                 'ckeditor/adapters/jquery.js',
                                 'js/editquiz.js');
			// View
			$this->load->view('view_top1', array('title' => $this->lang->line('edit_exam'),
															 'css_list' => array('styles/jstree.css'),
															 'js_list' => $javascripts));
			$this->load->view('view_font_css', array('fonts' => $this->mod_askemdros->font_selection));
			$this->load->view('view_top2');
			$this->load->view('view_menu_bar', array('langselect' => false));
			$this->load->view('view_alert_dialog');
			
			$center_text = $this->load->view('view_edit_exam',
														array('decoded_3et_json' => json_decode($this->mod_askemdros->decoded_3et),
																'dbinfo_json' => $this->mod_askemdros->dbinfo_json,
																'l10n_json' => $this->mod_askemdros->l10n_json,
																'l10n_js_json' => $this->mod_localize->get_json(),
																'typeinfo_json' => $this->mod_askemdros->typeinfo_json,
																'universe' => json_encode($this->mod_askemdros->universe),
																'dir' => dirname($_GET['exam']),
																'exam' => basename($_GET['exam'])),
														true);
			$this->load->view('view_main_page', array('left_title' => $this->lang->line('edit_exam'),
																	'left' => "TEST LEFT",
																	
																	'center' => $center_text));
			$this->load->view('view_bottom');
			   
    }

}









