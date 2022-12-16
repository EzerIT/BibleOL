<h1>Gloss links</h1>

<p>Bible Online Learner allows system administrators to add URLs to individual Hebrew or Aramaic
    lexemes. Each URL can be associated with an icon. The user may see these links by selecting
    <i>Word &gt; Lexeme &gt; link</i> in &ldquo;MyView&rdquo;, or by hovering the mouse over a word,
    or by clicking a word. For example, Genesis&nbsp;1:1-2 may be displayed thus:</p>


<?= $hdir->img("gloss_link1.png") ?>

<p>The icons below some of the words are hyperlinks. If users click on them, they will be taken to a
    website that, hopefully, provides additional information about that lexeme.</p>

<p>In order to change or add these gloss links you need <i>Sysadmin</i> privileges on the
    system. If you have that, selecting the <i>Administrator &gt; Gloss link</i> menu will take you
    to a page where you can edit the links. It looks like this:</p>

<?= $hdir->img("gloss_link2.png") ?>

<p>This figure above shows the start of the overview of Hebrew words. Further down the page you will
    find Aramaic. You can choose to view the words by frequency or alphabetically.
    If, for example, you click the button labelled &ldquo;1-300&rdquo;, you will see a list of
    the 300 most common Hebrew words. Alternatively, you can look up the
    word alphabetically.</p>

<p>Once you have click a button for the word range you wish to see, you will be taken to a web page
    that looks similar to this one:</p>

<?= $hdir->img("gloss_link3.png") ?>

<p>Above this table, you will find buttons for selecting other word ranges.</p>

<p>The table contains five columns:</p>
<ul>
    <li><b>Lexeme</b> is the lexeme that you wish to associate with a URL.</li>
    <li><b>English</b> is the English translation of that lexeme.</li>
    <li><b>Icon</b> shows the icon(s) currently associated with that lexeme. Each icon will be a
        hyperlink. A maximum of three hyperlinks can be associated with a lexeme.</li>
    <li><b>Link</b> contains the word &ldquo;Link&rdquo; which you can click to check that you have
        entered the URL correctly.</li>
    <li><b>Operations</b> contains buttons that allow you to add, delete, or modify links.</li>
</ul>
