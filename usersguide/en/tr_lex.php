<h1>Lexicon Translation</h1>

<p>&ldquo;Lexicon translation&rdquo; refers to the translation of Hebrew, Greek, or Latin lexemes
    (lemmas) into another language. These translations will become available under
    &ldquo;Glosses&rdquo; when viewing a Hebrew, Greek, or Latin text.</p>

<p>If you have <i>translator</i> privileges, you can add or modify the translations of lexemes.
    Select the <i>Administration &gt; Translate lexicon</i> menu. This will bring up an overview of
    Hebrew, Aramaic, Greek, and Latin words that need to be translated. It may look like this:</p>

<?= $hdir->img("tr_lex1.png") ?>

<p>This figure above shows the start of the overview of Hebrew words. Further down the page you will
    find Aramaic, Greek, and Latin. You can choose to view the words by frequency or alphabetically.
    If, for example, you click the button labelled &ldquo;101-200&rdquo;, you will see a list of
    Hebrew words with a frequency ranking between 101 and 200. Alternatively, you can look up the
    word alphabetically.</p>

<p>Once you have click a button for the word range you wish to see, you will be taken to a web page
    that looks similar to this one:</p>

<?= $hdir->img("tr_lex2.png") ?>


<p>Above this table, you will find buttons for selecting other word ranges.</p>

<p>Under &ldquo;Target language&rdquo; you select the language of the tanslation you are creating.
    In the figure above this is Danish. Note that if you want to modify a special translation
    <?= help_anchor('variant','variant') ?>, you must select that variant from the <i>Variant</i>
    menu at the very top of the page (not shown in the figure above).</p>

<p>The table below the target language selector contains a number of columns:</p>
<ul>
    <li><b>Occurrences</b> indicate how many times the word occurs in the text database.</li>
    <li><b>Symbolic lexeme</b> (only Hebrew and Aramaic) is an internal representation of the lexeme.</li>
    <li><b>Strongs</b> (only Greek) is Strong&rsquo;s Number for the lexeme.</li> 
    <li><b>Lexeme</b> is the word (lexeme or lemma) that is translated here.</li>
    <li><b>Stem</b> (only Hebrew and Aramaic) is the verbal stem of the lexeme.</li>
    <li><b>Part of speech</b> (only Latin) is the part of speech of the lexeme.</li>
    <li><b>First occurrence</b> is a reference to the first time this lexeme occurs in the text
        database. This reference is a hyperlink that you can click with your mouse.</li>
    <li><b>English</b> contains the English translation of each word. The heading is a selection box
        where you can choose another language instead of English.</li>
    <li><b>Danish</b> (or whatever your target language is) is where you type your own
        translations.</li>
    <li><b>Modified?</b> &ndash; If you modify the translation of a term a red button labelled
        &ldquo;Revert&rdquo; will appear in the &ldquo;Modified?&rdquo; column. You can click this
        button to revert to the original string. Alternatively you can click the &ldquo;Revert
        all&rdquo; button at the bottom of the page to revert all your translations. Note: Once you
        have saved a page of translations, you cannot go back to the old translation.</li>
</ul>

<p>Once you are done with a page of translations, click the &ldquo;Submit changes&rdquo; button at
    the bottom of the page to save your changes.</p>
