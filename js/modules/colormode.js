function colorMode() {
  $("header nav").append('<button class="color-mode-switcher"></button>');
  function colorSwitcher() {
    if(App.colormode === 'bw') {
     clearInterval(App.colorSwitch);
     App.colorSwitch = false;
     return false;
    }
    $('body').css({
      'color': App.colors[Math.floor((Math.random() * App.colors.length) + 0)]
    })
  }
  function buttonSet() {
   if(App.colormode === 'bw') {
    $('header nav button.color-mode-switcher').text('Color Mode').attr('title',"This site looks boring.").data('mode','bw');   
   } else {
     $('header nav button.color-mode-switcher').text('Simple Mode').attr('title',"This site hurts my eyes.").data('mode','color');
     
   }
  }
  if(App.colormode !== 'bw') {
    App.colorSwitch = setInterval(colorSwitcher,2000);
  }
  $('header nav button.color-mode-switcher').click(function(e){
    e.preventDefault();
    var mode = $(this).data('mode');
    if(mode == 'bw') {
      App.colormode = 'color';
      buttonSet();
      App.colorSwitch = setInterval(colorSwitcher,2000);  
    } else {
      App.colormode = 'bw';
      buttonSet();
    }
  //  document.cookie = "colormode=John Smith; expires=Thu, 18 Dec 2013 12:00:00 UTC; path=/"; 
  });
  buttonSet();

}
