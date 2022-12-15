<?php $hg = $hdir->heb_gr('Hebrew','Greek') ?>

<h1>Example: First <?= $hg ?> Exercise</h1>

<p>(You may be interested in a <?= help_anchor('firstex/' . $hdir->heb_gr('gr','heb'),
                                          'corresponding '. $hdir->heb_gr('Greek','Hebrew') . ' exercise')
    ?>.)</p>


<p>Find the folder named &ldquo;<?= $hdir->heb_gr('ETCBC4','Nestle&nbsp1904') ?>&rdquo; and click on
    that. Within that folder, you will find another folder called &ldquo;demo&rdquo;. If you click
    on that, you will see a list of all the exercises found within that folder:</p>

<?= $hdir->img("$sub_article-folder.png") ?>

<p>We will now focus on the exercise called &ldquo;demo1&rdquo;. The person who created this
    exercise will have configured it with a set of Bible passages that should be used for this
    exercises. If you click on one of the numbers 5, 10, or 25 under the heading &ldquo;Select
    number of questions using preset passages&rdquo;, you will start an exercise with 5, 10, or 25
    questions taken from the pre-configured Bible passages. If instead you click on one of the
    numbers 5, 10, or 25 under the heading &ldquo;Select number of questions and specify your own
    passages&rdquo;, you will be allowed to specify the Bible passages yourself.</p>

<p>Click on the number 5 under &ldquo;...preset passages&rdquo; and the exercise will start. The
    system will show you a random sentence from the specified Bible passages, for example, this
    one:</p>

<?= $hdir->img("$sub_article-exer1.png") ?>

<p>At the top of the exercise you see a short description &ndash; in this case &ldquo;Please
  indicate the gender and number of these nouns and pronouns.&rdquo; If you click the button
  labelled &ldquo;Locate&rdquo;, you will learn that this sentence is found in <?=
  $hdir->heb_gr('Genesis 1:6','Matthew 8:26') ?>. As described in <?=
  anchor("help/show_help/viewing_text2/$sub_article#view_grammar", "&ldquo;Viewing $hg Grammar
  Information&rdquo;") ?> you can see more information about each word by pointing to it with your
  mouse (if you are using an ordinary computer), by clicking on it, or by using the
  &ldquo;MyView&rdquo; selector.</p>

<p>In this example, the system has highlighted three nouns or pronouns in purple. Your task is to
    identify the grammatical gender and number of each of these words.</p>

<p>Below the text you see a green bar. This will show your progress through the five sentences of
    this exercise. Below the bar is a box in which you should provide your answers. This answer box
    provides some information about the noun: In this case it its the actual text and the English
    translation of the word. These items are known as the &ldquo;Display features&rdquo;. Below the
    display features is the information that you are expected to provide: In this case it is the
    gender and number of the word. These items are known as the &ldquo;Request features&rdquo;:</p>

<?= $hdir->img("$sub_article-exer1a.png") ?>

<p>The first noun, <?= $hdir->heb_gr('<span class="help hebrew">רָקִ֖יעַ</span>','ἀνέμοις') ?>, is shown
    in the top line of the answer box, and you must identify its gender by clicking either <?=
    $hdir->heb_gr('&ldquo;Masculine&rdquo;, &ldquo;Feminine&rdquo;, or &ldquo;' .
    make_footnote('Other value','&ldquo;Other value&rdquo; is not relevant in this exercise.') .
    '&rdquo;', '&ldquo;' . make_footnote('N/A','That is, &ldquo;Not applicable&rdquo;. This means
    that the word has no gender, which is the case for pronouns such as &ldquo;I&rdquo; and
    &ldquo;you&rdquo;.') .'&rdquo;, &ldquo;Masculine&rdquo;, &ldquo;Feminine&rdquo;, or
    &ldquo;Neuter&rdquo;') ?>.</p>

<p>After making your choices, you may then check your answer by clicking &ldquo;Check answer&rdquo;.
    A correct and a wrong answer looks like this:</p>

<?= $hdir->img("$sub_article-ans1.png") ?>

<p>If you don&rsquo;t know the answer, clicking the &ldquo;Show answer&rdquo; button will display
the correct answer.</p>

<p>In this sentence there are three nouns. We can move on to the next noun, <?= $hdir->heb_gr('<span
    class="help hebrew">תֹ֣וךְ</span>','θαλάσσῃ') ?>, by clicking the &#10095; symbol at the right side
    of the answer box. A &#10094; symbol will then appear at the left side of the answer box,
    allowing you to move back to the previous word.</p>

<p>When the last word in the sentence, <?= $hdir->heb_gr('<span class="help hebrew">מָּ֑יִם</span>','γαλήνη') ?>,
    is shown, three buttons appear below the answer box:</p>

<?= $hdir->img("$sub_article-exer2.png") ?>

<p>Use the &ldquo;Next&rdquo; button to move to the next sentence. When there are no more sentences, or if you
    want to terminate the exercise prematurely, you can click either &ldquo;GRADE task&rdquo; or &ldquo;SAVE outcome&rdquo;.
    Both of these terminate the exercise. If you are not logged in, no further action is taken,
    regardless of which of the two buttons you press. But if you <i>are</i> logged in, &ldquo;GRADE task&rdquo;
    causes the system to stores your result internally and indicates to the teacher that your result may
    be used for grading your progress. &ldquo;SAVE outcome&rdquo; also stores your result internally, but
    indicates that you do not want your result to be used for grading. (More information about this
    is available under <?= help_anchor('enroll#control_monitoring','&ldquo;Controlling
    Teacher&rsquo;s Monitoring&rdquo;') ?>.)</p>

<p>Go to <?= anchor("help/show_help/secondex/$sub_article","next example") ?>.</p>
