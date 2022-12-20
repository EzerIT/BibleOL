<h1>Variations to Exercises</h1>

<p>The examples presented in the previous sections illustrate how most exercises work. However, a
    teacher can vary certain details about the way exercises look to the student. These details are
    listed in the following sections.</p>

<h2><a name="disabling_locate"></a>Disabling &ldquo;Locate&rdquo;</h2>

<p>A teacher may disable the &ldquo;Locate&rdquo; button in an exercise.</p>


<h2><a name="sentence_context"></a>Sentence Context</h2>

<p>Normally, an exercise will show you one sentence at a time. However, an exercise may be
    constructed to display a few sentences surrounding the sentence in question. For example, the
    following figure shows one sentence in gray before and after the sentence that the exercise is
    concerned with.</p>

<?= $hdir->img('context.png') ?>


<h2><a name="fixed_exercises"></a>Fixed Exercises</h2>

<p>Normally, the sentences for exercises are chosen at random from a set of Bible passages. This
    means that every time you run an exercise, you may see new sentences.</p>

<p>However, an exercise may be constructed to display a fixed set of questions in a fixed order. If
    that is the case, you will always see the same sentences every time you run the exercise.</p>

<p>Furthermore, the student can normally choose between seeing 5, 10, or 25 questions in an
    exercise; but a teacher may restrict this number so that only a fixed number of questions can be
    shown.</p>

<p>If either the order of questions or the number of questions is fixed, the students cannot
    themselves choose the Bible passages for the exercise.</p>


<h2>Hints</h2>

<p>Sometimes a word form, taken on its own, may have multiple interpretations. For example,
    the word form <span class="help hebrew">תֶחֱזֶה</span> can be both 2nd person masculine and 3rd
    person feminine. A teacher may configure an exercise to provide hints to the correct
    interpretation. The following figure shows a sentence where the student is asked to provide the
    gender for this particular word. A hint tells the student that we are dealing with a 2nd person
    form, which aids the student in selecting the correct gender, masculine:</p>

<?= $hdir->img('ambig.png') ?>


<h2>Hidden Information</h2>

<p>When viewing text outside an exercise, you have access to a considerable amount of grammatical
    information. When doing an exercise, some of that information may be inaccessible, either
    because the information would give away the correct answer, or because the teacher has
    deliberately hidden some information.</p>

<p>As an example, consider the Hebrew exercise <i>demo1</i>, presented in <?=
    anchor("help/show_help/firstex/heb","the first example of a Hebrew exercise") ?>, in which you
    are required to provide the gender of a Hebrew noun. Gender information in normally available
    via the &ldquo;MyView&rdquo; selector, but as the following figure shows, the &ldquo;person,
    gender, number&rdquo; button has been disabled. Also, hovering the mouse over a word or clicking
    a word, will not display gender information.</p>

<?= $hdir->img('no-gender.png') ?>


<h2>Typing Nothing</h2>

<p>Occasionally you come across a question whose answer is <i>nothing.</i></p>

<p>Imagine, for example, a quiz about the English language in which you are required to type the
    plural ending of various words. What is the plural ending of the word &ldquo;cow&rdquo;? It is,
    of course, &ldquo;s&rdquo; since the plural of &ldquo;cow&rdquo; is &ldquo;cows&rdquo;. But what
    is the plural ending of the word &ldquo;sheep&rdquo;? Since the plural of &ldquo;sheep&rdquo; is
    &ldquo;sheep&rdquo;, the plural ending is &ldquo;&nbsp;&rdquo; &ndash; nothing!</p>

<p>How do you type nothing? If you leave an input field empty, Bible OL will think that you have not
    yet answered the question. The correct way to indicate that an answer is an empty text is to
    type a single dash (hyphen, minus) &ldquo;-&rdquo; in the answer field.</p>

<p>As an example, consider this question:</p>

<?= $hdir->img('empty-answer.png') ?>

<p>Here, Bible OL asks for the pronominal suffix of the word <span class="help hebrew">כְבֹ֥וד</span>;
    but that word has no pronominal suffix, so the correct answer is an empty text, which you
    indicate by typing a dash by pressing the character button indicated by the red arrow in the
    illustration.</p>

