<?php $hg = $hdir->heb_gr('Hebrew','Greek') ?>

<h1>Viewing a <?= $hg ?> Text</h1>

<p>To see <?= $hdir->heb_gr('Genesis 1:1-7','Luke 2:1-5') ?> fill out the text selection dialog thus:</p>

<a name="select-dialog"></a><?= $hdir->img("selecttext-$sub_article.png") ?>

<p>(For information about the &ldquo;Show link icons&rdquo; checkbox, see the article <?= help_anchor('link_icons','&ldquo;Link icons&rdquo;.') ?>)</p>

<p>Then click the <i>Display</i> button. This will show you <?= $hdir->heb_gr('Genesis 1:1-7','Luke 2:1-5') ?> in <?= $hg ?>:</p>

<?= $hdir->img("$sub_article-text-a.png") ?>

<?php if ($sub_article=='heb'): ?>
    <p>The small icon to the right of the first verse is a link to the same text at the
        <?= anchor('https://shebanq.ancient-data.org/','SHEBANQ',['target'=>'_blank']) ?>
        website.</p>
<?php endif; ?>

<a name="view_grammar"></a><h2>Viewing <?= $hg ?> Grammar Information</h2>

<p>There are different ways to display grammar information:</p>

<ul>
  <li>Hovering the mouse over a word or sentence part. (This is not available on tablets or
      smartphones.)</li>
  <li>Clicking a word or sentence part.</li>
  <li>Using the &ldquo;MyView&rdquo; selector.</li>
</ul>

<p>On a computer, you can use your mouse to point to a word in the text (known as letting your
  mouse &ldquo;hover&rdquo; over a word). You will then see a so-called <i>grammar information
    box.</i> to the right of the text:</p>

<a name="grammar-info-box"></a><?= $hdir->img("$sub_article-text-b.png") ?>

<p>In the grammar information box you will see detailed information about the word your mouse points
    to. When you move the mouse, the grammar information box disappears. Also, the grammar information box may
    be too tall to fit on your computer screen. You may find this inconvenient,
    so instead you can use the following method:</p>

<p>On a computer, table, or smartphone, you can click or tap on a word. In that case, a dialog box will
    appear containing the grammar information box. (You can click the × at the top of the dialog box or
    the Close button at the bottom of the box to close the dialog. Alternatively, press the &ldquo;Esc&rdquo; key
    on your keyboard.)</p>

<p>A third way to display grammar information is to use the &ldquo;MyView&rdquo; selector as described in the
    following section.</p>

<h2>The &ldquo;MyView&rdquo; Selector</h2>

<p>Above the <?= $hg ?> text you see an &ldquo;eye&rdquo; labelled &ldquo;MyView&rdquo;:
    <img alt="MyView" src="<?= $hdir->get_dir() ?>/images/myview.png">.
    If you click the eye icon, the system will show the so-called <i>grammar selection box.</i> At the same
    time the eye icon turns into a × icon. The grammar selection box looks like this:</p>

<?= $hdir->img("$sub_article-gram-sel-a.png") ?>

<p>The <?= $hg ?> grammar selection box contains four buttons, identifying the four levels of the
    grammar hierarchy used by the <?= $hg ?> text: The text contains <i>sentences,</i> which contain
    <i><?= $hdir->heb_gr('clauses','level 2 clauses') ?>,</i> which contain
    <i><?= $hdir->heb_gr('phrases','level 1 clauses') ?>,</i> which contain
    <i>words.</i> You can click on each of these to display relevant grammar information.</p>

<p>If, for example, you click the <i>Word</i> button and then the <i>Lexeme</i> button, the grammar
    selection box looks like this:</p>

<?= $hdir->img("$sub_article-gram-sel-b.png") ?>

<p>If you now click the <i>Part of speech</i> button, the <?= $hg ?> text changes to look as in the figure
    below, where you can see the part of speech of each word of the text.</p>

<?= $hdir->img("$sub_article-text-c.png") ?>

<p>You can add additional information by clicking the relevant buttons in the grammar selection box.</p>

<p>You can use the <i>Clear grammar</i> button to remove all the selected grammar information, and you can use the ×
    icon to hide the grammar selection box.</p>

<p>The grammar selection box also allows you to see borders between <?= $hdir->heb_gr('phrases, clauses, ','clauses ') ?>
or sentences, as well as grammatical information about each of these. If, for example, you click
<?= $hdir->heb_gr('Clause','Clause level 1') ?>, and then select
    <i>Show border</i>, you will see the borders of each clause.</p>

<p>By pointing your mouse to the word &ldquo;<?= $hdir->heb_gr('Clause','Clause1') ?>&rdquo; on the
border of a particular Clause (or, alternatively, clicking the word &ldquo;<?= $hdir->heb_gr('Clause','Clause1') ?>&rdquo;),
    a grammar information box for the particular clause will be shown:</p>

<?= $hdir->img("$sub_article-text-d.png") ?>

<?php if ($sub_article=='heb'): ?>
    <p>Sometimes clauses (or other parts of a sentence) can contain other clauses inside them. An
        example of this is seen in Genesis 1:7:</p>

    <?= $hdir->img("heb-text-e.png") ?>

    <p>Here, you can see how the clause <span class="help hebrew">וַיַּבְדֵּ֗ל בֵּ֤ין הַמַּ֨יִם֙ וּבֵ֣ין הַמַּ֔יִם</span> is split
        in two and contains the clause <span class="help hebrew">אֲשֶׁר֙ מִתַּ֣חַת לָרָקִ֔יעַ</span> inside
        it. The split clause is marked by its missing left and right borders.</p>
<?php endif; ?>

<?php if ($sub_article=='gr'): ?>
    <p>Some words may not belong to a particular clause, and a clause may be split into parts. In
    the figure above, the word καὶ at the start of verse 3 is not a member of a clause; and the
    clause in verse 1 is split into two parts around the word δὲ, which is not part of
    the clause. The split clause is marked by its missing right and left borders.</p>
<?php endif; ?>

<!--<p>The different items you can select in the grammar selection box are detailed
    <?= anchor("help/show_help/all_features/$sub_article", 'here') ?>, but
    <?= $hdir->heb_gr('a few items are','one item is') ?> worth mentioning here:</p>-->

<p>In the grammar selection box, under <i>Word</i> and <i>Lexeme</i> you can enter a &ldquo;Word frequency
color limit&rdquo;. Setting this value to, for example, 50, means that the 50 most common
<?= $hdir->heb_gr('Hebrew or Aramaic','Greek') ?> words in the <?= $hdir->heb_gr('Old','New') ?>
    Testament will be displayed in black, whereas rarer words will be displayed in blue:</p>

<?= $hdir->img("$sub_article-text-f.png") ?>

<p>If you are learning <?= $hg ?>, you may find this feature useful when deciding if a word is worth memorizing.
When determining how common words are, different morphological forms of the same word are counted as one.</p>

<?php if ($sub_article=='heb'): ?>
    <p>In most cases, the information you find in the grammar information box will be the same as
        what is shown between the lines using the &ldquo;MyView&rdquo; selector; but for glosses this is not the
        case. For example, in the grammar information box shown in <a href="#grammar-info-box">one of the figures above</a>
        you can see that the lexeme <span class="help hebrew">היה</span> is translated into English as &ldquo;be,
        happen, become, occur&rdquo;. But if you open the &ldquo;MyView&rdquo; selector and choose
        <i>Word</i> and <i>Glosses</i> and <i>English</i>, only
        the first gloss, &ldquo;be&rdquo;, will be shown between the lines of Hebrew text.</p>

    <h2>Viewing a Transliterated Hebrew text</h2>

    <p>You can choose to view a Hebrew text in Latin letters rather Hebrew letters. You do this by
        specifying the corpus &ldquo;Hebrew (ETCBC4, Transliterated, OT)&rdquo; in the
        <a href="#select-dialog">text selection dialog</a>.
        The first two verses of Genesis 1 will look like this:</p>

    <?= $hdir->img("heb-translit.png") ?>

    <p>You will notice that this text uses a number of variations of Latin letters plus
        the special characters <span class="help hebrew_translit">ʔ</span> (not to be
        confused with a question mark) and <span class="help hebrew_translit">ʕ</span>.
        These two characters correspond to the Hebrew characters <span class="help hebrew">א</span> and
        <span class="help hebrew">ע</span>, respectively.</p>
<?php endif; ?>
