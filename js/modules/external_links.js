function externalLinks() {
  const links = [ ...document.querySelectorAll('a') ];
  links.forEach(function(e,i){
    let href = e.getAttribute('href');
    if(!href.includes(App.URL.homeURL)) {
      e.setAttribute('target','_blank');
    }
  });
}
