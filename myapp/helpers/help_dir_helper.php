<?php

class HelpDir {
    private $dir;
    private $sub_article;

    // Constructor.
    // Parameter:
    //   $dir         The directory containing the files to load.
    //   $sub_article The last component of the help page URL
    public function __construct(string $dir, $sub_article) {
        $this->dir = site_url($dir);
        $this->sub_article = $sub_article;
    }

    // Function to display a centered image. For use in the help pages.
    // Parameter:
    //    $src   Image file relative to "$this->dir/images"
    public function img(string $src) {
        return "<p><a href=\"$this->dir/images/$src\" target=\"_blank\"><img class=\"mx-auto img-fluid d-block border border-info\" alt=\"Bible OL\" src=\"$this->dir/images/$src\"></a></p>\n";
    }

    public function get_dir() {
        return $this->dir;
    }

    // Select Hebrew or Greek version
    public function heb_gr(string $hebtext, string $grtext) {
        switch ($this->sub_article) {
            case 'heb':
                return $hebtext;
            case 'gr':
                return $grtext;
        }
        return "<strong>Error in help file</strong>\n";
    }

    // Select interface or grammar version
    public function if_gr(string $if_text, string $gr_text) {
        switch ($this->sub_article) {
            case 'if':
                return $if_text;
            case 'gr':
                return $gr_text;
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

function make_footnote($text,$note) {
    return "<a href=\"#\" data-toggle=\"tooltip\" title=\"$note\">$text</a>";
}

function help_anchor($subject, $title, $attributes = '') {
	return anchor("help/show_help/$subject",$title,$attributes);
}
