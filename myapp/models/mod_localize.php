<?php

// General (database-independent) localization information
class Mod_localize extends CI_Model {
    private $js_l10n;

    public function __construct() {
        parent::__construct();
        $this->js_l10n = $this->lang->load('js', $this->language, true);
    }

    public function get_json() {
        return json_encode($this->js_l10n);
    }
  }