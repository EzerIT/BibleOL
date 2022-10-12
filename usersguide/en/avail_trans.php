<h1>Available Translations</h1>

<p>If you have <i>translator</i> privileges, you can see an overview of the available translations
    by selecting the <i>Administration &gt; Available translations</i> menu. This will bring up a
    web page with an alphabetical list of all the translations currently available (to some degree)
    in Bible OL. An entry may look like this:</p>

<?= $hdir->img("avail_trans.png") ?>

<p>This entry gives information about the translation into Spanish. The three green buttons indicate
    that Spanish is available for the webpage itself (that is, user interface and grammatical terms)
    and for the Hebrew/Aramaic and Greek lexicons. The two red buttons indicated that a Spanish
    translation of the two Latin lexicons is not available.</p>

<p>You can click on the green or red buttons to disable or enable the corresponding translation.
    Disabling a translation does not delete the translation from the system; if you enable the
    translation again, all the translated terms still exist.</p>

<p>Enabling a new translation does not, of course, mean that a translation of the terms magically
    appears; it merely means that you can now enter translations of the various terms into the
    system.</p>

<p>The information under the heading &rdquo;Webpage completeness&rdquo; indicates how complete the
    translation of the interface and the grammatical terms is.</p>

<p>If you want to add a completely new language, press the &ldquo;Add language&rdquo; button at the
    bottom of the page. This will display this dialog:</p>

<?= $hdir->img("avail_trans2.png") ?>

<p>As the warning indicates, it is very important that the information you enter is correct, since
    it is quite difficult to change the information later.</p>

<p>If, for example, you want to add translations into Czech, you should specify these three
    terms:</p>
<ul>
    <li>Internal name of language: czech</li>
    <li>Native name of language: Čeština</li>
    <li>ISO language code: cs</li>
</ul>

<p>The internal name of the language and the language code are not shown to the users, and they
    could therefore, technically, be anything. But for practical reasons it is convenient that they
    have sensible values like the English name of the language and the ISO 2-letter abbreviation of
    the language.</p>
    
<p>The native name of the language will be added to the <i>Language</i> menu at the top of the
    pages.</p>
