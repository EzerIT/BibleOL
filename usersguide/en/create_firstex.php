<?php $hg = $hdir->heb_gr('Hebrew','Greek') ?>

<h1>Example: Create a Simple <?= $hg ?> Exercise</h1>

<p>(You may be interested in a <?= anchor('help/show_help/create_firstex/' . $hdir->heb_gr('gr','heb'),
                                          'corresponding '. $hdir->heb_gr('Greek','Hebrew') . ' exercise')
    ?>.)</p>


<!-- p>We shall create an exercise in the conjugation of <?= $hg ?> verbs in the
  <?= $hdir->heb_gr('qal perfect forms','present tense') ?>. We shall ask the user to identify
  <?= $hdir->heb_gr('the person, gender, and number of the perfect forms of various qal verbs','identify
  the person and number of various present tense verb forms') ?>.</p-->

<!-- - -->p>Start the program by double-clicking on the &ldquo;Bible OL &ndash; Facilitator&rdquo; icon on your
  desktop. (Note that this is not the &ldquo;Bible OL&rdquo; icon you have used in previous exercises.) This
  will start Bible OL in <i>Facilitator Mode</i>, which has a more complex interface than in Learner Mode,
  and which allows you to create and modify exercises.</p-->

<p>We shall create an exercise in the conjugation of <?= $hg ?> verbs in the <?= $hdir->heb_gr('qatal form of
    the qal stem','present tense') ?>. We shall ask the user to identify the <?= $hdir->heb_gr('person, gender, and
    number of the perfect forms of various qal verbs.','person and number of various present tense verb
    forms.') ?></p>

<p>Select the menu <i>Administration &gt; Manage exercises</i> then navigate to a folder where
    you want to create your exercises. This should preferably be a folder used only by you and your
    team. At the bottom of the page, click the <i>Create exercise</i> button. A dialog will appear in
    which you select the text database on which you want to base your exercise. Here, you should select
    &ldquo;<?= $hdir->heb_gr('Hebrew (ETCBC4, OT)','Greek (Nestle 1904, NT)') ?>&rdquo; and press
    the <i>OK</i> button.</p>

<p>You will now se a web page that looks like this:</p>

<?= $hdir->img("$sub_article-create-i.png") ?>

<p>At the top you'll see five tabs, labelled &ldquo;Description&rdquo;, &ldquo;Passages&rdquo;,
    &ldquo;Sentences&rdquo;, &ldquo;Sentence Units&rdquo;, and &ldquo;Features&rdquo;. On the left, you
    can see the name of the text database you are using.</p>

<h2>The &ldquo;Description&rdquo; Tab</h2>

<p>The &ldquo;Description&rdquo; tab is displayed when you start editing an exercise. Below the row of
    tabs, you see a text editing field in which you can write information and instructions to the
    students who will be running this exercise. You may, for example, write something like this:</p>

HERTIL


    what you see
    in Figure \ref{fig-\hebgr-description-tab}. \begin{figure} \begin{center}
\includegraphics[width=0.7\textwidth]{fig-\hebgr-description-tab.png} \end{center} \caption{Filling
out the description field.}\label{fig-\hebgr-description-tab} \end{figure}

\subsubsection{The &ldquo;Passages&rdquo; Tab}\index{passages tab@&ldquo;Passages&rdquo;
tab}\index{tabs!&ldquo;Passages&rdquo;}

Select the &ldquo;Passages&rdquo; tab and indicate which part of the <?= $hdir->heb_gr('Old','New} Testament you want to
use for the exercise. (See Figure \ref{fig-\hebgr-passages-tab}.) When generation questions for a
student, Bible OL will choose sentences from the passages you specify here. The more passages you
select, the more sentences Bible OL can choose from; but the more sentences there are, the longer it will
take the program to generate the exercise. Therefore it may be a good idea to limit the selection. On
most computers a selection comprising the entire New Testament is no problem; but if the selection
contains the entire Old Testament, the program may appear somewhat slow.

\begin{figure}
  \begin{center}
    \includegraphics[width=0.7\textwidth]{fig-\hebgr-passages-tab.png}
  \end{center}
  \caption{Chosing passages and other exercise options.}\label{fig-\hebgr-passages-tab}
\end{figure}

The passages you specify here are, in general, only a suggestion for the student; as we have seen in
Section \ref{sec-\hebgr-exer-i}, the passage selection can normally be altered by the student when
the exercise is run.

You can click on the small plus signs next to the names of the books of the Bible. This will allow
you to specify individual chapters or verses to use for the exercise.

Below the passage selector, you can specify a few specialities about the exercise:

\begin{itemize}
\item Should the &ldquo;Locate&rdquo; button be shown or not? (See Section \ref{sec-disabling-locate}.)
\item How many sentences of context should be shown before and after the relevant sentence? (See
  Section \ref{sec-sentence-context}.)
\item Should the number of questions be fixed, or should the student be able to choose? (See Section
  \ref{sec-fixed-exercises}.)
\item Should the order of questions be random or fixed? (See Section \ref{sec-fixed-exercises}.)
\end{itemize}

If either the number of questions or the order of questions is fixed, the students cannot themselves
choose the Bible passages for the exercise.


\subsubsection{The &ldquo;Sentences&rdquo; Tab}\index{sentences tab@&ldquo;Sentences&rdquo; tab}\index{tabs!&ldquo;Sentences&rdquo;}

Select the &ldquo;Sentences&rdquo; tab. You will see a dialog like the one in Figure
\ref{fig-\hebgr-sentences-tab}. Here you can indicate the criteria which the program is to use when
choosing sentences for the exercise.

\begin{figure}
  \begin{center}
    \includegraphics[width=0.7\textwidth]{fig-\hebgr-sentences-tab.png}
  \end{center}
  \caption{Specifying sentence units and their features.}\label{fig-\hebgr-sentences-tab}
\end{figure}


We shall return to the first two lines (&ldquo;Use this for sentence unit selection&rdquo; and &ldquo;MQL statement
to select sentences&rdquo;) later. Make sure that &ldquo;Friendly feature selector&rdquo; is marked.

Next to &ldquo;Sentence unit type&rdquo; there is a drop-down list where you can choose between the types of
sentence units available for exercises in this database. These are &ldquo;Word&rdquo;, &ldquo;Subphrase&rdquo;, &ldquo;Phrase
atom&rdquo;, &ldquo;Phrase&rdquo;, &ldquo;Clause atom&rdquo;, and &ldquo;Clause&rdquo;. Here you select the type of object that the
exercise should be about. In most cases the value should be &ldquo;Word&rdquo;.

Next to &ldquo;Feature&rdquo; there is another drop-down list. Here you can choose between the various
features available for the selected sentence unit type. For words, the features include &ldquo;Part of
speech&rdquo;, &ldquo;Gender&rdquo;, &ldquo;Number&rdquo; etc. Try selecting various features and note how the rest of the
window changes. When you choose a particular feature, Bible OL shows you the values that this
feature may have. Finally press the &ldquo;Clear&rdquo; button.

The &ldquo;Clear&rdquo; button erases all the criteria.

\begin{samepage}
For the exercise we are creating here, we need sentences containing words that...

\begin{itemize}[\null]
  \item ...are verbs,
  \item ...have the stem qal,
  \item ...are in the qatal tense.
\end{itemize}
\end{samepage}

This can be specified thus:

\begin{itemize}
  \item Set &ldquo;Sentence unit type&rdquo; to &ldquo;Word&rdquo;.
  \item Select the feature &ldquo;Part of speech&rdquo; and tick &ldquo;Verb&rdquo;.
  \item Select the feature &ldquo;Stem&rdquo; and tick &ldquo;Qal&rdquo;.
  \item Select the feature &ldquo;Tense&rdquo; and tick &ldquo;Perfect&rdquo;.
\end{itemize}

(Actually, in this example it is superfluous to require that the word should be a verb; if a word is
marked as qal perfect, it is always a verb.)

The window now looks like Figure \ref{fig-\hebgr-sentences-tab-ii}.

\begin{figure}
  \begin{center}
    \includegraphics[width=0.7\textwidth]{fig-\hebgr-sentences-tab-ii.png}
  \end{center}
  \caption{Specifying qatal tense.}\label{fig-\hebgr-sentences-tab-ii}
\end{figure}


(You may have noticed that as you chose feature values, the text next to &ldquo;MQL statement to select
sentences&rdquo; changed automatically. MQL is a command language that is used to specify how to search
the database, and the statement here is the one actually used for your search. But feel free to
ignore this for now. You may later read more about MQL in section XXX.)

Bible OL now knows how to find sentences for the exercises. It will choose the sentences
based on the criteria we have just specified. But now things get a bit more complicated: When Bibel
OL generates exercises, it actually has to make two choices: First it must choose some interesting
sentences; thereafter it must choose some interesting sentence units (words) within the chosen
sentences.

Often, the criteria used for these two choices are the same. In the current example, this is indeed
the case: First, we want to search the database for sentences that contain qal qatal verbs;
thereafter, we want to search each sentence for words that are qal qatal verbs. So in this
example, the words have to be chosen using exactly the same criteria as the sentences. The tick mark
next to &ldquo;Use this for sentence unit selection&rdquo; instructs Bible OL to use the same criteria when
selecting the interesting words. Try removing the tick next to &ldquo;Use this for sentence unit
selection&rdquo; and the set it again; you will then see that the &ldquo;Sentence Units&rdquo; tab is only active
when the tick mark is not set. (Be sure to leave the mark on.)


\subsubsection{The &ldquo;Features&rdquo; Tab}\index{features tab@&ldquo;Features&rdquo; tab}\index{tabs!&ldquo;Features&rdquo;}

Select the &ldquo;Features&rdquo; tab. You will see a dialog like the one in Figure
\ref{fig-\hebgr-features-tab}. Here you can indicate what information you will provide to the
student, and what information the student should provide.

\begin{figure}
  \begin{center}
    \includegraphics[width=0.7\textwidth]{fig-\hebgr-features-tab-i.png}
  \end{center}
  \caption{Part of the contents of the &ldquo;Features&rdquo; tab.}\label{fig-\hebgr-features-tab-i}
\end{figure}

When you first open this tab, all the marks will be in the &ldquo;Don’t care&rdquo; column. In the rightmost
column you will see all the features that are available for the sentence unit called &ldquo;Word&rdquo; in
this database. The first feature is always &ldquo;Text&rdquo; and represents the actual word in the Bible
verse.

Here you can choose which features to show to the student and which features the student must
provide. In this example we want to show the actual word and ask the student to identify its gender,
person, and number. Place a mark in the column &ldquo;Show&rdquo; next to &ldquo;Text&rdquo; and in the column
&ldquo;Request&rdquo; next to &ldquo;Person&rdquo;, &ldquo;Gender&rdquo;, and &ldquo;Number&rdquo; as shown in Figure \ref{fig-\hebgr-features-tab-ii}.

\begin{figure}
  \begin{center}
    \includegraphics[width=0.7\textwidth]{fig-\hebgr-features-tab-ii.png}
  \end{center}
  \caption{Show the text and request the person, gender, and number.}\label{fig-\hebgr-features-tab-ii}
\end{figure}


When students run the exercise, they will be able to display all the features by pointing to the
individual words with their mouse. If you want to prevent students from seeing certain features,
indicate that in the &ldquo;Don’t show&rdquo; column. The &ldquo;Request&rdquo; features are never visible to the
students.

A few of the features allow you to tick the &ldquo;Multiple choice&rdquo; column. If you do so, Bible OL will
provide the student with a menu of choices for that feature; if you remove the tick under &ldquo;Multiple
choice&rdquo;, the student must type the answer as text.

The &ldquo;Features&rdquo; tab is described in more detail in section XXX.

\subsubsection{Saving the Exercise}

Click the &ldquo;Save&rdquo; button at the bottom of the page to save the exercise. You will be asked to
provide a name for the exercise.

\subsection{Running and Refining the Exercise}

Go to the <i>Text and Exercise</i> > <i>Exercises</i> menu and find the exercise you just created.
You can now run it by clicking, for example, on the number <i>5</i> under the heading <i>Select
  number of questions using preset passages</i>. You may then, for example, get the sentence from
\bibleref{Genesis}{3}{1} which is shown in Figure \ref{fig-\hebgr-run-exercise-i}.

\begin{figure}
  \begin{center}
    \includegraphics[width=0.7\textwidth]{fig-\hebgr-run-exercise-i.png}
  \end{center}
  \caption{Show the text and request the person, gender, and number.}\label{fig-\hebgr-run-exercise-i}
\end{figure}

..... MERE HER

}
