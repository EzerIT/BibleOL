<h1>Available localizations</h1>
<?php
  function make_button(bool $enabled, string $what) {
     $CI =& get_instance();

     if ($enabled)
         return '<a class="badge badge-success" href="' . site_url("translate/modify_localization/disable/$what") . '">Enabled</a>';
     else
         return '<a class="badge badge-danger" href="'  . site_url("translate/modify_localization/enable/$what")  . '">Disabled</a>';
  }

  function format_frac(array $a) {
      //return sprintf("%d/%d (%d%%)",$a[0],$a[1],floor($a[0]/$a[1]*100));
      return sprintf("%d%%",floor($a[0]/$a[1]*100));
  }

?>

<div class="table-responsive">
  <table class="type2 table table-striped">
    <tr>
       <th class="centeralign">Internal name</th>
       <th class="centeralign">Native name</th>
       <th class="centeralign">Abbreviation</th>
       <th class="centeralign">Webpage</th>
       <th class="leftalign">Webpage completeness</th>
       <th class="centeralign">Lexicons</th>
       <th class="leftalign">Lexicon completeness</th>
    </tr>
    <?php foreach ($avail_translations as $trans): ?>
      <tr>
         <td class="centeralign"><?= $trans->internal ?></td>
         <td class="centeralign text-nowrap"><?= $trans->native ?></td>
         <td class="centeralign"><?= $trans->abb ?></td>
         <td class="centeralign"><?= make_button($trans->iface_enabled,"iface/{$trans->abb}") ?></td>
         <td>
           <table class="embedded table-borderless">
             <?php if ($trans->iface_enabled): ?>
               <tr><td class="text-nowrap">User interface</td><td class="text-nowrap"><?= format_frac($trans_if_items[$trans->abb]) ?></td></tr>
               <tr><td class="text-nowrap">Hebrew/Aramaic grammar</td><td class="text-nowrap"><?= format_frac($trans_hebgrammar_items[$trans->abb]) ?></td></tr>
               <tr><td class="text-nowrap">H/A transliterated grammar</td><td class="text-nowrap"><?= format_frac($trans_hebtgrammar_items[$trans->abb]) ?></td></tr>
               <tr><td class="text-nowrap">Greek grammar</td><td class="text-nowrap"><?= format_frac($trans_greekgrammar_items[$trans->abb]) ?></td></tr>
               <tr><td class="text-nowrap">Latin grammar</td><td class="text-nowrap"><?= format_frac($trans_latingrammar_items[$trans->abb]) ?></td></tr>
             <?php else: ?>
               <tr><td>&nbsp;</td></tr>
               <tr><td>&nbsp;</td></tr>
               <tr><td>&nbsp;</td></tr>
               <tr><td>&nbsp;</td></tr>
             <?php endif; ?>
           </table>
         </td>
         <td>
           <table class="embedded table-borderless">
             <tr><td>Hebrew/Aramaic</td><td><?= make_button($trans->heblex_enabled,"heblex/{$trans->abb}") ?></td></tr>
             <tr><td>&nbsp;</td><td>&nbsp;</td></tr>
             <tr><td>Greek</td><td><?= make_button($trans->greeklex_enabled,"greeklex/{$trans->abb}") ?></td></tr>
             <tr><td>Latin</td><td><?= make_button($trans->latinlex_enabled,"latinlex/{$trans->abb}") ?></td></tr>
           </table>
         </td>
         <td>
           <table class="embedded table-borderless">
             <?php if ($trans->heblex_enabled): ?>
               <tr><td>Hebrew<td><td class="text-nowrap"><?= format_frac($trans_heblex_items[$trans->abb]) ?></td></tr>
               <tr><td>Aramaic<td><td class="text-nowrap"><?= format_frac($trans_aramlex_items[$trans->abb]) ?></td></tr>
             <?php else: ?>
               <tr><td>&nbsp;</td><td>&nbsp;</td></tr>
               <tr><td>&nbsp;</td><td>&nbsp;</td></tr>
             <?php endif; ?>
             <?php if ($trans->greeklex_enabled): ?>
               <tr><td>Greek<td><td class="text-nowrap"><?= format_frac($trans_greeklex_items[$trans->abb]) ?></td></tr>
             <?php else: ?>
               <tr><td>&nbsp;</td><td>&nbsp;</td></tr>
             <?php endif; ?>
             <?php if ($trans->latinlex_enabled): ?>
               <tr><td>Latin<td><td class="text-nowrap"><?= format_frac($trans_latinlex_items[$trans->abb]) ?></td></tr>
             <?php else: ?>
               <tr><td>&nbsp;</td><td>&nbsp;</td></tr>
             <?php endif; ?>
           </table>
         </td>
      </tr>
    <?php endforeach; ?>
  </table>
</div>


<p><a class="btn btn-primary" href="#" onclick="create_language();return false;">Add language</a></p>

     
  <?php //*********************************************************************
        // 'Create language' dialog 
        //*********************************************************************
    ?>
  <div id="edit-language-dialog" class="modal fade">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header justify-content-between">
          <h4 id="heading-create" class="modal-title">Create Localization Language</h4>
          <div><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button></div>
        </div>
        <div class="modal-body">
          <div class="alert alert-danger" id="edit-language-error" role="alert">
            <span class="fas fa-exclamation-circle" aria-hidden="true"></span>
            <span id="edit-language-error-text"></span>
          </div>
          <div class="bg-warning"><b>Warning:</b>The information you enter below must be 100% correct before you press OK. Once you have created a language, you cannot delete it or change any of the information entered below. Contact a website administrator if you need to change or delete anything.</div>
          <div>&nbsp;</div>

          <form id="edit-language-form" action="<?= site_url('translate/add_language') ?>" method="post">

            <div class="form-group">
              <label for="edit-language-internal">Internal name of language<br/>(typically, the uncapitalized English name for the language):</label>
              <input type="text" name="internal-name" class="form-control" id="edit-language-internal">
            </div>

            <div class="form-group">
              <label for="edit-language-native">Native name of language:</label>
              <input type="text" name="native-name" class="form-control" id="edit-language-native">
            </div>
     
            <div class="form-group">
              <label for="edit-language-abbrev">ISO language code<br/>(see <a href="https://www.w3schools.com/Tags/ref_language_codes.asp" target="_blank">https://www.w3schools.com/Tags/ref_language_codes.asp</a>):</label>
              <input type="text" name="abbrev" class="form-control" id="edit-language-abbrev">
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" id="edit-language-dialog-ok" class="btn btn-primary"><?= $this->lang->line('OK_button') ?></button>
          <button type="button" class="btn btn-outline-dark" data-dismiss="modal"><?= $this->lang->line('cancel_button') ?></button>
        </div>
      </div>
    </div>
  </div>

  <script>
    $(function() {
        $('#edit-language-dialog-ok').click(function() {
            internal = $('#edit-language-internal').val().trim();
            native = $('#edit-language-native').val().trim();
            abbrev = $('#edit-language-abbrev').val().trim();
            if (internal=='' || native=='' || abbrev=='') {
                $('#edit-language-error-text').text('All fields must be filled out');
                $('#edit-language-error').show();
            }
            else
                $('#edit-language-form').submit();
        });
    });


    function create_language() {
        $('#heading-create').show();
        $('#edit-language-error').hide();
        $('#edit-language-dialog').modal('show');
    }
  </script>
