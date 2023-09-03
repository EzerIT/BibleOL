<?php

class Mod_intro_text extends CI_Model {
    public function __construct() {
        parent::__construct();
        $this->lang->load('intro_text', $this->language);
    }

    function center_text() {
//        return $this->lang->line('intro_center');
        switch ($this->language) {
            case 'da':
                return <<<END
<h1>Advarsel!</h1>

<p>Bible Online Learner flytter snart til en ny server. Det betyder at vi den 8. september 2023 begynder nedlukningen af den server du bruger nu.</p>
<p>Du vil fortsat kunne benytte denne server nogen tid endnu, men alt hvad du gemmer på serveren (såsom nye brugerkonti, resultater af øvelser m.m.) fra og med den 8. september vil gå tabt når der skiftes til den nye server.</p>
<p>Du kan se <a href="
END . site_url("flytning.html") . <<<END
">flere oplysninger her</a>.</p>
END;
                break;
            default:
                return <<<END
<h1>Warning!</h1>
<p>Bible Online Learner will soon move to a new server. This means that on 8 September 2023 we start closing the server you are currently using.</p>
<p>You will still be able to use this server for some time, but everything you save on this server (such as, new user accounts, results of exercises etc.) from 8 September and onward will be lost when we move to the new server.</p>
<p>You can read <a href="
END . site_url("moving.html") . <<<END
">more information here</a>.</p>
END;
                break;
        }
        return '';
    }

    function left_text_title() {
        $name = $this->mod_users->my_name();

        return empty($name) ? $this->lang->line('welcome')
                            : sprintf($this->lang->line('welcome2'), $name);
    }

    function left_text() {
        return '<p class="centeralign"><img alt="" src="images/BibleOL.png"></p>';
    }
}
