<h1>Class Management</h1>

<p>Classes serve a double purpose in Bible OL: Firstly, class control access to collections of
    exercises. Exersizes are stored in a hierarchy of folders, and access the contents of a folder
    can be restricted to students enrolled in a particular class. Secondly, teachers can monitor the
    students enrolled in a class and gather statistics about how each student is progressing.</p>

<p>To open class management, select <i>Administration &gt; Classes</i>. You will then see a table
    that looks something like this:</p>

<?= $hdir->img('classes1.png') ?>

<p>Here you see a list of all the classes that exist in the system. We assume that you are Lilian
    Lane. You are listed as the owner of the class &ldquo;Greek part 1&rdquo;. Your colleague Andy
    Anderson is the owner of the two Hebrew classes.</p>

<h2>Adding a New Class</h2>

<p>To add a new class, click the &ldquo;Add new class&rdquo; button. You will then see this
    dialog:</p>

<?= $hdir->img('classes2.png') ?>

<p>Enter this information about your new class:</p>

<ul>
    <li><i>Class name</i> &ndash; Choose a useful description of your class.</li>
    <li><i>Class password</i> &ndash; This field is optional. If you write a text here, students must know
        that password in order to enroll in the class.</li>
    <li><i>Enroll before</i> &ndash; This field is optional. If you specify a date here, enrollments
        after that date will not be accepted.</li>
</ul>

<p>When you press &ldquo;OK&rdquo;, your class will be created:</p>

<h2>Operations on an Existing Class</h2>

<p>In the table of classes, you'll find three or four buttons under the heading
    &ldquo;Operations&rdquo;:</p>

<ul>
    <li><i>Assign users</i> &ndash; Here you can assign or remove students to your class. See below.</li>
    <li><i>Edit</i> &ndash; This lets you modify the name, password and enrollment deadline of your class.</li>
    <li><i>Delete</i> &ndash; This deletes your class.</li>
    <li><i>Change owner</i> &ndash; (Available only if you have <i>sysadmin</i> privileges.) Allows
    you to transfer ownership of a class from one teacher to another.</li>
</ul>

<p>Normally, you can only modify classes you own, but if you have sysadmin privileges, you can
    modify any class in the system.</p>

<h2>Enrolling Students</h2>

<p>There are three ways to enroll students in a class: The <?=
    help_anchor('enroll','students can enroll themselves') ?>, or the teacher can enroll them
    through the Class Management menu discussed here or through the <?= help_anchor('usermgmt','User Management') ?> menu.</p>

<p>If you want to prevent students from enrolling themselves, you have two options:</p>

<ul>
    <li>Specify a class password, but don&rsquo;t tell it to the students. In this case, potential
        students will be able to see that your class is available, but they can&rsquo;t enroll
        themselves.</li>
    <li>Specify an &ldquo;Enroll before&rdquo; date in the past. In this case, potential students
        will not be able to see that your class is available.</li>
</ul>

<p>To enroll students in your class using the menu we are discussing here, press the &ldquo;Assign
    users&rdquo; button. This will show you a list of all users. (This can be a very long list
    indeed.) Here you can add or remove students from your class. The &ldquo;Enroll before&rdquo;
    date does not affect your ability to enroll students in your own class.</p>
