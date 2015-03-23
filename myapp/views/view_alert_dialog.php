<script>
    function myalert(dialogtitle, dialogtext) {
        $('#alert-text').text(dialogtext);
        $('#alert-dialog')
            .dialog('option', 'title', dialogtitle)
            .dialog('open');
    }

    $(function() {
        $("#alert-dialog").dialog({
            autoOpen: false,
            resizable: false,
            modal: true,
            buttons: {
                "OK": function() {
                    $( this ).dialog( "close" );
                },
            }
         });
    });
</script>

<!-- Generic alert dialog -->
<div id="alert-dialog" style="display:none">
  <p>
    <span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>
    <span id="alert-text"></span>
  </p>
</div>
