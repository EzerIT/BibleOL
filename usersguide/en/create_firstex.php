<?php $hg = $hdir->heb_gr('Hebrew','Greek') ?>

<h1>Example: Create a Simple <?= $hg ?> Exercise</h1>

<p>(You may be interested in a <?= anchor('help/show_help/create_firstex/' . $hdir->heb_gr('gr','heb'),
                                          'corresponding '. $hdir->heb_gr('Greek','Hebrew') . ' exercise')
    ?>.)</p>


<p>We shall create an exercise in the conjugation of <?= $hg ?> verbs in the <?= $hdir->heb_gr('qatal form of
    the qal stem','present tense') ?>. We shall ask the user to identify the <?= $hdir->heb_gr('person, gender, and
    number of the qatal forms of various qal verbs.','person and number of various present tense verb
    forms.') ?></p>

<p>Select the menu <i>Administration &gt; Manage exercises,</i> then navigate to a folder where
    you want to create your exercises. This should preferably be a folder used only by you and your
    team. At the bottom of the page, click the <i>Create exercise</i> button. A dialog will appear in
    which you select the text database on which you want to base your exercise. Here, you should select
    &ldquo;<?= $hdir->heb_gr('Hebrew (ETCBC4, OT)','Greek (Nestle 1904, NT)') ?>&rdquo; and press
    the <i>OK</i> button.</p>

<p>You will now se a web page that looks like this:</p>

<?= $hdir->img("$sub_article-create1.png") ?>

<p>At the top you'll see five tabs, labelled &ldquo;Description&rdquo;, &ldquo;Passages&rdquo;,
    &ldquo;Sentences&rdquo;, &ldquo;Sentence Units&rdquo;, and &ldquo;Features&rdquo;. On the left, you
    can see the name of the text database you are using.</p>

<h2>The &ldquo;Description&rdquo; Tab</h2>

<p>The &ldquo;Description&rdquo; tab is displayed when you start editing an exercise. Below the row of
    tabs, you see a text editing field in which you can write information and instructions to the
    students who will be running this exercise. You may, for example, write something like this:</p>

<?= $hdir->img("$sub_article-description-tab.png") ?>

<h2>The &ldquo;Passages&rdquo; Tab</h2>

<p>Select the &ldquo;Passages&rdquo; tab and indicate which part of the <?= $hdir->heb_gr('Old','New') ?> Testament
    you want to use for the exercise:</p>

<?= $hdir->img("$sub_article-passages-tab.png") ?>

<p>When generating questions for a student, Bible OL will choose sentences from the passages you
    specify here. The more passages you select, the more sentences Bible OL can choose from; but the
    more sentences there are, the longer it will take the program to generate the exercise.
    Therefore it may be a good idea to limit the selection. In most situations a selection
    comprising the entire New Testament is no problem; but if the selection contains the entire Old
    Testament, the program may appear somewhat slow.</p>

<p>The passages you specify here are, in general, only a suggestion for the student; as described under
    <?= anchor("help/show_help/firstex/$sub_article", "Example: First $hg Exercise") ?>,
    the student can normally use another passage selection by starting an exercise from the &ldquo;Select
    number of questions and specify your own passages&rdquo; column.</p>

<p>You can click on the small plus signs next to the names of the books of the Bible. This will allow
    you to specify individual chapters or verses to use for the exercise.</p>

<p>Below the passage selector, you can specify a few specialities about the exercise:</p>

<ul>
    <li> Should the &ldquo;Locate&rdquo; button be shown or not?
        (See <?= anchor('help/show_help/variations#disabling_locate','Disabling &ldquo;Locate&rdquo;') ?>.)</li>
    <li> How many sentences of context should be shown before and after the relevant sentence? (See
        <?= anchor('help/show_help/variations#sentence_context','Sentence Context') ?>.)</li>
    <li> Should the number of questions be fixed or should the student be able to choose? (See 
        <?= anchor('help/show_help/variations#fixed_exercises','Fixed Exercises') ?>.)</li>
    <li> Should the order of questions be random or fixed? (See
        <?= anchor('help/show_help/variations#fixed_exercises','Fixed Exercises') ?>.)</li>
</ul>

<p>If either the number of questions or the order of questions is fixed, the students cannot themselves
    choose the Bible passages for the exercise.</p>


<h2>The &ldquo;Sentences&rdquo; Tab</h2>

<p>Select the &ldquo;Sentences&rdquo; tab. You will see a dialog like this one:</p>

<?= $hdir->img("$sub_article-sentences-tab.png") ?>

<p>Here you can indicate the criteria which the program is to use when choosing sentences for the
    exercise.</p>


<p>We shall return to the first two lines (&ldquo;Use this for sentence unit selection&rdquo; and &ldquo;MQL statement
    to select sentences&rdquo;) later. Make sure that &ldquo;Friendly feature selector&rdquo; is marked.</p>

<p>Next to &ldquo;Sentence unit type&rdquo; there is a drop-down list where you can choose between the
    types of sentence units available for exercises in this database. These are <?= $hdir->heb_gr('&ldquo;Word&rdquo;,
    &ldquo;Subphrase&rdquo;, &ldquo;Phrase atom&rdquo;, &ldquo;Phrase&rdquo;, &ldquo;Clause atom&rdquo;,
    and &ldquo;Clause&rdquo;','&ldquo;Word&rdquo;, &ldquo;Clause level1&rdquo;, and &ldquo;Clause level 2&rdquo;') ?>.
    Here you select the type of object that the exercise should be about. In most cases the value
    should be &ldquo;Word&rdquo;.</p>

<p>Next to &ldquo;Feature&rdquo; there is another drop-down list. Here you can choose between the
    various features available for the selected sentence unit type. For words, the features include
    &ldquo;Part of speech&rdquo;, &ldquo;Gender&rdquo;, &ldquo;Number&rdquo; etc. Try selecting various
    features and note how the rest of the window changes. When you choose a particular feature, Bible OL
    shows you the values that this feature may have. Finally press the &ldquo;Clear&rdquo; button.</p>
    
<p>The &ldquo;Clear&rdquo; button erases all the criteria.</p>

For the exercise we are creating here, we need sentences containing words that...

<?php if ($sub_article=='heb'): ?>

    <ul style="list-style: none">
        <li>...are verbs,</li>
        <li>...have the stem qal,</li>
        <li>...are in the qatal tense.</li>
    </ul>

    <p>This can be specified thus:</p>

    <ul>
        <li>Set &ldquo;Sentence unit type&rdquo; to &ldquo;Word&rdquo;.</li>
        <li>Select the feature &ldquo;Part of speech&rdquo; and check the box next to &ldquo;Verb&rdquo;.</li>
        <li>Select the feature &ldquo;Stem&rdquo; and check the box next to &ldquo;Qal&rdquo;.</li>
        <li>Select the feature &ldquo;Tense&rdquo; and check the box next to &ldquo;Qatal (Perf)&rdquo;.</li>
    </ul>
<?php endif; ?>

<?php if ($sub_article=='gr'): ?>

    <ul style="list-style: none">
        <li>...are verbs,</li>
        <li>...are in the indicative mood,</li>
        <li>...are in the present tense.</li>
    </ul>

    <p>This can be specified thus:</p>

    <ul>
        <li>Set &ldquo;Sentence unit type&rdquo; to &ldquo;Word&rdquo;.</li>
        <li>Select the feature &ldquo;Part of speech&rdquo; and check the box next to &ldquo;Verb&rdquo;.</li>
        <li>Select the feature &ldquo;Mood&rdquo; and check the box next to &ldquo;Indicative&rdquo;.</li>
        <li>Select the feature &ldquo;Tense&rdquo; and check the box next to &ldquo;Present&rdquo;.</li>
    </ul>
<?php endif; ?>

<p>(Actually, in this example it is superfluous to require that the word should be a verb; if a word is
    marked as <?= $hdir->heb_gr('qal perfect','present indicative') ?>, it is always a verb.)</p>

<p>The window now looks like this:</p>

<?= $hdir->img("$sub_article-sentences-tab2.png") ?>

<p>(You may have noticed that as you chose feature values, the text next to &ldquo;MQL statement to
    select sentences&rdquo; changed automatically. MQL is a command language that is used to specify how
    to search the database, and the statement here is the one actually used for your search. But feel
    free to ignore this for now. You may later read more about MQL in section XXX.)</p>

<p>Bible OL now knows how to find sentences for the exercises. It will choose the sentences based on
    the criteria we have just specified. But now things get a bit more complicated: When Bibel OL
    generates exercises, it actually has to make two choices: First it must choose some interesting
    sentences; thereafter it must choose some interesting sentence units (words) within the chosen
    sentences.</p>

<p>Often, the criteria used for these two choices are the same. In the current example, this is indeed
    the case: First, we want to search the database for sentences that contain
    <?= $hdir->heb_gr('qal qatal','present indicative') ?> verbs; thereafter, we want to search each
    sentence for words that are <?= $hdir->heb_gr('qal qatal','present indicative') ?> verbs. So in
    this example, the words have to be chosen using exactly the same criteria as the sentences. The
    check mark next to &ldquo;Use this for sentence unit selection&rdquo; instructs Bible OL to use
    the same criteria when selecting the interesting words. Try removing the check mark next to
    &ldquo;Use this for sentence unit selection&rdquo; and the set it again; you will then see that
    the &ldquo;Sentence Units&rdquo; tab is only active when the check mark is not set. (Be sure to
    leave the mark on.)</p>

<h2>The &ldquo;Features&rdquo; Tab</h2>

<p>Select the &ldquo;Features&rdquo; tab. You will see a dialog like this one:</p>

<?= $hdir->img("$sub_article-features-tab1.png") ?>

<p>Here you can indicate what information you will provide to the student, and what information the
    student should provide.</p>

<p>When you first open this tab, all the marks will be in the &ldquo;Don’t care&rdquo; column. In the
    rightmost column you will see all the features that are available for the sentence unit called
    &ldquo;Word&rdquo; in this database. The first feature is always &ldquo;Text&rdquo; and represents
    the actual word in the Bible verse.</p>

<p>You can now choose which features to show to the student and which features the student must
    provide. In this example we want to show the actual word and ask the student to identify its gender,
    person, and number. Place a mark in the column &ldquo;Show&rdquo; next to &ldquo;Text&rdquo; and in
    the column &ldquo;Request&rdquo; next to <?= $hdir->heb_gr('&ldquo;Person&rdquo;, &ldquo;Gender&rdquo;, and
    &ldquo;Number&rdquo;','&ldquo;Person&rdquo; and &ldquo;Number&rdquo;') ?> as shown here:</p>

<?= $hdir->img("$sub_article-features-tab2.png") ?>

<p>When students run the exercise, they will be able to display all the features by pointing to the
    individual words with their mouse. If you want to prevent students from seeing certain features,
    indicate that in the &ldquo;Don’t show&rdquo; column. The &ldquo;Request&rdquo; features are never
    visible to the students.</p>

<?php if ($sub_article=='heb'): ?>
    <p>A few of the features allow you to tick the &ldquo;Multiple choice&rdquo; column. If you do so,
        Bible OL will provide the student with a menu of choices for that feature; if you remove the tick
        under &ldquo;Multiple choice&rdquo;, the student must type the answer as text.</p>
<?php endif; ?>

<?php if ($sub_article=='gr'): ?>
    <p>The &ldquo;Multiple choice&rdquo; column is not used for Greek exercises.</p>
<?php endif; ?>

<p>The &ldquo;Features&rdquo; tab is described in more detail in section XXX.</p>

<h2>Saving the Exercise</h2>

<p>Click the &ldquo;Save&rdquo; button at the bottom of the page to save the exercise. You will be
    asked to provide a name for the exercise.</p>

<h2>Running and Refining the Exercise</h2>

<p>Go to the <i>Text and Exercise</i> &gt; <i>Exercises</i> menu and find the exercise you just created.
    You can now run it by clicking, for example, on the number <i>5</i> under the heading <i>Select
    number of questions using preset passages</i>. You may then, for example, get this sentence from
    <?= $hdir->heb_gr('Genesis&nbsp;3:1','Matthew&nbsp;3:10') ?>:</p>

<?= $hdir->img("$sub_article-run-exercise1.png") ?>


<p>The correct answer (to both verbs in this sentence) is:</p>

<?= $hdir->img("$sub_article-run-exercise2.png") ?>

<p>In fact, <i>third person <?= $hdir->heb('masculine') ?> singular</i> is the answer to most
  questions posed by this exercise, simply because third person <?= $hdir->heb('masculine') ?>
  singular is the most common <?= $hdir->heb_gr('qatal','present tense') ?> form in the
  <?= $hdir->heb_gr('Old Testament','New Testament') ?>. This illustrates a feature of
  Bible OL which is both a strength and a weakness: The most common word forms in the Bible also
  become the most common word forms in the exercises. This is an advantage since the program will
  then primarily drill students in the forms they will need most often; but it is a drawback if the
  student is well acquainted with the common word forms and needs to train the less frequent
  ones.</p>

<p>We shall now change the exercise so that other verb forms will appear more frequently. We can do
  this by changing the search criteria so that the program will only look for sentences containing
  verb forms that are not in the third person.</p>

<p>Go back to the <i>Administration</i> &gt; <i>Manage exercises</i> menu, locate your exercise and
  click the &ldquo;Edit&rdquo; button next to it. You can now change the exercise. Select the
  &ldquo;Sentences&rdquo; tab and select the feature &ldquo;Person&rdquo;. In the box under the
  feature selector you must now indicate that you are not interested in the third person. You can do
  this in two different ways:</p>

<ul>
  <li>You can check both &ldquo;1st&rdquo; and &ldquo;2nd&rdquo;. This will cause Bible OL to
      look for words in the first or second person only.</li>
  <li>Alternatively, you can change the mark at the top of the box from = (equal to) to ≠ (different
      from) and then check &ldquo;3rd&rdquo;. This will cause Bible OL to look for words that are <i>not</i> in
      the third person. You can see this below:</li>
</ul>

<?= $hdir->img("$sub_article-sentences-tab3.png") ?>

<p>If you run the exercise now, the program will choose sentences with verbs
  in the first or second person <?= $hdir->heb_gr('qal qatal','present indicative') ?>.</p>

<p>You may wonder about the
    <?= $hdir->heb_gr('terms &ldquo;None&rdquo; and &ldquo;Unknown&rdquo;','term &ldquo;N/A&rdquo;') ?>
    in the figure above. The <?= $hdir->heb_gr('Hebrew','Greek') ?> database uses
    <?= $hdir->heb_gr('&ldquo;None&rdquo;','&ldquo;N/A&rdquo;') ?> for
    words where the feature makes no sense. For example, it makes no sense to ask if prepositions are
    the first, second, or third person.
    <?= $hdir->heb('The value &ldquo;Unknown&rdquo; is used in cases where the word has a person, but you cannot
  tell from the text which person it is.') ?></p>

<p>If you are interested, you may proceed to
    <?= anchor("help/show_help/create_secondex/$sub_article", "a more advanced version of this exercise")?>.</p>

