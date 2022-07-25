<?php $hg = $hdir->heb_gr('Hebrew','Greek') ?>

<h1>Example: Third <?= $hg ?> Exercise</h1>

<p>(You may be interested in a <?= anchor(site_url('help/show_help/thirdex/' .
    $hdir->heb_gr('gr','heb')), 'corresponding '. $hdir->heb_gr('Greek','Hebrew') . ' exercise')
    ?>.)</p>

<p>We will now look at the exercise called &ldquo;demo3&rdquo; in the
    &ldquo;<?= $hdir->heb_gr('ETCBC4/demo','Nestle&nbsp;1904/demo') ?>&rdquo; folder.
    Click on the number 5 under &ldquo;...preset passages&rdquo; for that exercise. The system will
    show you a random sentence from the specified Bible passages, for example, this one:</p>

<?= $hdir->img("$sub_article-exer4.png") ?>

<p>In this exercise the object of the exercise is not words but clauses. Each subquestion presents a
    <?= $hdir->heb_gr('clause','level 1 clause') ?>, and your task is to identify the
    <?= $hdir->heb_gr('type','function') ?> of the clause. The figure above shows a sentence from
    <?= $hdir->heb_gr('Genesis 20:3','Matthew 6:11') ?>. If you click the &ldquo;MyView&rdquo; icon
    and select <i><?= $hdir->heb_gr('Clause','Clause&nbsp;level&nbsp;1') ?></i> and <i>Show border</i>, the limits of
    each clause are obvious:</p>

<?= $hdir->img("$sub_article-exer5.png") ?>


<p> In this example you are asked to consider the <?= $hdir->heb_gr('type','function') ?> of the
    indicated clause. The first clause is
    <?= $hdir->heb_gr('<span class="help hebrew">הִנְּךָ֥ מֵת֙ עַל־הָאִשָּׁ֣ה</span>','Τὸν ἄρτον ἡμῶν τὸν ἐπιούσιον') ?>,
    and you must decide if the <?= $hdir->heb_gr('type','function') ?> of this clause is either
    <?= $hdir->heb_gr('<i>AjCl</i> (adjectival clause), <i>NmCl</i> (nominal clause), or <i>Ptcp</i> (participle clause).',
    '<i>object, indirect object,</i> or <i>second object.</i>') ?> (The answer <i>Other value</i> is not relevant in this
    exercise.) Once you have made your choice and checked if it is correct, you can press the
    &#10095; symbol to move on to the next clause in the sentence, which is
    <?= $hdir->heb_gr('<span class="help hebrew">וְהִ֖וא בְּעֻ֥לַת בָּֽעַל</span>','simply the word ἡμῖν') ?>.
    <?= $hdir->heb_gr('(The middle clause in the sentence (<span class="help hebrew">אֲשֶׁר־לָקַ֔חְתָּ</span>) has a type
    that is not covered by this exercise, and for that reason it is not shown in purple and is omitted from the
    exercise.)','(The middle clause in the sentence (δὸς) and the last one (σήμερον) have functions
    that are not covered by this exercise, and for that reason they are not shown in purple and are
    omitted from the exercise.)') ?>
