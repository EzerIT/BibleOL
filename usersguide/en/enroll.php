<h1>Class Membership</h1>

<p>Classes serve a double purpose in Bible OL: Firstly, access to certain exercises may be
    restricted to members of certain classes. Secondly, teachers can monitor the students enrolled
    in a class and gather statistics about how each student is progressing.</p>

<p>When you look at the list of folders containing exercises, you may see that some of the folders
    are marked &ldquo;Restricted access&rdquo;:</p>

<?= $hdir->img('toplevel.png') ?>

<p>&ldquo;Restricted access&rdquo; means that you have to be enrolled in a particular class to
    access the exercises in that folder.</p>

<h2><a name="enroll_in_class"></a>Enrolling in a Class</h2>

<p>In order to enroll in a class you must be <?=
    help_anchor('logging_in','logged into Bible OL') ?>. Once you are logged in,
    there are a number of methods you can use to enroll in a class:</p>

<p><i>Method 1: Teacher enrolls student.</i> If your teacher knows that you want to participate in a
    particular class, they can enroll you in that class.</p>

<p><i>Method 2: Enroll through menu.</i> If you select the menu <i>My data &gt; Enroll/unenroll in
    class</i>, you will see a list of the available classes and the classes in which you already
    participate. For example, thus:</p>

<?= $hdir->img('enroll1.png') ?>

<p>You can enroll in a class by clicking the blue &ldquo;Enroll&rdquo; button next to a class name.
    The teacher may have assigned a password to the class. In that case you will be asked to provide
    the password when you enroll.</p>

<p>If you are already enrolled in a class, you can withdraw from the class by clicking the yellow
    &ldquo;Unenroll&rdquo; button next to the class name.</p>

<p><i>Method 3: Access restricted folder.</i> If you try to access a folder marked &ldquo;Restricted
    access&rdquo; you will be offered the option to enroll in a class that has access to the folder,
    if any such class exists.</p>

<h2><a name="control_monitoring"></a>Controlling Teacher&rsquo;s Monitoring</h2>

<p>Every time you complete an exercise, information about how you did will be stored in the system's
    database, where it will be accessible to the teacher. You may want to restrict your
    teacher&rsquo;s access to this information.</p>

<p>When you have answered the questions in an exercise, you will have two ways to finish it. You can
    either click the &ldquo;Grade task&rdquo; or the &ldquo;SAVE outcome&rdquo; button:</p>

<?= $hdir->img("heb-exer2.png") ?>

<p>If you click the &ldquo;Grade task&rdquo; button your results will be saved and your teacher may
    use that result in grading your performance.</p>

<p>If you click the &ldquo;SAVE outcome&rdquo; button, your results will still be saved, but they
    will not be used to grade your performance. Whether your teacher is able to see how well you
    have done or not, is up to you. By default, your teacher will not be able to see exercises that
    were ended by &ldquo;SAVE outcome&rdquo;, but if you want to grant your teacher access to that
    information, you can do it in this manner:</p>

<p>If you select the menu <i>My data &gt; Enroll/unenroll in class</i>, you will see a list of the
    available classes. One of your classes may look like this:</p>

<?= $hdir->img('enroll2.png') ?>

<p>This means that your teacher cannot see the results of exercises you end by clicking the
    &ldquo;SAVE outcome&rdquo; button. If you click the green &ldquo;Grant access&rdquo; button
    above, the window will change to this:</p>

<?= $hdir->img('enroll3.png') ?>

<p>This means that your teacher will be able to see the results of exercises you end by clicking the &ldquo;SAVE
    outcome&rdquo; button. They will, however, still not participate in the grading process. You can
    revoke your permission by clicking the red &ldquo;Revoke access&rdquo; button.</p>

