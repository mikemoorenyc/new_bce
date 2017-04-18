function siteInit() {
  colorSet();
  //$('header nav button.color-mode-switcher').css('visibility','visible');
  document.getElementById("color-mode-switcher").style.visibility = 'visible';

  //$('body').append('<div id="liner" />');
  document.querySelector('body').appendChild('<div id="liner"></div>');




  lineSet();
  window.addEventListener("resize", lineSet);
  /*
  $(window).resize(function(){

    lineSet();
  });
  */
  document.getElementByID('#nav-opener').addEventListener('click',function(e){
    e.preventDefault();
    document.querySelector('html').classList.toggle('nav-opened');
  });
  /*
  $('#nav-opener').click(function(e){
    e.preventDefault();
    $('html').toggleClass('nav-opened');
    $(this).blur();
  });
  */
  pageLoader();
}


//DON'T TOUCH

siteInit();
