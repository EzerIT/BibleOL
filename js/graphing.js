function weekno_tooltip(canvas, labels, prefix, tips)
{
    for (var i in labels) {
        var found = RGraph.text2.find({
            id: canvas,
            text: labels[i]
        });

        for (var j in found) {
            if (found[j].tag=='labels') {
                $(found[j]).attr('title',prefix + tips[i]);
                break;
            }
        }
    }
}
