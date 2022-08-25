<h1>Edit Exercise - Features</h1>

<p>On the &ldquo;Features&rdquo; tab you indicate what information you will show to students and
    what information you want them to provide as answers.</p>

<p>When running an exercise, the program will display a table of information about each <?=
    anchor('help/show_help/terminology#question_object','question object') ?> below the green
    progress bar under the chosen sentence. For example, thus:</p>

<?= $hdir->img("features1.png") ?>

<p>The question objects, which in this example are <i>words,</i> are marked in purple in the
  sentence, and the information below shows the features <i>text, stem,</i> and <i>tense</i> (the
  so-called &ldquo;display features&rdquo;), and the program asks about <i>person</i> and
  <i>number</i> (the so-called &ldquo;request features&rdquo;).</p>

<p>When creating an exercise, the teacher specifies the display features and request features in the
    &ldquo;Features&rdquo; tab. A portion of the tab may look like this:</p>

<?= $hdir->img("features2.png") ?>

<p>This image shows part of the Features tab used by Hebrew.</p>

<p>If you click the word &ldquo;Word&rdquo; above the table, the table collapses and you see only
this:</p>

<?= $hdir->img("features3.png") ?>

<p>&ldquo;Word&rdquo;, &rdquo;Phrase&rdquo;, and &rdquo;Clause&rdquo; are the most important <?=
    anchor('help/show_help/terminology#sentence_unit','sentence units') ?> available in the Hebrew
    part of Bible OL. If you work with Greek instead, you will see buttons labelled
    &ldquo;Word&rdquo;, &rdquo;Clause level 2&rdquo;, and &rdquo;Clause level 1&rdquo;. The topmost
    sentence unit (in this example &ldquo;Word&rdquo;) is the one specified in the &rdquo;Sentence
    Unit&rdquo; tab.<?= make_footnote('*','Or the &ldquo;Sentences&rdquo; tab if the &rdquo;Sentence
    Unit&rdquo; tab is not available.') ?> In the following text, we will refer to the topmost
    sentence unit as the &ldquo;primary&rdquo; sentence unit, and the other sentence units as
    &rdquo;secondary&rdquo; sentence units.</p>

<p>If you click the primary sentence unit (&ldquo;Word&rdquo; in this example), the table unfolds
    again, and you see the list of available features. Here you can place marks in the five columns
    to indicate how each feature should behave when the exercise is run.</p>

<p>The <i>text</i> feature is always at the top. The <i>text</i> feature refers to the actual
  characters that make up the sentence unit. This may include punctuation marks.</p>

<p>You can select the following options for each of the features of the primary sentence unit:</p>

<dl> <dt>Show</dt> <dd>Select this option to make this a <i>display feature.</i> This value of this
    feature will be displayed for each question object </dd>

    <dt>Request</dt> <dd>Select this option to make this a <i>request feature.</i></dd>

    <dt>Don&rsquo;t care</dt> <dd>Select this option if you neither want to display nor request the
        feature from the student. The student can, however, choose to display the feature in the
        usual manner by letting the mouse hover over a word or using the &ldquo;MyView&rdquo;
        selector.</dd>

    <dt>Don&rsquo;t show</dt> <dd>Select this option if you neither want to display nor request the
        feature from the student, and you want to prevent the student from seeing this feature using
        other means.</dd>

    <dt>Multiple choice</dt> <dd>This option is available for a few features in Hebrew. If you
        select this option, the student will not be required to type the Hebrew word; instead
        multiple choice options will be presented.</dd> </dl>
  
<p>Some options may not be available for all features. For example, in the picture above, the
    &ldquo;Hint&rdquo; feature must be either <i>Show</i> or <i>Don&rsquo;t care</i>.</p>

<p>When creating an exercise, you can choose to make the exercise easier for the student by limiting
    the number of possible answers for certain features. Consider, for example, the question at the
    top of this page. For &ldquo;Person&rdquo; the student can choose between <i>1st, 2nd, 3rd,
    None, Unknown,</i> and <i>Absent</i>; and for &ldquo;Number&rdquo; the studen can choose between
    <i>Singular, Plural, Dual, None, Unknown,</i> and <i>Absent</i>. Some of these values may
    confuse the student, and therefore a teacher can limit the options.</p>

<p>When you indicate that a certain feature should be a request feature, a green button labelled
    &ldquo;Unlimited&rdquo; may appear:</p>

<?= $hdir->img("features4.png") ?>

<p>If you click on the &ldquo;Unlimited&rdquo; button next to the &ldquo;Person&rdquo; feature, this
dialog will appear:</p>

<?= $hdir->img("features5.png") ?>

<p>In the dialog you indicate which feature values should be presented to the students. You can use
    the &ldquo;Set all&rdquo; and &ldquo;Clear all&rdquo; buttons to set or remove all the check
    marks. Let us assume that you remove the checkmarks next to <i>None, Unknown,</i> and
    <i>Absent</i>:</p>

<?= $hdir->img("features6.png") ?>

<p>When you press the &ldquo;Save&rdquo; button, you return to the feature table and the green
    &ldquo;Unlimited&rdquo; button has turned into a red button labelled &rdquo;Limited&rdquo;. Let
    us further assume that you limit the &ldquo;Number&rdquo; feature to only the values
    <i>Singular</i> and <i>Plural</i>. Now, when the exercise is run, a question may look like
    this:</p>

<?= $hdir->img("features7.png") ?>

<p>You can see that all the disabled values have been combined into a single <i>Other value</i>.</p>

<p>Below the features for the primary sentence unit on the &ldquo;Features&rdquo; tab, you find a
    number of other sentence units. If you click one of these, a corresponding feature list will
    appear, for example this:</p>

<?= $hdir->img("features8.png") ?>

<p>In this illustration we are working with a Hebrew exercise whose primary sentence unit is a
    <i>Word</i>, and we have clicked on the &ldquo;Phrase&ldquo; button. Here, the only possible
    choices are <i>Don&rsquo;t care</i> and <i>Dont&rsquo;t show</i>. The blue buttons labelled
    &ldquo;Set all&rdquo; can be used to quickly specify a choice for all features.</p>

<p>Below all the secondary sentence units you will find a button labelled &ldquo;Gloss limit&rdquo;.
    If you click it, this text will appear:</p>

<?= $hdir->img("features9.png") ?>

<p>If you set this value to, for example, 40, glosses for the 40 most common words (lexemes) will
    not be available to the student.</p>
