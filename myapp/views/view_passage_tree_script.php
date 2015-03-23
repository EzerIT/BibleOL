<script>
    $(function () {
        var univ = $("#passagetree").jstree({
            "json_data" : {
                "ajax" : {
                    "url" : "<?= site_url('text/add_universe_level') ?>",
                    "data" : function (n) {
                                 if (!n.data)
                                     return null;
                                 return {
                                     "rangelow" : n.data("rangelow"),
                                     "rangehigh" : n.data("rangehigh"),
                                     "ref" : n.data("ref"),
                                     "lev" : n.data("lev")+1,
                                     "db" : "<?= $db ?>",
                                     "prop" : "<?= $prop ?>"
                                 };
                    },
                    "error" : function( jqXHR, textStatus, errorThrown ) {
                        //console.log("jqXHR",jqXHR,"textStatus",textStatus,"errorThrown",errorThrown);
                        //TODO: Handle error
                    },
                    "success" : function(data, textStatus, jqXHR) {
                        //console.log("jqXHR",jqXHR,"textStatus",textStatus,"data",data);
                    }
                },
                "data" : [<?= $tree_data ?>]
            },
            "checkbox" : { "checked_parent_open" : true },
            "themes" : { "theme" : "classic" },
            "plugins" : [ "themes", "json_data", "checkbox" ]
        }).bind("loaded.jstree", function (event, data) {
            <?php foreach ($markedList as $marked): ?>
                $("#passagetree").jstree("check_node",'[data-ref="<?= $marked ?>"]');
            <?php endforeach; ?>
        });
    });
</script>