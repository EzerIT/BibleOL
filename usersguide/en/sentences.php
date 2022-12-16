<h1>Edit Exercise - Sentences</h1>

<p>The &ldquo;Sentences&rdquo; tab shows the criteria used by Bible OL when choosing the sentences
used by the exercise.</p>

<p>Strictly speaking, these criteria are expressed as an <?= help_anchor('mql', 'MQL') ?> statement. But
MQL is a rather complex language and therefore Bible OL has a more user-friendly way to specify
simple search criteria.</p>

<p>If the user-friendly specification is used, a search criterion consists of:</p>

<ul>
  <li>The type of a sentence unit</li>
  <li>One or more sentence unit features</li>
</ul>

<p>A sentence unit is typically a <i>word,</i> but it can also be, for example, a <i>clause</i> or
    a <i>phrase</i>. A sentence unit has a number of <i>features</i>. If the sentence unit is <i>word,</i>
    the features may, for example, include <i>part of speech</i> and <i>case.</i> Each feature has a value.
    For example, the <i>case</i> feature may have the value <i>genitive.</i></p>

<p>The search criteria may specify that a certain feature must have a certain value, or that the
  feature must <i>not</i> have a certain value. When the program generates questions, it will look
  for sentences containing sentence units of the specified type with the specified feature values.</p>

<p>The &ldquo;Sentences&rdquo; tab may look like this:</p>

<a name="choice"></a><?= $hdir->img('heb-sentences-tab1.png') ?>

<p>By choosing either &ldquo;MQL statement to select sentences&rdquo; or &ldquo;Friendly
  feature selector&rdquo;, you indicate if you want to specify the search criteria
  as an MQL statement or in a more user-friendly manner.</p>

<p>If the user-friendly method is selected, you use &ldquo;Sentence unit type&rdquo; to
  specify the kind of sentence unit that should be used when selecting
  sentences. The drop-down list next to &ldquo;Feature&rdquo; shows the features that are
  available for the specified sentence unit; and in the box below, you
  specify the feature values. In the illustration above, we have specified
  that we will be looking for <i>words</i> whose <i>Part of speech</i> feature is either
  &ldquo;Noun&rdquo; or &ldquo;Personal pronoun&rdquo;.</p>

<p>The &ldquo;Clear&rdquo; button erases all criteria on this page.</p>

<p>You will probably often want to use the same criteria for choosing sentence units as for choosing
  sentences. The check mark next to &ldquo;Use this for sentence unit selection&rdquo; indicates
  that sentence units should be chosen based on the same criteria as the ones specified here. This
  option is only available if the friendly feature selector is being used.</p>

<p>If you choose to specify the search criteria as an MQL statement, the
  program will surround the statement by <code>[sentence ...]</code>, where
  the three dots are replaced by the statement you provide. This means
  that Bible OL will search for a sentence containing whatever is specified in the
  MQL statement. It is recommended to include the word <code>NORETRIEVE</code>
  in the MQL statement as this will cause the program to run considerably
  faster. More information about MQL can be found <?= help_anchor('mql', 'here') ?>.</p>
