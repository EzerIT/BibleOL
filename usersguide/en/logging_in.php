<h1>Logging In</h1>

<p>You can use Bible OL to display biblical text and run some exercises without logging in to the
    system. But if you want to customize the way text is displayed and if you want to take full
    advantage of the system, you should have an account and log in when you use Bible OL.</p>

<h2>Creating An Account</h2>

<p>You can create an account yourself, or you can have your teacher create an account for you.</p>

<p>To create an account yourself, select <i>Login</i> from the <i>User Access</i> menu. This brings up
    the login page, where you can select <i>Create a new account</i>, or &ndash; if allowed by your
    installation &ndash; you can use an existing Google or Facebook account to log in.</p>

<h2>Advantages of Having An Account</h2>

<p>When you are logged in using an account, you get a number of extra possibilities:</p>

<ul>
    <li>You can customize the fonts used for displaying text.</li>
    <li>You can join classes set up by your teacher and access exercises that anonymous users cannot
        access.</li>
    <li>When you take an exercise, your progress can be recorded and you and your teacher can monitor
        and grade your progress.</li>
</ul>

<p>When you are logged in, an extra menu item appears on the Bible OL website: &ldquo;My Data&rdquo;. This menu
    has these items:</p>

<ul>
    <li><?= help_anchor('fontpref','<i>Font preferences</i>') ?> &ndash; Use this to change the fonts used to display Hebrew, Greek, or Latin.</li>
    <li><?= help_anchor('uprof','<i>Profile</i>') ?> &ndash; Here you can change your name, e-mail address, preferred interface language,
        and password. (If you are logged in via Google or Facebook, you can only change your preferred
        interface language.)</li>
    <li><?= help_anchor('enroll','<i>Enroll/unenroll in class</i>') ?> &ndash; Use this to join or leave a class.</li>
    <li><?= help_anchor('mystat','<i>My performance</i>') ?> &ndash; See how you are doing solving the exercises.</li>
</ul>

<p>To log out, select <i>Logout</i> from the <i>User Access</i> menu.</p>


<h2>Special Account Privileges</h2>

<p>A system administrator may assign special privileges to your account. The special privileges are:</p>

<ul>
    <li>Facilitator</li>
    <li>Translator</li>
    <li>Sysadmin</li>
</ul>

<p>The facilitator and translator privileges are independent of each other. A user may have either or
    both sets of privileges.</p>

<p>A sysadmin has all the privileges of both facilitators and translators plus additional privileges.</p>

<h3>Facilitator</h3>

<p>As a facilitator (or teacher) you have the following additional rights on the system:</p>

<ul>
    <li>Add, modify, or delete user accounts (except for facilitator, translator, or sysadmin accounts).</li>
    <li>Create and manage exercises.</li>
    <li>Create and manage exams.</li>
    <li>Create and manage classes.</li>
    <li>Monitor how the students in your classes are doing in the exercises.</li>
    <li>Assign or remove facilitator privileges to other accounts.</li>
</ul>

<p>When you are logged in with facilitator privileges, additional items appears in the &ldquo;My Data&rdquo; menu:</p>

<ul>
    <li><?= help_anchor('studentstat','<i>Students&rsquo; performance</i>') ?> &ndash; See how your students are doing solving the exercises.</li>
    <li><i>Grade Quizzes</i> &ndash; Grade students&rsquo; quizzes.</li>
    <li><i>Grade Exams</i> &ndash; Grade students&rsquo; quizzes.</li>
</ul>

<p>Additionally, you will see a new menu item &ldquo;Administration&rdquo; with these items:</p>

<ul>
    <li><?= help_anchor('usermgmt','<i>Users</i>') ?> &ndash; Manage user accounts.</li>
    <li><i>Classes</i> &ndash; Manage classes.</li>
    <li><?= help_anchor('exercise_mgmt','<i>Manage exercises.</i>') ?></li>
    <li><i>Manage exams.</i></li>
</ul>


<h3>Translator</h3>

<p>As a translator you have the right to modify the translation of</p>

<ul>
    <li>The user interface.</li>
    <li>The names for Hebrew, Greek, and Latin grammatical terms.</li>
    <li>The Hebrew, Greek, and Latin lexicons.</li>
    <li>Add new languages to the set of available languages.</li>
</ul>

<p>When you are logged in with translator privileges, you will see a new menu item
    &ldquo;Administration&rdquo; with these items:</p>

<ul>
    <li><i>Translate interface</i> &ndash; Translate the user interface.</li>
    <li><i>Translate grammar items</i> &ndash; Translate the names for Hebrew, Greek, and Latin grammatical terms.</li>
    <li><i>Translate lexicon</i> &ndash; Translate the Hebrew, Greek, and Latin lexicons.</li>
    <li><i>Download lexicon</i> &ndash; Download a translation of a Hebrew, Greek, or Latin lexicon.</li>
    <li><i>Available translations</i> &ndash; View the available translations and add new languages.</li>
</ul>

<h3>Sysadmin</h3>

<p>As a sysadmin (or system administrator) you have all the privileges of facilitators and translators
    plus these additional rights:</p>

<ul>
    <li>Add, modify, or delete user accounts (including facilitator, translator, or sysadmin accounts).</li>
    <li>Manage gloss links (see Section XXX).</li>
    <li>Change ownership of exercises.</li>
    <li>Manage exercises created by other facilitators.</li>
    <li>Manage exams created by other facilitators.</li>
    <li>Manage classes created by other facilitators.</li>
</ul>

<p>The &ldquo;Administration&rdquo; menu will have one additional item:</p>

<ul>
    <li><i>Gloss links</i> &ndash; See Section XXX.</li>
</ul>

