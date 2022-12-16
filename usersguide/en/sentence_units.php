<h1>Edit Exercise - Sentence Units</h1>

<p>The &ldquo;Sentence Units&rdquo; tab shows the criteria used by Bible OL when searching within a
  sentence for the sentence units to display to the user as the objects of the question.</p>

<p>Frequently, the criteria specified in the &ldquo;Sentences&rdquo; tab are also used to
  choose sentence units within a sentence and in that case the &ldquo;Sentence
  Units&rdquo; tab is not available.</p>

<p>The appearance and use of this tab is much like the
    &ldquo;<?= help_anchor('sentences','Sentences') ?>&rdquo;
    tab; therefore much information about that tab is not repeated here.</p>

<p>The &ldquo;Sentence Units&rdquo; tab may look like this:</p>

<?= $hdir->img('heb-sentence-units-tab1.png') ?>

<p>This image is almost identical to
  the <?= help_anchor('sentences#choice','corresponding') ?> one in the &ldquo;Sentences&rdquo;
  tab. There are two important differences, however:</p>

<ul>
    <li>The type of the sentence unit is at the top of the window.
        This is because the sentence unit type here serves a triple purpose:
        Firstly, as in the &ldquo;Sentences&rdquo; tab, it indicates the kind of sentence unit
        to look for. Secondly, it is used by the feature selector in the final tab,
        &ldquo;<?= help_anchor('features','Features') ?>&rdquo;.
        Thirdly, it is used when constructing MQL statements (see below). This means that the
        sentence unit type selector remains active even if you select &ldquo;MQL feature
        selector&rdquo;.</li>

    <li>The &ldquo;MQL feature selector&rdquo; contains no specification of the sentence unit type
        (see below).</li>
</ul>

<p>The friendly feature selector is used in exactly the same manner as in the
    &ldquo;Sentences&rdquo; tab. But if you want to specify an MQL statement, you will notice a
    difference: If you choose to type the search criteria as an MQL statement, the program will
    surround the statement by <code>[ttt ...]</code>, where
    <code>ttt</code> is the type of the sentence unit and the three dots are replaced by the
    statement you provide. This means that Bible OL will look in the chosen sentence for sentence
    units that can be described as specified in the MQL statement. Here, the MQL statement must not
    contain the characters <code>[</code> and <code>]</code>; so you can only specify one sentence
    unit. Furthermore, you must not include the word <code>NORETRIEVE</code> in the statement.</p>

