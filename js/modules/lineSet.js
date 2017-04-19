function lineSet() {
	var theight = $(window).height();
	var twidth = $(window).width();
	var hypo = Math.sqrt(theight*theight + twidth*twidth);
	var sinner = (theight/hypo);
	var degree = Math.asin( sinner )*(180/Math.PI);
	var theLine = document.getElementById('liner');
	theLine.style.width = hypo+'px';
	theLine.style.transform = 'rotate(-'+degree+'deg)'

}
