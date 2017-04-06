function siteInit() {
  $('body').append('<div id="liner" />');
  $("header nav").append('<button class="mode-switcher"></button>');
  
  
  App.colorSwitch = setInterval(function(){
    if(App.colormode === 'bw') {
     clearInterval(App.colorSwitch);
     App.colorSwitch = false;
     return false;
    }
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
