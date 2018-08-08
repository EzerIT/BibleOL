<script>
    function genericConfirm(dialogtitle,dialogtext,destination) {
        $('#generic-dialog-confirm2').removeClass('modal-sm');
        $('#generic-confirm-title').html(dialogtitle);
        $('#generic-confirm-text').html(dialogtext);
        $('#generic-destination').text(destination);
        $('#generic-dialog-confirm').modal('show');
    }

    function genericConfirmSm(dialogtitle,dialogtext,destination) {
        $('#generic-dialog-confirm2').addClass('modal-sm');
        $('#generic-confirm-title').html(dialogtitle);
        $('#generic-confirm-text').html(dialogtext);
        $('#generic-destination').text(destination);
        $('#generic-dialog-confirm').modal('show');
    }

    $(function() {
        $('#yesbutton').click(function() {
            location = $("#generic-destination").text();
        });
        });
</script>

<!-- Generic confirmation dialog -->
<div id="generic-dialog-confirm" class="modal fade">
  <div class="modal-dialog" id="generic-dialog-confirm2">
    <div class="modal-content">
      <div class="modal-header justify-content-between">
        <div><h4 class="modal-title" id="generic-confirm-title">&nbsp;</h4></div>
        <div><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button></div>
      </div>
      <div class="modal-body">
        <span class="fas fa-question-circle" style="float:left; margin:0 7px 20px 0;" aria-hidden="true"></span>
        <span id="generic-confirm-text"></span>
        <span id="generic-destination" style="display:none"></span>
      </div>
      <div class="modal-footer">
        <button type="button" id="yesbutton" class="btn btn-primary"><?= $this->lang->line('yes') ?></button>
        <button type="button" class="btn btn-outline-dark" data-dismiss="modal"><?= $this->lang->line('no') ?></button>
      </div>
    </div>
  </div>
</div>
