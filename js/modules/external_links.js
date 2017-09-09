function externalLinks() {
  const links = [ ...document.querySelectorAll('a') ];
  links.forEach(function(e,i){
    let href = e.getAttribute('href');

    if(href.indexOf(App.URL.homeURL) === -1) {
      e.setAttribute('target','_blank');
    }
  });
}
