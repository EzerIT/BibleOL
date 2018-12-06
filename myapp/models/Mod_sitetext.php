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
  }