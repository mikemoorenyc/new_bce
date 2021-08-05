const switcher = document.getElementById("color-mode-switcher");
const clrBtn = document.getElementById('color-mode-button');
const fav = document.getElementById("dynamic_favicon");
clrBtn.addEventListener('click',function(f){
  let e = f.target;
  let turnOnDark = (localStorage.getItem("dark_mode") == "yes") ? "no" : "yes"; 
  colorModeSwitch(turnOnDark);
  return false; 

});
function colorModeSwitch(turnDark) {
  if(turnDark == "yes") {
    //TURN ON DARK MODE
    localStorage.setItem("dark_mode", "yes");
    if(!document.head.contains(APP.darkModeLink)) {
      document.head.appendChild(APP.darkModeLink);
    }
    
    clrBtn.setAttribute('title', 'Switch to color mode');
    clrBtn.classList.remove("in_color")
    fav.setAttribute("href", APP.faviconURL);
    
  } else {
    //TURN OFF DARK MODE
    localStorage.setItem("dark_mode", "no");
    if(document.head.contains(APP.darkModeLink)) {
      document.head.removeChild(APP.darkModeLink);
    }
    
    clrBtn.setAttribute('title', 'Switch to dark & simple mode');
    clrBtn.classList.add("in_color");
    fav.setAttribute("href", APP.faviconURL+"?color="+APP.currentColor);
  }
  
}

colorModeSwitch(localStorage.getItem("dark_mode")); 



//const s_path = App.URL.path.replace('/','')+'_colormode';
/*
function colorSet(path) {
  var s_path = App.URL.path.replace('/','')+'_colormode';
}
*/
/*
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
document.getElementById("color-mode-button").addEventListener('click',function(e){
  e.preventDefault();
  let mode = e.target.parentNode.getAttribute('data-colormode');
  let newColor = 'bw';
  if(mode === 'bw') {
    newColor = 'color';
  }
  localStorage.setItem(App.URL.path.replace('/','')+'_colormode',newColor);
  colorSet();

});
*/
