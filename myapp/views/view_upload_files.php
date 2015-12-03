<script>
    $(function(){
            var uploader = new qq.FileUploader({
                  element: document.getElementById('file-uploader'),
                  action: '<?= site_url("upload?dir=$dir") ?>',
                  allowedExtensions: ['3et'],        
                  template: '<div class="qq-uploader">' + 
                            '<div class="qq-upload-drop-area btn"><span><?= $this->lang->line('drop_files_here') ?></span></div>' +
                            '<div class="qq-upload-button btn"><?= $this->lang->line('upload_files_button') ?></div>' +
                            '<ul class="qq-upload-list"></ul>' + 
                            '</div>',

              });
        });

</script>

<p>&nbsp;</p>
<div id="file-uploader">      
</div>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p><a class="btn btn-primary" href="<?= site_url("file_manager?dir=$dir") ?>"><?= $this->lang->line('view_folder') ?></a></p>