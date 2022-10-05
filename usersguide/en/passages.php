<h1>Edit Exercise - Passages</h1>

<p>On the &ldquo;Passages&rdquo; tab you indicate which part of the Bible you want to use for the
    exercise:</p>

<?= $hdir->img('heb-passages-tab.png') ?>

<p>When generating questions for a student, Bible OL will choose sentences from the passages you
    specify here. You can click on the small plus signs next to the names of the books of the Bible.
    This will allow you to specify individual chapters or verses to use for the exercise.</p>

<p>The more passages you select, the more sentences Bible OL can choose from; but the more
sentences there are, the longer it will take the program to generate the exercise. Therefore it may
be a good idea to limit the selection. On most computers a selection comprising the entire New
Testament is no problem; but if the selectioncontains the entire Old Testament, the program may
appear somewhat slow.</p>

<p>The passages you specify here are, in general, only a suggestion for the student; the passage
    selection can normally be altered by the student when the exercise is run.</p>

<p>Below the passage selector, you can specify a few specialities about the exercise:</p>

<ul>
    <li> Should the &ldquo;Locate&rdquo; button be <?= help_anchor('variations#disabling_locate','shown or not') ?>?</li>
    <li> How many <?= help_anchor('variations#sentence_context','sentence of context') ?> should be
        shown before and after the relevant sentence?</li>
    <li> Should the number of questions be <?= help_anchor('variations#fixed_exercises','fixed') ?>
        or should the student be able to choose?</li>
    <li> Should the order of questions be random or <?= help_anchor('variations#fixed_exercises','fixed') ?>?</li>
</ul>

<p>If either the number of questions or the order of questions is fixed, the students cannot themselves
    choose the Bible passages for the exercise.</p>
