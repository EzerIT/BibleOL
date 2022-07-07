<?php

class Mod_config extends CI_Model {
    public function __construct() {
        parent::__construct();
    }

    public function alphabets() {
        $query = $this->db->select('id,name')->get('alphabet');
        $data = array();
        foreach ($query->result() as $row)
            $data[$row->id] = $row->name;
        return $data;
    }

    public function font_setting(string $alphabet) {
        $query = $this->db->from('alphabet')->join('font','font.alphabet_id=alphabet.id')
            ->where('alphabet.name',$alphabet)->where('user_id',$this->mod_users->my_id())->get();

        $res = $query->result();
        if (empty($res)) {
            // No user configuration, use default configuration
            $query = $this->db->from('alphabet')->join('font','font.alphabet_id=alphabet.id')
                ->where('alphabet.name',$alphabet)->where('user_id',0)->get();
            $res = $query->result();
        }
            
        return $res[0];
    }

    public function avail_fonts(string $alphabet) {
        switch ($alphabet) {
          case 'hebrew':
                $data = array(array('Ezra SIL Webfont',true),
                              array('Frank Ruehl CLM Webfont',true),
                              array('David CLM Webfont',true),
                              array('Times New Roman',false),
                              array('Arial',false),
                    );
                break;

          case 'hebrew_translit':
                $data = array(array('Doulos SIL Webfont',true),
                              array('Segoe UI', false),
                              array('Times New Roman', false),
                              array('Arial',false),
                    );
                break;

          case 'greek':
                $data = array(array('Galatia SIL Webfont',true),
                              array('Gentium Plus Webfont',true),
                              array('Segoe UI', false),
                              array('Times New Roman', false),
                              array('Arial',false),
                    );
                break;

          case 'latin':
                $data = array(array('Titillium',true),
                              array('Times New Roman', false),
                              array('Arial',false),
                    );
                break;
        }
        return $data;
    }

    public function get_radio_button_value(string $chosen_font, array $avail_fonts, string $my_font) {
        for ($ix=0; $ix<count($avail_fonts); ++$ix)
            if ($avail_fonts[$ix][0]==$chosen_font)
                return $ix;

        if ($my_font==$chosen_font)
            return 'mine';

        return 'none';
    }

    public function personal_font(string $alphabet) {
        $query = $this->db->from('alphabet')->join('personal_font','personal_font.alphabet_id=alphabet.id')
            ->where('alphabet.name',$alphabet)->where('user_id',$this->mod_users->my_id())->get();

        if ($query->num_rows() == 0)
            return '';
        return $query->row()->font_family;
    }

    private static function is_set_on(&$p) {
        return isset($p) && $p==='on';
    }

    public function set_font(array $alphabets, array $font_setting, array $avail_fonts, array $post) {
/* Array (
    [hebrewchoice] => hebrew_1
    [hebrew_myfont] => Courier New

    )*/
        $this->load->helper('varset');
        foreach ($alphabets as $alphid => $alph) {
            $use_personal_font = false;

            if (isset($post[$alph . 'choice'])) {
                $font_ix = end(explode('_',$post[$alph . 'choice']));
                if ($font_ix==='mine') {
                    $font_family = $post[$alph . '_myfont'];
                    $use_personal_font = true;
                }
                else
                    $font_family = $avail_fonts[$alph][$font_ix][0];
            }
            else
                $font_family = $font_setting[$alph]->font_family;

            $record = array('font_family' => $font_family,
                            'text_size' => $post[$alph . 'textsize'],
                            'text_italic' => is_set_on($post[$alph . 'textitalic']),
                            'text_bold' => is_set_on($post[$alph . 'textbold']),
                            'feature_size' => $post[$alph . 'featuresize'],
                            'feature_italic' => is_set_on($post[$alph . 'featureitalic']),
                            'feature_bold' => is_set_on($post[$alph . 'featurebold']),
                            'tooltip_size' => $post[$alph . 'tooltipsize'],
                            'tooltip_italic' => is_set_on($post[$alph . 'tooltipitalic']),
                            'tooltip_bold' => is_set_on($post[$alph . 'tooltipbold']),
                            'input_size' => $post[$alph . 'inputsize'],
                            'input_italic' => is_set_on($post[$alph . 'inputitalic']),
                            'input_bold' => is_set_on($post[$alph . 'inputbold']));

            // Update user preferences
            if ($this->db->from('font')
                ->where('alphabet_id',$alphid)->where('user_id',$this->mod_users->my_id())
                ->count_all_results() == 0) {

                // A record does not exist, insert one.
                $record['user_id'] = $this->mod_users->my_id();
                $record['alphabet_id'] = $alphid;
                $query = $this->db->insert('font', $record);
            }
            else
                // A record does exist, update it.
                $query = $this->db->where('alphabet_id',$alphid)->where('user_id',$this->mod_users->my_id())->update('font', $record);

            if ($use_personal_font) {
                // Update personal font
                if ($this->db->from('personal_font')
                    ->where('alphabet_id',$alphid)->where('user_id',$this->mod_users->my_id())
                    ->count_all_results() == 0)
                    // A record does not exist, insert one.
                    $query = $this->db->insert('personal_font', array('user_id' => $this->mod_users->my_id(),
                                                                      'alphabet_id' => $alphid,
                                                                      'font_family' => $font_family));
                else
                    // A record does exist, update it.
                    $query = $this->db->where('alphabet_id',$alphid)->where('user_id',$this->mod_users->my_id())
                        ->update('personal_font', array('font_family' => $font_family));
            }
        }
    }
  }
