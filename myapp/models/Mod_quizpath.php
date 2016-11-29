<?php

class Mod_quizpath extends CI_Model {
    private $quizpath;
    private $qpl1; // Length of $quizpath + 1

    private $canonical_relative;
    private $canonical_relative_slash; // Same as $canonical_relative, but with a / added if not empty
    private $canonical_absolute;

    private $check_access;
    private $users_classes;

    public function __construct() {
        parent::__construct();
        $this->lang->load('file_manager', $this->language);
    }

    public function init(string $path, boolean $must_be_dir, boolean $check_access, $must_exist = true) {
        $path = rtrim($path, '/');

        $this->quizpath = realpath('quizzes');
        if (!$this->quizpath)
            throw new DataException(sprintf($this->lang->line('cannot_open_folder'), 'quizzes'));

        $this->qpl1 = strlen($this->quizpath)+1;
        $this->check_access = $check_access;

        $this->canonical_absolute = $this->rel2abs($path, $must_exist); // Throws exception for non-existant directory name

        // Verify that we are below the quizzes directory
        if (strpos($this->canonical_absolute, $this->quizpath)!==0)
            throw new DataException(sprintf($this->lang->line('illegal_folder'), $path));

        // Make $this->canonical_relative the relative directory name with . and .. removed and
        $this->canonical_relative = $this->abs2rel($this->canonical_absolute, $must_exist); // Handles . and ..
        $this->canonical_relative_slash = $this->canonical_relative==='' ? '' : ($this->canonical_relative . '/');

        // Verify that this is a directory, if required
        if ($must_be_dir && !is_dir($this->canonical_absolute)) 
            throw new DataException(sprintf($this->lang->line('not_a_folder'), $path));

        // Verify that we have access to this directory
        if ($check_access) {
            $this->load->model('mod_classdir');
            $this->load->model('mod_userclass');

            $this->users_classes = $this->mod_userclass->get_classes_for_user( $this->mod_users->my_id() );
            if (!$this->mod_classdir->may_access($this->canonical_relative, $this->users_classes))
                throw new DataException(sprintf($this->lang->line('access_denied_to'), $path));
        }
    }

    public function file_exists() {
        return file_exists($this->canonical_absolute);
    }

    public function get_absolute() {
        return $this->canonical_absolute;
    }

    public function get_relative() {
        return $this->canonical_relative;
    }

    
    public function dirlist(boolean $doing_test) {
        $this->load->helper('directory');
        $d = directory_map($this->canonical_absolute, 2); // A value of 2 allows us to recognize empty directories

        if ($d===false)
            throw new DataException(sprintf($this->lang->line('illegal_folder'), $dirname));

        $files = array();
        $directories = array();
        $dir_is_empty = array();

        foreach ($d as $ix => $nam)
            if (is_array($nam)) {
                $ix = rtrim($ix, '/');
                $directories[] = $ix;
                $dir_is_empty[$ix] = count($nam)===0;
            }
            else {
                if (self::endswith_nocase($nam,'.3et')) {
                    $f = new stdClass;
                    $f->filename = $doing_test ? str_replace('.3et','',$nam) : $nam;
                    $f->userid = null;
                    $files[] = $f;
                }
            }

        sort($files);
        sort($directories);

        // Add owner information
        if (!$doing_test) {
            // We're managing exercise files
            $this->get_excercise_owners($files);
        }

        if ($this->is_top())
            $parentdir = null;
        else
            $parentdir = $this->abs2rel("$this->canonical_absolute/..", true); // Note: $parentdir may be ''


        if ($this->check_access)
            $directories = $this->mod_classdir->filter_directories($directories, $this->canonical_relative, $this->users_classes);

        return array('directories' => $directories,
                     'is_empty'    => $dir_is_empty,
                     'files'       => $files,
                     'parentdir'   => $parentdir,
                     'relativedir' => $this->canonical_relative);
    }


    // Returns owner ID of a file
    // If $filename==null, assume that $this is a file object
    // If $filename!=null, assume that $this is a directory object containing the file
    public function get_excercise_owner(string $filename=null) {
        if (is_null($filename)) {
            assert(is_file($this->canonical_absolute));
            $pathname = $this->canonical_relative;
        }
        else {
            assert(is_dir($this->canonical_absolute));
            $pathname = $this->canonical_relative_slash . $filename;
        }

        $query = $this->db->select('ownerid')->where('pathname', $pathname)->get('exerciseowner');


        if ($row = $query->row())
            return intval($row->ownerid);
        else
            return 0;
    }
    
    // Adds owner information to all file names in $files
    private function get_excercise_owners(array &$files) {
        // Collect all owner IDs
        $owners = array();

        foreach ($files as &$f) {
            $query = $this->db->select('ownerid')->where('pathname', $this->canonical_relative_slash . $f->filename)->get('exerciseowner');
            if ($row = $query->row()) {
                $f->userid = $row->ownerid;
                if ($f->userid!=0 && !in_array($f->userid, $owners))
                    $owners[] = $f->userid;
            }
        }

        // Look up user names
        $people = array();
        foreach ($owners as $o) {
            $query = $this->db->select('first_name, last_name, family_name_first')->where('id',$o)->get('user');
            if ($row = $query->row())
                $people[$o] = make_full_name($row);
        }

        // Update $files array
        foreach ($files as &$f) {
            if (!$f->userid || !isset($people[$f->userid]))
                $f->username = $this->lang->line('no_owner');
            else
                $f->username = $people[$f->userid];
        }
    }

    // Sets owner ID of a file, unless it already has an owner
    // If $filename==null, assume that $this is a file object
    // If $filename!=null, assume that $this is a directory object containing the file
    public function set_owner(integer $owner, string $filename=null) {
        if (is_null($filename)) {
            assert(is_file($this->canonical_absolute));
            $pathname = $this->canonical_relative;
        }
        else {
            assert(is_dir($this->canonical_absolute));
            $pathname = $this->canonical_relative_slash . $filename;
        }

        if ($this->db->from('exerciseowner')->where('pathname', $pathname)->count_all_results() == 0)
            // A record does not exist, insert one.
            $query = $this->db->insert('exerciseowner', array('pathname' => $pathname, 'ownerid' => $owner));
    }


    public function is_top() {
        return $this->canonical_absolute === $this->quizpath;
    }


    public function mkdir(string $dir) {
        $res = @mkdir("$this->canonical_absolute/$dir");
        if (!$res)
            throw new DataException(sprintf($this->lang->line('cannot_create_folder'), $dir));
    }

    public function rename(string $oldname, string $newname) {
        $oldname .= '.3et';
        $newname .= '.3et';
        if (file_exists("$this->canonical_absolute/$newname"))
            throw new DataException(sprintf($this->lang->line('already_exists'), $newname));
        else
            if (!@rename("$this->canonical_absolute/$oldname","$this->canonical_absolute/$newname"))
                throw new DataException(sprintf($this->lang->line('cannot_rename'), $oldname, $newname));

        $query = $this->db->where('pathname', "$this->canonical_relative_slash$oldname")->update('exerciseowner',
                                                                           array('pathname' => "$this->canonical_relative_slash$newname"));
    }

    public function rmdir(string $dir) {
        $relativedir = $this->abs2rel("$this->canonical_absolute/$dir", true); // We must calculate this before the directory is removed

        $res = @rmdir("$this->canonical_absolute/$dir");
        if (!$res)
            throw new DataException(sprintf($this->lang->line('cannot_delete_folder'), $dir));
        
        $this->load->model('mod_classdir');
        $this->mod_classdir->rmdir($relativedir);
    }

    public function check_delete_files(array $files) {
        foreach ($files as $f) {
            $owner = $this->get_excercise_owner($f);

            if ($owner!=$this->mod_users->my_id() && !$this->mod_users->is_admin()) {
                if (count($files)==1)
                    throw new DataException($this->lang->line('not_owner'));
                else
                    throw new DataException($this->lang->line('not_owner_all'));
            }
        }
    }

    public function delete_files(array $files) {
        $this->check_delete_files($files);

        foreach ($files as $f) {
            $res = @unlink("$this->canonical_absolute/$f");
            if (!$res)
                throw new DataException(sprintf($this->lang->line('cannot_delete_file'), $f));

            $this->db->where('pathname', $this->canonical_relative_slash . $f)->delete('exerciseowner');
        }
    }

    // $path is absolute
    private function abs2rel(string $path, boolean $must_exist) {
        $real_path = realpath($path);
        if (!$real_path) {
            if ($must_exist)
                throw new DataException(sprintf($this->lang->line('cannot_open_file'), $path));

            $real_path = realpath(dirname($path));
            if (!$real_path)
                throw new DataException(sprintf($this->lang->line('cannot_open_file'), $path));

            $real_path .= '/' . basename($path);
        }

        $stripped_path = substr($real_path, $this->qpl1);
        if ($stripped_path===false)
            return '';
        else
            return str_replace('\\','/',$stripped_path);
    }

    // $path is relative
    private function rel2abs(string $path, boolean $must_exist) {
        $real_path = realpath("quizzes/$path");
        if ($real_path)
            return $real_path;

        if ($must_exist)
            throw new DataException(sprintf($this->lang->line('cannot_open_file'), $path));

        $real_path = realpath(dirname("quizzes/$path"));
        if ($real_path)
            return $real_path . '/' . basename($path);

        throw new DataException(sprintf($this->lang->line('cannot_open_file'), $path));
    }

    private static function endswith_nocase(string $haystack, string $needle) {
        return strcasecmp(substr($haystack, -strlen($needle)),$needle)===0;
    }

    private function create_exercise_entry($dirtree, $dir, &$added) {
        // $dir ends in a slash unless it is empty
        foreach ($dirtree as $ix => $nam) {
            if (is_array($nam))
                $this->create_exercise_entry($nam, $dir . $ix, $added);
            else
                if (self::endswith_nocase($nam,'.3et')) {
                    if ($this->db->from('exerciseowner')->where('pathname', $dir . $nam)->count_all_results() == 0) {
                        // The exercise is not in the exerciseowner table
                        $this->db->insert('exerciseowner', array('pathname' => $dir . $nam, 'ownerid' => 0));
                        $added[] = $dir . $nam;
                    }
                }
        }
    }


    // Loop through all exercises and ensure that
    //   1) existing exercises have an entry in the exerciseowner table, and
    //   2) non-existing exercises do not have an entry in the exerciseowner table.
    public function fix_exerciseowner(&$added, &$deleted) {
        $quizpath = realpath('quizzes');
        $this->load->helper('directory');
        $d = directory_map("$quizpath");

        // Add entries to exerciseowner as needed
        $this->create_exercise_entry($d, '', $added);

        // Delete entries from exerciseowner as needed
        $query = $this->db->get('exerciseowner');

        foreach ($query->result() as $row) {
            if (!file_exists('quizzes/' . $row->pathname)) {
                $this->db->where('id', $row->id)->delete('exerciseowner');
                $deleted[] = $row->pathname;
            }
        }
    }

    public function chown_files(array $files, integer $userid) {
        $query = $this->db->where("`id`=$userid AND (`isteacher`=1 OR `isadmin`=1)",null,false)->get('user');
        if ($row = $query->row()) {
            // User exists and is a teacher
            foreach ($files as $f) {
                if ($this->db->from('exerciseowner')->where('pathname', $this->canonical_relative_slash . $f)
                    ->count_all_results() == 0)
                    // A record does not exist, insert one.
                    $query = $this->db->insert('exerciseowner', array('pathname' => $this->canonical_relative_slash . $f,
                                                                      'ownerid' => $userid));
                else
                    // A record does exist, update it.
                    $query = $this->db->where('pathname', $this->canonical_relative_slash . $f)->update('exerciseowner', array('ownerid' => $userid));
            }
        }
        else
            throw new DataException($this->lang->line('not_teacher'));
    }

  }
