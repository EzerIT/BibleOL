<h1>Tabs</h1>

<p>When a teacher edits an exercise, the program shows five tabs at the top of the edit window:</p>

<dl>
    <dt><?= anchor('help/show_help/description', 'Description') ?></dt>
    <dd>Shows which database is being used. The tab also contains a description
        of the exercise.</dd>

    <dt><?= anchor('help/show_help/passages', 'Passages') ?></dt>
    <dd>Shows which parts of the database are used for this exercise. The
        selected passages can often be modified by the student.</dd>

    <dt><?= anchor('help/show_help/sentences', 'Sentences') ?></dt>
    <dd>Shows the criteria used by the program when it chooses sentences in the
        database. Frequently, these criteria are also used for choosing sentence
        units within each sentence; in that case the &ldquo;Sentence Units&rdquo; tab is not available.</dd>
    
    <dt><?= anchor('help/show_help/sentence_units', 'Sentence Units') ?></dt>
    <dd>Shows the criteria used by the program when it chooses sentence units
        within each selected sentence. The program presents these sentence units (which are normally just
        words) to the student as questions.</dd>

    <dt><?= anchor('help/show_help/features', 'Features') ?></dt>
    <dd>Shows which sentence unit features the program will show to the student
        and which features it requests from the student.</dd>
</dl>
