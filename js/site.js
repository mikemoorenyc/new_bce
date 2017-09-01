function siteInit() {
  var pointerCheck = function() {
    let style = document.createElement('a').style;
    style.cssText = 'pointer-events:auto';
    return style.pointerEvents === 'auto';
  }
  App.pointerEvents = pointerCheck();

  colorSet(App.URL.path.replace('/','')+'_colormode');

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


//DON'T TOUCH

siteInit();
