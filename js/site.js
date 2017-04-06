function siteInit() {
  colorMode();
  
  
  
  $('body').append('<div id="liner" />');
  
  
  


  lineSet();
  $(window).resize(function(){

    lineSet();
  });
}


//DON'T TOUCH

siteInit();
