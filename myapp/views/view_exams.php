<h1>Please choose from all current exercises</h1>

<style>
.ar_class, #selected_exercises{
	padding: 8px;
	border: 1px solid white;
	background-color: #337ab7;
	color: white;	
	border-radius: 0px 0px 8px 8px;
	border-style: none none solid;
}

.btn-link{
	color: white;
	font-weight: bold;;
}

#selected_exercises{
	list-style-type: none;
	width: 70%;
	margin: 5px;
}

#wrap{
	background-color: #337ab7;
	color: white;
}

.add_btn:hover{
	background-color: #a5182d;
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


<?php if (!is_null($dirlist['parentdir']) || !empty($dirlist['directories'])): ?>

  <h2>Available Exercises</h2>

  <table class="type2 table table-condensed">

  <?php if (!is_null($dirlist['parentdir'])): ?>
    <tr>
      <td>
        <span class="glyphicon glyphicon-arrow-up" style="display:inline-block;"></span>
        <a href="<?= site_url("file_manager?dir={$dirlist['parentdir']}") ?>"><?= $this->lang->line('parent_folder') ?></a>
      </td>
      <td></td>
    </tr>
  <?php endif; ?>



   <!--
   - Creates a table which contains all the exercise folders and all exercises
   - One row for each element of $ar
   - Adds "add" button to the end of each row which adds the exercise to the list of exercises
   - 		used for the exam
   - @param $ar Array which contains all the exercises and folders that will be displayed
   -->
	<?php function showContents($ar) {
			echo '<table style="width: 100%; border-collapse: separate; border-spacing: 0px; background-color: #337ab7; border-radius: 0px 0px 8px 8px;">';
				foreach ($ar as $key => $d){
					if (is_array($d) && $d != '.' && $d != '..'){
						echo '<tr>';
							echo '<td class="ar_class" style="width: 100%" colspan=4>';
								echo '<button type="button" class="btn-link" style="text-decoration: none;"data-parent="#wrap" data-toggle="collapse" data-target=".'.str_replace(".3et", "", basename($d[0])).'">'.basename($d[0]).'</button>';
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
  
	<p id="btn_test"></p> 
	
	 
	<!------------------------------------
	- Make exam dialog
	-------------------------------------->
			
	<div id="mkexam-dialog" class="modal fade">
    <div class="modal-dialog modal-sm">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title">Create exam</h4>
        </div>
        <div class="modal-body">
          <form id="mkexam-form" action="<?= site_url('exams/create_exam') ?>" method="post">
          
          	<div class="alert alert-danger" id="mkexam-error" role="alert" aria-hidden="true">
            	<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
            	<span id="mkexam-error-text"></span>
          	</div>

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
	
	$(function() {
        $('#mkexam-dialog-ok').click(function() {
            exam_name = $('#mkexam-name').val().trim();
            if (exam_name=='') {
                $('#mkexam-error-text').text('Missing exam name');
                $('#mkexam-error').show();
            }
            else if (filename_bad(exam_name)) {
                $('#mkexam-error-text').text('Illegal character in exam name');
                $('#mkexam-error').show();
            }
            else
                $('#mkexam-form').submit();
        });
    });
</script>


<?php endif; ?>





<?php if (!empty($dirlist['files'])): ?>
  <h2><?= $this->lang->line('exercises') ?></h2>
  <form id='copy-delete-form' action="<?= site_url('file_manager/copy_delete_files') ?>" method="post">
    <input type="hidden" name="dir" value="<?= $dirlist['relativedir'] ?>">
    <input type="hidden" name="operation" value="">
    <input type="hidden" name="newowner" value="">
    <table class="type2 table table-striped table-condensed">
      <tr>
        <th><?= $this->lang->line('mark') ?></th>
        <th><?= $this->lang->line('name') ?></th>
        <th><?= $this->lang->line('owner_name') ?></th>
        <th><?= $this->lang->line('operations') ?></th>
      </tr>
    <?php foreach ($dirlist['files'] as $f): ?>
      <tr>
        <td><input type="checkbox" name="file[]" value="<?= $f->filename ?>"></td>
        <td class="leftalign"><?= substr($f->filename,0,-4) ?></td>
        <td class="leftalign"><?= $f->username ?></td>
        <td><span class="small">
            <?php // Note: The following download link will cause this error to be written on Chrome's console:
                  // "Resource interpreted as Document but transferred with MIME type application/octet-stream".
                  // (See https://code.google.com/p/chromium/issues/detail?id=9891.)
                  // Adding the attibute 'download' to the <a ...> tag removes the error, but prevents
                  // the server from sending error messages during download.
            ?>
            <a class="label label-primary" href="<?= site_url("file_manager/download_ex?dir={$dirlist['relativedir']}&file=$f->filename") ?>"><?= $this->lang->line('download') ?></a>
            <a class="label label-primary" href="<?= site_url("text/edit_quiz?quiz={$dirlist['relativedir']}/$f->filename") ?>"><?= $this->lang->line('edit') ?></a>
            <a class="label label-primary" href="#" onclick="rename('<?= substr($f->filename,0,-4) ?>'); return false;"><?= $this->lang->line('rename') ?></a></span>
        </td>
      </tr>
    <?php endforeach; ?>
    </table>
  </form>
  <p>
    <a class="btn btn-primary" onclick="deleteConfirm(); return false;" href="#"><?= $this->lang->line('delete_marked') ?></a>
    <a class="btn btn-primary" onclick="copyWarning(); return false;" href="#"><?= $this->lang->line('copy_marked') ?></a>
    <a class="btn btn-primary" onclick="moveWarning(); return false;" href="#"><?= $this->lang->line('move_marked') ?></a>
    <?php if ($isadmin): ?>
      <a class="btn btn-primary" onclick="changeOwner(); return false;" href="#"><?= $this->lang->line('change_owner_files') ?></a>
    <?php endif; ?>
  </p>
<?php endif; ?>

<?php if ($copy_or_move): ?>
  <p>
  <a class="btn btn-primary" href="<?= site_url("file_manager/insert_files?dir={$dirlist['relativedir']}") ?>"><?= $copy_or_move==='move' ? $this->lang->line('insert_moved_files') : $this->lang->line('insert_copied_files') ?></a>
    <a class="btn btn-primary" href="<?= site_url("file_manager/cancel_copy?dir={$dirlist['relativedir']}") ?>"><?= $copy_or_move==='move' ? $this->lang->line('cancel_move') : $this->lang->line('cancel_copy')?></a>
  </p>
<?php endif; ?>

 <!-- <p>
    <a class="btn btn-primary" href="#" onclick="create_exercise(); return false;"><?= $this->lang->line('create_exam_button') ?></a>
  </p> -->



  <!-- Dialogs for this page follow -->

  <?php //*********************************************************************
        // Make Directory dialog 
        //*********************************************************************
    ?>
  <div id="mkdir-dialog" class="modal fade">
    <div class="modal-dialog modal-sm">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title"><?= $this->lang->line('create_folder_heading') ?></h4>
        </div>
        <div class="modal-body">
          <div class="alert alert-danger" id="mkdir-error" role="alert">
            <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
            <span id="mkdir-error-text"></span>
          </div>

          <form id="mkdir-form" action="<?= site_url('file_manager/create_folder') ?>" method="post">

            <div class="form-group">
              <label for="mkdir-name"><?= $this->lang->line('folder_name_prompt') ?></label>
              <input type="text" name="create" class="form-control" id="mkdir-name">
            </div>

            <input type="hidden" name="dir" id="mkdir-parent">

            </form>
        </div>
        <div class="modal-footer">
          <button type="button" id="create-exam" class="btn btn-primary"><?= $this->lang->line('OK_button') ?></button>
          <button type="button" class="btn btn-default" data-dismiss="modal"><?= $this->lang->line('cancel_button') ?></button>
        </div>
      </div>
    </div>
  </div>

  <script>
    $(function() {
        $('#mkdir-dialog-ok').click(function() {
            dirname = $('#mkdir-name').val().trim();
            if (dirname=='') {
                $('#mkdir-error-text').text('<?= $this->lang->line('missing_folder_name') ?>');
                $('#mkdir-error').show();
            }
            else if (filename_bad(dirname)) {
                $('#mkdir-error-text').text('<?= $this->lang->line('illegal_char_folder') ?>');
                $('#mkdir-error').show();
            }
            else
                $('#mkdir-form').submit();
        });
    });

    function make_dir(parent) {
        $('#mkdir-parent').attr('value',parent);
        $('#mkdir-error').hide();
        $('#mkdir-dialog').modal('show');
    }
  </script>


  <?php //*********************************************************************
        // Rename dialog 
        //*********************************************************************
    ?>
  <div id="rename-dialog" class="modal fade">
    <div class="modal-dialog modal-sm">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title"><?= $this->lang->line('rename_file_heading') ?></h4>
        </div>
        <div class="modal-body">
          <div class="alert alert-danger" id="rename-error" role="alert">
            <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
            <span id="rename-error-text"></span>
          </div>

          <form id="rename-form" action="<?= site_url('file_manager/rename_file') ?>" method="post">

            <div class="form-group">
              <label for="rename-newname"><?= $this->lang->line('new_filename_prompt') ?></label>
              <input type="text" name="newname" class="form-control" id="rename-newname">
            </div>

            <input type="hidden" name="dir" value="<?= $dirlist['relativedir'] ?>">
            <input type="hidden" name="oldname" id="rename-oldname">

          </form>
        </div>
        <div class="modal-footer">
          <button type="button" id="rename-dialog-ok" class="btn btn-primary"><?= $this->lang->line('OK_button') ?></button>
          <button type="button" class="btn btn-default" data-dismiss="modal"><?= $this->lang->line('cancel_button') ?></button>
        </div>
      </div>
    </div>
  </div>

  <script>
    $(function() {
        $('#rename-dialog-ok').click(function() {
            filename = $('#rename-newname').val().trim();
            if (filename=='') {
                $('#rename-error-text').text('<?= $this->lang->line('missing_filename') ?>');
                $('#rename-error').show();
            }
            else if (filename_bad(filename)) {
                $('#rename-error-text').text('<?= $this->lang->line('illegal_char_filename') ?>');
                $('#rename-error').show();
            }
            else
                $('#rename-form').submit();
        });
    });

    function rename(oldname) {
        $('#rename-oldname').attr('value',oldname);
        $('#rename-error').hide();
        $('#rename-dialog').modal('show');
    }
  </script>



  <?php //*********************************************************************
        // Create Quiz dialog 
        //*********************************************************************
    ?>
  <div id="newquiz-dialog" class="modal fade">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title"><?= $this->lang->line('create_exercise_heading') ?></h4>
        </div>
        <div class="modal-body">
          <form id="newquiz-form" action="<?= site_url('text/new_quiz') ?>" method="post">
            <div class="form-group">
              <h4>Please Enter the Exam Name</h4>

              <input id="exam-name-field" type="text">
            </div>

            <input type="hidden" name="dir" value="<?= $dirlist['relativedir'] ?>">
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" id="exam-create" class="btn btn-primary"><?= $this->lang->line('OK_button') ?></button>
          <button type="button" class="btn btn-default" data-dismiss="modal"><?= $this->lang->line('cancel_button') ?></button>
        </div>
      </div>
    </div>
  </div>

  <script>
    $(function() {
        $('#exam-create').click(function() {
           var data = [];
           var name = $("#exam-name-field").val()
           $("input:checked").each(function (){
               var url = $(this).attr("data-url");
               data.push(url);
           })
           $.post( "/exams/build", {exam: data, name: name}, function( data ) {
               location.reload()
           })
        });
    });

    function create_exercise() {
        $('#newquiz-dialog').modal('show');
    }
  </script>



  <?php //*********************************************************************
        // Copy/Move Warning dialog 
        //*********************************************************************
    ?>
  <div id="copy-dialog-warning" class="modal fade">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title" id="copy-dialog-warning-title"></h4>
        </div>
        <div class="modal-body">
          <span class="glyphicon glyphicon-alert" style="float:left; margin:0 7px 20px 0;" aria-hidden="true"></span>
          <span><?= sprintf($this->lang->line('click_and_go_to'), '<span id="copiedmoved"></span>') ?></span>
        </div>
        <div class="modal-footer">
          <button type="button" id="copy-dialog-warning-ok" class="btn btn-primary"><?= $this->lang->line('OK_button') ?></button>
          <button type="button" id="copy-dialog-warning-cancel" class="btn btn-default"><?= $this->lang->line('cancel_button') ?></button>
        </div>
      </div>
    </div>
  </div>

  <script>
    $(function() {
        $('#copy-dialog-warning-ok').click(function() {
            $('#copy-delete-form').submit();
        });
        $('#copy-dialog-warning-cancel').click(function() {
            $('input[name="operation"]').val(''); // Paranoia
            $('#copy-dialog-warning').modal('hide');
        });
    });

    function copyWarning() {
        if ($('input[name="file[]"]:checked').length===0) {
            myalert('<?= $this->lang->line('file_selection') ?>','<?= $this->lang->line('no_files_selected') ?>');
            return;
        }
        $('input[name="operation"]').val('copy');
        $('#copiedmoved').text('<?= $this->lang->line('insert_copied_files') ?>');
        $('#copy-dialog-warning-title').html('<?= $this->lang->line('copy_files') ?>');
        $('#copy-dialog-warning').modal('show');
    }

    function moveWarning() {
        if ($('input[name="file[]"]:checked').length===0) {
            myalert('<?= $this->lang->line('file_selection') ?>','<?= $this->lang->line('no_files_selected') ?>');
            return;
        }
        $('input[name="operation"]').val('move');
        $('#copiedmoved').text('<?= $this->lang->line('insert_moved_files') ?>');
        $('#copy-dialog-warning-title').html('<?= $this->lang->line('move_files') ?>');
        $('#copy-dialog-warning').modal('show');
    }
  </script>


  <?php //*********************************************************************
        // Confirm Deletion dialog
        //*********************************************************************
    ?>
  <div id="delete-dialog-confirm" class="modal fade">
    <div class="modal-dialog modal-sm">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title"><?= $this->lang->line('confirm_deletion') ?></h4>
        </div>
        <div class="modal-body">
          <span class="glyphicon glyphicon-question-sign" style="float:left; margin:0 7px 20px 0;" aria-hidden="true"></span>
          <span><?= $this->lang->line('delete_file_confirm') ?></span>
        </div>
        <div class="modal-footer">
          <button type="button" id="delete-dialog-confirm-yes" class="btn btn-primary"><?= $this->lang->line('yes') ?></button>
          <button type="button" id="delete-dialog-confirm-no" class="btn btn-default" data-dismiss="modal"><?= $this->lang->line('no') ?></button>
        </div>
      </div>
    </div>
  </div>

  <script>
    $(function() {
        $('#delete-dialog-confirm-yes').click(function() {
            $('#copy-delete-form').submit();
        });
        $('#delete-dialog-confirm-no').click(function() {
            $('input[name="operation"]').val(''); // Paranoia
            $("#delete-dialog-confirm").modal('hide');
        });
    });

    function deleteConfirm() {
        if ($('input[name="file[]"]:checked').length===0) {
            myalert('<?= $this->lang->line('file_selection') ?>','<?= $this->lang->line('no_files_selected') ?>');
            return;
        }
        $('input[name="operation"]').val('delete');
        $('#delete-dialog-confirm').modal('show');
    }
  </script>

  <?php //*********************************************************************
        // Change Owner dialog
        //*********************************************************************
    ?>
  <div id="chown-dialog" class="modal fade">
    <div class="modal-dialog modal-sm">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title"><?= $this->lang->line('change_owner_title') ?></h4>
        </div>
        <div class="modal-body">
          <div class="alert alert-danger" id="chown-error" role="alert">
            <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
            <span id="chown-error-text"></span>
          </div>
          <span><?= $this->lang->line('new_owner_prompt') ?></span>
          <select id="chown-selector">
            <option value="0" selected="selected"></option>
            <?php foreach ($teachers as $t): ?>
              <option value="<?= $t->id ?>"><?= $t->first_name . ' ' . $t->last_name ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="modal-footer">
          <button type="button" id="chown-dialog-ok" class="btn btn-primary"><?= $this->lang->line('OK_button') ?></button>
          <button type="button" class="btn btn-default" data-dismiss="modal"><?= $this->lang->line('cancel_button') ?></button>
        </div>
      </div>
    </div>
  </div>

  <script>
    $(function() {
        $('#chown-dialog-ok').click(function() {
            userid = $("#chown-selector option:selected").val();
            if (userid==0) {
                $('#chown-error-text').text('<?= $this->lang->line('no_user_selected') ?>');
                $('#chown-error').show();
                return false;
            }    
            $('input[name="newowner"]').val(userid);
            $('#copy-delete-form').submit();
        });
    });


    function changeOwner() {
        if ($('input[name="file[]"]:checked').length===0) {
            myalert('<?= $this->lang->line('file_selection') ?>','<?= $this->lang->line('no_files_selected') ?>');
            return;
        }
        $('input[name="operation"]').val('chown');
        $('#chown-error').hide();
        $('#chown-dialog').modal('show');
    }
  </script>



  <script>
    // Miscellaneous functions

    function filename_bad(filename) {
        badchars = /[\/?*;'"{}\\]/;
        return !!filename.match(badchars);
    }

    $(function() {
        // Prevent 'return' key from submitting forms
        // (Why? I don't remember. Perhaps because we don't want to submit hidden forms.)
        $(window).on("keydown",function(event) {
            if (event.keyCode == 13) {
                event.preventDefault();
                return false;
            }
        });
    });
  </script>