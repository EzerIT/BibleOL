<?php

class Mod_intro_text extends CI_Model {
    public function __construct() {
        parent::__construct();
    }

    function center_text() {
        return
            '<p>On this website you have free access to the Hebrew Bible in the ETCBC4 database and exercises
             which are linked to 12 introductory lessons
             <a href="http://bh.3bmoodle.dk/course/view.php?id=10" target="_blank">Persuasive Biblical Hebrew e-Learning
             (Login as guest)</a>. You can also test your knowledge of Greek on Nestle’s 1904 text
             of the Greek New Testament with grammatical annotations. Further support for
             class-enrollment and learner-performance is available in partnerships with Global
             Learning Initiative: contact Nicolai Winther-Nielsen
             (nwn&nbsp;[at]&nbsp;dbi&nbsp;.&nbsp;edu) for further information.</p>

             <img style="float:left;padding-right:5px;" alt="SHEBANQ Logo" src="images/shebanq_logo55.png">

             <p>We are pleased to announce a co-operation with the kind people at <a
             href="http://shebanq.ancient-data.org" target="shebanq">SHEBANQ</a>, who have developed
             an award-winning query and presentation tool based on the ETCBC4 Hebrew database, which we are also
             using. Whenenver you look up a passage in the Hebrew Bible on our website, you will
             find the SHEBANQ logo next to the name of the book. If you click the logo, you will be
             taken to the relevant page at SHEBANQ.</p>';
    }

    function right_text() {
        return
            '<h1>Bible Online Learner for free global learning</h1>

            <p>Since 2014, Bible Online Learner is part of the <a href="http://global-learning.org"
            target="_blank">Global Learning Initiative</a>.<br> Bible Online Learner was developed
            by Claus Tøndering of <a href="http://www.ezer.dk" target="_blank">Ezer IT
            Consulting</a>.</p>

            <img style="float:right;" src="images/eplotlogo.png" alt="Logo">

            <p>Bible Online Learner is based on PLOTLearner, a PC program developed as part of the
            <a href="http://www.eplot.eu" target="_blank">EuroPLOT</a> project 2010-2013.
            PLOTLearner can be downloaded from the <a
            href="http://eplot.3bmoodle.dk/index.php/downloads" target="_blank">EuroPLOT PLOTLearner
            site</a>. Professor of Hebrew Bible and ICT Nicolai Winther-Nielsen of <a
            href="http://fih.fjellhaug.no/om-oss/in-english" target="_blank">Fjellhaug International
            University College Denmark (FIUC-Dk)</a> and Aalborg University coordinated the
            development and testing of persuasive language learning from the Hebrew Bible.</p>
            <p>The present website is the result of further development by Ezer IT Consulting
            outside the EuroPLOT project and the site is maintained by <a href="http://3bmoodle.dk"
            target="_blank">3BM Bible Software and Multimedia</a>.</p>';
    }

    function left_text() {
        $name = $this->mod_users->my_name();

        return
            (is_null($name) ? '<h1>Welcome to Bible Online Learner</h1>' : "<h1>$name, Welcome to Bible Online Learner</h1>")
            . '<p>&nbsp;</p><p class="centeralign"><img alt="" src="images/BibleOL.png"></p>';


    }
}
