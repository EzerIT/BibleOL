<script>
    function genericConfirm(dialogtitle,dialogtext,destination) {
        $('#generic-dialog-confirm').dialog('option', 'title', dialogtitle);
        $('#generic-confirm-text').html(dialogtext);
        $('#generic-destination').text(destination);
        $('#generic-dialog-confirm').dialog('open');
    }

    $(function() {
        $("#generic-dialog-confirm").dialog({
            autoOpen: false,
            resizable: false,
            modal: true,
            buttons: {
                "<?= $this->lang->line('yes') ?>": function() {
                    location = $("#generic-destination").text();
                    $( this ).dialog( "close" );
                },
                "<?= $this->lang->line('no') ?>": function() {
                    $( this ).dialog( "close" );
                }
            }
            });
        });
</script>

<!-- Generic confirmation dialog -->
<div id="generic-dialog-confirm" style="display:none">
  <p>
    <span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>
    <span id="generic-confirm-text"></span>
    <span id="generic-destination" style="display:none"></span>
  </p>
</div>
