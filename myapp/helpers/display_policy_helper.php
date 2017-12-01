<?php

// This is common code to display a new policy from either Ctrl_login.php or Ctrl_oauth2.php

function display_new_policy($thiscopy) {
    $thiscopy->lang->load('privacy', $thiscopy->language);

    $acceptance_code = $thiscopy->mod_users->generate_acceptance_code();

    // VIEW:
    $thiscopy->load->view('view_top1', array('title' => $thiscopy->lang->line('policy')));
    $thiscopy->load->view('view_top2');
    $thiscopy->load->view('view_menu_bar', array('langselect' => true));
    $center_text = $thiscopy->load->view('view_accept_policy', array('acceptance_code' => $acceptance_code,
                                                                 'user_id' => $thiscopy->mod_users->my_id()), true);

    $thiscopy->load->view('view_main_page', array('left_title' => $thiscopy->lang->line('new_privacy_header'),
                                              'left' => $thiscopy->lang->line('new_privacy_intro'),
                                              'center' => $center_text));
        
    $thiscopy->load->view('view_bottom');
  }
