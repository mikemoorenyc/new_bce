function siteInit() {
  var pointerCheck = function() {
    let style = document.createElement('a').style;
    style.cssText = 'pointer-events:auto';
    return style.pointerEvents === 'auto';
  }
  App.pointerEvents = pointerCheck();
/*
  colorSet(App.URL.path.replace('/','')+'_colormode');
*/
  document.getElementById("color-mode-switcher").style.visibility = 'visible';
  if(App.pointerEvents) {
    if(!document.getElementById('liner')) {
      let line = document.createElement('div');
      line.setAttribute('id', 'liner');
      document.querySelector('body').appendChild(line);
    }
    App.windowDimensions = {
     w: document.documentElement.clientWidth,
     h: document.documentElement.clientHeight
    }
    lineSet();
    window.addEventListener("resize", _.debounce(lineSet,200));
    window.addEventListener("scroll", _.debounce(lineSet,200));
  }


  document.getElementById('color-mode-button').addEventListener('click',function(f){
    let e = f.target;


    if(e.getAttribute('data-colormode') == 'color') {
      e.setAttribute('title', 'Switch to color mode');
      e.setAttribute('data-colormode', 'bw');

      colorSetter('bw');
      document.querySelector('body').setAttribute('data-colormode', 'bw');

    } else {
      e.setAttribute('title', 'Switch to simple mode');
      e.setAttribute('data-colormode', 'color');
      colorSetter('color');
      document.querySelector('body').setAttribute('data-colormode', 'color');

    }
    return false;
  });



  /*
  document.getElementById('nav-opener').addEventListener('click',function(e){
    e.preventDefault();
    document.querySelector('html').classList.toggle('nav-opened');
  });
  */

  pageLoader();
  if(document.getElementById('ie9_mask')) {
    ieIdc();
  }
}
function colorSetter(set) {
  Cookies.set('colormode', set);
}

//DON'T TOUCH

siteInit();
