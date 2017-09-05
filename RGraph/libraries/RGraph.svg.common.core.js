
RGraph=window.RGraph||{isRGraph:true,isRGraphSVG:true};RGraph.SVG=RGraph.SVG||{};RGraph.SVG.FX=RGraph.SVG.FX||{};(function(win,doc,undefined)
{var RG=RGraph,ua=navigator.userAgent,ma=Math;RG.SVG.REG={store:[]};RG.SVG.OR={objects:[]};RG.SVG.TRIG={};RG.SVG.TRIG.HALFPI=ma.PI*.4999;RG.SVG.TRIG.PI=RG.SVG.TRIG.HALFPI*2;RG.SVG.TRIG.TWOPI=RG.SVG.TRIG.PI*2;RG.SVG.ISIE=ua.indexOf('rident')>0;RG.SVG.ISFF=ua.indexOf('irefox')>0;RG.SVG.events=[];RG.SVG.GLOBALS={};RG.SVG.ISFF=ua.indexOf('Firefox')!=-1;RG.SVG.ISOPERA=ua.indexOf('Opera')!=-1;RG.SVG.ISCHROME=ua.indexOf('Chrome')!=-1;RG.SVG.ISSAFARI=ua.indexOf('Safari')!=-1&&!RG.ISCHROME;RG.SVG.ISWEBKIT=ua.indexOf('WebKit')!=-1;RG.SVG.ISIE=ua.indexOf('Trident')>0||navigator.userAgent.indexOf('MSIE')>0;RG.SVG.ISIE6=ua.indexOf('MSIE 6')>0;RG.SVG.ISIE7=ua.indexOf('MSIE 7')>0;RG.SVG.ISIE8=ua.indexOf('MSIE 8')>0;RG.SVG.ISIE9=ua.indexOf('MSIE 9')>0;RG.SVG.ISIE10=ua.indexOf('MSIE 10')>0;RG.SVG.ISIE11UP=ua.indexOf('MSIE')==-1&&ua.indexOf('Trident')>0;RG.SVG.ISIE10UP=RG.SVG.ISIE10||RG.SVG.ISIE11UP;RG.SVG.ISIE9UP=RG.SVG.ISIE9||RG.SVG.ISIE10UP;RG.SVG.createSVG=function(opt)
{var container=opt.container,obj=opt.object;if(container.__svg__){return container.__svg__;}
var svg=doc.createElementNS("http://www.w3.org/2000/svg","svg");svg.setAttribute('style','top: 0; left: 0; position: absolute');svg.setAttribute('width',container.offsetWidth);svg.setAttribute('height',container.offsetHeight);svg.setAttribute('version','1.1');svg.setAttributeNS("http://www.w3.org/2000/xmlns/",'xmlns','http://www.w3.org/2000/svg');svg.setAttributeNS("http://www.w3.org/2000/xmlns/","xmlns:xlink","http://www.w3.org/1999/xlink");container.appendChild(svg);container.__svg__=svg;container.style.position='relative';var numLayers=10;for(var i=1;i<=numLayers;++i){var group=RG.SVG.create({svg:svg,type:'g',attr:{className:'background'+i}});obj.layers['background'+i]=group;svg['background'+i]=group;}
var group=RG.SVG.create({svg:svg,type:'g',attr:{className:'all-elements'}});container.__svg__.all=group;return svg;};RG.SVG.createDefs=function(obj)
{if(!obj.svg.defs){var defs=RG.SVG.create({svg:obj.svg,type:'defs'});obj.svg.defs=defs;}
return defs;};RG.SVG.create=function(opt)
{var ns="http://www.w3.org/2000/svg",tag=doc.createElementNS(ns,opt.type);for(var o in opt.attr){if(typeof o==='string'){var name=o;if(o==='className'){name='class';}
if((opt.type==='a'||opt.type==='image')&&o==='xlink:href'){tag.setAttributeNS('http://www.w3.org/1999/xlink',o,String(opt.attr[o]));}else{tag.setAttribute(name,String(opt.attr[o]));}}}
for(var o in opt.style){if(typeof o==='string'){tag.style[o]=String(opt.style[o]);}}
if(opt.parent){opt.parent.appendChild(tag);}else{opt.svg.appendChild(tag);}
return tag;};RG.SVG.getMouseXY=function(e)
{if(!e.target){return;}
var el=e.target,offsetX=0,offsetY=0,x,y;if(typeof el.offsetParent!=='undefined'){do{offsetX+=el.offsetLeft;offsetY+=el.offsetTop;}while((el=el.offsetParent));}
x=e.pageX;y=e.pageY;x-=(2*(parseInt(document.body.style.borderLeftWidth)||0));y-=(2*(parseInt(document.body.style.borderTopWidth)||0));return[x,y];};RG.SVG.drawXAxis=function(obj)
{var prop=obj.properties;if(prop.xaxis){var y=obj.type==='hbar'?obj.height-prop.gutterBottom:obj.getYCoord(obj.scale.min<0&&obj.scale.max<0?obj.scale.max:(obj.scale.min>0&&obj.scale.max>0?obj.scale.min:0));var axis=RG.SVG.create({svg:obj.svg,parent:obj.svg.all,type:'path',attr:{d:'M{1} {2} L{3} {4}'.format(prop.gutterLeft,y,obj.width-prop.gutterRight,y),fill:prop.xaxisColor,stroke:prop.xaxisColor,'stroke-width':typeof prop.xaxisLinewidth==='number'?prop.xaxisLinewidth:1,'shape-rendering':'crispEdges','stroke-linecap':'square'}});if(obj.type==='hbar'){var width=obj.graphWidth/obj.data.length,x=prop.gutterLeft,startY=(obj.height-prop.gutterBottom),endY=(obj.height-prop.gutterBottom)+prop.xaxisTickmarksLength;}else{var width=obj.graphWidth/obj.data.length,x=prop.gutterLeft,startY=obj.getYCoord(0)-(prop.yaxisMin<0?prop.xaxisTickmarksLength:0),endY=obj.getYCoord(0)+prop.xaxisTickmarksLength;if(obj.scale.min<0&&obj.scale.max<=0){startY=prop.gutterTop;endY=prop.gutterTop-prop.xaxisTickmarksLength;}
if(obj.scale.min>0&&obj.scale.max>0){startY=obj.getYCoord(obj.scale.min);endY=obj.getYCoord(obj.scale.min)+prop.xaxisTickmarksLength;}}
if(prop.xaxisTickmarks){if(prop.xaxisScale){for(var i=0;i<(obj.scale.numlabels+(prop.yaxis&&prop.xaxisMin===0?0:1));++i){if(obj.type==='hbar'){var dataPoints=obj.data.length;}
x=prop.gutterLeft+((i+(prop.yaxis&&prop.xaxisMin===0?1:0))*(obj.graphWidth/obj.scale.numlabels));RG.SVG.create({svg:obj.svg,parent:obj.svg.all,type:'path',attr:{d:'M{1} {2} L{3} {4}'.format(x+0.001,startY,x,endY),stroke:prop.xaxisColor,'stroke-width':typeof prop.xaxisLinewidth==='number'?prop.xaxisLinewidth:1,'shape-rendering':"crispEdges"}});}}else{if(prop.xaxisLabelsPosition==='section'){if(obj.type==='bar'||obj.type==='waterfall'){var dataPoints=obj.data.length;}else if(obj.type==='line'){var dataPoints=obj.data[0].length;}else if(obj.type==='scatter'){var dataPoints=prop.xaxisLabels?prop.xaxisLabels.length:10;}
for(var i=0;i<dataPoints;++i){x=prop.gutterLeft+((i+1)*(obj.graphWidth/dataPoints));RG.SVG.create({svg:obj.svg,parent:obj.svg.all,type:'path',attr:{d:'M{1} {2} L{3} {4}'.format(x+0.001,startY,x,endY),stroke:prop.xaxisColor,'stroke-width':typeof prop.xaxisLinewidth==='number'?prop.xaxisLinewidth:1,'shape-rendering':"crispEdges"}});}}else if(prop.xaxisLabelsPosition==='edge'){if(typeof prop.xaxisLabelsPositionEdgeTickmarksCount==='number'){var len=prop.xaxisLabelsPositionEdgeTickmarksCount;}else{var len=obj.data&&obj.data[0]&&obj.data[0].length?obj.data[0].length:0;}
for(var i=0;i<len;++i){var gap=((obj.graphWidth)/(len-1)),x=prop.gutterLeft+((i+1)*gap);RG.SVG.create({svg:obj.svg,parent:obj.svg.all,type:'path',attr:{d:'M{1} {2} L{3} {4}'.format(x+0.001,startY,x,endY),stroke:prop.xaxisColor,'stroke-width':typeof prop.xaxisLinewidth==='number'?prop.xaxisLinewidth:1,'shape-rendering':"crispEdges"}});}}}
if(prop.yaxis===false){RG.SVG.create({svg:obj.svg,parent:obj.svg.all,type:'path',attr:{d:'M{1} {2} L{3} {4}'.format(prop.gutterLeft+0.001,startY,prop.gutterLeft,endY),stroke:obj.properties.xaxisColor,'stroke-width':typeof prop.xaxisLinewidth==='number'?prop.xaxisLinewidth:1,'shape-rendering':"crispEdges",parent:obj.svg.all,}});}}}
if(prop.xaxisScale){var segment=obj.graphWidth/prop.xaxisLabelsCount;for(var i=0;i<obj.scale.labels.length;++i){var x=prop.gutterLeft+(segment*i)+segment+prop.xaxisLabelsOffsetx;RG.SVG.text({object:obj,parent:obj.svg.all,text:obj.scale.labels[i],x:x,y:(obj.height-prop.gutterBottom)+(prop.xaxis?prop.xaxisTickmarksLength+6:10)+(prop.xaxisLinewidth||1)+prop.xaxisLabelsOffsety,halign:'center',valign:'top',font:prop.xaxisTextFont||prop.textFont,size:prop.xaxisTextSize||(typeof prop.textSize==='number'?prop.textSize+'pt':prop.textSize),bold:prop.xaxisTextBold||prop.textBold,italic:prop.xaxisTextItalic||prop.textItalic,color:prop.xaxisTextColor||prop.textColor});}
if(prop.xaxisLabelsCount>0){var y=obj.height-prop.gutterBottom+prop.xaxisLabelsOffsety+(prop.xaxis?prop.xaxisTickmarksLength+6:10),str=RG.SVG.numberFormat({object:obj,num:prop.xaxisMin.toFixed(prop.xaxisDecimals),prepend:prop.xaxisUnitsPre,append:prop.xaxisUnitsPost,point:prop.xaxisPoint,thousand:prop.xaxisThousand,formatter:prop.xaxisFormatter});var text=RG.SVG.text({object:obj,parent:obj.svg.all,text:typeof prop.xaxisFormatter==='function'?(prop.xaxisFormatter)(this,prop.xaxisMin):str,x:prop.gutterLeft+prop.xaxisLabelsOffsetx,y:y,halign:'center',valign:'top',font:prop.xaxisTextFont||prop.textFont,size:prop.xaxisTextSize||(typeof prop.textSize==='number'?prop.textSize+'pt':prop.textSize),bold:prop.xaxisTextBold||prop.textBold,italic:prop.xaxisTextItalic||prop.textItalic,color:prop.xaxisTextColor||prop.textColor});}}else{if(typeof prop.xaxisLabels==='object'&&!RG.SVG.isNull(prop.xaxisLabels)){if(prop.xaxisLabelsPosition==='section'){var segment=(obj.width-prop.gutterLeft-prop.gutterRight)/prop.xaxisLabels.length;for(var i=0;i<prop.xaxisLabels.length;++i){var x=prop.gutterLeft+(segment/2)+(i*segment);if(obj.scale.max<=0&&obj.scale.min<obj.scale.max){var y=prop.gutterTop-(RG.SVG.ISFF?5:10)-(prop.xaxisLinewidth||1)+prop.xaxisLabelsOffsety;var valign='bottom';}else{var y=obj.height-prop.gutterBottom+(RG.SVG.ISFF?5:10)+(prop.xaxisLinewidth||1)+prop.xaxisLabelsOffsety;var valign='top';}
RG.SVG.text({object:obj,parent:obj.svg.all,text:prop.xaxisLabels[i],x:x+prop.xaxisLabelsOffsetx,y:y,valign:valign,halign:'center',size:prop.xaxisTextSize||prop.textSize,italic:prop.xaxisTextItalic||prop.textItalic,font:prop.xaxisTextFont||prop.textFont,bold:prop.xaxisTextBold||prop.textBold,color:prop.xaxisTextColor||prop.textColor});}}else if(prop.xaxisLabelsPosition==='edge'){if(obj.type==='line'){var hmargin=prop.hmargin;}else{var hmargin=0;}
var segment=(obj.graphWidth-hmargin-hmargin)/(prop.xaxisLabels.length-1);for(var i=0;i<prop.xaxisLabels.length;++i){var x=prop.gutterLeft+(i*segment)+hmargin;if(obj.scale.max<=0&&obj.scale.min<0){valign='bottom';y=prop.gutterTop-(RG.SVG.ISFF?5:10)-(prop.xaxisTickmarksLength-5)-(prop.xaxisLinewidth||1)+prop.xaxisLabelsOffsety}else{valign='top';y=obj.height-prop.gutterBottom+(RG.SVG.ISFF?5:10)+(prop.xaxisTickmarksLength-5)+(prop.xaxisLinewidth||1)+prop.xaxisLabelsOffsety;}
RG.SVG.text({object:obj,parent:obj.svg.all,text:prop.xaxisLabels[i],x:x+prop.xaxisLabelsOffsetx,y:y,valign:valign,halign:'center',size:prop.xaxisTextSize||prop.textSize,italic:prop.xaxisTextItalic||prop.textItalic,font:prop.xaxisTextFont||prop.textFont,bold:prop.xaxisTextBold||prop.textBold,color:prop.xaxisTextColor||prop.textColor});}}}}};RG.SVG.drawYAxis=function(obj)
{var prop=obj.properties;if(prop.yaxis){if(obj.type==='hbar'){var x=obj.getXCoord(prop.xaxisMin>0?prop.xaxisMin:0);if(prop.xaxisMin<0&&prop.xaxisMax<=0){x=obj.getXCoord(prop.xaxisMax);}}else{var x=prop.gutterLeft;}
var axis=RG.SVG.create({svg:obj.svg,parent:obj.svg.all,type:'path',attr:{d:'M{1} {2} L{3} {4}'.format(x,prop.gutterTop,x,obj.height-prop.gutterBottom),stroke:prop.yaxisColor,fill:prop.yaxisColor,'stroke-width':typeof prop.yaxisLinewidth==='number'?prop.yaxisLinewidth:1,'shape-rendering':"crispEdges",'stroke-linecap':'square'}});if(obj.type==='hbar'){var height=(obj.graphHeight-prop.vmarginTop-prop.vmarginBottom)/prop.yaxisLabels.length,y=prop.gutterTop+prop.vmarginTop,len=prop.yaxisLabels.length,startX=obj.getXCoord(0)+(prop.xaxisMin<0?prop.yaxisTickmarksLength:0),endX=obj.getXCoord(0)-prop.yaxisTickmarksLength;if(obj.type==='hbar'&&prop.xaxisMin<0&&prop.xaxisMax<=0){startX=obj.getXCoord(prop.xaxisMax);endX=obj.getXCoord(prop.xaxisMax)+5;}
if(prop.yaxisTickmarks){for(var i=0;i<len;++i){var axis=RG.SVG.create({svg:obj.svg,parent:obj.svg.all,type:'path',attr:{d:'M{1} {2} L{3} {4}'.format(startX,y,endX,y+0.001),stroke:prop.yaxisColor,'stroke-width':typeof prop.yaxisLinewidth==='number'?prop.yaxisLinewidth:1,'shape-rendering':"crispEdges"}});y+=height;}
if(prop.xaxis===false){if(obj.type==='hbar'&&prop.xaxisMin<=0&&prop.xaxisMax<0){var startX=obj.getXCoord(prop.xaxisMax);var endX=obj.getXCoord(prop.xaxisMax)+prop.yaxisTickmarksLength;}else{var startX=obj.getXCoord(0)-prop.yaxisTickmarksLength;var endX=obj.getXCoord(0)+(prop.xaxisMin<0?prop.yaxisTickmarksLength:0);}
var axis=RG.SVG.create({svg:obj.svg,parent:obj.svg.all,type:'path',attr:{d:'M{1} {2} L{3} {4}'.format(startX,obj.height-prop.gutterBottom-parseFloat(prop.vmarginBottom),endX,obj.height-prop.gutterBottom-parseFloat(prop.vmarginBottom)-0.001),stroke:obj.properties.yaxisColor,'stroke-width':typeof prop.yaxisLinewidth==='number'?prop.yaxisLinewidth:1,'shape-rendering':"crispEdges"}});}}}else{var height=obj.graphHeight/prop.yaxisLabelsCount,y=prop.gutterTop,len=prop.yaxisLabelsCount,startX=prop.gutterLeft,endX=prop.gutterLeft-prop.yaxisTickmarksLength;if(prop.yaxisTickmarks){for(var i=0;i<len;++i){var axis=RG.SVG.create({svg:obj.svg,parent:obj.svg.all,type:'path',attr:{d:'M{1} {2} L{3} {4}'.format(startX,y,endX,y+0.001),stroke:prop.yaxisColor,'stroke-width':typeof prop.yaxisLinewidth==='number'?prop.yaxisLinewidth:1,'shape-rendering':"crispEdges"}});y+=height;}
if((prop.yaxisMin!==0||prop.xaxis===false)&&!(obj.scale.min>0&&obj.scale.max>0)){var axis=RG.SVG.create({svg:obj.svg,parent:obj.svg.all,type:'path',attr:{d:'M{1} {2} L{3} {4}'.format(prop.gutterLeft-prop.yaxisTickmarksLength,obj.height-prop.gutterBottom,prop.gutterLeft,obj.height-prop.gutterBottom-0.001),stroke:prop.yaxisColor,'stroke-width':typeof prop.yaxisLinewidth==='number'?prop.yaxisLinewidth:1,'shape-rendering':"crispEdges"}});}}}}
if(prop.yaxisScale){var segment=(obj.height-prop.gutterTop-prop.gutterBottom)/prop.yaxisLabelsCount;for(var i=0;i<obj.scale.labels.length;++i){var y=obj.height-prop.gutterBottom-(segment*i)-segment;RG.SVG.text({object:obj,parent:obj.svg.all,text:obj.scale.labels[i],x:prop.gutterLeft-7-(prop.yaxis?(prop.yaxisTickmarksLength-3):0)+prop.yaxisLabelsOffsetx,y:y+prop.yaxisLabelsOffsety,halign:'right',valign:'center',font:prop.yaxisTextFont||prop.textFont,size:prop.yaxisTextSize||(typeof prop.textSize==='number'?prop.textSize+'pt':prop.textSize),bold:prop.yaxisTextBold||prop.textBold,italic:prop.yaxisTextItalic||prop.textItalic,color:prop.yaxisTextColor||prop.textColor});}
var y=obj.height-prop.gutterBottom,str=(prop.yaxisUnitsPre+prop.yaxisMin.toFixed(prop.yaxisDecimals).replace(/\./,prop.yaxisPoint)+prop.yaxisUnitsPost);var text=RG.SVG.text({object:obj,parent:obj.svg.all,text:typeof prop.yaxisFormatter==='function'?(prop.yaxisFormatter)(this,prop.yaxisMin):str,x:prop.gutterLeft-7-(prop.yaxis?(prop.yaxisTickmarksLength-3):0)+prop.yaxisLabelsOffsetx,y:y+prop.yaxisLabelsOffsety,halign:'right',valign:'center',font:prop.yaxisTextFont||prop.textFont,size:prop.yaxisTextSize||(typeof prop.textSize==='number'?prop.textSize+'pt':prop.textSize),bold:prop.yaxisTextBold||prop.textBold,italic:prop.yaxisTextItalic||prop.textItalic,color:prop.yaxisTextColor||prop.textColor});}else if(prop.yaxisLabels&&prop.yaxisLabels.length){for(var i=0;i<prop.yaxisLabels.length;++i){var segment=(obj.graphHeight-(prop.vmarginTop||0)-(prop.vmarginBottom||0))/prop.yaxisLabels.length,y=prop.gutterTop+(prop.vmarginTop||0)+(segment*i)+(segment/2)+prop.yaxisLabelsOffsety,x=prop.gutterLeft-7-(prop.yaxisLinewidth||1)+prop.yaxisLabelsOffsetx,halign='right';if(obj.type==='hbar'&&obj.scale.min<obj.scale.max&&obj.scale.max<=0){halign='left';x=obj.width-prop.gutterRight+7+prop.yaxisLabelsOffsetx;}else if(obj.type==='hbar'&&!prop.yaxisLabelsSpecific){var segment=(obj.graphHeight-(prop.vmarginTop||0)-(prop.vmarginBottom||0))/(prop.yaxisLabels.length);y=prop.gutterTop+(prop.vmarginTop||0)+(segment*i)+(segment/2)+prop.yaxisLabelsOffsetx;}else{var segment=(obj.graphHeight-(prop.vmarginTop||0)-(prop.vmarginBottom||0))/(prop.yaxisLabels.length-1);y=obj.height-prop.gutterBottom-(segment*i)+prop.yaxisLabelsOffsetx;}
var text=RG.SVG.text({object:obj,parent:obj.svg.all,text:prop.yaxisLabels[i]?prop.yaxisLabels[i]:'',x:x,y:y,halign:halign,valign:'center',font:prop.yaxisTextFont||prop.textFont,size:prop.yaxisTextSize||(typeof prop.textSize==='number'?prop.textSize+'pt':prop.textSize),bold:prop.yaxisTextBold||prop.textBold,italic:prop.yaxisTextItalic||prop.textItalic,color:prop.yaxisTextColor||prop.textColor});}}};RG.SVG.drawBackground=function(obj)
{var prop=obj.properties;if(typeof prop.variant3dOffsetx!=='number')prop.variant3dOffsetx=0;if(typeof prop.variant3dOffsety!=='number')prop.variant3dOffsety=0;if(prop.backgroundColor){RG.SVG.create({svg:obj.svg,parent:obj.svg.all,type:'rect',attr:{x:-1+prop.variant3dOffsetx+prop.gutterLeft,y:-1-prop.variant3dOffsety+prop.gutterTop,width:parseFloat(obj.svg.getAttribute('width'))+2-prop.gutterLeft-prop.gutterRight,height:parseFloat(obj.svg.getAttribute('height'))+2-prop.gutterTop-prop.gutterBottom,fill:prop.backgroundColor}});}
if(prop.backgroundImage){var attr={'xlink:href':prop.backgroundImage,preserveAspectRatio:prop.backgroundImageAspect||'none',x:prop.gutterLeft,y:prop.gutterTop};if(prop.backgroundImageStretch){attr.x=prop.gutterLeft+prop.variant3dOffsetx;attr.y=prop.gutterTop+prop.variant3dOffsety;attr.width=obj.width-prop.gutterLeft-prop.gutterRight;attr.height=obj.height-prop.gutterTop-prop.gutterBottom;}else{if(typeof prop.backgroundImageX==='number'){attr.x=prop.backgroundImageX+prop.variant3dOffsetx;}
if(typeof prop.backgroundImageY==='number'){attr.y=prop.backgroundImageY+prop.variant3dOffsety;}
if(typeof prop.backgroundImageW==='number'){attr.width=prop.backgroundImageW;}
if(typeof prop.backgroundImageH==='number'){attr.height=prop.backgroundImageH;}}
if(prop.variant==='3d'){attr.x+=prop.variant3dOffsetx;attr.y-=prop.variant3dOffsety;}
var img=RG.SVG.create({svg:obj.svg,parent:obj.svg.all,type:'image',attr:attr,style:{opacity:typeof prop.backgroundImageOpacity==='number'?prop.backgroundImageOpacity:1}});}
if(prop.backgroundGrid){var parts=[];if(prop.backgroundGridHlines){var count=typeof prop.backgroundGridHlinesCount==='number'?prop.backgroundGridHlinesCount:(obj.type==='hbar'?(prop.yaxisLabels.length||obj.data.length||5):prop.yaxisLabelsCount);for(var i=0;i<=count;++i){parts.push('M{1} {2} L{3} {4}'.format(prop.gutterLeft+prop.variant3dOffsetx,prop.gutterTop+(obj.graphHeight/count)*i-prop.variant3dOffsety,obj.width-prop.gutterRight+prop.variant3dOffsetx,prop.gutterTop+(obj.graphHeight/count)*i-prop.variant3dOffsety));}
parts.push('M{1} {2} L{3} {4}'.format(prop.gutterLeft+prop.variant3dOffsetx,obj.height-prop.gutterBottom-prop.variant3dOffsety,obj.width-prop.gutterRight+prop.variant3dOffsetx,obj.height-prop.gutterBottom-prop.variant3dOffsety));}
if(prop.backgroundGridVlines){if(obj.type==='line'&&RG.SVG.isArray(obj.data[0])){var len=obj.data[0].length;}else if(obj.type==='hbar'){var len=prop.xaxisLabelsCount||10;}else if(obj.type==='scatter'){var len=(prop.xaxisLabels&&prop.xaxisLabels.length)||10;}else{var len=obj.data.length;}
var count=typeof prop.backgroundGridVlinesCount==='number'?prop.backgroundGridVlinesCount:len;if(prop.xaxisLabelsPosition==='edge'){count--;}
for(var i=0;i<=count;++i){parts.push('M{1} {2} L{3} {4}'.format(prop.gutterLeft+((obj.graphWidth/count)*i)+prop.variant3dOffsetx,prop.gutterTop-prop.variant3dOffsety,prop.gutterLeft+((obj.graphWidth/count)*i)+prop.variant3dOffsetx,obj.height-prop.gutterBottom-prop.variant3dOffsety));}}
if(prop.backgroundGridBorder){parts.push('M{1} {2} L{3} {4} L{5} {6} L{7} {8} z'.format(prop.gutterLeft+prop.variant3dOffsetx,prop.gutterTop-prop.variant3dOffsety,obj.width-prop.gutterRight+prop.variant3dOffsetx,prop.gutterTop-prop.variant3dOffsety,obj.width-prop.gutterRight+prop.variant3dOffsetx,obj.height-prop.gutterBottom-prop.variant3dOffsety,prop.gutterLeft+prop.variant3dOffsetx,obj.height-prop.gutterBottom-prop.variant3dOffsety));}
var dasharray;if(prop.backgroundGridDashed){dasharray=[3,5];}else if(prop.backgroundGridDotted){dasharray=[1,3];}else if(prop.backgroundGridDashArray){dasharray=prop.backgroundGridDashArray;}else{dasharray='';}
var grid=RG.SVG.create({svg:obj.svg,parent:obj.svg.all,type:'path',attr:{d:parts.join(' '),stroke:prop.backgroundGridColor,fill:'rgba(0,0,0,0)','stroke-width':prop.backgroundGridLinewidth,'shape-rendering':"crispEdges",'stroke-dasharray':dasharray}});}
RG.SVG.drawTitle(obj);};RG.SVG.isNull=function(arg)
{if(arg==null||typeof arg==='object'&&!arg){return true;}
return false;};RG.SVG.getScale=function(opt)
{var obj=opt.object,prop=obj.properties,numlabels=opt.numlabels,unitsPre=opt.unitsPre,unitsPost=opt.unitsPost,max=Number(opt.max),min=Number(opt.min),strict=opt.strict,decimals=Number(opt.decimals),point=opt.point,thousand=opt.thousand,originalMax=max,round=opt.round,scale={max:1,labels:[],values:[]},formatter=opt.formatter;if(max===0&&min===0){var max=1;for(var i=0;i<numlabels;++i){var label=((((max-min)/numlabels)*(i+1))+min).toFixed(decimals);scale.labels.push(unitsPre+label+unitsPost);scale.values.push(parseFloat(label))}}else if(max<=1&&!strict){var arr=[1,0.5,0.10,0.05,0.010,0.005,0.0010,0.0005,0.00010,0.00005,0.000010,0.000005,0.0000010,0.0000005,0.00000010,0.00000005,0.000000010,0.000000005,0.0000000010,0.0000000005,0.00000000010,0.00000000005,0.000000000010,0.000000000005,0.0000000000010,0.0000000000005],vals=[];for(var i=0;i<arr.length;++i){if(max>arr[i]){i--;break;}}
scale.max=arr[i]
scale.labels=[];scale.values=[];for(var j=0;j<numlabels;++j){var value=((((arr[i]-min)/numlabels)*(j+1))+min).toFixed(decimals);scale.values.push(value);scale.labels.push(RG.SVG.numberFormat({object:obj,num:value,prepend:unitsPre,append:unitsPost,point:prop.yaxisPoint,thousand:prop.yaxisThousand,formatter:formatter}));}}else if(!strict){max=ma.ceil(max);var interval=ma.pow(10,ma.max(1,Number(String(Number(max)-Number(min)).length-1)));var topValue=interval;while(topValue<max){topValue+=(interval/2);}
if(Number(originalMax)>Number(topValue)){topValue+=(interval/2);}
if(max<=10){topValue=(Number(originalMax)<=5?5:10);}
if(obj&&typeof(round)=='boolean'&&round){topValue=10*interval;}
scale.max=topValue;for(var i=0;i<numlabels;++i){var label=RG.SVG.numberFormat({object:obj,num:((((i+1)/numlabels)*(topValue-min))+min).toFixed(decimals),prepend:unitsPre,append:unitsPost,point:point,thousand:thousand,formatter:formatter});scale.labels.push(label);scale.values.push(((((i+1)/numlabels)*(topValue-min))+min).toFixed(decimals));}}else if(typeof max==='number'&&strict){for(var i=0;i<numlabels;++i){scale.labels.push(RG.SVG.numberFormat({object:obj,formatter:formatter,num:((((i+1)/numlabels)*(max-min))+min).toFixed(decimals),prepend:unitsPre,append:unitsPost,point:point,thousand:thousand}));scale.values.push(((((i+1)/numlabels)*(max-min))+min).toFixed(decimals));}
scale.max=max;}
scale.unitsPre=unitsPre;scale.unitsPost=unitsPost;scale.point=point;scale.decimals=decimals;scale.thousand=thousand;scale.numlabels=numlabels;scale.round=Boolean(round);scale.min=min;for(var i=0;i<scale.values.length;++i){scale.values[i]=parseFloat(scale.values[i]);}
return scale;};RG.SVG.arrayFill=RG.SVG.arrayPad=function(opt)
{var arr=opt.array,len=opt.length,value=(opt.value?opt.value:null);if(arr.length<len){for(var i=arr.length;i<len;i+=1){arr[i]=value;}}
return arr;};RG.SVG.arraySum=function(arr)
{if(typeof arr==='number'){return arr;}
if(RG.SVG.isNull(arr)){return 0;}
var i,sum,len=arr.length;for(i=0,sum=0;i<len;sum+=arr[i++]);return sum;};RG.SVG.arrayMax=function(arr)
{var max=null
if(typeof arr==='number'){return arr;}
if(RG.SVG.isNull(arr)){return 0;}
for(var i=0,len=arr.length;i<len;++i){if(typeof arr[i]==='number'){var val=arguments[1]?ma.abs(arr[i]):arr[i];if(typeof max==='number'){max=ma.max(max,val);}else{max=val;}}}
return max;};RG.SVG.arrayMin=function(arr)
{var max=null,min=null,ma=Math;if(typeof arr==='number'){return arr;}
if(RG.SVG.isNull(arr)){return 0;}
for(var i=0,len=arr.length;i<len;++i){if(typeof arr[i]==='number'){var val=arguments[1]?ma.abs(arr[i]):arr[i];if(typeof min==='number'){min=ma.min(min,val);}else{min=val;}}}
return min;};RG.SVG.arrayPad=function(arr,len)
{if(arr.length<len){var val=arguments[2]?arguments[2]:null;for(var i=arr.length;i<len;i+=1){arr[i]=val;}}
return arr;};RG.SVG.arraySum=function(arr)
{if(typeof arr==='number'){return arr;}
if(RG.SVG.isNull(arr)){return 0;}
var i,sum,len=arr.length;for(i=0,sum=0;i<len;sum+=arr[i++]);return sum;};RG.SVG.arrayLinearize=function()
{var arr=[],args=arguments
for(var i=0,len=args.length;i<len;++i){if(typeof args[i]==='object'&&args[i]){for(var j=0,len2=args[i].length;j<len2;++j){var sub=RG.SVG.arrayLinearize(args[i][j]);for(var k=0,len3=sub.length;k<len3;++k){arr.push(sub[k]);}}}else{arr.push(args[i]);}}
return arr;};RG.SVG.arrayShift=function(arr)
{var ret=[];for(var i=1,len=arr.length;i<len;++i){ret.push(arr[i]);}
return ret;};RG.SVG.arrayReverse=function(arr)
{if(!arr){return;}
var newarr=[];for(var i=arr.length-1;i>=0;i-=1){newarr.push(arr[i]);}
return newarr;};RG.SVG.arrayClone=function(obj)
{if(obj===null||typeof obj!=='object'){return obj;}
if(RG.SVG.isArray(obj)){var temp=[];for(var i=0,len=obj.length;i<len;++i){if(typeof obj[i]==='number'){temp[i]=(function(arg){return Number(arg);})(obj[i]);}else if(typeof obj[i]==='string'){temp[i]=(function(arg){return String(arg);})(obj[i]);}else if(typeof obj[i]==='function'){temp[i]=obj[i];}else{temp[i]=RG.SVG.arrayClone(obj[i]);}}}else if(typeof obj==='object'){var temp={};for(var i in obj){if(typeof i==='string'){temp[i]=obj[i];}}}
return temp;};RG.SVG.arrayInvert=function(arr)
{for(var i=0,len=arr.length;i<len;++i){arr[i]=!arr[i];}
return arr;};RG.SVG.arrayTrim=function(arr)
{var out=[],content=false;for(var i=0;i<arr.length;i++){if(arr[i]){content=true;}
if(content){out.push(arr[i]);}}
out=RG.SVG.arrayReverse(out);var out2=[],content=false;for(var i=0;i<out.length;i++){if(out[i]){content=true;}
if(content){out2.push(out[i]);}}
out2=RG.SVG.arrayReverse(out2);return out2;};RG.SVG.isArray=function(obj)
{if(obj&&obj.constructor){var pos=obj.constructor.toString().indexOf('Array');}else{return false;}
return obj!=null&&typeof pos==='number'&&pos>0&&pos<20;};RG.SVG.abs=function(value)
{if(typeof value==='string'){value=parseFloat(value)||0;}
if(typeof value==='number'){return ma.abs(value);}
if(typeof value==='object'){for(i in value){if(typeof i==='string'||typeof i==='number'||typeof i==='object'){value[i]=RG.SVG.abs(value[i]);}}
return value;}
return 0;};RG.SVG.numberFormat=function(opt)
{var obj=opt.object,prepend=opt.prepend?String(opt.prepend):'',append=opt.append?String(opt.append):'',output='',decimal_seperator=typeof opt.point==='string'?opt.point:'.',thousand_seperator=typeof opt.thousand==='string'?opt.thousand:',',num=opt.num;RegExp.$1='';if(typeof opt.formatter==='function'){return opt.formatter(obj,num);}
if(String(num).indexOf('e')>0){return String(prepend+String(num)+append);}
num=String(num);if(num.indexOf('.')>0){var tmp=num;num=num.replace(/\.(.*)/,'');decimal=tmp.replace(/(.*)\.(.*)/,'$2');}else{decimal='';}
var seperator=thousand_seperator;var foundPoint;for(i=(num.length-1),j=0;i>=0;j++,i--){var character=num.charAt(i);if(j%3==0&&j!=0){output+=seperator;}
output+=character;}
var rev=output;output='';for(i=(rev.length-1);i>=0;i--){output+=rev.charAt(i);}
if(output.indexOf('-'+thousand_seperator)==0){output='-'+output.substr(('-'+thousand_seperator).length);}
if(decimal.length){output=output+decimal_seperator+decimal;decimal='';RegExp.$1='';}
if(output.charAt(0)=='-'){output=output.replace(/-/,'');prepend='-'+prepend;}
return prepend+output+append;};RG.SVG.text=function(opt)
{var obj=opt.object,parent=opt.parent||opt.object.svg.all,size=opt.size,bold=opt.bold,font=opt.font,italic=opt.italic,halign=opt.halign,valign=opt.valign,str=opt.text,x=opt.x,y=opt.y,color=opt.color?opt.color:'black',background=opt.background||null,padding=opt.padding||0;if(halign==='right'){halign='end';}else if(halign==='center'||halign==='middle'){halign='middle';}else{halign='start';}
if(valign==='top'){valign='hanging';}else if(valign==='center'||valign==='middle'){valign='central';valign='middle';}else{valign='bottom';}
var text=RG.SVG.create({svg:obj.svg,parent:opt.parent,type:'text',attr:{fill:color,x:x,y:y,'font-size':typeof size==='number'?size+'pt':size,'font-weight':bold?900:100,'font-family':font?font:'sans-serif','font-style':italic?'italic':'normal','text-anchor':halign,'dominant-baseline':valign}});var textNode=document.createTextNode(str);text.appendChild(textNode);if(typeof background==='string'){var bbox=text.getBBox(),rect=RG.SVG.create({svg:obj.svg,parent:opt.parent,type:'rect',attr:{x:bbox.x-padding,y:bbox.y-padding,width:bbox.width+(padding*2),height:bbox.height+(padding*2),fill:background}});parent.insertBefore(rect,text);}
if(RG.SVG.ISIE&&(valign==='hanging')){text.setAttribute('y',y+(text.scrollHeight/2));}else if(RG.SVG.ISIE&&valign==='middle'){text.setAttribute('y',y+(text.scrollHeight/3));}
if(RG.SVG.ISFF){Y=y+(text.scrollHeight/3);}
return text;};RG.SVG.createUID=function()
{return'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g,function(c)
{var r=ma.random()*16|0,v=c=='x'?r:(r&0x3|0x8);return v.toString(16);});};RG.SVG.isFixed=function(svg)
{var obj=svg.parentNode,i=0;while(obj&&obj.tagName.toLowerCase()!='body'&&i<99){if(obj.style.position==='fixed'){return obj;}
obj=obj.offsetParent;}
return false;};RG.SVG.REG.set=function(name,value)
{RG.SVG.REG.store[name]=value;return value;};RG.SVG.REG.get=function(name)
{return RG.SVG.REG.store[name];};RG.SVG.trim=function(str)
{return RG.SVG.ltrim(RG.SVG.rtrim(str));};RG.SVG.ltrim=function(str)
{return str.replace(/^(\s|\0)+/,'');};RG.SVG.rtrim=function(str)
{return str.replace(/(\s|\0)+$/,'');};RG.SVG.hideTooltip=function()
{var tooltip=RG.SVG.REG.get('tooltip');if(tooltip&&tooltip.parentNode){tooltip.parentNode.removeChild(tooltip);tooltip.style.display='none';tooltip.style.visibility='hidden';RG.SVG.REG.set('tooltip',null);}
if(tooltip&&tooltip.__object__){RG.SVG.removeHighlight(tooltip.__object__);}};RG.SVG.setShadow=function(options)
{var obj=options.object,offsetx=options.offsetx||0,offsety=options.offsety||0,blur=options.blur||0,opacity=options.opacity||0,id=options.id;var filter=RG.SVG.create({svg:obj.svg,parent:obj.svg.defs,type:'filter',attr:{id:id,width:"130%",height:"130%"}});RG.SVG.create({svg:obj.svg,parent:filter,type:'feOffset',attr:{result:'offOut','in':'SourceGraphic',dx:offsetx,dy:offsety}});RG.SVG.create({svg:obj.svg,parent:filter,type:'feColorMatrix',attr:{result:'matrixOut','in':'offOut',type:'matrix',values:'0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 {1} 0'.format(opacity)}});RG.SVG.create({svg:obj.svg,parent:filter,type:'feGaussianBlur',attr:{result:'blurOut','in':'matrixOut',stdDeviation:blur}});RG.SVG.create({svg:obj.svg,parent:filter,type:'feBlend',attr:{'in':'SourceGraphic','in2':'blurOut',mode:'normal'}});};RG.SVG.sequentialIndexToGrouped=function(index,data)
{var group=0,grouped_index=0;while(--index>=0){if(RG.SVG.isNull(data[group])){group++;grouped_index=0;continue;}
if(typeof data[group]=='number'){group++
grouped_index=0;continue;}
grouped_index++;if(grouped_index>=data[group].length){group++;grouped_index=0;}}
return[group,grouped_index];};RG.SVG.TRIG.toCartesian=function(options)
{return{x:options.cx+(options.r*ma.cos(options.angle)),y:options.cy+(options.r*ma.sin(options.angle))};};RG.SVG.TRIG.getArcPath=function(options)
{options.start-=1.57;options.end-=1.57;var start=RG.SVG.TRIG.toCartesian({cx:options.cx,cy:options.cy,r:options.r,angle:options.start});var end=RG.SVG.TRIG.toCartesian({cx:options.cx,cy:options.cy,r:options.r,angle:options.end});var diff=options.end-options.start;var largeArc='0';var sweep='0';if(options.anticlockwise&&diff>3.14){largeArc='0';sweep='0';}else if(options.anticlockwise&&diff<=3.14){largeArc='1';sweep='0';}else if(!options.anticlockwise&&diff>3.14){largeArc='1';sweep='1';}else if(!options.anticlockwise&&diff<=3.14){largeArc='0';sweep='1';}
if(options.start>options.end&&options.anticlockwise&&diff<=3.14){largeArc='0';sweep='0';}
if(options.start>options.end&&options.anticlockwise&&diff>3.14){largeArc='1';sweep='1';}
if(typeof options.moveto==='boolean'&&options.moveto===false){var d=["A",options.r,options.r,0,largeArc,sweep,end.x,end.y];}else{var d=["M",start.x,start.y,"A",options.r,options.r,0,largeArc,sweep,end.x,end.y];}
if(options.array===true){return d;}else{return d.join(" ");}};RG.SVG.TRIG.getArcPath2=function(options)
{options.start-=1.57;options.end-=1.57;var start=RG.SVG.TRIG.toCartesian({cx:options.cx,cy:options.cy,r:options.r,angle:options.start});var end=RG.SVG.TRIG.toCartesian({cx:options.cx,cy:options.cy,r:options.r,angle:options.end});var diff=ma.abs(options.end-options.start);var largeArc='0';var sweep='0';if(!options.anticlockwise){if(diff>RG.SVG.TRIG.PI){largeArc='1';sweep='1';}else{largeArc='0';sweep='1';}}else{if(diff>RG.SVG.TRIG.PI){largeArc='1';sweep='0';}else{largeArc='0';sweep='0';}}
if(typeof options.lineto==='boolean'&&options.lineto===false){var d=["M",start.x,start.y,"A",options.r,options.r,0,largeArc,sweep,end.x,end.y];}else{var d=["M",options.cx,options.cy,"L",start.x,start.y,"A",options.r,options.r,0,largeArc,sweep,end.x,end.y];}
if(options.array===true){return d;}else{return d.join(" ");}};RG.SVG.TRIG.getArcPath3=function(options)
{options.start-=1.57;options.end-=1.57;var start=RG.SVG.TRIG.toCartesian({cx:options.cx,cy:options.cy,r:options.r,angle:options.start});var end=RG.SVG.TRIG.toCartesian({cx:options.cx,cy:options.cy,r:options.r,angle:options.end});var diff=ma.abs(options.end-options.start);var largeArc='0';var sweep='0';if(!options.anticlockwise){if(diff>RG.SVG.TRIG.PI){largeArc='1';sweep='1';}else{largeArc='0';sweep='1';}}else{if(diff>RG.SVG.TRIG.PI){largeArc='1';sweep='0';}else{largeArc='0';sweep='0';}}
if(typeof options.lineto==='boolean'&&options.lineto===false){var d=["M",start.x,start.y,"A",options.r,options.r,0,largeArc,sweep,end.x,end.y];}else{var d=["L",start.x,start.y,"A",options.r,options.r,0,largeArc,sweep,end.x,end.y];}
if(options.array===true){return d;}else{return d.join(" ");}};RG.SVG.TRIG.getRadiusEndPoint=function(opt)
{if(arguments.length===1){var angle=opt.angle,r=opt.r;}else if(arguments.length===4){var angle=arguments[0],r=arguments[1];}
var x=ma.cos(angle)*r,y=ma.sin(angle)*r;return[x,y];};RG.SVG.drawTitle=function(obj)
{var prop=obj.properties,valign='bottom';if(obj.type==='pie'){if(RG.SVG.isNull(prop.titleX)){prop.titleX=obj.centerx;prop.titleSubtitleX=obj.centerx;}
if(RG.SVG.isNull(prop.titleY)){prop.titleY=obj.centery-obj.radius-10;}}
if(obj.scale&&obj.scale.max<=0&&obj.scale.min<0&&typeof prop.titleY!=='number'&&obj.type!=='hbar'){prop.titleY=obj.height-prop.gutterBottom+10;var positionBottom=true;valign='top';}else if(typeof prop.titleY!=='number'){var positionBottom=false;prop.titleY=prop.gutterTop-10;valign='bottom';if(!RG.SVG.isNull(prop.key)){prop.titleY-=(2*(prop.keyTextSize||prop.textSize));}}
if(prop.titleSubtitle&&typeof prop.titleSubtitleY!=='number'&&!positionBottom){prop.titleY=prop.titleY-(prop.titleSubtitleSize*1.5);}
prop.titleSubTitleSize=prop.titleSubTitleSize||prop.textSize;prop.titleSubtitleY=prop.titleSubtitleY||prop.titleY+18;if(positionBottom&&typeof prop.titleSubtitleY!=='number'){prop.titleSubtitleY=prop.titleY+26;}
if(prop.title){RG.SVG.text({object:obj,svg:obj.svg,parent:obj.svg.all,text:prop.title.toString(),size:prop.titleSize||(prop.textSize+4)||16,x:typeof prop.titleX==='number'?prop.titleX+(prop.variant3dOffsetx||0):prop.gutterLeft+(obj.graphWidth/2)+(prop.variant3dOffsetx||0),y:prop.titleY+(prop.variant3dOffsety||0),halign:prop.titleHalign||'center',valign:prop.titleValign||valign,color:prop.titleColor||prop.textColor||'black',bold:prop.titleBold||false,italic:prop.titleItalic||false,font:prop.titleFont||prop.textFont||'Arial'});}
if(prop.titleSubtitle){RG.SVG.text({object:obj,svg:obj.svg,parent:obj.svg.all,text:prop.titleSubtitle,size:prop.titleSubtitleSize,x:typeof prop.titleSubtitleX==='number'?prop.titleSubtitleX:prop.gutterLeft+(obj.graphWidth/2)+(prop.variant3dOffsetx||0),y:prop.titleSubtitleY+(prop.variant3dOffsety||0),halign:prop.titleSubtitleHalign||'center',valign:prop.titleSubtitleValign||valign,color:prop.titleSubtitleColor||prop.textColor||'#aaa',bold:prop.titleSubtitleBold||false,italic:prop.titleSubtitleItalic||false,font:prop.titleSubtitleFont||prop.textFont||'Arial'});}};RG.SVG.trim=function(str)
{return RG.SVG.ltrim(RG.SVG.rtrim(str));};RG.SVG.ltrim=function(str)
{return String(str).replace(/^(\s|\0)+/,'');};RG.SVG.rtrim=function(str)
{return String(str).replace(/(\s|\0)+$/,'');};RG.SVG.parseColorLinear=function(opt)
{var obj=opt.object,color=opt.color;if(!color||typeof color!=='string'){return color;}
if(color.match(/^gradient\((.*)\)$/i)){var parts=RegExp.$1.split(':'),diff=1/(parts.length-1);if(opt&&opt.direction&&opt.direction==='horizontal'){var grad=RG.SVG.create({type:'linearGradient',parent:obj.svg.defs,attr:{id:'RGraph-linear-gradient-'+obj.uid+'-'+obj.gradientCounter,x1:opt.start||0,x2:opt.end||'100%',y1:0,y2:0,gradientUnits:"userSpaceOnUse"}});}else{var grad=RG.SVG.create({type:'linearGradient',parent:obj.svg.defs,attr:{id:'RGraph-linear-gradient-'+obj.uid+'-'+obj.gradientCounter,x1:0,x2:0,y1:opt.start||0,y2:opt.end||'100%',gradientUnits:"userSpaceOnUse"}});}
var stop=RG.SVG.create({type:'stop',parent:grad,attr:{offset:'0%','stop-color':RG.SVG.trim(parts[0])}});for(var j=1,len=parts.length;j<len;++j){RG.SVG.create({type:'stop',parent:grad,attr:{offset:(j*diff*100)+'%','stop-color':RG.SVG.trim(parts[j])}});}}
color=grad?'url(#RGraph-linear-gradient-'+obj.uid+'-'+(obj.gradientCounter++)+')':color;return color;};RG.SVG.parseColorRadial=function(opt)
{var obj=opt.object,color=opt.color;if(!color||typeof color!=='string'){return color;}
if(color.match(/^gradient\((.*)\)$/i)){var parts=RegExp.$1.split(':'),diff=1/(parts.length-1);var grad=RG.SVG.create({type:'radialGradient',parent:obj.svg.defs,attr:{id:'RGraph-radial-gradient-'+obj.uid+'-'+obj.gradientCounter,gradientUnits:opt.gradientUnits||'userSpaceOnUse',cx:opt.cx||obj.centerx,cy:opt.cy||obj.centery,fx:opt.fx||obj.centerx,fy:opt.fy||obj.centery,r:opt.r||obj.radius}});var stop=RG.SVG.create({type:'stop',parent:grad,attr:{offset:'0%','stop-color':RG.SVG.trim(parts[0])}});for(var j=1,len=parts.length;j<len;++j){RG.SVG.create({type:'stop',parent:grad,attr:{offset:(j*diff*100)+'%','stop-color':RG.SVG.trim(parts[j])}});}}
color=grad?'url(#RGraph-radial-gradient-'+obj.uid+'-'+(obj.gradientCounter++)+')':color;return color;};RG.SVG.resetColorsToOriginalValues=function(opt)
{var obj=opt.object;if(obj.originalColors){for(var j in obj.originalColors){if(typeof j==='string'){obj.properties[j]=RG.SVG.arrayClone(obj.originalColors[j]);}}}
if(typeof obj.resetColorsToOriginalValues==='function'){obj.resetColorsToOriginalValues();}
obj.originalColors={};obj.colorsParsed=false;obj.gradientCounter=1;};RG.SVG.clear=function(svg)
{for(var i=1;i<=100;++i){if(svg['background'+i]){while(svg['background'+i].lastChild){svg['background'+i].removeChild(svg['background'+i].lastChild);}}else{break;}}
while(svg.all.lastChild){svg.all.removeChild(svg.all.lastChild);}};RG.SVG.addCustomEventListener=function(obj,name,func)
{if(typeof RG.SVG.events[obj.uid]==='undefined'){RG.SVG.events[obj.uid]=[];}
if(name.substr(0,2)!=='on'){name='on'+name;}
RG.SVG.events[obj.uid].push({object:obj,event:name,func:func});return RG.SVG.events[obj.uid].length-1;};RG.SVG.fireCustomEvent=function(obj,name)
{if(obj&&obj.isRGraph){var uid=obj.uid;if(typeof uid==='string'&&typeof RG.SVG.events==='object'&&typeof RG.SVG.events[uid]==='object'&&RG.SVG.events[uid].length>0){for(var j=0,len=RG.SVG.events[uid].length;j<len;++j){if(RG.SVG.events[uid][j]&&RG.SVG.events[uid][j].event===name){RG.SVG.events[uid][j].func(obj);}}}}};RG.SVG.removeAllCustomEventListeners=function()
{var uid=arguments[0];if(uid&&RG.SVG.events[uid]){RG.SVG.events[uid]={};}else{RG.SVG.events=[];}};RG.SVG.removeCustomEventListener=function(obj,i)
{if(typeof RG.SVG.events==='object'&&typeof RG.SVG.events[obj.uid]==='object'&&typeof RG.SVG.events[obj.uid][i]==='object'){RG.SVG.events[obj.uid][i]=null;}};RG.SVG.removeHighlight=function(obj)
{var highlight=RG.SVG.REG.get('highlight');if(highlight&&RG.SVG.isArray(highlight)&&highlight.length){for(var i=0,len=highlight.length;i<len;++i){if(highlight[i].parentNode){highlight[i].parentNode.removeChild(highlight[i]);}}}else if(highlight&&highlight.parentNode){if(obj.type==='scatter'){highlight.setAttribute('fill','transparent');}else{highlight.parentNode.removeChild(highlight);}}};RG.SVG.redraw=function()
{if(arguments.length===1){var svg=arguments[0];RG.SVG.clear(svg);var objects=RG.SVG.OR.get('id:'+svg.parentNode.id);for(var i=0,len=objects.length;i<len;++i){RG.SVG.resetColorsToOriginalValues({object:objects[i]});objects[i].draw();}}else{var tags=RG.SVG.OR.tags();for(var i in tags){RG.SVG.redraw(tags[i]);}}};RG.SVG.parseDate=function(str)
{var d=new Date();var defaults={seconds:'00',minutes:'00',hours:'00',date:d.getDate(),month:d.getMonth()+1,year:d.getFullYear()};var months=['january','february','march','april','may','june','july','august','september','october','november','december'],months_regex=months.join('|');for(var i=0;i<months.length;++i){months[months[i]]=i;months[months[i].substring(0,3)]=i;months_regex=months_regex+'|'+months[i].substring(0,3);}
var sep='[-./_=+~#:;,]+';var tokens=str.split(/ +/);for(var i=0,len=tokens.length;i<len;++i){if(tokens[i]){if(tokens[i].match(/^\d\d\d\d$/)){defaults.year=tokens[i];}
var res=isMonth(tokens[i]);if(typeof res==='number'){defaults.month=res+1;}
if(tokens[i].match(/^\d?\d(?:st|nd|rd|th)?$/)){defaults.date=parseInt(tokens[i]);}
if(tokens[i].match(/^(\d\d):(\d\d)(?:(\d\d))?$/)){defaults.hours=parseInt(RegExp.$1);defaults.minutes=parseInt(RegExp.$2);if(RegExp.$3){defaults.seconds=parseInt(RegExp.$3);}}
if(tokens[i].match(new RegExp('^(\\d\\d\\d\\d)'+sep+'(\\d\\d)'+sep+'(\\d\\d)$','i'))){defaults.date=parseInt(RegExp.$3);defaults.month=parseInt(RegExp.$2);defaults.year=parseInt(RegExp.$1);}
if(tokens[i].match(new RegExp('^(\\d\\d)'+sep+'(\\d\\d)'+sep+'(\\d\\d\\d\\d)$','i'))){defaults.date=parseInt(RegExp.$1);defaults.month=parseInt(RegExp.$2);defaults.year=parseInt(RegExp.$3);}}}
str='{1}/{2}/{3} {4}:{5}:{6}'.format(defaults.year,String(defaults.month).length===1?'0'+(defaults.month):defaults.month,String(defaults.date).length===1?'0'+(defaults.date):defaults.date,String(defaults.hours).length===1?'0'+(defaults.hours):defaults.hours,String(defaults.minutes).length===1?'0'+(defaults.minutes):defaults.minutes,String(defaults.seconds).length===1?'0'+(defaults.seconds):defaults.seconds);return Date.parse(str);function isMonth(str)
{var res=str.toLowerCase().match(months_regex);return res?months[res[0]]:false;}};RG.SVG.OR.add=function(obj)
{RG.SVG.OR.objects.push(obj);return obj;};RG.SVG.OR.get=function()
{if(typeof arguments[0]==='string'&&arguments[0].substr(0,3).toLowerCase()==='id:'){var ret=[];for(var i=0;i<RG.SVG.OR.objects.length;++i){if(RG.SVG.OR.objects[i].id===arguments[0].substr(3)){ret.push(RG.SVG.OR.objects[i]);}}
return ret;}
if(typeof arguments[0]==='string'&&arguments[0].substr(0,4).toLowerCase()==='type'){var ret=[];for(var i=0;i<RG.SVG.OR.objects.length;++i){if(RG.SVG.OR.objects[i].type===arguments[0].substr(5)){ret.push(RG.SVG.OR.objects[i]);}}
return ret;}
if(typeof arguments[0]==='string'&&arguments[0].substr(0,3).toLowerCase()==='uid'){var ret=[];for(var i=0;i<RG.SVG.OR.objects.length;++i){if(RG.SVG.OR.objects[i].uid===arguments[0].substr(4)){ret.push(RG.SVG.OR.objects[i]);}}
return ret;}
return RG.SVG.OR.objects;};RG.SVG.OR.tags=function()
{var tags=[];for(var i=0;i<RG.SVG.OR.objects.length;++i){if(!tags[RG.SVG.OR.objects[i].svg.parentNode.id]){tags[RG.SVG.OR.objects[i].svg.parentNode.id]=RG.SVG.OR.objects[i].svg;}}
return tags;};RG.SVG.getSVGXY=function(svg)
{var x=0,y=0,el=svg.parentNode;do{x+=el.offsetLeft;y+=el.offsetTop;if(el.tagName.toLowerCase()=='table'&&(RG.SVG.ISCHROME||RG.SVG.ISSAFARI)){x+=parseInt(el.border)||0;y+=parseInt(el.border)||0;}
el=el.offsetParent;}while(el&&el.tagName&&el.tagName.toLowerCase()!='body');var paddingLeft=svg.style.paddingLeft?parseInt(svg.style.paddingLeft):0,paddingTop=svg.style.paddingTop?parseInt(svg.style.paddingTop):0,borderLeft=svg.style.borderLeftWidth?parseInt(svg.style.borderLeftWidth):0,borderTop=svg.style.borderTopWidth?parseInt(svg.style.borderTopWidth):0;if(navigator.userAgent.indexOf('Firefox')>0){x+=parseInt(document.body.style.borderLeftWidth)||0;y+=parseInt(document.body.style.borderTopWidth)||0;}
return[x+paddingLeft+borderLeft,y+paddingTop+borderTop];};RG.SVG.FX.update=function(func)
{win.requestAnimationFrame=win.requestAnimationFrame||win.webkitRequestAnimationFrame||win.msRequestAnimationFrame||win.mozRequestAnimationFrame||(function(func){setTimeout(func,16.666);});win.requestAnimationFrame(func);};RG.SVG.FX.getEasingMultiplier=function(frames,frame)
{var multiplier=ma.pow(ma.sin((frame/frames)*RG.SVG.TRIG.HALFPI),3);return multiplier;};RG.SVG.measureText=function(opt)
{var text=opt.text||'',bold=opt.bold||false,font=opt.font||'Arial',size=opt.size||10,str=text+':'+bold+':'+font+':'+size;if(typeof RG.SVG.measuretext_cache==='undefined'){RG.SVG.measuretext_cache=[];}
if(typeof RG.SVG.measuretext_cache=='object'&&RG.SVG.measuretext_cache[str]){return RG.SVG.measuretext_cache[str];}
if(!RG.SVG.measuretext_cache['text-span']){var span=document.createElement('SPAN');span.style.position='absolute';span.style.padding=0;span.style.display='inline';span.style.top='-200px';span.style.left='-200px';span.style.lineHeight='1em';document.body.appendChild(span);RG.SVG.measuretext_cache['text-span']=span;}else if(RG.SVG.measuretext_cache['text-span']){var span=RG.SVG.measuretext_cache['text-span'];}
span.innerHTML=text.replace(/\r\n/g,'<br />');span.style.fontFamily=font;span.style.fontWeight=bold?'bold':'normal';span.style.fontSize=size+'pt';var sizes=[span.offsetWidth,span.offsetHeight];RG.SVG.measuretext_cache[str]=sizes;return sizes;};RG.SVG.stringsToNumbers=function(str)
{var sep=arguments[1]||',';if(typeof str==='number'){return str;}
if(typeof str==='string'){if(str.indexOf(sep)!=-1){str=str.split(sep);}else{str=parseFloat(str);}}
if(typeof str==='object'){for(var i=0,len=str.length;i<len;i+=1){str[i]=parseFloat(str[i]);}}
return str;};RG.SVG.getAdjustedNumber=function(opt)
{var value=opt.value,prop=opt.prop;if(typeof prop==='string'&&match(/^(\+|-)([0-9.]+)/)){if(RegExp.$1==='+'){value+=parseFloat(RegExp.$2);}else if(RegExp.$1==='-'){value-=parseFloat(RegExp.$2);}}
return value;};RG.SVG.attribution=function(){return;};RG.SVG.parseGradient=function(str)
{};RG.SVG.random=function(opt)
{var min=opt.min,max=opt.max,dp=opt.dp||opt.decimals||0,r=ma.random();return Number((((max-min)*r)+min).toFixed(dp));};RG.SVG.arrayRand=RG.SVG.arrayRandom=RG.SVG.random.array=function(opt)
{var num=opt.num,min=opt.min,max=opt.max,dp=opt.dp||opt.decimals||0;for(var i=0,arr=[];i<num;i+=1){arr.push(RG.SVG.random({min:min,max:max,dp:dp}));}
return arr;};RG.SVG.commonSetter=function(opt)
{var obj=opt.object,name=opt.name,value=opt.value;if(name==='tooltipsEvent'&&value!=='click'&&value!=='mousemove'){value='click';}
return{name:name,value:value};};RG.SVG.log=function(opt)
{var num=opt.num,base=opt.base;return ma.log(num)/(base?ma.log(base):1);};RG.SVG.donut=function(opt)
{var arcPath1=RG.SVG.TRIG.getArcPath3({cx:opt.cx,cy:opt.cy,r:opt.outerRadius,start:0,end:RG.SVG.TRIG.TWOPI,anticlockwise:false,lineto:false});var arcPath2=RG.SVG.TRIG.getArcPath3({cx:opt.cx,cy:opt.cy,r:opt.innerRadius,start:RG.SVG.TRIG.TWOPI,end:0,anticlockwise:true,lineto:false});var path=RG.SVG.create({svg:opt.svg,type:'path',attr:{d:arcPath1+arcPath2,stroke:opt.stroke,fill:opt.fill}});return path;};RG.SVG.getGlobals=function(obj)
{var prop=obj.properties;for(i in RG.SVG.GLOBALS){if(typeof i==='string'){prop[i]=RG.SVG.arrayClone(RG.SVG.GLOBALS[i]);}}};if(typeof RG.SVG.tooltip!=='function'){RG.SVG.tooltip=function()
{$a('The tooltip library has not been included!');};}})(window,document);window.$p=function(obj)
{var indent=(arguments[2]?arguments[2]:'    ');var str='';var counter=typeof arguments[3]=='number'?arguments[3]:0;if(counter>=5){return'';}
switch(typeof obj){case'string':str+=obj+' ('+(typeof obj)+', '+obj.length+')';break;case'number':str+=obj+' ('+(typeof obj)+')';break;case'boolean':str+=obj+' ('+(typeof obj)+')';break;case'function':str+='function () {}';break;case'undefined':str+='undefined';break;case'null':str+='null';break;case'object':if(RGraph.SVG.isNull(obj)){str+=indent+'null\n';}else{str+=indent+'Object {'+'\n'
for(j in obj){str+=indent+'    '+j+' => '+window.$p(obj[j],true,indent+'    ',counter+1)+'\n';}
str+=indent+'}';}
break;default:str+='Unknown type: '+typeof obj+'';break;}
if(!arguments[1]){alert(str);}
return str;};window.$a=function(v)
{alert(v);};window.$cl=function(v)
{return console.log(v);};if(!String.prototype.format){String.prototype.format=function()
{var args=arguments;return this.replace(/{(\d+)}/g,function(str,idx)
{return typeof args[idx-1]!=='undefined'?args[idx-1]:str;});};}