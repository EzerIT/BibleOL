function adaptScale(obj, e)
{
    // Change number of decimals on y axis depending on max value
    if (obj.scale2.max < 0.05)
        obj.set('scaleDecimals', 3);
    else if (obj.scale2.max < 0.5)
        obj.set('scaleDecimals', 2);
    else if (obj.scale2.max < 5)
        obj.set('scaleDecimals', 1);
    else
        obj.set('scaleDecimals', 0);

    this.firstDraw=false; // Prevent firstdraw event from firing again. (Probably bug in RGraph.)
    RGraph.redraw();
}


function graph_bar(canvas, data, xlabels, ytitle, xtitle)
{
    new RGraph.Bar({
        id: canvas,
        data: data,
        options: {
            labels: xlabels,
            colors: ['#f00'],
            gutterLeft: 55,
            gutterBottom: 45,
            titleYaxis: ytitle,
            titleYaxisX: 12,
            titleXaxis: xtitle,
            titleXaxisY: 490,
            textAccessible: true
        }
    }).on('firstdraw', adaptScale).draw();
}

/**
 * The function that is called once per tickmark, to draw it
 *
 * @param object obj           The chart object
 * @param object data          The chart data
 * @param number x             The X coordinate
 * @param number y             The Y coordinate
 * @param number xVal          The X value
 * @param number yVal          The Y value
 * @param number xMax          The maximum X scale value
 * @param number xMax          The maximum Y scale value
 * @param string color         The color of the tickmark
 * @param string dataset_index The index of the data (which starts at zero
 * @param string data_index    The index of the data in the dataset (which starts at zero)
 */
function myTick (obj, data, x, y, xVal, yVal, xMax, yMax, color, dataset_index, data_index) {
    co = document.getElementById(obj.canvas.id).getContext('2d');
    co.strokeStyle = color;
    co.fillStyle = obj.original_colors['chart.line.colors'][dataset_index];
    co.fillRect(x-4, y-4, 8, 8);
}


function make_scatterconfig(canvas, data, xmin, xmax, postfix, ytitle, xtitle, xlabels, numxticks)
{
    return {
        id: canvas,
        data: data,
        options: {
            xmin: xmin,
            xmax: xmax,
            gutterLeft: 70,
            gutterBottom: 45,
            tickmarks: myTick,
            ymin: 0,
            ymax: 100,
            shadow: false,
            unitsPost: postfix,
            titleYaxis: ytitle,
            titleYaxisX: 12,
            titleXaxis: xtitle,
            titleXaxisY: 490,
            textAccessible: true,

            line: true,
            lineLinewidth: 2,
            lineColors: ['#f00','#0f0','#00f','#0ff','#ff0','#f0f','#000',
                         '#800','#080','#008','#08f','#8f0','#80f','#0f8','#f80','#f08',
                         '#088','#880','#808',
                         '#f88','#8f8','#88f',
                         '#ff8','#f8f','#8ff','#888'],
            labels: xlabels,
            numxticks: numxticks
        }
    }
}

function make_hbarconfig(canvas, data, labels, xtitle, xtitley)
{
    return {
        id: canvas,
        data: data,
        options: {
            labels: labels,
            gutterLeftAutosize: true,
            gutterBottom: 45,
            vmargin: 5,
            scaleZerostart: true,
            xmin: 0,
            xmax: 100,
            unitsPost: '%',
            titleXaxis: xtitle,
            titleXaxisY: xtitley,
            textAccessible: true
        }
    }
}

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
