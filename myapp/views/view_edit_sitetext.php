<?= form_textarea(array('id' => 'edittext',
                        'name' => 'edittext',
                        'form' => 'textform'),
                  $text) ?>
<?= form_open("site/site_text?lang=$lang", array('id' => 'textform')) ?>
<form action="http://dbi.bibleol.ezer.dk/site/site_text2?lang=da" method="post" accept-charset="utf-8" id="usrform">
<div class="buttons">
    <button class="btn btn-primary" type="submit" value="Submit"><?= $this->lang->line('OK_button') ?></button>
    <input class="btn btn-outline-dark" type="button" onclick="window.location='<?=site_url() ?>';" value="<?= $this->lang->line('cancel_button') ?>">
</div>
</form>

<script>
 $(function() {
     ckeditor = $('#edittext').ckeditor(
         {
             uiColor : '#feeebd',
	     toolbarGroups : [
	         { name: 'clipboard',   groups: [ 'clipboard', 'undo' ] },
	         { name: 'editing',     groups: [ 'find', 'selection' ] },
	         { name: 'links' },
	         { name: 'insert' },
	         { name: 'tools' },
	         { name: 'document',    groups: [ 'mode' ] },
	         { name: 'others' },
	         '/',
	         { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
	         { name: 'paragraph',   groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ] },
	         { name: 'styles' },
	         { name: 'colors' },
	         { name: 'about' }
	     ],

	     // Remove some buttons, provided by the standard plugins, which we don't need
	     removeButtons : 'Print,Preview,NewPage,Save,Flash,PageBreak,Iframe,CreateDiv,language',

	     // Make dialogs simpler.
	     removeDialogTabs : 'image:advanced;link:advanced'
         }
     );
 });

     // Show the exercise description in the text editor
     //    ckeditor.val(decoded_3et.desc);
</script>
