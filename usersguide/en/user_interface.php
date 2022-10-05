<h1>The User Interface</h1>

<p>You access Bible OL through a web browser using an appropriate URL. Most users will use
    <a href="https://bibleol.3bmoodle.dk" target="_blank">https://bibleol.3bmoodle.dk</a>.</p>

<p>If you access the Bible OL main web site using a computer, you will see an introductory page,
    at the top of which there is a menu:</p>

<?= $hdir->img("menu1.png") ?>

<p>If you are using a smartphone, you will see a small rectangle with
    three horizontal lines at the top right of the screen. Tap that rectangle to display the menu:</p>

<?= $hdir->img("menu2.png") ?>

<p>The menu has five items:</p>

<ul>
<li><i>Home</i> &ndash; Selecting this item, takes you to the main web page.</li>
<li><i>Text and Exercises</i> &ndash; This allows you to view the Hebrew and Greek biblical texts and to run exercises.</li>
<li><i>User Access</i> &ndash; Here you can log in to the system and view the privacy policy.</li>
<li><i>Language</i> &ndash; This lets you select the language of the user interface.</li>
<li><i>Variant</i> &ndash; Here you can select between different <?= help_anchor('variant','variants') ?> of the terms and translations used.</li>
</ul>


<p>If you have a user account (see <a href="">Logging in</a>), you can select Login from the User Access menu to
    access your personal features of the system. Once you have logged, in, a new menu item appears:</p>

<ul>
    <li><i>My Data</i> &ndash; This menu item lets you change your <?= help_anchor('uprof', 'user profile') ?>,
        set up <?= help_anchor('fontpref','font preferences') ?>,
        <?= help_anchor('enroll#enroll_in_class', 'enroll in classes') ?>,
        and view how you are doing on the exercises.</li>
</ul>
