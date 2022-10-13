<?php if ($sub_article==='if'): ?>
    <h1>Interface Translation</h1>

    <p>&ldquo;Interface translation&rdquo; refers to the translation of the parts of the Bible OL web
    pages that are independent of Hebrew, Greek, and Latin.</p>

<?php else: ?>

    <h1>Grammar Translation</h1>

    <p>&ldquo;Grammar translation&rdquo; refers to the translation of terms relating to Hebrew, Greek,
        or Latin grammar.</p>

<?php endif; ?>


<p>If you have <i>translator</i> privileges, you can add or modify the translations of
    <?= $hdir->if_gr('the terms used in the Bible OL user interface','grammar terms used in the Bible OL') ?>
    Select the <i>Administration &gt; Translate <?= $hdir->if_gr('interface','grammar terms') ?></i> menu.
    This will bring up a web page with a list of the <?= $hdir->if_gr('interface','grammar') ?> terms that
    must have a translation Bible OL. It may look like this:</p>

<?= $hdir->img("tr_" . $hdir->if_gr("if","gr") . "1.png") ?>

<p>Under &ldquo;Target language&rdquo; you select the language of the tanslation you are creating.
    In the figure above this is Danish. Note that if you want to modify a special translation
    <?= help_anchor('variant','variant') ?>, you must select that variant from the <i>Variant</i>
    menu at the very top of the page (not shown in the figure above).</p>

<?php if ($sub_article==='if'): ?>
    <p>The various terms to translate have be grouped into &ldquo;text groups.&rdquo; A text group
        contains terms that are connected with similar functions. In the figure above, the selected
        text group is &ldquo;class&rdquo;, which contains terms relating to class management. As you
        can see, there are 23 terms in this group, and each page shows 20 terms. You can use the
        page selector (with the numbers 1 and 2 in the figure above) to switch between the pages.</p>

<?php else: ?>
    
    <p>The various terms to translate have be grouped under a so-called &ldquo;name prefix,&rdquo;
        which is a concept found in the underlying corpus databases. Under each name prefix, you
        will find terms that are grammatically connected in some way. In the figure above, the
        selected name prefix is &ldquo;emdrostype.case_t&rdquo;, which contains the names for Greek
        noun cases.</p>

<?php endif; ?>


<p>The red button labeled &ldquo;Show <?= $hdir->if_gr('162','2') ?> item(s) without
    translation&rdquo; in the figure above informs you that a total of <?= $hdir->if_gr('162 interface','two grammar') ?>
    terms do not have translations into Danish. If you click on the button, you will see a list of
    the <?= $hdir->if_gr('text groups','name prefixes') ?> and names of each untranslated term.</p>

<p>The table below the page selector contains five columns:</p>
<ul>
    <li><b>Symbolic name</b> is used internally in Bible OL to identify the term.
        <?php if ($sub_article==='if'): ?>
            The small triangle
            &#x23f6; next to the heading indicates that the table is currenly sorted by ascending
            symbolic name. If you click on &ldquo;Symbolic name&rdquo;, the triangle changes to &#x23f7; and
            the table will be sorted by descending symbolic name. You can click on the other blue
            heading (&ldquo;Danish&rdquo; in this example) to sort the table by that value.
        <?php endif ?></li>
    <li><b>Comment</b> contains additional information to the translator about where this term is
        used. This information is, regrettably, rather sparse.</li>
    <li><b>English</b> contains the English translation of each term. The heading is a selection box
        where you can choose another language instead of English.</li>
    <li><b>Danish</b> (or whatever your target language is) is where you type your own
        translations.</li>
    <li><b>Modified?</b> &ndash; If you modify the translation of a term a red button labelled
        &ldquo;Revert&rdquo; will appear in the &ldquo;Modified?&rdquo; column. You can click this
        button to revert to the original string. Alternatively you can click the &ldquo;Revert
        all&rdquo; button at the bottom of the page to revert all your translations. Note: Once you
        have saved a page of translations, you cannot go back to the old translation.</li>
</ul>

<?php if ($sub_article==='gr'): ?>
    <p>As illustrated in the figure above, in some cases you are allowed to prefix the translation
        with a # sign and a number. When the user is presented with a list of terms (case names in
        this example) they are normally sorted alphabetically, but if a # sign and a number is
        present, then that number is used to sort the list.</p>
<?php endif; ?>

<p>Once you are done with a page of translations, click the &ldquo;Submit changes&rdquo; button at
    the bottom of the page to save your changes.</p>

<?php if ($sub_article==='if'): ?>
    <h2>What if an interface translation is absent?</h2>

    <p>If an interface term does not have a translation in the currently selected
        <?= help_anchor('variant','variant') ?>, the translation from the main variant is used.</p>

    <p>If an interface term does not have a translation in the currently selected interface
    language, the English translation is used.</p>

    <p>If an interface term does not have a translation in English (not even an empty translation),
        the text is displayed as &ldquo;??xxxx??&rdquo;, where xxxx is the symbolic name of the
        term.</p>
<?php endif; ?>


