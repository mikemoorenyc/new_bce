function siteInit() {
  App.pointerEvents = pointerCheck();
  var pointerCheck = function() {
    let style = createElement('a').style;
    style.cssText = 'pointer-events:auto';
    return style.pointerEvents === 'auto';
  }
  colorSet();

  document.getElementById("color-mode-switcher").style.visibility = 'visible';
  if(App.pointerEvents) {
    if(!document.getElementById('liner')) {
      let line = document.createElement('div');
      line.setAttribute('id', 'liner');
      document.querySelector('body').appendChild(line);  
    }
    
    lineSet();
    window.addEventListener("resize", _.debounce(lineSet,400));
  }
  




  
  /*
  document.getElementById('nav-opener').addEventListener('click',function(e){
    e.preventDefault();
    document.querySelector('html').classList.toggle('nav-opened');
  });
  */

  pageLoader();
}


//DON'T TOUCH

siteInit();
