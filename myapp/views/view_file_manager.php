<?php if (!$is_top): ?>
  <h1><?= sprintf($this->lang->line('this_is_folder'), rtrim($dirlist['relativedir'],'/')) ?></h1>

  <p><a class="btn btn-primary" href="<?= site_url(build_get('file_manager/edit_visibility',array('dir' => $dirlist['relativedir']))) ?>"><span class="fas fa-eye"></span> <?= $this->lang->line('edit_visibility_button') ?></a></p>
<?php else: ?>
  <h1><?= $this->lang->line('this_is_top_folder') ?></h1>
<?php endif; ?>


<?php if (!is_null($dirlist['parentdir']) || !empty($dirlist['directories'])): ?>

  <h2><?= $this->lang->line('folders') ?></h2>

  <table class="type2 table table-sm">

  <?php if (!is_null($dirlist['parentdir'])): ?>
    <tr>
      <td>
        <span class="fas fa-arrow-up" style="display:inline-block;"></span>
        <a href="<?= site_url(build_get('file_manager',array('dir' => $dirlist['parentdir']))) ?>"><?= $this->lang->line('parent_folder') ?></a>
      </td>
      <td></td>
    </tr>
  <?php endif; ?>

  <?php foreach ($dirlist['directories'] as $d): ?>
    <tr>
      <td>
        <span class="fas fa-folder" style="display:inline-block;"></span>
        <a href="<?= site_url(build_get('file_manager',array('dir' => composedir($dirlist['relativedir'], $d[0])))) ?>"><?= $d[0] ?>
      </td>
      <?php if ($dirlist['is_empty'][$d[0]]): ?>

        <td><a class="badge badge-danger" onclick="genericConfirmSm('<?= $this->lang->line('delete_folder_confirm') ?>',
                                     '<?= sprintf($this->lang->line('delete_folder_question'), "\'$d[0]\'") ?>',
                                     '<?= site_url(build_get('file_manager/delete_folder',array('dir' => $dirlist['relativedir'], 'delete' => $d[0]))) ?>');
                      return false;"
             href="#"><?= $this->lang->line('delete_folder_button') ?></a></td>
      <?php else: ?>
          <td><?= $this->lang->line('folder_not_empty') ?></td>
      <?php endif; ?>
    </tr>
  <?php endforeach; ?>

  </table>

<?php endif; ?>

  <p><a class="btn btn-primary" href="#" onclick="make_dir('<?= $dirlist['relativedir'] ?>'); return false;"><span class="fas fa-folder-plus"></span> <?= $this->lang->line('create_folder_button') ?></a></p>


<?php if (!empty($dirlist['files'])): ?>
  <h2><?= $this->lang->line('exercises') ?></h2>
  <form id='copy-delete-form' action="<?= site_url('file_manager/copy_delete_files') ?>" method="post">
    <input type="hidden" name="dir" value="<?= $dirlist['relativedir'] ?>">
    <input type="hidden" name="operation" value="">
    <input type="hidden" name="newowner" value="">
    <table class="type2 table table-striped table-sm">
      <tr>
        <th><?= $this->lang->line('mark') ?><br><a class="badge badge-primary" href="#" onclick="uncheckAll(); return false;"><?= $this->lang->line('uncheck_all') ?></a></th>
        <th><?= $this->lang->line('name') ?></th>
        <th><?= $this->lang->line('owner_name') ?></th>
        <th><?= $this->lang->line('operations') ?></th>
      </tr>
    <?php foreach ($dirlist['files'] as $f): ?>
      <tr>
        <td><input type="checkbox" name="file[]" value="<?= $f->filename ?>"></td>
        <td class="leftalign"><?= substr($f->filename,0,-4) ?></td>
        <td class="leftalign"><?= $f->username ?></td>
        <td>
            <?php // Note: The following download link will cause this error to be written on Chrome's console:
                  // "Resource interpreted as Document but transferred with MIME type application/octet-stream".
                  // (See https://code.google.com/p/chromium/issues/detail?id=9891.)
                  // Adding the attibute 'download' to the <a ...> tag removes the error, but prevents
                  // the server from sending error messages during download.
            ?>
            <a class="badge badge-primary" href="<?= site_url(build_get('file_manager/download_ex', array('dir' => $dirlist['relativedir'], 'file' => $f->filename))) ?>"><?= $this->lang->line('download') ?></a>
            <a class="badge badge-primary" href="<?= site_url(build_get('text/edit_quiz',array('quiz' => $dirlist['relativedir'] . '/' . $f->filename))) ?>"><?= $this->lang->line('edit') ?></a>
            <a class="badge badge-primary" href="#" onclick="rename('<?= substr($f->filename,0,-4) ?>'); return false;"><?= $this->lang->line('rename') ?></a>
            <a class="badge badge-primary passage_copied" href="#" data-filename="<?= substr($f->filename,0,-4) ?>" onclick="copy_passages(this); return false;"><?= $this->lang->line('copy_passages') ?></a>
        </td>
      </tr>
    <?php endforeach; ?>
    </table>
  </form>
  <p>
    <a class="btn btn-primary" onclick="deleteConfirm(); return false;" href="#"><span class="fas fa-trash-alt"></span> <?= $this->lang->line('delete_marked') ?></a>
    <a class="btn btn-primary" onclick="copyWarning(); return false;" href="#"><span class="fas fa-copy"></span> <?= $this->lang->line('copy_marked') ?></a>
    <a class="btn btn-primary" onclick="moveWarning(); return false;" href="#"><span class="fas fa-cut"></span> <?= $this->lang->line('move_marked') ?></a>
    <?php if ($isadmin): ?>
      <a class="btn btn-primary" onclick="changeOwner(); return false;" href="#"><span class="fas fa-user"></span> <?= $this->lang->line('change_owner_files') ?></a>
    <?php endif; ?>
  </p>
<?php endif; ?>

<?php if ($copy_or_move): ?>
  <p>
  <a class="btn btn-primary" href="<?= site_url(build_get('file_manager/insert_files',array('dir' => $dirlist['relativedir']))) ?>"><span class="fas fa-file-import"></span> <?= $copy_or_move==='move' ? $this->lang->line('insert_moved_files') : $this->lang->line('insert_copied_files') ?></a>
    <a class="btn btn-primary" href="<?= site_url(build_get('file_manager/cancel_copy',array('dir' => $dirlist['relativedir']))) ?>"><span class="fas fa-ban"></span> <?= $copy_or_move==='move' ? $this->lang->line('cancel_move') : $this->lang->line('cancel_copy')?></a>
  </p>
<?php endif; ?>

  <p>
    <a class="btn btn-primary" href="<?= site_url(build_get('file_manager/upload_files',array('dir' => $dirlist['relativedir']))) ?>"><span class="fas fa-file-upload"></span> <?= $this->lang->line('upload_exercises_button') ?></a>
    <a class="btn btn-primary" href="#" onclick="create_exercise(); return false;"><span class="fas fa-plus-circle"></span> <?= $this->lang->line('create_exercise_button') ?></a>
    <a class="btn btn-primary" id="passage-insert-confirm" onclick="passageInsertConfirm(); return false;" href="#"><span class="far fa-clone"></span> <?= $this->lang->line('insert_passages') ?></a>
  </p>


  <script>
    function uncheckAll() {
        $('input[name="file[]"]:checked').prop('checked',false);   
    }
  </script>
      

  <!-- Dialogs for this page follow -->

  <?php //*********************************************************************
        // Make Directory dialog 
        //*********************************************************************
    ?>
  <div id="mkdir-dialog" class="modal fade">
    <div class="modal-dialog modal-sm">
      <div class="modal-content">
        <div class="modal-header justify-content-between">
          <div><h4 class="modal-title"><?= $this->lang->line('create_folder_heading') ?></h4></div>
          <div><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button></div>
        </div>
        <div class="modal-body">
          <div class="alert alert-danger" id="mkdir-error" role="alert">
            <span class="fas fa-exclamation-circle" aria-hidden="true"></span>
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
          <button type="button" id="mkdir-dialog-ok" class="btn btn-primary"><?= $this->lang->line('OK_button') ?></button>
          <button type="button" class="btn btn-outline-dark" data-dismiss="modal"><?= $this->lang->line('cancel_button') ?></button>
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
        <div class="modal-header justify-content-between">
          <div><h4 class="modal-title"><?= $this->lang->line('rename_file_heading') ?></h4></div>
          <div><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button></div>
        </div>
        <div class="modal-body">
          <div class="alert alert-danger" id="rename-error" role="alert">
            <span class="fas fa-exclamation-circle" aria-hidden="true"></span>
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
          <button type="button" class="btn btn-outline-dark" data-dismiss="modal"><?= $this->lang->line('cancel_button') ?></button>
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
            else {
                if (sessionStorage.copy_passage_dir=="<?= $dirlist['relativedir'] ?>" &&
                    sessionStorage.copy_passage_file==$('#rename-oldname').attr('value')) {
                    // We're renaming the copy_passage source
                    sessionStorage.removeItem("copy_passage_dir");
                    sessionStorage.removeItem("copy_passage_file");
                }
                $('#rename-form').submit();
            }
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
        <div class="modal-header justify-content-between">
          <div><h4 class="modal-title"><?= $this->lang->line('create_exercise_heading') ?></h4></div>
          <div><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button></div>
        </div>
        <div class="modal-body">
          <form id="newquiz-form" action="<?= site_url('text/new_quiz') ?>" method="post">
            <div class="form-group">
              <h4><?= $this->lang->line('select_database_prompt') ?></h4>

              <?php $default = true; ?>
              <?php foreach($databases as $db): ?>
                <input type="radio" name="db" value="<?= $db['name'] ?>" <?= $default ? 'checked' : '' ?>><?= $db['loc_desc'] ?><br>
                <?php $default = false; ?>
              <?php endforeach; ?>
            </div>

            <input type="hidden" name="dir" value="<?= $dirlist['relativedir'] ?>">
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" id="newquiz-dialog-ok" class="btn btn-primary"><?= $this->lang->line('OK_button') ?></button>
          <button type="button" class="btn btn-outline-dark" data-dismiss="modal"><?= $this->lang->line('cancel_button') ?></button>
        </div>
      </div>
    </div>
  </div>

  <script>
    $(function() {
        $('#newquiz-dialog-ok').click(function() {
            $('#newquiz-form').submit();
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
        <div class="modal-header justify-content-between">
          <div><h4 class="modal-title" id="copy-dialog-warning-title"></h4></div>
          <div><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button></div>
        </div>
        <div class="modal-body">
          <span class="fas fa-exclamation-triangle" style="float:left; margin:0 7px 20px 0;" aria-hidden="true"></span>
          <span><?= sprintf($this->lang->line('click_and_go_to'), '<span id="copiedmoved"></span>') ?></span>
        </div>
        <div class="modal-footer">
          <button type="button" id="copy-dialog-warning-ok" class="btn btn-primary"><?= $this->lang->line('OK_button') ?></button>
          <button type="button" id="copy-dialog-warning-cancel" class="btn btn-outline-dark"><?= $this->lang->line('cancel_button') ?></button>
        </div>
      </div>
    </div>
  </div>

  <script>
    $(function() {
        $('#copy-dialog-warning-ok').click(function() {
            if ($('input[name="operation"]').val()=='move' &&
                sessionStorage.copy_passage_dir=="<?= $dirlist['relativedir'] ?>") {
                // Check if we're moving the copy_passage source
                $('input[name="file[]"]:checked').each(function (ix,el) {
                    if ((sessionStorage.copy_passage_file+'.3et')==$(el).val()) {
                        // We're renaming the copy_passage source
                        sessionStorage.removeItem("copy_passage_dir");
                        sessionStorage.removeItem("copy_passage_file");
                    }
                });
            }

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
        <div class="modal-header justify-content-between">
          <div><h4 class="modal-title"><?= $this->lang->line('confirm_deletion') ?></h4></div>
          <div><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button></div>
        </div>
        <div class="modal-body">
          <span class="fas fa-question-circle" style="float:left; margin:0 7px 20px 0;" aria-hidden="true"></span>
          <span><?= $this->lang->line('delete_file_confirm') ?></span>
        </div>
        <div class="modal-footer">
          <button type="button" id="delete-dialog-confirm-yes" class="btn btn-primary"><?= $this->lang->line('yes') ?></button>
          <button type="button" id="delete-dialog-confirm-no" class="btn btn-outline-dark" data-dismiss="modal"><?= $this->lang->line('no') ?></button>
        </div>
      </div>
    </div>
  </div>

  <script>
    $(function() {
        $('#delete-dialog-confirm-yes').click(function() {
            if (sessionStorage.copy_passage_dir=="<?= $dirlist['relativedir'] ?>") {
                // Check if we're deleting the copy_passage source
                $('input[name="file[]"]:checked').each(function (ix,el) {
                    if ((sessionStorage.copy_passage_file+'.3et')==$(el).val()) {
                        // We're renaming the copy_passage source
                        sessionStorage.removeItem("copy_passage_dir");
                        sessionStorage.removeItem("copy_passage_file");
                    }
                });
            }
            
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
        // Passage Insert Warning dialog 
        //*********************************************************************
    ?>
  <div id="passage-insert-dialog-confirm" class="modal fade">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header justify-content-between">
          <div><h4 class="modal-title"><?= $this->lang->line('confirm_passage_insert') ?></h4></div>
          <div><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button></div>
        </div>
        <div class="modal-body">
          <span class="fas fa-question-circle" style="float:left; margin:0 7px 20px 0;" aria-hidden="true"></span>
          <span id="passage-insert-description"></span>
        </div>
        <div class="modal-footer">
          <button type="button" id="passage-insert-dialog-confirm-yes" class="btn btn-primary"><?= $this->lang->line('yes') ?></button>
          <button type="button" id="passage-insert-dialog-confirm-no" class="btn btn-outline-dark" data-dismiss="modal"><?= $this->lang->line('no') ?></button>
        </div>
      </div>
    </div>
  </div>

  <script>
    $(function() {
        if (!sessionStorage.copy_passage_file)
            $('#passage-insert-confirm').hide();
        else if (sessionStorage.copy_passage_dir=="<?= $dirlist['relativedir'] ?>")
            $('[data-filename="' + sessionStorage.copy_passage_file + '"]').removeClass('badge-primary').addClass('badge-success'); // Mark the active 'Copy passages' button

              
        $('#passage-insert-dialog-confirm-yes').click(

            // Called when the 'Yes' button in the passage insert dialog has been pressed
            function() {
                $.ajax({
                    "url":"<?= site_url('file_manager/passage_insert') ?>",
                    "method":"POST",
                    "data":{ "dir": "<?= $dirlist['relativedir'] ?>",
                             "file": $.map($('input[name="file[]"]:checked'),function (el) {return $(el).val();}), // An array of the names of all marked files
                             "passage-source": sessionStorage.copy_passage_dir + "/" + sessionStorage.copy_passage_file + ".3et"
                           }
                }).done(function (data, textStatus, jqXHR) {
                    var pdata = JSON.parse(data);
                    if (pdata.status=='error')
                        myalert_large("<?= $this->lang->line('passage_copy_error') ?>",pdata.error_text);
                    else
                        alert("<?= $this->lang->line('passage_copy_ok') ?>");
                }).fail(function(jqXHR, textStatus, errorThrown) {
                    myalert("<?= $this->lang->line('server_error') ?>",errorThrown);
                });
                
                $("#passage-insert-dialog-confirm").modal('hide');
            }
        );
        
        $('#passage-insert-dialog-confirm-no').click(

            // Called when the 'No' button in the passage insert dialog has been pressed
            function() {
                $("#passage-insert-dialog-confirm").modal('hide');
            }
        );
    });

    // string.format function - replaces {0}, {1} etc. with parameter
    String.prototype.format = function () {
        var args = arguments;
        return this.replace(/{(\d+)}/g, function (match, num) {
            return typeof args[num] != 'undefined'
                ? args[num]
                : match;
        });
    };

    // Called when the 'Insert passages into marked files' button has been pressed
    function passageInsertConfirm() {
        if ($('input[name="file[]"]:checked').length===0) {
            myalert('<?= $this->lang->line('file_selection') ?>','<?= $this->lang->line('no_files_selected') ?>');
            return;
        }
        if (!sessionStorage.copy_passage_file) {
            // Should never happen
            return;
        }
        $('#passage-insert-description').text("<?= $this->lang->line('insert_passages_from') ?>".format(sessionStorage.copy_passage_dir + "/" + sessionStorage.copy_passage_file));

        $('input[name="passage-source"]').val(sessionStorage.copy_passage_dir + "/" + sessionStorage.copy_passage_file + ".3et");
          
        $('#passage-insert-dialog-confirm').modal('show');
    }

    // Called when the 'Copy passages' button has been pressed
    function copy_passages(elem) {
        $('#passage-insert-confirm').show();
        sessionStorage.copy_passage_dir = '<?= $dirlist['relativedir'] ?>';
        sessionStorage.copy_passage_file = $(elem).attr('data-filename');

        $('.passage_copied').removeClass('badge-success').addClass('badge-primary'); // Unmark all 'Copy passages' buttons
        $(elem).removeClass('badge-primary').addClass('badge-success'); // Mark the active 'Copy passages' button
    }
  </script>
                                                                                                        


  <?php //*********************************************************************
        // Change Owner dialog
        //*********************************************************************
    ?>
  <div id="chown-dialog" class="modal fade">
    <div class="modal-dialog modal-sm">
      <div class="modal-content">
        <div class="modal-header justify-content-between">
          <div><h4 class="modal-title"><?= $this->lang->line('change_owner_title') ?></h4></div>
          <div><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button></div>
        </div>
        <div class="modal-body">
          <div class="alert alert-danger" id="chown-error" role="alert">
            <span class="fas fa-exclamation-circle" aria-hidden="true"></span>
            <span id="chown-error-text"></span>
          </div>
          <span><?= $this->lang->line('new_owner_prompt') ?></span>
          <select id="chown-selector">
            <option value="0" selected="selected"></option>
            <?php foreach ($teachers as $t): ?>
            <option value="<?= $t->id ?>"><?= make_full_name($t) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="modal-footer">
          <button type="button" id="chown-dialog-ok" class="btn btn-primary"><?= $this->lang->line('OK_button') ?></button>
          <button type="button" class="btn btn-outline-dark" data-dismiss="modal"><?= $this->lang->line('cancel_button') ?></button>
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

