<?php $hg = $hdir->heb_gr('Hebrew','Greek') ?>

<h1>Example: Second <?= $hg ?> Exercise</h1>

<p>(You may be interested in a <?= anchor(site_url('help/show_help/secondex/' .
    $hdir->heb_gr('gr','heb')), 'corresponding '. $hdir->heb_gr('Greek','Hebrew') . ' exercise')
    ?>.)</p>


<p>We will now look at the exercise called &ldquo;demo2&rdquo; in the
    &ldquo;<?= $hdir->heb_gr('ETCBC4/demo','Nestle&nbsp;1904/demo') ?>&rdquo; folder. Click on the number 5 under
    &ldquo;...preset passages&rdquo; for that exercise. The system will show you a random sentence from the specified Bible
    passages, for example, this one:</p>

<?= $hdir->img("$sub_article-exer3.png") ?>

<p>Here, we have a sentence from <?= $hdir->heb_gr('Genesis 3:14','Luke 8:17') ?>. You will
    immediately notice an important difference from the previous example: Some of the words have been
    replaced by a number in parentheses. The reason is that in this case Bible Online Learner asks the user to
    provide a word that is actually part of the text; therefore the word must not be shown in the
    window. The system has therefore replaced the interesting words with numbers.</p>

<p>In this example, the answer box contains five lines labelled
    <?= $hdir->heb_gr('&ldquo;Item number&rdquo;, &ldquo;Lexeme (with variant)&rdquo;, &ldquo;Gender&rdquo;, &ldquo;Number&rdquo;, and &ldquo;State&rdquo;',
                      '&ldquo;Item number&rdquo;, &ldquo;Lexeme&rdquo;, &ldquo;Tense&rdquo;, &ldquo;Mood&rdquo;, &ldquo;Voice&rdquo;, &ldquo;Person&rdquo;, and &ldquo;Number&rdquo;') ?>.
    The item number refers to the number in parentheses in the sentence. <?= $hdir->heb('In this case we
    have moved through the words to the third word.') ?> The lexeme is the dictionary form of the word
    in question, and the <?= $hdir->heb_gr('number, gender, and state','voice, mood, person, number, and tense') ?>
    should help you create the word form that is actually in the text.</p>

<p>Your task is to type the word form that is found in the text<?= $hdir->heb_gr(', but without the Hebrew
  cantillation marks (which you rarely need to know in detail).',' in lower case without
  accents. (The label &ldquo;Normalized&rdquo; refers to a version of the text without punctuation and
    certain accents.)') ?> Below the empty field for the text, you will see a few buttons labelled
    with <?= $hg ?> characters. You can use these buttons to spell the correct word form, which in
    this case is <?= $hdir->heb_gr('<span class="help hebrew">יְמֵי</span>','εστιν') ?>. The key marked
    <?= $hdir->heb_gr('&rarr;','&larr;') ?> is a backspace key that deletes the last character
    you entered. The small characters in the upper <?= $hdir->heb_gr('right','left') ?> corner can be used to type
    the <?= $hg ?> character on you computer keyboard, if you prefer to do so rather than to click
    with your mouse. (More about this in Section XXX.)</p>

<p>When you have entered your answer, you can use the &ldquo;Check answer&rdquo; button to verify that your
    answer is correct.</p>

<p>Go to <?= anchor("help/show_help/thirdex/$sub_article","next example") ?>.</p>
