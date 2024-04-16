<?php
$menu = array(
    'User&rsquo;s Guide' => array('intro'              => array(0,'Introduction'),
                                  'user_interface'     => array(0,'User interface'),
                                  'viewing_text'       => array(0,'Viewing text'),
                                  'viewing_text2/heb'  => array(1,'Viewing Hebrew text'),
                                  'viewing_text2/gr'   => array(1,'Viewing Greek text'),
                                  'logging_in'         => array(0,'Logging in'),
                                  'terminology'        => array(0,'Terminology'),
                                  'fontpref'           => array(0,'Font preferences'),
                                  'uprof'              => array(0,'User profile'),
                                  'variant'            => array(0,'Translation variants'),
                                  'link_icons'         => array(0,'Link icons'),
    ),

    'Student&rsquo;s Guide' => array('running_exercises' => array(0,'Running exercises'),
                                     'firstex/heb'       => array(1,'Example: First Hebrew exercise'),
                                     'secondex/heb'      => array(1,'Example: Second Hebrew exercise'),
                                     'thirdex/heb'       => array(1,'Example: Third Hebrew exercise'),
                                     'firstex/gr'        => array(1,'Example: First Greek exercise'), 
                                     'secondex/gr'       => array(1,'Example: Second Greek exercise'),
                                     'thirdex/gr'        => array(1,'Example: Third Greek exercise'), 
                                     'variations'        => array(0,'Variations to exercises'),
                                     'answer_types'      => array(0,'Answering various types of questions'),
                                     'shortcuts'         => array(0,'How to use shortcuts'),
                                     'mystat'            => array(0,'How am I doing?'),
                                     'enroll'            => array(0,'Class membership'),
        ),

    'Teacher&rsquo;s Guide'  => array('teacher'             => array(0,'Features for teachers'),
                                      'usermgmt'            => array(0,'User management'),
                                      'classes'             => array(0,'Class management'),
                                      'folders'             => array(0,'Folder management'),
                                      'exercise_mgmt'       => array(0,'Exercise management'),
                                      'create_firstex/heb'  => array(1, 'Example: Create a simple Hebrew exercise'),
                                      'create_secondex/heb' => array(1, 'Example: Create an advanced Hebrew exercise'),
                                      'create_firstex/gr'   => array(1, 'Example: Create a simple Greek exercise'),
                                      'create_secondex/gr'  => array(1, 'Example: Create an advanced Greek exercise'),
                                      'tabs'                => array(0,'Tabs'),
                                      'description'         => array(1,'The “Description” tab'),
                                      'passages'            => array(1,'The “Passages” tab'),  
                                      'sentences'           => array(1,'The “Sentences” tab'), 
                                      'mql'                 => array(2,'MQL'),
                                      'sentence_units'      => array(1,'The “Sentence Units” tab'),
                                      'features'            => array(1,'The “Features” tab'),
                                      'studentstat'         => array(0,'How are my students doing?'),
                                      'gloss_links'         => array(0,'Gloss links'),
        ),

    'Translator&rsquo;s Guide' => array('translator'   => array(0,'Introduction for translators'),
                                        'avail_trans'  => array(0,'Available translations'),
                                        'tr_ifgr/if'   => array(0,'Interface translation'),
                                        'tr_ifgr/gr'   => array(0,'Grammar translation'),
                                        'tr_lex'       => array(0,'Lexicon translation'),
                                        'down_lex'     => array(0,'Download lexicon'),
        ),

    'Exams' => array('exam_introduction'   => array(0,'Introduction to Exams'),
        'AU_exam_hebrewI'  => array(1,'Andrews University Final Exam: Hebrew I'),
        'AU_exam_hebrewII'  => array(1,'Andrews University Final Exam: Hebrew II'),
        'AU_qualifier_hebrew'  => array(1,'Andrews University Hebrew Placement Exam'),
        'AU_exam_greek'  => array(1,'Andrews University final Greek Exam'),
        'AU_qualifier_greek'  => array(1,'Andrews University Greek Placement Exam'),        
        'UBS_exam_hebrew'  => array(1,'United Bible Society Hebrew Exam'),
        'exam_location'   => array(0,'Where are the Exams?'),
        'exam_creation'   => array(0,'Creating Exams'),
        ),

    );
?>

<div class="accordion" id="accordionExample2">

    <?php $ix = 0; ?>
    <?php foreach ($menu as $heading => $submenu): ?>
        <button class="ml-0 btn btn-block text-left" type="button" data-toggle="collapse" data-target="#target-<?= $ix ?>"  ><?= $heading ?></button>
        <div class="pl-1 collapse <?= array_key_exists($current,$submenu) ? 'show' : '' ?>" id="target-<?= $ix ?>" data-parent="#accordionExample2">
            <?php foreach ($submenu as $article => $disp): ?>
                <?php list($level, $text) = $disp; ?>
                <p class="mb-0 level<?=$level?>"><?php
                                                 if ($article!=$current)
                                                     echo help_anchor($article,$text);
                                                 else
                                                     echo $text;
                                                 ?></p>
            <?php endforeach; ?>
        </div>
        <?php ++$ix; ?>
    <?php endforeach; ?>
</div>
