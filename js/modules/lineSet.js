function lineSet() {
	var theight = $(window).height();
	var twidth = $(window).width();
	var hypo = Math.sqrt(theight*theight + twidth*twidth);
	var sinner = (theight/hypo);
	var degree = Math.asin( sinner )*(180/Math.PI);
	$('#liner').width(hypo);
	$('#liner').css({
	'transform' : 'rotate(-'+degree+'deg)',
	'-ms-transform' : 'rotate(-'+degree+'deg)',
	'-webkit-transform' : 'rotate(-'+degree+'deg)',
	'-o-transform' : 'rotate(-'+degree+'deg)',
	'-moz-transform' : 'rotate(-'+degree+'deg)'
	});
}
