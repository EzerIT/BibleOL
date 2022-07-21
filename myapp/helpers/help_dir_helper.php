<?php

class HelpDir {
    private $dir;
    private $hebgr;

    // Constructor.
    // Parameter:
    //   $dir    The directory containing the files to load.
    public function __construct(string $dir, $hebgr) {
        $this->dir = site_url($dir);
        $this->hebgr = $hebgr;
    }

    // Function to display a centered image. For use in the help pages.
    // Parameter:
    //    $src   Image file relative to "$this->dir/images"
    public function disp(string $src) {
        return "<p><a href=\"$this->dir/images/$src\" target=\"_blank\"><img class=\"mx-auto img-fluid d-block border border-info\" alt=\"Bible OL\" src=\"$this->dir/images/$src\"></a></p>\n";
    }

    public function get_dir() {
        return $this->dir;
    }
    
    public function heb_gr(string $hebtext, string $grtext) {
        switch ($this->hebgr) {
            case 'heb':
                return $hebtext;
            case 'gr':
                return $grtext;
        }
        return "<strong>Error in help file</strong>\n";
    }

    public function heb(string $hebtext) {
        return $this->heb_gr($hebtext,'');
    }

    public function gr(string $grtext) {
        return $this->heb_gr('',$grtext);
    }
}

