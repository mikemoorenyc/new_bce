function posterSwap(img) {
  var imgs = document.querySelectorAll('.poster-img img.main-img');
  imgs.forEach(function(e,i){
    if (!e.complete) {

        e.addEventListener('load',loadEvent);
    } else {

        loadEvent(e);
    }
  });

  function loadEvent(img) {
    if(img.target) {
      img.target.removeEventListener('load',loadEvent);
    } else {
      img.removeEventListener('load',loadEvent);
    }

    var parent = img.parentNode;
    img.style.visibility = 'visible';
    var preloader = parent.querySelectorAll('img.preload')[0];
    parent.removeChild(preloader);
  }

}
