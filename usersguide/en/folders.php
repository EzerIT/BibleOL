<h1>Folder Management</h1>

<p>Exercises are stored in a hierarchy of folders similar to the folder hierarchy found on most
    computers. Users with <i>facilitator</i> privileges can manipulate the folders. A folder can
    contain exercises and other folders. Although it is possible to store exercises in the top level
    of the folder hierarchy, this is not recommended.</p>

<p>If you select the menu <i>Administration &gt; Manage exercises</i> you will be taken to the top
    level of the folder hierarchy. Here you will see a list of folders and, perhaps, some exercises
    (although exercises should really not bestore at the top level):</p>

<?= $hdir->img('folder1.png') ?>

<h2>Creating a Folder</h2>

<p>To create a new folder, press the button labelled &ldquo;Create folder&rdquo; below the folder
    list. You will be prompted for a name, whereafter the folder will be created. If, for example,
    you give the folder the name &ldquo;Greek semester I&rdquo;, you will see this:</p>

<?= $hdir->img('folder2.png') ?>

<h2>Deleting a Folder</h2>

<p>Only empty folders can be deleted. To do so, press the red button labelled &ldquo;Delete
    folder&rdquo; next to the name of the folder you want to delete.</p>

<h2>Viewing the Contents of a folder</h2>

<p>To view the contents of a folder, click on its name. You may see something like this:</p>

<?= $hdir->img('folder3.png') ?>

<p>In this example, the folder contains two subfolders, &ldquo;Exercise 1-10&rdquo; and
    &ldquo;Exercise 11-20&rdquo;. Below the folders there are two exercises, &ldquo;Ex 1&rdquo; and
    &ldquo;Qal Verbs&rdquo;.</p>

<h2><a name="folder_visibility"></a>Folder Visibility</h2>

<p>Each folder has a specific <i>visibility</i>. This means that only users enrolled in certain
    classes can see the contents of the folder. When you create a new folder, nobody is allowed to
    see the contents of that folder, so you must specify whom you will allow access to the folder
    contensts.</p>

<p>To specify the visibility of a folder, click &ldquo;Edit visibility&rdquo; at the top of the
    page. You will see something like this:</p>

<?= $hdir->img('folder5.png') ?>

<p>If you check the box next to the text &ldquo;Check here if folder is visible to everybody&rdquo;,
    all users can see the contents of the folder. Alternatively, you can check specific classes in
    the list of classes. Only users enrolled in those classes can see the contents.</p>

<p>The class list serves a double purpose. As specified in the previous paragraph, you can limit
    visibility of a folder to only a specific set of classes. Furthermore, only selected classes
    have access to the grading functionality. The class selection allows enrolled users to track
    their progress under <i>My data &gt My performance</i>. Note that this means that even if you
    make the folder visible to everybody, you may still need to specify certain classes in the class
    list.</p>
    
<h2>Operations On Exercises</h2>

<p>You can perform a number of operations on these exercises using the blue buttons next to the
    exercise:</p>

<ul>
    <li><i>Download</i> downloads the exercise to your computer. The exercise is an
        XML file with a file extension of &ldquo;.3et&rdquo;. You can use the downloaded exercise to
        upload to another folder or to another server.</li>
    <li><i>Edit</i> allows you to edit the exercise as described in the section
        &ldquo;<?= help_anchor('exercise_mgmt','Exercise management') ?>&ldquo;.</li>
    <li><i>Rename</i> allows you to change the name of the exercise.</li>
    <li><i>Copy passages</i> reads the list of passages used for the exercise. After pressing this
        key, a new button labelled &ldquo;Insert passages into marked files&rdquo; will appear at the
        bottom. If you check one or more exercises and press that button, the passage list will be stored
        in the marked files.</li>
</ul>

<p>To delete one or more files, place a check mark next to the file(s) you want to delete and press
    &ldquo;Delete marked files&rdquo;.</p>

<p>To move or copy one or more files to another folder, place a check mark next to the file(s) you
    want to copy or move and press either &ldquo;Copy marked files&rdquo; or &ldquo;Move marked
    files&rdquo;. You can then move to the destination folder and click &ldquo;Insert
    copied/moved files&rdquo;.</p>

<p>To upload an exercise file from your computer to the Bible OL server, press &ldquo;Upload
    exercises&rdquo;. You will then see this:</p>

<?= $hdir->img('folder4.png') ?>

<p>You can either press &ldquo;Upload files&rdquo; and select exercise files to upload,
    or you can drag and drop your local files onto the &ldquo;Upload files&rdquo; button. Clicking
    &ldquo;View folder&rdquo; takes you back to the folder where your uploaded files have been
    stored.</p>

<p>Note: Exercise files must be in a special format and have the extension &ldquo;.3et&rdquo;. The
    only way to reliably create such a file is by downloading it from another Bible OL server or
    folder.</p>

<p>To create a new exercise, click &ldquo;Create exercise&rdquo; and proceed as described in section 
    &ldquo;<?= help_anchor('exercise_mgmt','Exercise management') ?>&ldquo;.</p>
