function colorSet() {
  var colormode =  Cookies.get('colormode',{domain: App.URL.domain, path: App.URL.path}),
      $switcher = document.getElementById("color-mode-switcher"),
      $body = document.querySelector('body');

  App.colormode = colormode;
  if(colormode === 'bw') {
    clearInterval(App.colorSwitch);
    App.colorSwitch = false;
    $switcher.setAttribute('title',"Switch to color mode");
    $switcher.setAttribute('data-colormode','bw');
    $body.style.color = '';
    $body.setAttribute('data-colormode','bw');
  } else {

    $switcher.setAttribute('title',"Switch to simple mode");
    $switcher.setAttribute('data-colormode','color');
    $body.style.color = App.colors[Math.floor((Math.random() * App.colors.length) + 0)]
    $body.setAttribute('data-colormode','color');
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
  document.querySelector('body').style.color = App.colors[Math.floor((Math.random() * App.colors.length) + 0)];
}
document.getElementById("color-mode-switcher").addEventListener('click',function(e){
  e.preventDefault();
  var mode = e.target.getAttribute('data-colormode');
  var newColor = 'bw';
  if(mode === 'bw') {
    newColor = 'color';
  }
  Cookies.set('colormode',newColor, {domain: App.URL.domain, path: App.URL.path, expires: 365});
  colorSet();

});
