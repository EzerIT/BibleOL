<?php

$this->load->helper('icon_helper');

function make_icon_radio_button(string $name) {
   return get_instance()->lang->line("icon_$name")
       . "&nbsp;<span class=\"" . L_icon::css_class($name) . "\"></span>&nbsp;"
          . "<input type=\"radio\" name=\"icon\" value=\"$name\">";
}
?>


<table class="table table-striped">

<tr>
     <th class="text-right"><?= $this->lang->line('lexeme') ?></th>
     <th><?= $this->lang->line('english') ?></th>
     <th><?= $this->lang->line('icon') ?></th>
     <th><?= $this->lang->line('link') ?></th>
     <th><?= $this->lang->line('urls_operations') ?></th>
</tr>

<?php foreach ($words as $w): ?>
  <tr>
    <td class="heb-default rtl"><?= $w->vocalized_lexeme_utf8 . ' ' . $w->roman  ?></td>
    <td><?= htmlspecialchars($w->gloss) ?></td>
    <td>
       <?php if (isset($w->urls)): ?>
         <?php for ($i=0; $i<3; ++$i): ?>
           <?php if (isset($w->urls[$i])): ?>
             <span class="<?= L_icon::css_class($w->urls[$i]->icon) ?>" aria-hidden="true"></span><br/>
           <?php endif; ?>
         <?php endfor; ?>
       <?php endif; ?>
     </td>
     <td>
       <?php if (isset($w->urls)): ?>
         <?php for ($i=0; $i<3; ++$i): ?>
           <?php if (isset($w->urls[$i])): ?>
             <a href="<?= htmlspecialchars($w->urls[$i]->url) ?>" target="_blank"><?= $this->lang->line('link') ?></a><br/>
           <?php endif; ?>
         <?php endfor; ?>
       <?php endif; ?>
     </td>
     <td>
       <?php $make_add = !isset($w->urls); /* Should we add an 'Add link' button */ ?>
       <?php if (isset($w->urls)): ?>
         <?php for ($i=0; $i<3; ++$i): ?>
           <?php if (isset($w->urls[$i])): ?>
             <a class="badge badge-primary" href="#"
                onclick="edit_url(<?= $w->urls[$i]->id ?>,
                                  '<?= '<span class=&quot;heb-default rtl&quot;>' . $w->vocalized_lexeme_utf8 . ' ' . $w->roman . '</span>' ?>',
                                  '<?= str_replace("'", 'QQzQQ', htmlspecialchars($w->urls[$i]->url)) ?>',
                                  '<?= $w->urls[$i]->icon ?>');
                         return false;"><?= $this->lang->line('urls_edit') ?></a>


             <a class="badge badge-danger" href="#"
                onclick="deleteUrlConfirm('<?= sprintf($this->lang->line('delete_url_confirm'),
                                                       '<span class=&quot;heb-default rtl&quot;>' . $w->vocalized_lexeme_utf8 . ' ' . $w->roman . '</span>',
                                                       str_replace("'", 'QQzQQ', htmlspecialchars($w->urls[$i]->url))
                                                       ) ?>',
                                                       <?= $w->urls[$i]->id ?>);
                         return false;"><?= $this->lang->line('urls_delete') ?></a>
             <br/>
           <?php else: ?>
             <?php $make_add = true; ?>
           <?php endif; ?>
         <?php endfor; ?>
       <?php endif; ?>
       <?php if ($make_add): ?>
          <a class="badge badge-primary" href="#"
             onclick="create_url('<?= $w->lex ?>',
                                 '<?= '<span class=&quot;heb-default rtl&quot;>' . $w->vocalized_lexeme_utf8 . ' ' . $w->roman . '</span>' ?>');
                      return false;"><?= $this->lang->line('urls_add') ?></a>
       <?php endif; ?>
     </td>
  </tr>
<?php endforeach; ?>

</table>


  <?php //*********************************************************************
        // Edit URL dialog 
        //*********************************************************************
    ?>
  <div id="edit-url-dialog" class="modal fade">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header justify-content-between">
          <div><h4 id="heading-create" class="modal-title"><?= sprintf($this->lang->line('create_url_heading'), '<span id="create-url-gloss"></span>') ?></h4>
          <h4 id="heading-edit" class="modal-title"><?= sprintf($this->lang->line('edit_url_heading'), '<span id="edit-url-gloss"></span>') ?></h4></div>
          <div><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button></div>
        </div>
        <div class="modal-body">
          <div class="alert alert-danger" id="edit-url-error" role="alert">
            <span class="fas fa-exclamation-circle" aria-hidden="true"></span>
            <span id="edit-url-error-text"></span>
          </div>

          <form id="edit-url-form" action="<?= site_url('urls/change_url') ?>" method="post">

            <div class="form-group">
              <label for="edit-url-link"><?= $this->lang->line('link_colon') ?></label>
              <input type="text" name="link" class="form-control" id="edit-url-link">
            </div>

            <div class="form-group">
              <label for=""><?= $this->lang->line('link_icon') ?></label>
              <table class="iconlist" id="iconlist">
                <tr>
                  <td><?= make_icon_radio_button('l-icon-link') ?></td>
                  <td><?= make_icon_radio_button('l-icon-file') ?></td>
                  <td><?= make_icon_radio_button('l-icon-logos') ?></td>
                </tr>
                <tr>
                  <td><?= make_icon_radio_button('l-icon-music') ?></td>
                  <td><?= make_icon_radio_button('l-icon-picture') ?></td>
                  <td><?= make_icon_radio_button('l-icon-film') ?></td>
                </tr>
                <tr>
                  <td><?= make_icon_radio_button('l-icon-speaker') ?></td>
                  <td><?= make_icon_radio_button('l-icon-book') ?></td>
                  <td><?= make_icon_radio_button('l-icon-globe') ?></td>
                </tr>
              </table>

            </div>

            <input type="hidden" name="id" id="edit-url-id">
            <input type="hidden" name="scrolltop" id="edit-url-scrolltop">
            <input type="hidden" name="lex" id="edit-url-lex">
            <input type="hidden" name="longlang" value="<?= $longlang ?>">
            <input type="hidden" name="requesturi" value="<?= $_SERVER['REQUEST_URI'] ?>">
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" id="edit-url-dialog-ok" class="btn btn-primary"><?= $this->lang->line('OK_button') ?></button>
          <button type="button" class="btn btn-outline-dark" data-dismiss="modal"><?= $this->lang->line('cancel_button') ?></button>
        </div>
      </div>
    </div>
  </div>

  <script>
    $(function() {
        $('#edit-url-dialog-ok').click(function() {
            linkname = $('#edit-url-link').val().trim();
            if (linkname=='') {
                $('#edit-url-error-text').text('<?= $this->lang->line('missing_link') ?>');
                $('#edit-url-error').show();
            }
            else
                // Valiate URL (from http://stackoverflow.com/questions/1303872/trying-to-validate-url-using-javascript)
                if (! /^(?:(?:(?:https?|ftp):)?\/\/)(?:\S+(?::\S*)?@)?(?:(?!(?:10|127)(?:\.\d{1,3}){3})(?!(?:169\.254|192\.168)(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z\u00a1-\uffff0-9]-*)*[a-z\u00a1-\uffff0-9]+)(?:\.(?:[a-z\u00a1-\uffff0-9]-*)*[a-z\u00a1-\uffff0-9]+)*(?:\.(?:[a-z\u00a1-\uffff]{2,})).?)(?::\d{2,5})?(?:[/?#]\S*)?$/i.test(linkname)) {
                $('#edit-url-error-text').text('<?= $this->lang->line('invalid_link') ?>');
                $('#edit-url-error').show();
            }
            else
                $('#edit-url-form').submit();
        });

        <?php if (isset($_GET['scrolltop']) && is_numeric($_GET['scrolltop'])): ?>
            $(document).scrollTop(<?= $_GET['scrolltop'] ?>);
        <?php endif; ?>
    });

    function edit_url(id,gloss,url,icon) {
        $('#edit-url-id').attr('value',id);
        $('#edit-url-gloss').html(gloss);
        $('#edit-url-link').attr('value',url.replace(/QQzQQ/g,"'"));
        $('#iconlist input').prop('checked',false);
        $('#iconlist input[value="l-icon-link"]').prop('checked',true);  // Default selection
        $('#iconlist input[value="' + icon +'"]').prop('checked',true);
        $('#edit-url-scrolltop').attr('value',$(document).scrollTop()); // For positioning the window later
        $('#heading-create').hide();
        $('#heading-edit').show();
        $('#edit-url-error').hide();
        $('#edit-url-dialog').modal('show');
    }

    function create_url(lex,gloss) {
        $('#edit-url-id').attr('value',-1);
        $('#create-url-gloss').html(gloss);
        $('#edit-url-lex').attr('value',lex);
        $('#edit-url-link').attr('value','');
        $('#iconlist input').prop('checked',false);
        $('#iconlist input[value="l-icon-link"]').prop('checked',true);  // Default selection
        $('#edit-url-scrolltop').attr('value',$(document).scrollTop()); // For positioning the window later
        $('#heading-create').show();
        $('#heading-edit').hide();
        $('#edit-url-error').hide();
        $('#edit-url-dialog').modal('show');
    }
  </script>

  <?php //*********************************************************************
        // Delete URL dialog 
        //*********************************************************************
    ?>

  <div id="delete-url-dialog" class="modal fade">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header justify-content-between">
          <div><h4 class="modal-title" id="delete-url-title"><?= $this->lang->line('delete_url') ?></h4></div>
          <div><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button></div>
        </div>
        <div class="modal-body">
          <span class="fas fa-question-circle" style="float:left; margin:0 7px 20px 0;" aria-hidden="true"></span>
          <span id="delete-confirm-text"></span>

          <form id="delete-url-form" action="<?= site_url('urls/delete_url') ?>" method="post">
            <input type="hidden" name="urlid" id="delete-url-id">
            <input type="hidden" name="scrolltop" id="delete-url-scrolltop">
            <input type="hidden" name="requesturi" value="<?= $_SERVER['REQUEST_URI'] ?>">
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" id="delete-yesbutton" class="btn btn-primary"><?= $this->lang->line('yes') ?></button>
          <button type="button" class="btn btn-outline-dark" data-dismiss="modal"><?= $this->lang->line('no') ?></button>
        </div>
      </div>
    </div>
  </div>
   
  <script>
    $(function() {
        $('#delete-yesbutton').click(function() {
            $('#delete-url-form').submit();
        });

        <?php if (isset($_GET['scrolltop']) && is_numeric($_GET['scrolltop'])): ?>
            $(document).scrollTop(<?= $_GET['scrolltop'] ?>);
        <?php endif; ?>
    });


      function deleteUrlConfirm(dialogtext,id) {
          $('#delete-confirm-text').html(dialogtext.replace(/QQzQQ/g,"'"));
          $('#delete-url-scrolltop').attr('value',$(document).scrollTop()); // For positioning the window later
          $('#delete-url-id').attr('value',id);

          $('#delete-url-dialog').modal('show');
      }
  </script>
