<?php if (!$is_top): ?>
  <h1><?= sprintf($this->lang->line('this_is_folder'), rtrim($dirlist['relativedir'],'/')) ?></h1>

  <p><a class="makebutton" href="<?= site_url("file_manager/edit_visibility?dir={$dirlist['relativedir']}") ?>"><?= $this->lang->line('edit_visibility_button') ?></a></p>
<?php else: ?>
  <h1><?= $this->lang->line('this_is_top_folder') ?></h1>
<?php endif; ?>


<?php if (!is_null($dirlist['parentdir']) || !empty($dirlist['directories'])): ?>

  <h2><?= $this->lang->line('folders') ?></h2>

  <table>

  <?php if (!is_null($dirlist['parentdir'])): ?>
    <tr>
      <td>
        <span class="ui-icon ui-icon-arrowreturnthick-1-w" style="display:inline-block;"></span>
        <a href="<?= site_url("file_manager?dir={$dirlist['parentdir']}") ?>"><?= $this->lang->line('parent_folder') ?></a>
      </td>
      <td></td>
    </tr>
  <?php endif; ?>

  <?php foreach ($dirlist['directories'] as $d): ?>
    <tr>
      <td>
        <span class="ui-icon ui-icon-folder-collapsed" style="display:inline-block;"></span>
        <a href="<?= site_url('file_manager?dir=' . composedir($dirlist['relativedir'], $d)) ?>"><?= $d ?>
      </td>
      <?php if ($dirlist['is_empty'][$d]): ?>

        <td><a class="makebutton" onclick="genericConfirm('<?= $this->lang->line('delete_folder_confirm') ?>',
                                     '<?= sprintf($this->lang->line('delete_folder_question'), "\'$d\'") ?>',
                                     '<?= site_url("file_manager/delete_folder?dir={$dirlist['relativedir']}&delete=$d") ?>');
                      return false;"
             href="#"><?= $this->lang->line('delete_folder_button') ?></a></td>
      <?php else: ?>
          <td><?= $this->lang->line('folder_not_empty') ?></td>
      <?php endif; ?>
    </tr>
  <?php endforeach; ?>

  </table>

<?php endif; ?>

  <p><a class="makebutton" href="#" onclick="make_dir('<?= $dirlist['relativedir'] ?>'); return false;"><?= $this->lang->line('create_folder_button') ?></a></p>


<?php if (!empty($dirlist['files'])): ?>
  <h2><?= $this->lang->line('exercises') ?></h2>
  <form id='copy-delete-form' action="<?= site_url('file_manager/copy_delete_files') ?>" method="post">
    <input type="hidden" name="dir" value="<?= $dirlist['relativedir'] ?>">
    <input type="hidden" name="operation" value="">
    <input type="hidden" name="newowner" value="">
    <table class="type1">
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
            <a href="<?= site_url("file_manager/download_ex?dir={$dirlist['relativedir']}&file=$f->filename") ?>"><?= $this->lang->line('download') ?></a>
            <a href="<?= site_url("text/edit_quiz?quiz={$dirlist['relativedir']}/$f->filename") ?>"><?= $this->lang->line('edit') ?></a>
            <a href="#" onclick="rename('<?= substr($f->filename,0,-4) ?>'); return false;"><?= $this->lang->line('rename') ?></a></span>
        </td>
      </tr>
    <?php endforeach; ?>
    </table>
  </form>
  <p>
    <a class="makebutton" onclick="deleteConfirm(); return false;" href="#"><?= $this->lang->line('delete_marked') ?></a>
    <a class="makebutton" onclick="copyWarning(); return false;" href="#"><?= $this->lang->line('copy_marked') ?></a>
    <a class="makebutton" onclick="moveWarning(); return false;" href="#"><?= $this->lang->line('move_marked') ?></a>
    <?php if ($isadmin): ?>
      <a class="makebutton" onclick="changeOwner(); return false;" href="#"><?= $this->lang->line('change_owner_files') ?></a>
    <?php endif; ?>
  </p>
<?php endif; ?>

<?php if ($copy_or_move): ?>
  <p>
  <a class="makebutton" href="<?= site_url("file_manager/insert_files?dir={$dirlist['relativedir']}") ?>"><?= $copy_or_move==='move' ? $this->lang->line('insert_moved_files') : $this->lang->line('insert_copied_files') ?></a>
    <a class="makebutton" href="<?= site_url("file_manager/cancel_copy?dir={$dirlist['relativedir']}") ?>"><?= $copy_or_move==='move' ? $this->lang->line('cancel_move') : $this->lang->line('cancel_copy')?></a>
  </p>
<? endif; ?>

  <p>
    <a class="makebutton" href="<?= site_url("file_manager/upload_files?dir={$dirlist['relativedir']}") ?>"><?= $this->lang->line('upload_exercises_button') ?></a>
    <a class="makebutton" href="#" onclick="create_exercise(); return false;"><?= $this->lang->line('create_exercise_button') ?></a>
  </p>



  <!-- Dialogs for this page follow -->

  <!-- Make Directory dialog -->

  <div id="mkdir-dialog-form" style="display:none" title="<?= $this->lang->line('enter_folder_name') ?>">
    <p class="error" id="mkdir-error"></p>
    <form id="mkdir-form" action="<?= site_url('file_manager/create_folder') ?>" method="post">
        <table>
          <tr>
            <td><?= $this->lang->line('folder_name_prompt') ?></td>
            <td><input type="text" name="create" id="mkdir-name" size="35" class="text ui-widget-content ui-corner-all"></td>
          </tr>
        </table>
        <input type="hidden" name="dir" id="mkdir-parent">
    </form>
  </div>


  <!-- Rename dialog -->

  <div id="rename-dialog-form" style="display:none" title="<?= $this->lang->line('rename_file') ?>">
    <p class="error" id="rename-error"></p>
    <form id="rename-form" action="<?= site_url('file_manager/rename_file') ?>" method="post">
        <table>
          <tr>
            <td><?= $this->lang->line('new_filename_prompt') ?></td>
            <td><input type="text" name="newname" id="rename-newname" size="35" class="text ui-widget-content ui-corner-all"></td>
          </tr>
        </table>
        <input type="hidden" name="dir" value="<?= $dirlist['relativedir'] ?>">
        <input type="hidden" name="oldname" id="rename-oldname">
    </form>
  </div>


  <!-- Create Quiz dialog -->

  <div id="newquiz-dialog-form" style="display:none" title="<?= $this->lang->line('select_database_heading') ?>">
    <form id="newquiz-form" action="<?= site_url('text/new_quiz') ?>" method="post">
        <table>
          <tr>
            <td><?= $this->lang->line('select_database_prompt') ?></td>
            <td>
            <?php $default = true; ?>
            <?php foreach($databases as $db): ?>
              <input type="radio" name="db" value="<?= $db['name'] ?>" <?= $default ? 'checked' : '' ?>><?= $db['loc_desc'] ?><br>
              <?php $default = false; ?>
            <?php endforeach; ?>
            </td>
          </tr>
        </table>
        <input type="hidden" name="dir" value="<?= $dirlist['relativedir'] ?>">
    </form>
  </div>


  <!-- Copy/Move Warning dialog -->
  <div id="copy-dialog-warning" style="display:none">
    <p>
      <span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>
      <span><?= sprintf($this->lang->line('click_and_go_to'), '<span id="copiedmoved"></span>') ?></span>
    </p>
  </div>


  <!-- Confirm Deletion dialog -->
  <div id="delete-dialog-confirm" style="display:none">
    <p>
      <span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>
      <span><?= $this->lang->line('delete_file_confirm') ?></span>
    </p>
  </div>

  <!-- ChangeOwner dialog -->
  <div id="chown-dialog" style="display:none">
    <p>
      <span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>
      <span><?= $this->lang->line('new_owner_prompt') ?></span>
      <select id="chown-selector">
        <option value="0" selected="selected"></option>
        <?php foreach ($teachers as $t): ?>
          <option value="<?= $t->id ?>"><?= $t->first_name . ' ' . $t->last_name ?></option>
        <?php endforeach; ?>
      </select>
    </p>
  </div>

  <script>
    function filename_bad(filename) {
        badchars = /[\/?*:;{}\\]/;
        return !!filename.match(badchars);
    }

    $(function() {
        $('#mkdir-dialog-form').dialog({
            autoOpen: false,
            width: 450,
            modal: true,
            buttons: {
                '<?= $this->lang->line('OK_button') ?>': function() {
                    dirname = $('#mkdir-name').val().trim();
                    
                    if (dirname=='')
                        $('#mkdir-error').text('<?= $this->lang->line('missing_folder_name') ?>');
                    else if (filename_bad(dirname))
                        $('#mkdir-error').text('<?= $this->lang->line('illegal_char_folder') ?>');
                    else {
                        $('#mkdir-form').submit();
                        $(this).dialog('close');
                    }
                },
         
                '<?= $this->lang->line('cancel_button') ?>': function() {
                    $(this).dialog('close');
                }
            },

            close: function() {
                $('#mkdir-error').text('');
            }
        });

        $('#rename-dialog-form').dialog({
            autoOpen: false,
            width: 400,
            modal: true,
            buttons: {
                '<?= $this->lang->line('OK_button') ?>': function() {
                    filename = $('#rename-newname').val().trim();

                    if (filename=='')
                        $('#rename-error').text('<?= $this->lang->line('missing_filename') ?>');
                    else if (filename_bad(filename))
                        $('#rename-error').text('<?= $this->lang->line('illegal_char_filename') ?>');
                    else {
                        $('#rename-form').submit();
                        $(this).dialog('close');
                    }
                },

                '<?= $this->lang->line('cancel_button') ?>': function() {
                    $(this).dialog('close');
                }
            },

            close: function() {
                $('#rename-error').text('');
            }
        });

        $('#newquiz-dialog-form').dialog({
            autoOpen: false,
            width: 600,
            modal: true,
            buttons: {
                '<?= $this->lang->line('OK_button') ?>': function() {
                    $('#newquiz-form').submit();
                    $(this).dialog('close');
                },
                '<?= $this->lang->line('cancel_button') ?>': function() {
                    $(this).dialog('close');
                }
            },
            close: function() {
            }
        });

        $("#copy-dialog-warning").dialog({
            autoOpen: false,
            resizable: false,
            modal: true,
            buttons: {
                "<?= $this->lang->line('OK_button') ?>": function() {
                    $('#copy-delete-form').submit();
                    $(this).dialog("close");
                },
                '<?= $this->lang->line('cancel_button') ?>': function() {
                    $('input[name="operation"]').val(''); // Paranoia
                    $(this).dialog("close");
                }
            }
            });

        $("#delete-dialog-confirm").dialog({
            autoOpen: false,
            resizable: false,
            modal: true,
            buttons: {
                "<?= $this->lang->line('yes') ?>": function() {
                    $('#copy-delete-form').submit();
                    $(this).dialog("close");
                },
                "<?= $this->lang->line('no') ?>": function() {
                    $('input[name="operation"]').val(''); // Paranoia
                    $(this).dialog("close");
                }
            }
            });

        $("#chown-dialog").dialog({
            autoOpen: false,
            resizable: false,
            modal: true,
            buttons: {
                '<?= $this->lang->line('OK_button') ?>': function() {
                    userid = $("#chown-selector option:selected").val();
                    if (userid==0) {
                        $(this).dialog('close');
                        myalert('<?= $this->lang->line('no_user_selected_title') ?>', '<?= $this->lang->line('no_user_selected') ?>');
                        return false;
                    }    
                    $('input[name="newowner"]').val(userid);
                    $('#copy-delete-form').submit();
                    $(this).dialog('close');
                },
         
                '<?= $this->lang->line('cancel_button') ?>': function() {
                    $(this).dialog('close');
                }
            }
            });

        // Prevent 'return' key from submitting forms
        $(window).on("keydown",function(event) {
            if (event.keyCode == 13) {
                event.preventDefault();
                return false;
            }
        });
    });

    function make_dir(parent) {
        $('#mkdir-parent').attr('value',parent);
        $('#mkdir-dialog-form').dialog('open');
    }

    function rename(oldname) {
        $('#rename-oldname').attr('value',oldname);
        $('#rename-dialog-form').dialog('open');
    }

    function create_exercise() {
        $('#newquiz-dialog-form').dialog('open');
    }

    function copyWarning() {
        if ($('input[name="file[]"]:checked').length===0) {
            myalert('<?= $this->lang->line('file_selection') ?>','<?= $this->lang->line('no_files_selected') ?>');
            return;
        }
        $('input[name="operation"]').val('copy');
        $('#copiedmoved').text('<?= $this->lang->line('insert_copied_files') ?>');
        $('#copy-dialog-warning')
            .dialog('option', 'title', '<?= $this->lang->line('copy_files') ?>')
            .dialog('open');
    }

    function moveWarning() {
        if ($('input[name="file[]"]:checked').length===0) {
            myalert('<?= $this->lang->line('file_selection') ?>','<?= $this->lang->line('no_files_selected') ?>');
            return;
        }
        $('input[name="operation"]').val('move');
        $('#copiedmoved').text('<?= $this->lang->line('insert_moved_files') ?>');
        $('#copy-dialog-warning')
            .dialog('option', 'title', '<?= $this->lang->line('move_files') ?>')
            .dialog('open');
    }

    function deleteConfirm() {
        if ($('input[name="file[]"]:checked').length===0) {
            myalert('<?= $this->lang->line('file_selection') ?>','<?= $this->lang->line('no_files_selected') ?>');
            return;
        }
        $('input[name="operation"]').val('delete');
        $('#delete-dialog-confirm')
            .dialog('option', 'title', '<?= $this->lang->line('confirm_deletion') ?>')
            .dialog('open');
    }

    function changeOwner() {
        if ($('input[name="file[]"]:checked').length===0) {
            myalert('<?= $this->lang->line('file_selection') ?>','<?= $this->lang->line('no_files_selected') ?>');
            return;
        }
        $('input[name="operation"]').val('chown');
        $('#chown-dialog')
            .dialog('option', 'title', '<?= $this->lang->line('change_owner_title') ?>')
            .dialog('open');
    }
  </script>

