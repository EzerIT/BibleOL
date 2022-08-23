<h1>Edit Exercise - Features</h1>

<p>On the &ldquo;Features&rdquo; tab you indicate what information you will show to students and
    what information you want them to provide as answers.</p>

<p>When running an exercise, the program will display a table of interesting
  sentence units below the green progress bar under the chosen sentence. For example, thus:</p>

<?= $hdir->img("features1.png") ?>

<p>The sentence units (in this case <i>words</i>) are marked in purple in the
  sentence, and the table below shows the features <i>text, stem,</i>
  and <i>tense</i>, and the program asks about <i>person</i>
  and <i>number</i>. The first three are the so-called &ldquo;display features&rdquo;, while
  the last two are the so-called &ldquo;request features&rdquo;.</p>

<p>In the &ldquo;Features&rdquo; tab the teacher specifies the display features and request features.</p>

<p>A portion of the tab may look like this:</p>

<?= $hdir->img("features2.png") ?>

<p>This image shows part of the Features tab used by Hebrew.</p>

<p>If you click the word &ldquo;Word&rdquo; above the table, the table collapses and you see only this:</p>

<?= $hdir->img("features3.png") ?>

<p>&ldquo;Word&rdquo;, &rdquo;Phrase&rdquo;, and &rdquo;Clause&rdquo; are the most important
    sentence units available in the Hebrew part of Bible OL. If you work with Greek instead, you
    will see buttons labelled &ldquo;Word&rdquo;, &rdquo;Clause level 2&rdquo;, and &rdquo;Clause
    level 1&rdquo;. The topmost sentence unit (in this example &ldquo;Word&rdquo;) is the one
    specified in the &rdquo;Sentence Unit&rdquo; tab.<?= make_footnote('*','Or the
    &ldquo;Sentences&rdquo; tab if the &rdquo;Sentence Unit&rdquo; tab is not available.') ?> In the
    following text, we will refer to the topmost sentence unit as the &ldquo;primary&rdquo; sentence
    unit, and the other sentence units as &rdquo;secondary&rdquo; sentence units.</p>

<p>If you click the primary sentence unit (&ldquo;Word&rdquo; in this example), the table unfolds
    again, and you see the list of available features. Here you can place marks in the five columns
    to indicate how each feature should behave when the exercise is run.</p>

<p>The <i>text</i> feature is always at the top. The <i>text</i> feature refers to the actual
  characters that make up the sentence unit. This may include punctuation marks.</p>

<p>You can select the following options for each of the features of the primary sentence unit:</p>

<dl>
  <dt>Show</dt>
  <dd>Select this option to make this a <i>display feature.</i> This value of this feature will be
  displayed for each question object </dd>

  <dt>Request</dt>
  <dd>Select this option to make this a &ldquo;requested&rdquo; feature.</dd>

  <dt>Don&rsquo;t care</dt>
  <dd>Select this option if you neither want to show nor request the feature from the student. The
  student can, however, choose to display the feature through the <i>Display</i> menu or by resting
  the mouse arrow on a word.</dd>

  <dt>Don&rsquo;t show</dt>
  <dd>Select this option if you neither want to show nor request the feature from the student, and
    you want to prevent the student from seeing this feature through the <i>Display</i> menu or by
    resting the mouse arrow on a word.</dd>

  <dt>Multiple choice</dt>
  <dd>This option is available for a few features in Hebrew. If you select this option, the student
  will not be required to type the Hebrew word; instead a multiple choice drop-down box will be
  presented.</p>
</dl>
  
<p>Some options may not be available for all features. For example, in the picture above the &ldquo;Text
(transliterated)&rdquo; feature must be either <i>Don&rsquo;t care</i> or <i>Don&rsquo;t show</i>.</p>
