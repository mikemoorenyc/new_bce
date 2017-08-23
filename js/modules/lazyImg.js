function lazyImg() {
   if(window.IntersectionObserver) {
      createIntersections();
   } else {

      document.querySelectorAll('img.lazy-img').forEach(function(e,i){
         swapSrc(e);
      });

   }

   function createIntersections() {
      let observer = new IntersectionObserver(onChange);

      function onChange(changes) {
       changes.forEach(change => {
         swapSrc(change.target);


        observer.unobserve(change.target);
      })
      }
      const imgs = [ ...document.querySelectorAll('img.lazy-img') ];
      imgs.forEach(img => observer.observe(img));
   }

   function swapSrc(i) {
      let img = i;
    
      let loadEvent = function() {
         img.removeEventListener('load',loadEvent);
         img.classList.remove('preload-image');
      }
      img.addEventListener('load',loadEvent);
      let src = img.getAttribute('data-src'),
          srcset = img.getAttribute('data-srcset');
      img.setAttribute('srcset',srcset);
      img.setAttribute('src',src);
      img.removeAttribute('data-src');
      img.removeAttribute('data-srcset');

   }

}
