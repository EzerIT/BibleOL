<?php if (!$is_top): ?>
  <h1>This is the Folder <i><?= rtrim($dirlist['relativedir'],'/') ?></i></h1>

  <p><a class="makebutton" href="<?= site_url("file_manager/edit_visibility?dir={$dirlist['relativedir']}") ?>">Edit visibility</a></p>
<?php else: ?>
  <h1>This is the Top Folder</h1>
<?php endif; ?>


<?php if (!is_null($dirlist['parentdir']) || !empty($dirlist['directories'])): ?>

  <h2>Folders</h2>

  <table>

  <?php if (!is_null($dirlist['parentdir'])): ?>
    <tr>
      <td>
        <span class="ui-icon ui-icon-arrowreturnthick-1-w" style="display:inline-block;"></span>
        <a href="<?= site_url("file_manager?dir={$dirlist['parentdir']}") ?>">Parent</a>
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

        <td><a class="makebutton" onclick="genericConfirm('Delete folder',
                                     'Do you want to delete the folder \'<?= $d ?>\'?',
                                     '<?= site_url("file_manager/delete_folder?dir={$dirlist['relativedir']}&delete=$d") ?>');
                      return false;"
             href="#">Delete folder</a></td>
      <?php else: ?>
          <td>Folder contains files</td>
      <?php endif; ?>
    </tr>
  <?php endforeach; ?>

  </table>

<?php endif; ?>

  <p><a class="makebutton" href="#" onclick="make_dir('<?= $dirlist['relativedir'] ?>'); return false;">Create folder</a></p>


<?php if (!empty($dirlist['files'])): ?>
  <h2>Exercises</h2>
  <form id='copy-delete-form' action="<?= site_url('file_manager/copy_delete_files') ?>" method="post">
    <input type="hidden" name="dir" value="<?= $dirlist['relativedir'] ?>">
    <input type="hidden" name="operation" value="">
    <table class="type1">
      <tr><th>Mark</th><th>Name</th><th>Operations</th></tr>
    <?php foreach ($dirlist['files'] as $f): ?>
      <tr>
        <td><input type="checkbox" name="file[]" value="<?= $f ?>"></td>
        <td class="leftalign"><?= substr($f,0,-4) ?></td>
        <td><span class="small">
            <?php // Note: The following download link will cause this error to be written on Chrome's console:
                  // "Resource interpreted as Document but transferred with MIME type application/octet-stream".
                  // (See https://code.google.com/p/chromium/issues/detail?id=9891.)
                  // Adding the attibute 'download' to the <a ...> tag removes the error, but prevents
                  // the server from sending error messages during download.
            ?>
            <a href="<?= site_url("file_manager/download_ex?dir={$dirlist['relativedir']}&file=$f") ?>">Download</a>
            <a href="<?= site_url("text/edit_quiz?quiz={$dirlist['relativedir']}/$f") ?>">Edit</a>
            <a href="#" onclick="rename('<?= substr($f,0,-4) ?>'); return false;">Rename</a>
        </td>
      </tr>
    <?php endforeach; ?>
    </table>
  </form>
  <p>
    <a class="makebutton" onclick="deleteConfirm(); return false;" href="#">Delete marked files</a>
    <a class="makebutton" onclick="copyWarning(); return false;" href="#">Copy marked files</a>
    <a class="makebutton" onclick="moveWarning(); return false;" href="#">Move marked files</a>
  </p>
<?php endif; ?>

<?php if ($copy_or_move): ?>
  <?php $past = $copy_or_move==='move' ? 'moved' : 'copied'; ?>
  <p>
    <a class="makebutton" href="<?= site_url("file_manager/insert_files?dir={$dirlist['relativedir']}") ?>">Insert <?= $past ?> files</a>
    <a class="makebutton" href="<?= site_url("file_manager/cancel_copy?dir={$dirlist['relativedir']}") ?>">Cancel <?= $copy_or_move ?> operation</a>
  </p>
<? endif; ?>

  <p>
    <a class="makebutton" href="<?= site_url("file_manager/upload_files?dir={$dirlist['relativedir']}") ?>">Upload exercises</a>
    <a class="makebutton" href="#" onclick="create_exercise(); return false;">Create exercise</a>
  </p>



  <!-- Dialogs for this page follow -->

  <!-- Make Directory dialog -->

  <div id="mkdir-dialog-form" style="display:none" title="Enter Folder Name">
    <p class="error" id="mkdir-error"></p>
    <form id="mkdir-form" action="<?= site_url('file_manager/create_folder') ?>" method="post">
        <table>
          <tr>
            <td>Folder name</td>
            <td><input type="text" name="create" id="mkdir-name" size="35" class="text ui-widget-content ui-corner-all"></td>
          </tr>
        </table>
        <input type="hidden" name="dir" id="mkdir-parent">
    </form>
  </div>


  <!-- Rename dialog -->

  <div id="rename-dialog-form" style="display:none" title="Rename File">
    <p class="error" id="rename-error"></p>
    <form id="rename-form" action="<?= site_url('file_manager/rename_file') ?>" method="post">
        <table>
          <tr>
            <td>New filename</td>
            <td><input type="text" name="newname" id="rename-newname" size="35" class="text ui-widget-content ui-corner-all"></td>
          </tr>
        </table>
        <input type="hidden" name="dir" value="<?= $dirlist['relativedir'] ?>">
        <input type="hidden" name="oldname" id="rename-oldname">
    </form>
  </div>


  <!-- Create Quiz dialog -->

  <div id="newquiz-dialog-form" style="display:none" title="Select Database">
    <form id="newquiz-form" action="<?= site_url('text/new_quiz') ?>" method="post">
        <table>
          <tr>
            <td>Select database:</td>
            <td>
            <?php $default = true; ?>
            <?php foreach($databases as $db): ?>
              <input type="radio" name="db" value="<?= $db['name'] ?>" <?= $default ? 'checked' : '' ?>><?= $db['loc_desc'] ?><br>
              <?php $default = false; ?>
            <?php endforeach; ?>
            </td>
          </tr>
        </table>
        <input type="hidden" name="dir" id="mkdir-parent" value="<?= $dirlist['relativedir'] ?>">
    </form>
  </div>


  <!-- Copy/Move Warning dialog -->
  <div id="copy-dialog-warning" style="display:none">
    <p>
      <span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>
      <span>Click 'OK' here, and then go to the destination folder and press 'Insert <span id="copiedmoved"></span> files'.</span>
    </p>
  </div>


  <!-- Confirm Deletion dialog -->
  <div id="delete-dialog-confirm" style="display:none">
    <p>
      <span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>
      <span>Do you want to delete the indicated files?</span>
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
            width: 400,
            modal: true,
            buttons: {
                'OK': function() {
                    dirname = $('#mkdir-name').val().trim();
                    
                    if (dirname=='')
                        $('#mkdir-error').text("Missing folder name");
                    else if (filename_bad(dirname))
                        $('#mkdir-error').text("Illegal character in folder name");
                    else {
                        $('#mkdir-form').submit();
                        $(this).dialog('close');
                    }
                },
         
                Cancel: function() {
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
                'OK': function() {
                    filename = $('#rename-newname').val().trim();

                    if (filename=='')
                        $('#rename-error').text("Missing filename");
                    else if (filename_bad(filename))
                        $('#rename-error').text("Illegal character in filename");
                    else {
                        $('#rename-form').submit();
                        $(this).dialog('close');
                    }
                },

                Cancel: function() {
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
                'OK': function() {
                    $('#newquiz-form').submit();
                    $(this).dialog('close');
                },
                Cancel: function() {
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
                "OK": function() {
                    $('#copy-delete-form').submit();
                    $(this).dialog("close");
                },
                Cancel: function() {
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
                "Yes": function() {
                    $('#copy-delete-form').submit();
                    $(this).dialog("close");
                },
                "No": function() {
                    $('input[name="operation"]').val(''); // Paranoia
                    $(this).dialog("close");
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
            myalert('File selection','No files selected');
            return;
        }
        $('input[name="operation"]').val('copy');
        $('#copiedmoved').text('copied');
        $('#copy-dialog-warning')
            .dialog('option', 'title', 'Copy Files')
            .dialog('open');
    }

    function moveWarning() {
        if ($('input[name="file[]"]:checked').length===0) {
            myalert('File selection','No files selected');
            return;
        }
        $('input[name="operation"]').val('move');
        $('#copiedmoved').text('moved');
        $('#copy-dialog-warning')
            .dialog('option', 'title', 'Move Files')
            .dialog('open');
    }

    function deleteConfirm() {
        if ($('input[name="file[]"]:checked').length===0) {
            myalert('File selection','No files selected');
            return;
        }
        $('input[name="operation"]').val('delete');
        $('#delete-dialog-confirm')
            .dialog('option', 'title', 'Confirm Deletion')
            .dialog('open');
    }
  </script>

