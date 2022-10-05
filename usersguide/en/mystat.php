<h1>How Am I Doing?</h1>

<p>Every time you run an exercise, statistics about that exercise is stored in the system. If you
    terminated the exercise by pressing &ldquo;GRADE task&rdquo;, your results will be available to
    your teacher. If you terminated the execise by pressing &ldquo;SAVE outcome&rdquo;, only the
    time you spent on the exercise will be available to your teacher, and only if you granted the
    teacher access when you <?= help_anchor('enroll#enroll_in_class','enrolled in the relevant class') ?>.</p>

<p>You can always check the status of your own progress. If you select the menu <i>My Data &gt; My
    performance</i> you will see a bar graph similar to this one:</p>

<?= $hdir->img("stat1.png") ?>

<p>The horizontal axis is the <?= anchor('https://www.tondering.dk/claus/cal/week.php#weekno','ISO
    week number',['target'=>'_blank']) ?>, and the vertical axis shows the number of hours you spent
    on exercises during each week.</p>

<p>The blue &ldquo;Show selector&rdquo; button at the top allows you to specify the date range you
    are interested in. A maximum of 26 weeks (6 months) can be shown. You can also limit the data to
    exercises in a particular class in which you are enrolled.</p>

<p>The blue &ldquo;Show table&rdquo; button below the bar graph allows you to see the data in the
    form of a table.</p>

<p>Below this graph, another graph shows the time spent on each exercise. It may look like this:</p>

<?= $hdir->img("stat2.png") ?>

<p>In this example, you have spent almost 60 minutes on the exercise ETCBC4/demo/demo1 and 20
    minutes on the exercise ETCBC4/demo/demo2. <a name="exlinks"></a>The exercise names are hyperlinks, and clicking a
    name brings up details about that exercise. For example, clicking
    &ldquo;ETCBC4/demo/demo1&rdquo; may display this graph:</p>

<?= $hdir->img("stat3.png") ?>

<p>This table shows how many correct answers you had each day you ran the exercise. If you use your
    mouse to point to a data point, the date and answer speed will be displayed:</p>

<?= $hdir->img("stat4.png") ?>

<p>Below this graph, another graph displays your answer speed each day, and hovering your mouse over
    a data point shows the percentage of correct answers. A final graph shows a total of how well
    you answered each of the <?= help_anchor('terminology#display_request_feature','request features') ?>:</p>

<?= $hdir->img("stat5.png") ?>
