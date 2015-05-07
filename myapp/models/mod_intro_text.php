<?php

class Mod_intro_text extends CI_Model {
    public function __construct() {
        parent::__construct();
    }

    function center_text() {
        return
            '<p>Bible Online Learner (Bible OL) gives free access to the Hebrew Bible in the ETCBC4 database and
             a number of exercises which are linked to 12 introductory lessons <a
             href="http://bh.3bmoodle.dk/course/view.php?id=10" target="_blank">Persuasive Biblical
             Hebrew e-Learning (Login as guest)</a>. You can also test your knowledge of Greek on
             Nestle’s 1904 text of the Greek New Testament with grammatical annotations.</p>

             <p>Bible OL is interlinked with the award-winning Hebrew query and text display site <a
             href="http://shebanq.ancient-data.org" target="shebanq">SHEBANQ</a>. From a chapter in
             Bible OL, click the SHEBANQ logo to jump directly to that chapter on their website.
             Furthermore, teachers can use queries from SHEBANQ to generate exercises in Bible
             OL.</p>

             <p>The source code for Bible OL is available at <a
             href="https://github.com/EzerIT/BibleOL" target="_blank">GitHub</a>. We hope developers
             will fork this code and join us to add additional features.</p>';
    }

    function right_text() {
        return
            '<h1>Bible Online Learner for free global learning</h1>

             <p>Since 2014, Bible Online Learner is part of the <a href="http://global-learning.org"
             target="_blank">Global Learning Initiative</a>. For more information, please contact
             Professor Nicolai Winther-Nielsen (nwn [at] dbi . edu) of <a
             href="http://fih.fjellhaug.no/om-oss/in-english" target="_blank">Fjellhaug
             International University College Denmark (FIUC-Dk)</a>.<br>
             
             Bible Online Learner was developed by Claus Tøndering of <a href="http://www.ezer.dk"
             target="_blank">Ezer IT Consulting</a>.<br>

             Judith Gottschalk has developed Learning Journey, an associated website with statistics
             on learner performance.</p>

             <img style="float:right;" src="images/eplotlogo.png" alt="Logo">

             <p>Bible Online Learner is based on PLOTLearner, a PC program developed as part of the
             <a href="http://www.eplot.eu" target="_blank">EuroPLOT</a> project 2010-2013. The
             present website is the result of further development by Ezer IT Consulting outside the
             EuroPLOT project, and the site is maintained by <a href="http://3bmoodle.dk"
             target="_blank">3BM Bible Software and Multimedia</a>.</p>

             <img style="float:right;" alt="SHEBANQ Logo" src="images/shebanq_logo55.png">

             <p>The SHEBANQ web site was created by Wido van Peursen and Dirk Roorda.</p>';
    }

    function left_text() {
        $name = $this->mod_users->my_name();

        return
            (is_null($name) ? '<h1>Welcome to Bible Online Learner</h1>' : "<h1>$name, Welcome to Bible Online Learner</h1>")
            . '<p>&nbsp;</p><p class="centeralign"><img alt="" src="images/BibleOL.png"></p>';
    }
}
