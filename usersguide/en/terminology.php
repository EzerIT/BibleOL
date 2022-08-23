<h1>Terminology</h1>

<p>Below you will find an alphabetical list of some of the terms used in connection with Bible Online Learner.</p>

<h2><a name="display_request_feature"></a>Display Feature and Request Feature</h2>

<p>When running an exercise, the Bible OL will display a list of <a href="#feature">features</a> for
    each <a href="#question_object">question object</a>. The &ldquo;display features&rdquo; on the list give information
    about the question object; the &ldquo;request features&rdquo; have values that the student should provide:</p>

<?= $hdir->img("heb-exer1b.png") ?>


<h2><a name="feature"></a>Feature</h2>

<p>A <a href="'#sentence_unit">sentence unit</a> has various <i>features.</i> A feature has a name
    and a value. For example, a word can have a feature called <i>part of speech</i> with the value <i>noun</i>
    and a feature called <i>gender</i> with the value <i>masculine.</i> An important feature is called
    <i>text,</i> which simply refers to the actual characters making up the word; for example, the very
    first word of the Greek New Testament has a <i>text</i> feature with the value &ldquo;Βίβλος&rdquo;.</p>

<h2><a name="question_object"></a>Question Object</h2>

<p>When an exercise is being run, the system selects certain <a href="'#sentence_unit">sentence units</a>
    (typically words) and asks the student about them. These selected sentence units are the
    &ldquo;question objects&rdquo;. For example, in the following illustration, there are three
    question objects, namely νοεῖτε, χωρεῖ, and ἐκβάλλεται, and student move between the question
    objects by clicking the &#10094; and &#10095; symbols.</p>

<?= $hdir->img("qo.png") ?>

<h2>Request Feature</h2>

<p>See <a href="#display_request_feature">Display Feature and Request Feature</a></p>

<h2><a name="sentence_unit"></a>Sentence Unit</h2>

<p>A sentence can be seen as consisting of a set of <i>sentence units</i>. By far the most common thing
    is to see a sentence as a set of <i>words</i>, but you could also see the sentence as comprised of
    <i>clauses</i> or <i>phrases</i>. Thus a <i>sentence unit</i> can be a <i>word,</i> a <i>clause,</i>
    a <i>phrase,</i> or perhaps something else.</p>
    
