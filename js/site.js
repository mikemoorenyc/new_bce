function siteInit() {
  $('body').append('<div id="liner" />');
  App.colorSwitch = setInterval(function(){
    $('body').css({
      'color': App.colors[Math.floor((Math.random() * App.colors.length) + 0)]
    })
  },2000);

  lineSet();
  $(window).resize(function(){

    lineSet();
  });
}


//DON'T TOUCH

siteInit();
