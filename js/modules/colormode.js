function colorSet() {
  var colormode =  Cookies.get('colormode',  { domain: App.URL.domain, path: App.URL.path });
  App.colormode = colormode;
  if(colormode === 'bw') {
    clearInterval(App.colorSwitch);
    App.colorSwitch = false;
    $('header nav button.color-mode-switcher').attr('title',"Switch to color mode").data('mode','bw');
    $(body).css('color','').data('colormode','bw');
  } else {
    $('header nav button.color-mode-switcher').attr('title',"Switch to simple mode").data('mode','color');
    $(body).css('color',App.colors[Math.floor((Math.random() * App.colors.length) + 0)]).data('colormode','color');
    App.colorSwitch = setInterval(colorSwitcher,2000);
  }
  return false;
}
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
$('header nav button.color-mode-switcher').click(function(e){
  e.preventDefault();
  var mode = $(this).data('mode');
  if(mode === 'bw') {
    Cookies.set('colormode', 'color', { domain: App.URL.domain, path: App.URL.path });
  } else {
    Cookies.set('colormode', 'bw', { domain: App.URL.domain, path: App.URL.path });
  }
  colorSet();
  
});

