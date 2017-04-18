function siteInit() {
  colorSet();
  $('header nav button.color-mode-switcher').css('visibility','visible');


  $('body').append('<div id="liner" />');





  lineSet();
  $(window).resize(function(){

    lineSet();
  });
  $('#nav-opener').click(function(e){
    e.preventDefault();
    $('html').toggleClass('nav-opened');
    $(this).blur();
  });
  pageLoader();
}


//DON'T TOUCH

siteInit();
