function colorSet() {
  var colormode =  Cookies.get('colormode',{domain: App.URL.domain, path: App.URL.path});

  App.colormode = colormode;
  if(colormode === 'bw') {
    clearInterval(App.colorSwitch);
    App.colorSwitch = false;
    document.getElementById("color-mode-switcher").setAttribute('title',"Switch to color mode");
    document.getElementById("color-mode-switcher").setAttribute('data-colormode','bw');
    document.querySelector('body').style.color = '';
    document.querySelector('body').setAttribute('data-colormode','bw');
  } else {

    document.getElementById("color-mode-switcher").setAttribute('title',"Switch to simple mode");
    document.getElementById("color-mode-switcher").setAttribute('data-colormode','color');
    document.querySelector('body').style.color = App.colors[Math.floor((Math.random() * App.colors.length) + 0)]
    document.querySelector('body').setAttribute('data-colormode','color');
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
