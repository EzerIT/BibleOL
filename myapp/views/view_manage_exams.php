<!--
This page is the home page for exams.
It has a list of all the created exams and
professors can edit exams or choose to create
a new exam. In each case a the professor is
redirected to the appropriate page.
-->

<style>
/*
Styling for the page.
This is used to fine tune individual CSS
style characteristics for individual elements
on the page.
*/
.editButton{
  justify-content: center;
  cursor: pointer;

  background-color: white;
  color: #007bff;

  padding: 8px;
  border-radius: 8px;
}
</style>


<div>
  <h1>
    Manage exams
  </h1>
</div>
<br>


<!--
This button redirects to the exam creation page.
-->
<div>
  <a class="btn btn-primary" href="/exams/new_exam" onclick="create_new_exam()"><?= $this->lang->line('create_new_exam_button') ?></a>
</div>


<!--
Display the table of exams.
-->
<table class="type2 table table-condensed">
<?php
# This function takes in one array and then puts
# each of the elements in a table except for the
# first element which is skipped.
function showContents($ar){
  # Table that will hold all the array elements.
  echo '<table style="width: 100%; background-color: #007bff; border-radius: 8px;">';
    # Iterate through the array starting with the second element.
    for ($i = 0; $i < count($ar); $i++){
      # Store the exam name only (not the whole path).
      $exname = str_replace("/var/www/BibleOL/exam/","",$ar[$i]);
      if ($exname != "README"){
        # Each table row represents a different exam.
        echo '<tr>';
          # Table column that displays the exam names.
          echo '<td style="padding: 8px; width: 75%; color: white;">';
            echo $exname;
          echo '</td>';
          # Table column that stores the edit exam buttons for each
          # exam.
          echo '<td style="width: 25%;">';
            echo '<a class="editButton" href="/exams/edit_exam?exam='.$exname.'">';
              echo "Edit Exam";
            echo '</a>';
          echo '</td>';
        echo '</tr>';
      }
    }
}

# Call the showContents function in order
# to display all the exams.
showContents($examlist);
?>
</table>
