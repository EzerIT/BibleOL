<h1>Welcome to the exam</h1>

<?= var_dump($exerciselist) ?>
<br>
<?= $exerciselist->examname; ?>
<br>
<?php
foreach ($exerciselist->exercise as $exercise) {
  echo $exercise->exercisename;
  echo "<br>";
}
?>

<div>

</div>
