function lazyImg() {
   document.querySelectorAll('img.lazy-img').forEach(function(e,i){
     var src = e.getAttribute('data-src');
     var srcset = e.getAttribute('data-srcset');
     e.setAttribute('srcset',srcset);
     e.setAttribute('src',src);
     e.removeAttribute('data-src');
     e.removeAttribute('data-srcset');
     e.style.visibility = 'visible';
   });
}
