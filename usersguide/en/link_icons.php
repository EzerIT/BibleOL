<h1>Link Icons</h1>

<p>When you specify that you select the <i>Text and exercises &gt; Display text</i> menu item, you
    are shown this dialog:</p>

<?= $hdir->img("selecttext.png") ?>

<p>At the bottom of this dialog, there is a checkbox labelled &ldquo;Show link icons&rdquo;.
    Currently, this only has an effect when working with the Old Testament, and only if you are
    using the Bible OL installation at <?= anchor("https://bibleol.3bmoodle.dk",
    "bibleol.3bmoodle.dk", ['target'=>'_blank'])?>.</p>

<p>If you put a check mark in this checkbox, you will see a icon with a P in a blue circle at the
    start of certain verses. For example Genesis&nbsp;2:14:</p>

<?= $hdir->img("picture_link.png") ?>

<p>This P is a hyperlink to a set of pictures relating to that verse. The pictures are located at
    the website <?= anchor("https://resources.3bmoodle.dk", "resources.3bmoodle.dk", ['target'=>'_blank']) ?></p>

<p>Instead of a P in a blue circle you may find a D, V, or U in a green circle. In this case the
    letter is a hyperlink to a document, video, or other URL. These other icons are, however, almost
    unused at present.</p>

