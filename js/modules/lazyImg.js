function lazyImg() {
   document.querySelectorAll('img.lazy-img').forEach(function(e,i){
     var src = e.getAttribute('data-src');
     var srcset = e.getAttribute('data-srcset');
     e.setAttribute('src',src);
     e.setAttribute('srcset',srcset);
     e.style.visibility = 'visible';
   });
}
