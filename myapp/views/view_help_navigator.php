<?php
$menu = array(
    'User&rsquo;s Guide' => array('intro'              => array(0,'Introduction'),
                                  'user_interface'     => array(0,'User interface'),
                                  'viewing_text'       => array(0,'Viewing text'),
                                  'viewing_text2/heb'  => array(1,'Viewing Hebrew text'),
                                  'viewing_text2/gr'   => array(1,'Viewing Greek text'),
                                  'logging_in'         => array(0,'Logging in'),
    ),

    'Student&rsquo;s Guide' => array('running_exercises' => array(0,'Running exercises'),
                                     'firstex/heb'       => array(1,'Hebrew: First example'),
                                     'secondex/heb'      => array(1,'Hebrew: Second example'),
                                     'thirdex/heb'       => array(1,'Hebrew: Third example'),
                                     'firstex/gr'        => array(1,'Greek: First example'),
                                     'secondex/gr'       => array(1,'Greek: Second example'),
                                     'thirdex/gr'        => array(1,'Greek: Third example'),
                                     'variations'        => array(0,'Variations to exercises'),
                                     'answering_glosses' => array(0,'Answering gloss requests'),
                                     'statistics'        => array(0,'How am I doing?'),
                                     'enroll'            => array(0,'Enrolling in a class'),
        ),

    'Teacher&rsquo;s Guide'  => array('teacher'          => array(0,'Features for teachers'),
                                      'classes'          => array(1,'Class management'),
                                      'folders'          => array(1,'Folder management'),
                                      'exercise_mgmt'    => array(1,'Exercise management'),
                                      'create_firstex/heb' => array(2, 'Create a simple Hebrew exercise'),
                                      'create_secondex/heb' => array(2, 'Create an advanced Hebrew exercise'),
                                      'create_firstex/gr' => array(2, 'Create a simple Greek exercise'),
                                      'create_secondex/gr' => array(2, 'Create an advanced Greek exercise'),
                                      'monitor_students' => array(1,'Monitor students'),
                                      'create_exams'     => array(1,'Exam management'),
        ),


    'Reference Guide' => array('modes'         => array(0,'Learner vs. Facilitator Mode'),
                               'menus'         => array(0,'Menus'),
                               'pref'          => array(0,'Program preferences'),
                               'tabs'          => array(0,'Tabs'),
                               'description'   => array(1,'The “Description” tab'),
                               'passages'      => array(1,'The “Passages” tab'),  
                               'sentences'     => array(1,'The “Sentences” tab'), 
                               'featurechoice' => array(2,'Choosing features'),
                               'mql'           => array(2,'MQL'),
                               'sentence_units'=> array(1,'The “Sentence Units” tab'),
                               'features'      => array(1,'The “Features” tab'),
                               'qpanel'        => array(0,'Question window'),
                               'greekkey'      => array(0,'Typing Greek'),
                               'hebrewkey'     => array(0,'Typing Hebrew'),
        ),

    'Installation and License' => array('installation' => array(0,'System requirements and installation'),
                                        'license'      => array(0,'License and acknowledgements'),
        ),

    'Contact' => array('contact' => array(0,'Email addresses'),
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
                                                     echo anchor(site_url("help/show_help/$article"),$text);
                                                 else
                                                     echo $text;
                                                 ?></p>
            <?php endforeach; ?>
        </div>
        <?php ++$ix; ?>
    <?php endforeach; ?>
</div>
