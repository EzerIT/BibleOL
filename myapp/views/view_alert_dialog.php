<script>
    function myalert(dialogtitle, dialogtext) {
        $('#alert-title').html(dialogtitle);
        $('#alert-text').html(dialogtext);
        $('#alert-dialog').modal('show');
    }
</script>

<!-- Generic alert dialog -->
<div id="alert-dialog" class="modal fade">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="alert-title">&nbsp;</h4>
      </div>
      <div class="modal-body">
        <span class="glyphicon glyphicon-alert" style="float:left; margin:0 7px 20px 0;" aria-hidden="true"></span>
        <span id="alert-text"></span>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?= $this->lang->line('OK_button') ?></button>
      </div>
    </div>
  </div>
</div>

