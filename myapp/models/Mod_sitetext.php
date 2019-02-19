<?php

class Mod_sitetext extends CI_Model {
    public function __construct() {
        parent::__construct();

        //$this->config->load('ol');
        $this->load->database();
    }

    public function get_sitetext(string $use, string $language, string $site=null) {
        $query = $this->db
            ->select('text')
            ->where('use',$use)
            ->where('language',$language)
            ->where('site',$site)
            ->get('sitetext');

        $res = $query->row();

        if (empty($res) && $language!='en') {
            // Look for English
            
            $query = $this->db
                ->select('text')
                ->where('use',$use)
                ->where('language','en')
                ->where('site',$site)
                ->get('sitetext');
            $res = $query->row();
        }

        if (empty($res)) {
            // Look for basic site

            $query = $this->db
                ->select('text')
                ->where('use',$use)
                ->where('language',$language)
                ->where('site',null)
                ->get('sitetext');
            $res = $query->row();
        }

        if (empty($res) && $language!='en') {
            // Look for English at basic site
            
            $query = $this->db
                ->select('text')
                ->where('use',$use)
                ->where('language','en')
                ->where('site',null)
                ->get('sitetext');
            $res = $query->row();
        }

        return empty($res) ? '' : $res->text;
    }

    public function get_sitetext_strict(string $use, string $language, string $site=null) {
        $query = $this->db
            ->select('text')
            ->where('use',$use)
            ->where('language',$language)
            ->where('site',$site)
            ->get('sitetext');

        $res = $query->row();
        
        return empty($res) ? '' : $res->text;
    }

    public function set_sitetext(string $use, string $language, string $site=null, string $text) {
        if ($this->db
            ->from('sitetext')
            ->where('use',$use)
            ->where('language',$language)
            ->where('site',$site)
            ->count_all_results() == 0) {
            // A record does not exist, insert one.
            $query = $this->db->insert('sitetext', array('use' => $use,
                                                         'language' => $language,
                                                         'site' => $site,
                                                         'text' => $text));
        }
        else {
            // A record does exist, update it.
            $query = $this->db
                ->where('use',$use)
                ->where('language',$language)
                ->where('site',$site)
                ->update('sitetext', array('text' => $text));
        }
    }
        
  }