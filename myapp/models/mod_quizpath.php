<?php

class Mod_quizpath extends CI_Model {
    private $quizpath;
    private $qpl1; // Length of $quizpath + 1

    private $canonical_relative;
    private $canonical_absolute;

    private $check_access;
    private $users_classes;

    public function __construct() {
        parent::__construct();
    }

    public function init(string $path, boolean $must_be_dir, boolean $check_access, $must_exist = true) {
        $path = rtrim($path, '/');

        $this->quizpath = realpath('quizzes');
        if (!$this->quizpath)
            throw new DataException("Bad directory name: quizzes");

        $this->qpl1 = strlen($this->quizpath)+1;
        $this->check_access = $check_access;

        $this->canonical_absolute = $this->rel2abs($path, $must_exist); // Throws exception for non-existant directory name

        // Verify that we are below the quizzes directory
        if (strpos($this->canonical_absolute, $this->quizpath)!==0)
            throw new DataException("Illegal directory: $path");

        // Make $this->canonical_relative the relative directory name with . and .. removed and
        // terminated by a slash if it is not ''
        $this->canonical_relative = $this->abs2rel($this->canonical_absolute, $must_exist); // Handles . and ..

        // Verify that this is a directory, if required
        if ($must_be_dir && !is_dir($this->canonical_absolute)) 
            throw new DataException("Not a directory: $path");

        // Verify that we have access to this directory
        if ($check_access) {
            $this->load->model('mod_classdir');
            $this->load->model('mod_userclass');

            $this->users_classes = $this->mod_userclass->get_classes_for_user( $this->mod_users->my_id() );
            if (!$this->mod_classdir->may_access($this->canonical_relative, $this->users_classes))
                throw new DataException("Access denied to $path");
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

    
    public function dirlist(boolean $no_3et) {
        $this->load->helper('directory');
        $d = directory_map($this->canonical_absolute, 2); // A value of 2 allows us to recognize directories

        $files = array();
        $directories = array();
        $dir_is_empty = array();

        foreach ($d as $ix => $nam)
            if (is_array($nam)) {
                $directories[] = $ix;
                $dir_is_empty[$ix] = count($nam)===0;
            }
            else {
                if (self::endswith_nocase($nam,'.3et'))
                    $files[] = $no_3et ? str_replace('.3et','',$nam) : $nam;
            }

        sort($files);
        sort($directories);

        if ($d===false)
            throw new DataException("Illegal directory: $dirname");

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

    public function is_top() {
        return $this->canonical_absolute === $this->quizpath;
    }


    public function mkdir(string $dir) {
        $res = @mkdir("$this->canonical_absolute/$dir");
        if (!$res)
            throw new DataException("Cannot create folder '$dir'");
    }

    public function rename(string $oldname, string $newname) {
        $oldname .= '.3et';
        $newname .= '.3et';
        if (file_exists("$this->canonical_absolute/$newname"))
            throw new DataException("$newname already exists");
        else
            if (!@rename("$this->canonical_absolute/$oldname","$this->canonical_absolute/$newname"))
                throw new DataException("Cannot rename '$oldname' to '$newname'");

    }

    public function rmdir(string $dir) {
        $relativedir = $this->abs2rel("$this->canonical_absolute/$dir", true); // We must calculate this before the directory is removed

        $res = @rmdir("$this->canonical_absolute/$dir");
        if (!$res)
            throw new DataException("Cannot remove folder '$dir'");
        
        $this->load->model('mod_classdir');
        $this->mod_classdir->rmdir($relativedir);
    }

    public function delete_files(array $files) {
        foreach ($files as $f) {
            $res = @unlink("$this->canonical_absolute/$f");
            if (!$res)
                throw new DataException("Cannot delete file '$f'");
        }
    }

    // $path is absolute
    private function abs2rel(string $path, boolean $must_exist) {
        $real_path = realpath($path);
        if (!$real_path) {
            if ($must_exist)
                throw new DataException("Bad file path: $path");

            $real_path = realpath(dirname($path));
            if (!$real_path)
                throw new DataException("Bad file path: $path");

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
            throw new DataException("Bad file path: $path");

        $real_path = realpath(dirname("quizzes/$path"));
        if ($real_path)
            return $real_path . '/' . basename($path);

        throw new DataException("Bad file path: $path");
    }

    private static function endswith_nocase(string $haystack, string $needle) {
        return strcasecmp(substr($haystack, -strlen($needle)),$needle)===0;
    }
  }
