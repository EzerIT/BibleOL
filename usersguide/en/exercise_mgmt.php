<h1>Exercise Management</h1>

<p>In order to create and modify exercises, you need to be logged in to an account with
    <i>facilitator</i> privileges. You should also be familiar with <?=
    anchor('help/show_help/folders','folder management') ?>.</p>

<p>Before we describe how to create exercises, we need to discuss two important concepts used by
Bible OL:</p>

<dl>
    <dt>Sentence units</dt>
    <dd>A sentence can be seen as consisting of a set of <i>sentence units</i>. By
        far the most common thing is to see a sentence as a set of <i>words</i>, but you
        could also see the sentence as comprised of <i>clauses</i> or <i>phrases</i>. Thus
        a <i>sentence unit</i> can be a <i>word,</i> a <i>clause,</i>
        a <i>phrase,</i> or perhaps something else. As you read on, you will rarely
        go wrong if you assume that &ldquo;sentence unit&rdquo; means &ldquo;word&rdquo;.</dd>
    
    <dt>Features</dt>
    <dd>A sentence unit has various <i>features.</i> A feature has a name and a
        value. For example, a word can have a feature called <i>part of speech</i>
        with the value <i>noun</i> and a feature called <i>gender</i> with the value
        <i>masculine.</i> An important feature is called <i>text,</i> which simply
        refers to the actual characters making up the word; for example, the
        <i>text</i> feature may have the value <i>&ldquo;elephant&rdquo;.</i></dd>
</dl>

<p>You should also realise that in the context of Bible OL, an <i>exercise</i> is
  actually a description of how the program should generate questions. An exercise is stored
  in the folder hierarchy.</p>

<p>An exercise specifies:</p>

<ul class="notspaced">
  <li>The database that is to be used (typically, the Old or the New Testament).</li>
  <li>The <i>passages</i> from which the program chooses the exercise sentences
      (for example, the minor prophets or the Gospels).</li>
  <li>The criteria that the program should use when choosing sentences.</li>
  <li>The criteria that the program should use when choosing the
    <i>sentence units</i> (typically, words) that form the actual questions.</li>
  <li>The sentence unit features whose values are shown to the user.</li>
  <li>The sentence unit features whose values are requested from the user.</li>
  <li>The sentence unit features we don&rsquo;t want the user to see because it may give hints to
  solve the exercise.</li>
</ul>

<p>In the following examples, we shall create new exercises or modify existing ones.</p>

<p>You should study at least one of these sets of examples:</p>

<ul>
    <li>Examples of Hebrew exercises:</li>
    <ul>
        <li><?= anchor('help/show_help/create_firstex/heb', 'Create a simple Hebrew exercise') ?>.</li>
        <li><?= anchor('help/show_help/create_secondex/heb', 'Create an advanced Hebrew exercise') ?>.</li>
    </ul>
    <li>Examples of Greek exercises:</li>
    <ul>
        <li><?= anchor('help/show_help/create_firstex/gr', 'Create a simple Greek exercise') ?>.</li>
        <li><?= anchor('help/show_help/create_secondex/gr', 'Create an advanced Greek exercise') ?>.</li>
    </ul>
</ul>
