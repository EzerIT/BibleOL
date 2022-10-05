<h1>How Are My Students Doing?</h1>

<p>Every time students run an exercise, statistics about that exercise is stored in the system. If a student
    terminates the exercise by pressing &ldquo;GRADE task&rdquo;, the results will be available to
    you if you own the class in which the student is enrolled.</p>

<p>To check the status of your students&rsquo; progress, select the menu <i>My Data &gt; Students&rsquo;
    performance</i>. You will then see a page like this:</p>

<?= $hdir->img("stustat1.png") ?>

<p>For each class you own, you have two buttons, one labelled &ldquo;Students&rdquo; and one
    labelled &ldquo;Execises&rdquo;.</p>

<h2>The &ldquo;Students&rdquo; Button</h2>

<p>If you click the &ldquo;Students&rdquo; button, you will see a bar graph similar to this one:</p>

<?= $hdir->img("stustat2.png") ?>

<p>The horizontal axis is the <?= anchor('https://www.tondering.dk/claus/cal/week.php#weekno','ISO
    week number',['target'=>'_blank']) ?>, and the vertical axis shows the total number of hours spent
    on exercises during each week by students in the selected class.</p>

<p>The blue &ldquo;Show selector&rdquo; button at the top allows you to specify the date range you
    are interested in. A maximum of 26 weeks (6 months) can be shown.</p>

<p>Below this graph, another graph shows the time spent by each student. It may look like this:</p>

<?= $hdir->img("stustat3.png") ?>

<p>In this case there are two students in the class. Each color represents a student, and you can
    see the meaning of the colors in the &ldquo;Students&rdquo; box at the left top of your
    screen:</p>

<?= $hdir->img("stustat4.png") ?>

<p>Here you see that the red color corresponds to John Doe and the green color to Judith Jameson.
    You can use the check boxes to remove information about individual students from the graph.
    Also, you can click a student&rsquo;s name; this will bring up a page showing that
    student&rsquo;s progress similar to what the student can see themselves under the menu <i>My Data &gt;
    <?= help_anchor('mystat','My performance') ?></i>.</p>

<p>The blue &ldquo;Show table&rdquo; button below the bar graph allows you to see the data in the
    form of a table.</p>

<h2>The &ldquo;Exercises&rdquo; Button</h2>

<p>If you click the &ldquo;Exercises&rdquo; button, you will see a dialog similar to this one:</p>

<?= $hdir->img("stustat5.png") ?>

<p>Here you must provide a date range (at most 26 weeks or 6 months). Select an exercise in the
    &ldquo;Exercise&rdquo; drop-down menu, and indicate if exercises marked as not intended for
    grading should be included.</p>

<p>You will then see graphs similar to the ones <?= help_anchor('mystat#exlinks','students can see for their
   own exercises') ?>, except that here each student&rsquo;s data is included:</p>

<?= $hdir->img("stustat6.png") ?>

<p>Just like students can do, you can hover your mouse over data points to get more information. And
    just as mentioned above, you can remove information about each student by changing the
    checkboxes int the &ldquo;Student&rdquo; box at the top left of your screen.</p>
