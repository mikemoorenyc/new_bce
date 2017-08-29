//const s_path = App.URL.path.replace('/','')+'_colormode';

function colorSet(path) {
  var s_path = App.URL.path.replace('/','')+'_colormode';

  let colormode =  localStorage.getItem(s_path),
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
  var s_path = App.URL.path.replace('/','')+'_colormode';
  if(App.colormode === 'bw') {
   clearInterval(App.colorSwitch);
   App.colorSwitch = false;
   return false;
  }
  document.querySelector('body').style.color = App.colors[Math.floor((Math.random() * App.colors.length) + 0)];
}
document.getElementById("color-mode-switcher").addEventListener('click',function(e){
  e.preventDefault();
  let mode = e.target.getAttribute('data-colormode');
  let newColor = 'bw';
  if(mode === 'bw') {
    newColor = 'color';
  }
  localStorage.setItem(App.URL.path.replace('/','')+'_colormode',newColor);
  colorSet();

});
