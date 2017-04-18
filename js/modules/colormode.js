function colorSet() {
  var colormode =  Cookies.get('colormode',{domain: App.URL.domain, path: App.URL.path});

  App.colormode = colormode;
  if(colormode === 'bw') {
    clearInterval(App.colorSwitch);
    App.colorSwitch = false;
    $('header nav button.color-mode-switcher').attr('title',"Switch to color mode").attr('data-colormode','bw');
    $('body').css('color','').attr('data-colormode','bw');
  } else {

    $('header nav button.color-mode-switcher').attr('title',"Switch to simple mode").attr('data-colormode','color');
    $('body').css('color',App.colors[Math.floor((Math.random() * App.colors.length) + 0)]).attr('data-colormode','color');
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
document.getElementById("color-mode-switcher").addEventListener('click',function(e){

  e.preventDefault();
  var mode = e.getAttribute('data-colormode');
  var newColor = 'bw';
  if(mode === 'bw') {
    newColor = 'color';
  }
  Cookies.set('colormode',newColor, {domain: App.URL.domain, path: App.URL.path, expires: 365});
  colorSet();

});
