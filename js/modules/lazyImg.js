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
      let loaded = 0;
      function onChange(changes) {

       changes.forEach(change => {
        // console.log(change);
        if(change.isIntersecting) {

          swapSrc(change.target);


         observer.unobserve(change.target);
           loaded++;
           if(loaded == imgs.length) {
            observer.disconnect();
           }
        }
       })
      }
      const imgs = [ ...document.querySelectorAll('img.preload-image') ];

      imgs.forEach(img => observer.observe(img));
   }

   function swapSrc(i) {

      let parent = i.parentNode,
          full = document.createElement('img');
          full.style.visibility = 'hidden';

      function loadEvent() {
         full.removeEventListener('load',loadEvent);
         full.setAttribute('class',i.getAttribute('class'));
         full.classList.remove('preload-image');
         parent.removeChild(i);
         full.style.visibility = 'visible';
      }
      full.addEventListener('load',loadEvent);
      let  src = i.getAttribute('data-src'),
           srcset = i.getAttribute('data-srcset');
     if(srcset) {
       full.setAttribute('srcset',srcset);
     }
     full.setAttribute('src',src);
     parent.appendChild(full);

   }

}
