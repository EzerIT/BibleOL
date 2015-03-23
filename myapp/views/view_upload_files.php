<script>
    $(function(){
            var uploader = new qq.FileUploader({
                  element: document.getElementById('file-uploader'),
                  action: '<?= site_url("valums_uploader.php?dir=$dir") ?>',
                  allowedExtensions: ['3et'],        
                  template: '<div class="qq-uploader">' + 
                            '<div class="qq-upload-drop-area ui-corner-all"><span>Drop files here to upload</span></div>' +
                            '<div class="qq-upload-button ui-corner-all">Upload files</div>' +
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
<p><a class="makebutton" href="<?= site_url("file_manager?dir=$dir") ?>">View folder</a></p>