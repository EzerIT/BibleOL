<h1>User Management</h1>

<p>Users with <i>facilitator</i> privileges can add, modify, and delete user accounts, except
    accounts belonging to system administrators and other facilitators.</p>

<p>To open user management, select <i>Administration &gt; Users</i>. You will then see a table
    that looks something like this:</p>

<?= $hdir->img('users1.png') ?>

<p>The table can be quite wide (and is not suitable for managing on a handheld device). If the table
is wider than your browser window, you can drag the bar indicated by the arrow below to scroll left and right:</p>

<?= $hdir->img('users2.png') ?>

<p>The figures above show one page of the list of users. The number of users on each page is
    configured inside Bible OL and although the number is five in the illustrations above, the
    actual number is typically larger than five . Below the text &ldquo;Each page shows ...
    users&rdquo;, you see a page selector. Clicking on one of the numbers there takes you to that
    page.</p>

<p>The small triangle &#x23f6; next to the heading &ldquo;User name&rdquo; indicates that the table
    is currently sorted by ascending user name. If you click on &ldquo;User name&rdquo;, the triangle
    changes to &#x23f7; and the table will be sorted by descending user name. You can click on any
    of the other blue headings to sort the table by that value.</p>

<p>To the right of each user there are buttons that allow you to assign the user to a class, edit
    the user&rsquo;s profile, or delete the user. (In the above example, you cannot delete user John
    Doe because he has facilitator privileges like you.)</p>

<p>The &ldquo;Add new user&rdquo; button at the bottom allows you to add a user account. In this
    case the user does not need to have an email address.</p>

<h2>Enrolling Students in a Class</h2>

<p>There are three ways to enroll students in a class: The <?=
    help_anchor('enroll#enroll_in_class','students can enroll themselves') ?>, or the teacher can enroll them
    through the User Management menu discussed here or through the <?= help_anchor('classes','Class Management') ?> menu.</p>

<p>To enroll students in your class using the menu we are discussing here, press the &ldquo;Assign
    to class&rdquo; button. This will show you a list of all your classes. Here you can add or
    remove the selected student from your classes. The &ldquo;Enroll before&rdquo; date of your
    class does not affect your ability to enroll students in your own class.</p>

<p>If you want to prevent students from enrolling themselves in a class, see the page on
    <?= help_anchor('classes','Class Management') ?>.</p>
