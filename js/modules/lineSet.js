function lineSet() {
	var tester = document.createElement('div');
	tester.innerHTML = '<div style="visibility:hidden; position:fixed; left: 0; top: 0; width: 100%; height: 100%;">asdf</div>'
	document.querySelector('body').appendChild(tester);
	var windowDimensions = tester.querySelector('div').getBoundingClientRect();
	document.querySelector('body').removeChild(tester);
	var theight = windowDimensions.height;
	var twidth = windowDimensions.width;
	var hypo = Math.sqrt(theight*theight + twidth*twidth);
	var sinner = (theight/hypo);
	var degree = Math.asin( sinner )*(180/Math.PI);
	var theLine = document.getElementById('liner');
	theLine.style.width = hypo+'px';
	theLine.style.transform = 'rotate(-'+degree+'deg)'

}
