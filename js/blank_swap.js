function blankSwap() {
  let blankSwapper = new IntersectionObserver(onChange);
  let toSwap = [ ...document.querySelectorAll('.blank-swap') ];
  let innerContents = [];
  toSwap.forEach((e,i) => {
    innerContents.push({
      html: e.innerHTML,
      styles: e.getAttribute('style') 
    });
    e.setAttribute('data-swap-id', i);
  });
  
  function onChange(changes) {
    changes.forEach(c => {
      let key = parseInt(c.target.getAttribute("data-swap-id")),
          el = c.target,
          h = c.target.offsetHeight;
     
      if(c.isIntersecting) {
       returnContents(c.target,key);
      } else {
        //TURN BLANK
        //Store current html
        innerContents[key]['html'] = el.innerHTML;
        //Store any current inline styles or set to blank;
        innerContents[key]['styles'] = el.getAttribute('style') || "";
        //Set height, remove padding & border to make sure it works correctly and set to invisible
        el.setAttribute('style',(
          `border-top: 0 !important;
          border-bottom:0 !important; 
          padding-top: 0 !important; 
          padding-bottom: 0 !important;
          visibility: hidden;
          pointer-events: none; 
          height: ${h}px`
        ) );
       //REMOVE CONTENT
       change.target.innerHTML = '';
      }
    })
  }
  
  function returnContents(el,key) {
    //Add back in the HTML
    el.innerHTML = theContents[key]['html'];
    //Replace styles that were added with original inline styles or nothing
    el.setAttribute('style',theContents[key]['styles']);
  }
                    
                    
  
  
  //FIGURE OUT HOW TO TURN THIS OFF!!!!
}
