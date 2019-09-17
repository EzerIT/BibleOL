<h1>Please choose from all current exercises</h1>

<style>
.ar_class, #selected_exercises{
	padding: 8px;
	border: 1px solid white;
	background-color: #007bff;
	color: white;
	border-radius: 8px;
	border-style: none none solid;
}

.btn-link{
	color: white;
	font-weight: bold;
}

#selected_exercises{
	list-style-type: none;
	width: 70%;
	margin: 5px;
}

#wrap{
	background-color: #007bff;
	color: white;
}

.add_btn:hover{
	background-color: white;
  color: #007bff;
	border-radius: 2px;
}

.add_btn{
	justify-content: center;
	cursor: pointer;
}

#exam_name{
	margin-top: 20px;
	width: 70%;
}

#ar_class{
	width: 100%;
}
</style>


<table class="type2 table table-condensed">

<?php function showContents($ar) {
    echo '<table style="width: 100%; border-collapse: separate; border-spacing: 0px; background-color: #007bff; border-radius: 8px;">';
      foreach ($ar as $key => $d){
        if (is_array($d) && $d != '.' && $d != '..'){
          echo '<tr>';
            echo '<td class="ar_class" style="width: 100%" colspan=4>';
              echo '<button type="button" class="btn-link" style="text-decoration: none; border-radius: 8px;"data-parent="#wrap" data-toggle="collapse" data-target=".'.str_replace(".3et", "", basename($d[0])).'">'.basename($d[0]).'</button>';
              echo '<div id="wrap">';
                echo '<div class="'.str_replace(".3et", "", basename($d[0])).' collapse">';
                  showContents($d);
                echo '</div>';
              echo '</div>';
            echo '</td>';
          echo '</tr>';
        } elseif($key != 0 && !is_array($d)) {
          $ex_name = str_replace(".3et","",basename($d));
          echo '<tr>';
            echo '<td id="exr_nm_id" style="width: 50%;">'.$ex_name.'</td>';
            echo '<td id="ownr_id">Owner info</td>';
            $nm_arg = "'".$ex_name."'";
            $pth_arg = "'".str_replace("/var/www/BibleOL/quizzes/","",$d)."'";
            $hi = "'hi'";
            echo '<td><span id="add_btn_id" class="add_btn" onclick="addExercise('.$nm_arg.','.$pth_arg.')">Add</span></td>';
          echo '</tr>';
        }
      }
    echo '</table>';

 }

showContents($dir_files);
?>

<!-- Displays the exercises that have been selected for exam creation -->
<ul id="selected_exercises">
  <li>Selected exercises:</li>
</ul>

</table>

<div>
  <a class="btn btn-primary" href="#" onclick="create_exam()"><?= $this->lang->line('create_exam_button') ?></a>
</div>


<div id="mkexam-dialog" class="modal fade">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">Create exam</h4>
      </div>
      <div class="modal-body">
        <form id="mkexam-form" action="<?= site_url("/exams/create_exam") ?>" method="post">

          <div class="form-group">
            <label for="mkexam-name">Exam name</label>
            <input type="text" name="create_exam" class="form-control" id="mkexam-name">
            <input type="hidden" name="exercise_list" id="ex_list">
          </div>

          <input type="hidden" name="exam" id="mkexam-parent">

        </form>
      </div>
      <div class="modal-footer">
        <button type="button" id="mkexam-dialog-ok" class="btn btn-primary"><?= $this->lang->line('OK_button') ?></button>
        <button type="button" class="btn btn-default" data-dismiss="modal"><?= $this->lang->line('cancel_button') ?></button>
      </div>
    </div>
  </div>
</div>


<script>
   /** Stores exercises that have been selected for exam creation */
	var exercise_list;

	/**
	 * Adds exercise name to selected_exercises
	 * Adds exercise pth to exercise_list
	 * @param ex_name Basename of exercise being added
	 * @param pth Exercise path starting after "/var/www/BibleOL/quizzes/"
	 */
	function addExercise(ex_name, pth){
		var ul = document.getElementById("selected_exercises");
		var li = document.createElement("li");
		li.appendChild(document.createTextNode(pth));
		ul.appendChild(li);
		exercise_list = exercise_list + "," + pth;
	}

  function filename_bad(filename) {
      badchars = /[\/?*;'"{}\\]/;
      return !!filename.match(badchars);
  }

	function create_exam() {
		var txt;

		if (!exercise_list.length) {
			alert("You must select at least one exercise to create an exam");
			return;
		}

		txt = JSON.stringify(exercise_list);
		document.getElementById("ex_list").value = txt;
		$('#mkexam-dialog').modal('show');
	}

  $(document).ready(function(){
    $("#mkexam-dialog-ok").click(function(){
      exam_name = $("#mkexam-name").val().trim();
      if (exam_name=='') {
        alert("You must enter a valid name for your exam.");
      }
      else if (filename_bad(exam_name)) {
        alert("Illegal character in the exam name.");
      }
      else {
        alert("good");
        $("#mkexam-form").submit();
      }
    });
  });
</script>
