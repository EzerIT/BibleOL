<?php $hg = $hdir->heb_gr('Hebrew','Greek') ?>

<h1>Example: Create an Advanced <?= $hg ?> Exercise</h1>

<p>(You may be interested in a <?= help_anchor('create_secondex/' . $hdir->heb_gr('gr','heb'),
                                          'corresponding '. $hdir->heb_gr('Greek','Hebrew') . ' exercise')
    ?>.)</p>

<p>In this example we shall continue working with the exercise from the
    <?= anchor("help/show_help/create_firstex/$sub_article","previous example") ?>.</p>

<p>In the previous example, we created an exercise that found <?= $hg ?>
  verbs in the <?= $hdir->heb_gr('qal qatal forms','present indicative') ?> and asked the student about
  the <?= $hdir->heb_gr('person, gender, and number','person and number') ?> of these verbs. We saw,
  furthermore, that most verbs are in the third person <?= $hdir->heb('masculine') ?> singular, so we
  chose to exclude all third person verbs.</p>

<p>So far so good; but suppose we don&rsquo;t mind that words in the third person <i>occasionally</i>
    turn up. We might, for example, say that when choosing <i>sentences</i> we insist that they
    must contain words in the first or second person, but we don&rsquo;t mind if the chosen <i>sentence
    units</i> (words) are in the third person if the sentence happens to contain such words.
    We can do that in this way:</p>

<p>In the &ldquo;Sentences&rdquo; tab, remove the check mark next to &ldquo;Use this for sentence
  unit selection&rdquo;. This enables you to use different criteria when choosing
  sentences and when choosing words within the sentence. Now click the
  &ldquo;Sentence Units&rdquo; tab:</p>

<?= $hdir->img("$sub_article-sentence-units-tab1.png") ?>

<p>You can see that this tab is very similar to the &ldquo;Sentences&rdquo; tab, but the
  first few lines come in a slightly different order. Depending on how you
  reached this point, this window may show different things. If you started with
  an empty exercise, the window will show no selected features. But if you opened
  the exercise using <i>Edit</i> in the File Manager, the window will show the
  original content of the <i>sentence</i> choice criteria. This may be a bit
  confusing, but if you press the &ldquo;Clear&rdquo; button, all the criteria in this tab
  are removed.</p>

<p>Just as you originally did under &ldquo;Sentences&rdquo;, you can here choose Part-of-speech=Verb,
  <?= $hdir->heb_gr('Stem=Qal and Tense=Qatal','Mood=Indicative and Tense=Present') ?>. But refrain from
  specifying any requirements about the Person feature. In this way you allow the program to ask
  about third person verbs if they happen to occur in the chosen sentences.</p>

<p>You can no run the exercise again, and you might get this sentence from
  <?= $hdir->heb_gr('Genesis&nbsp;3:12','Luke&nbsp;11:8') ?>:</p>

<?= $hdir->img("$sub_article-run-exercise3.png") ?>

<p>You can see that &ndash; as requested &ndash; the sentence contains at least one word which is
    not in the third person; but as the sentence also happens to contain a third person verb
    (namely, <?= $hdir->heb_gr('<span class="help hebrew">נָֽתְנָה</span>','χρῄζει') ?>), you are asked
    about this one as well. (In this example, you need to click the &#10095; symbol at the right
    side of the answer box to see that word.)</p>

<p>But even this is not ideal. We saw earlier that among the present tense forms there was an excess
  of third person <?= $hdir->heb('masculine') ?> singular forms. We have now chosen to exclude
  sentences that only contain verbs in the third person, but we were actually only interested in
  excluding words in the third person <i><?= $hdir->heb('masculine') ?> singular</i>. We don&rsquo;t mind
  words in the third person <?= $hdir->heb('<i>feminine</i> or') ?> <i>plural.</i></p>

<p>But if we want to exclude only the third person <?= $hdir->heb('masculine') ?> singular, the
  feature selectors we have seen so far will not suffice. We need sentences containing words whose
  part of speech is verb, whose <?= $hdir->heb_gr('stem is qal','mood is indicative') ?>, whose tense is
  <?= $hdir->heb_gr('perfect','present') ?>, and whose <?= $hdir->heb_gr('person, gender, and number','person
  and number') ?> are not simultaneously <?= $hdir->heb_gr('3rd, masculine, and singular','3rd and
  singular') ?>. This is a fairly complex rule and you rarely need to specify something as
  complicated as this. You <i>can</i> specify this in Bible OL, but you need to use the command
  language MQL.</p>

<p>If you are not interested in more complex sentence choices using MQL, you can skip the rest of
    this example. But if you want a small taste of how <?= help_anchor('mql','MQL') ?>
    is used, you may return to the &ldquo;Sentences&rdquo; tab and select the option &ldquo;MQL
    statement to select sentences&rdquo;:</p>

<?= $hdir->img("$sub_article-sentences-tab4.png") ?>

<p>The system here shows an MQL command that corresponds to the features we
  have chosen: <code><?= $hdir->heb_gr(
  '[word NORETRIEVE vs IN (qal) AND vt IN (perf) AND NOT ps IN (p3) AND sp IN (verb)]',
  '[word NORETRIEVE psp IN (verb) AND tense IN (present) AND mood IN (indicative) AND NOT person IN (third_person)]'
   ) ?></code>.<?= make_footnote('*','You may need to move the small triangle at the lower right corner of the edit field
   in order to see the entire text') ?>
  This statement begins with the word &ldquo;word&rdquo; which indicates the type of sentence unit
  we are looking for. The word &ldquo;NORETRIEVE&rdquo; has no effect on the choice, but it makes
  the request run faster. The rest of the command contains our four selection criteria:</p>

<?php if ($sub_article=='heb'): ?>

    <table>
        <tr>
            <th>MQL</th><th style="padding-left:2em">Meaning</th>
        </tr>
        <tr>
            <td><code>vs IN (qal)</code></td><td style="padding-left:2em"><i>verbal stem</i> is <i>qal</i></td>
        </tr>
        <tr>
            <td><code>vt IN (perf)</code></td><td style="padding-left:2em"><i>verbal tense</i> is <i>perfect</i>, that is, <i>qatal</i></td>
        </tr>
        <tr>
            <td><code>NOT ps IN (p3)</code></td><td style="padding-left:2em"><i>person</i> is not <i>3rd person</i></td>
        </tr>
        <tr>
            <td><code>sp IN (verb)</code></td><td style="padding-left:2em"><i>speech part</i> is <i>verb</i></td>
        </tr>
    </table>

<?php endif; ?>

<?php if ($sub_article=='gr'): ?>

    <table>
        <tr>
            <th>MQL</th><th style="padding-left:2em">Meaning</th>
        </tr>
        <tr>
            <td><code>psp IN (verb)</code></td><td style="padding-left:2em"><i>part-of-speech</i> is <i>verb</i></td>
        </tr>
        <tr>
            <td><code>tense IN (present)</code></td><td style="padding-left:2em"><i>tense</i> is <i>present</i></td>
        </tr>
        <tr>
            <td><code>mood IN (indicative)</code></td><td style="padding-left:2em"><i>mood</i> is <i>indicative</i></td>
        </tr>
        <tr>
            <td><code>NOT person IN (third_person)</code></td><td style="padding-left:2em"><i>person</i> is not <i>3rd person</i></td>
        </tr>
    </table>

<?php endif; ?>

<p style="margin-top:1em">The criteria are separated by the word &ldquo;AND&rdquo;.</p>


<?php if ($sub_article=='heb'): ?>
    <p>If you replace the code &ldquo;<code>NOT ps IN (p3)</code>&rdquo; with &ldquo;<code>NOT (ps IN (p3) AND
    gn IN (m) AND nu IN (sg))</code>&rdquo;, you indicate that you do not wish the third person,
    masculine, and singular at the same time. The modified MQL statement now reads: <code>[word
    NORETRIEVE vs IN (qal) AND vt IN (perf) AND NOT (ps IN (p3) AND gn IN (m) AND nu IN (sg)) AND sp
    IN (verb)]</code>.</p>
<?php endif; ?>

<?php if ($sub_article=='gr'): ?>
    <p>If you replace the code &ldquo;<code>NOT person IN (third_person)</code>&rdquo; with
    &ldquo;<code>NOT (person IN (third_person) AND number IN (singular))</code>&rdquo;,
    you indicate that you do not wish the third person and singular at the same time. The modified
    MQL statement now reads:
    <code>[word NORETRIEVE psp IN (verb) AND tense IN (present) AND mood IN (indicative)
    AND NOT (person IN (third_person) AND number IN (singular))]</code>.</p>
<?php endif; ?>

<p>When you run the exercise, most of the sentences will show no difference
  from before. But occasionally you will come across a sentence such as <?= $hdir->heb_gr('Genesis
  1:21','John 7:25') ?>:</p>

<?= $hdir->img("$sub_article-run-exercise4.png") ?>


<p>This sentence was chosen because it contains a third person <i>plural</i>
  verb. <?= $hdir->heb_gr('(The gender is <i>unknown</i> here because there is no difference
  between the masculine and feminine 3rd person plural form of the verb.)',
  '(The chosen sentence units are still all verbs in the present indicative; therefore the program
  also asks about <span class="greek">ἐστιν</span> which is third person singular.)') ?></p>

<p>The Reference Guide contains more information about
    <?=help_anchor('sentence_units','the &ldquo;Sentence Units&rdquo; tab') ?>.</p>
