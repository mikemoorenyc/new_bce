function siteInit() {
  colorSet();

  document.getElementById("color-mode-switcher").style.visibility = 'visible';

  var line = document.createElement('div');
  line.setAttribute('id', 'liner');
  document.querySelector('body').appendChild(line);




  lineSet();
  window.addEventListener("resize", lineSet);

  document.getElementById('nav-opener').addEventListener('click',function(e){
    e.preventDefault();
    document.querySelector('html').classList.toggle('nav-opened');
  });

  pageLoader();
}


//DON'T TOUCH

siteInit();
