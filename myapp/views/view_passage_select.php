<script>
    function getchk() {
        var x =$("#passagetree").jstree("get_checked",null,false);
 
        if (x.length==0) {
            myalert('Passage selection', 'No passages selected');
            return;
        }
 
        var form = '<form action="<?= site_url("text/show_quiz_sel") ?>" method="post">'
                 + '<input type="hidden" name="quiz" value="<?= $quiz ?>">'
                 + '<input type="hidden" name="count" value="<?= $count ?>">';
 
        for (var i=0; i<x.length; ++i) {
            var y = $(x[i]);
            form += '<input type="hidden" name="sel[]" value="' + y.data('ref')
                + '/' + y.data('rangelow') + '/' + y.data('rangehigh')
                + '">';
        }
 
        form += '</form>';
        
        var f = $(form);
 
        $('body').append(f);
        f.submit();
    }
</script>

<h1><?= $this->lang->line('select_passages') ?></h1>
<div id="passagetree">
</div>

<p><a class="btn btn-outline-dark" href="#" onclick="getchk(); return false;"><?= $this->lang->line('start_quiz') ?></a></p>
