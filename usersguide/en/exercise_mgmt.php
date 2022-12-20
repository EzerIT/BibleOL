<h1>Exercise Management</h1>

<p>In order to create and modify exercises, you need to be logged in to an account with
    <i>facilitator</i> privileges. You should also be familiar with <?=
    help_anchor('folders','folder management') ?>.</p>

<p>Before we describe how to create exercises, you must be familiar with three important terms
    from the <?= help_anchor('terminology','terminology') ?> page:</p>

<ul>
    <li><?= help_anchor('terminology#sentence_unit','sentence units') ?></li>
    <li><?= help_anchor('terminology#feature','features') ?></li>
    <li><?= help_anchor('terminology#question_object','question objects') ?></li>
</ul>

<p>You should also realize that in the context of Bible OL, an <i>exercise</i> is
  actually a description of how the program should generate questions. An exercise is stored
  in the folder hierarchy.</p>

<p>An exercise specifies:</p>

<ul>
  <li>The database (corpus) that is to be used (typically, the Old or the New Testament).</li>
  <li>The <i>passages</i> from which the program chooses the exercise sentences
      (for example, the minor prophets or the Gospels).</li>
  <li>The criteria that the program should use when choosing sentences.</li>
  <li>The criteria that the program should use when choosing the
    <i>sentence units</i> (typically, words) that form the actual questions.</li>
  <li>The sentence unit features whose values are shown to the user.</li>
  <li>The sentence unit features whose values are requested from the user.</li>
  <li>The sentence unit features we don&rsquo;t want the user to see because it may give hints to
  solving the exercise.</li>
</ul>

<p>In the following examples we shall create new exercises or modify existing ones.</p>

<p>You should study at least one of these sets of examples:</p>

<ul>
    <li>Examples of Hebrew exercises:</li>
    <ul>
        <li><?= help_anchor('create_firstex/heb', 'Create a simple Hebrew exercise') ?>.</li>
        <li><?= help_anchor('create_secondex/heb', 'Create an advanced Hebrew exercise') ?>.</li>
    </ul>
    <li>Examples of Greek exercises:</li>
    <ul>
        <li><?= help_anchor('create_firstex/gr', 'Create a simple Greek exercise') ?>.</li>
        <li><?= help_anchor('create_secondex/gr', 'Create an advanced Greek exercise') ?>.</li>
    </ul>
</ul>
