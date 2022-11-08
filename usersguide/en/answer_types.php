<h1>Answering Various Types of Questions</h1>

<p>Different types of questions may require different types of answers. Below you will find an
    overview of the different possibilities.</p>

<div class="shortcuts">
    <h5 class="card-header bg-info text-light">Using shortcuts</h5>
    <div class="card-body border border-info mb-4">
        <p>If you are using a computer, you can perform a number of functions from your keyboard. See
            <?= help_anchor('shortcuts', '&ldquo;How to use shortcuts&rdquo;') ?> for more
            information.</p>
    </div>
</div>


<h2>Multiple Choice</h2>

<p>A multiple choice question may look like this:</p>

<?= $hdir->img("qtype-mc.png") ?>

<p>Only one of the indicated values is the correct answer. You select that answer by clicking on the value.</p>

<div class="shortcuts">
    <h5 class="card-header bg-info text-light">Using shortcuts</h5>
    <div class="card-body border border-info mb-4">
        <p>If you are using a computer, you can press the key for the first letter of a value to select
            it. For example, in the figure above, you can press &ldquo;p&rdquo; on your keyboard to
            select the value &ldquo;Plural&rdquo;. If more that one value starts with the same letter,
            you can press the letter repeatedly to select the different values.</p>

        <p>A special type of multiple choice question involves selecting a correct Hebrew word,
            like this:</p>

        <?= $hdir->img("qtype-mc2.png") ?>

        <p>In this case you can use the letter in the small square at the top right of each word as a
            shortcut key. For example, in the figure above, you can press &ldquo;e&rdquo; to select the word
            <span class="help hebrew">מְימָֽיו</span>.</p>
    </div>
</div>


<h2>Choose Several</h2>

<p>Sometimes a multiple choice question has several options, all of which are part of a correct
    answer. This is, for example, the case with Hebrew verb classes, where a verb may belong to
    several classes simultaneously. A multiple choice question where you may choose several answers
    may look like this:</p>

<?= $hdir->img("qtype-mc3.png") ?>

<p>The text &ldquo;Select one or more&rdquo; above the options indicate that you may select
    several options. Click on an option to select it; click again to deselect it. If none of the
    options is correct, click &ldquo;None of these&rdquo;.</p>

<div class="shortcuts">
    <h5 class="card-header bg-info text-light">Using shortcuts</h5>
    <div class="card-body border border-info mb-4">
        <p>If you are using a computer, you can use your keyboard to select and deselect options. Press
            the key shown in the small square at the top left of each option to select that option;
            press the key again to deselect it. For example, in the figure above, you can press
            &ldquo;h&rdquo; on your keyboard to select the value &ldquo;II guttural&rdquo;; press
            &ldquo;h&rdquo; again to deselect that option.</p>
    </div>
</div>

<h2>Typing Text</h2>

<p>Some questions require an answer to be typed. If the answer is in Latin letters, you can just use
    your keyboard, as in this example, where you are asked to provide an English translation of a word:</p>

<?= $hdir->img("qtype-latin.png") ?>

<p>But if the answer is in Hebrew or Greek, the system will provide you with a few buttons that you
    can use to type the word. For Hebrew this may look like this:</p>

<?= $hdir->img("qtype-heb.png") ?>

<p>To provide the answer <span class="help hebrew">בֶּן</span>, click the buttons marked with the
    Hebrew letters <i>bet, dagesh, segol,</i> and <i>final nun.</i> You can click
    &ldquo;&rarr;&rdquo; to erase the last character in your answer.</p>

<div class="shortcuts">
    <h5 class="card-header bg-info text-light">Using shortcuts</h5>
    <div class="card-body border border-info mb-4">
        <p>If you are using a computer, you can type each key by typing the letter in the square
            at the top right of each Hebrew character. For example, in the figure above, you can type
            &ldquo;b.eN&rdquo; on your keyboard to produce the answer <span class="help hebrew">בֶּן</span>.</p>
        <p>You can use the backspace key to erase the last character in your answer.<p>
        <p>Note that there is a difference between upper and lower case shortcuts; in the example above, &ldquo;n&rdquo; is
            <span class="help hebrew">נ</span> and &ldquo;N&rdquo; is <span class="help hebrew">ן</span>.
            Also note that that the shortcut is not necessarily the Latin equivalent of the Hebrew
            letter.</p>
    </div>
</div>

<p>For Greek it may look like this:</p>

<?= $hdir->img("qtype-gr.png") ?>

<p>To provide the answer κύριος, click the buttons marked with the Greek letters
    <i>κ, ύ, ρ, ι, ο,</i> and <i>ς.</i> You can click &ldquo;&larr;&rdquo; to erase
    the last character in your answer.</p>

<div class="shortcuts">
    <h5 class="card-header bg-info text-light">Using shortcuts</h5>
    <div class="card-body border border-info mb-4">
        <p>If you are using a computer, you can type each key by typing the letter in the square at
            the top left of each Greek character. For example, in the figure above, you can type
            &ldquo;k&rdquo; &ndash; then use your mouse to click ύ &ndash; the type &ldquo;rioc&rdquo; to produce
            the answer κύριος. Note that accented Greek letters do not have a corresponding shortcut
            keystroke and you must use your mouse to click the character.</p>
        <p>You can use the backspace key to erase the last character in your answer.</p>
        <p>Note that there is a difference between upper and lower case shortcuts. Upper case
            shortcuts are used for Greek upper case letters, lower case shortcuts are used for
            Greek lower case letters.</p>
        <p>Also note that that the shortcut is not necessarily the Latin equivalent of the Greek
            letter; in the example above, the keystroke &ldquo;c&rdquo; generates the Greek
            character ς.</p>
    </div>
</div>


<h2>Glosses</h2>

<p>In a gloss request an exercise asks you to provide the English translation of a foreign word
&ndash; the <i>gloss</i>.</p>

<p>Very often, a foreign word has several translations into English. For example, the
  Hebrew word <span class="help hebrew">רוּחַ</span> may mean <i>breath, wind,</i> or <i>spirit</i>. When Bible OL
  asks for a translation of <span class="help hebrew">רוּחַ</span>, it is enough to provide one of these words:</p>

<?= $hdir->img("ans_gloss1.png") ?>

<p>As you can see from the check mark, Bible OL has accepted the answer &ldquo;spirit&rdquo;. If you click
  “Show answer”, all the correct answers appear:</p>

<?= $hdir->img("ans_gloss2.png") ?>

