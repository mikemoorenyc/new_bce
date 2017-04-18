function posterSwap(img) {
  
  $('.poster-img img.main-img').each(function(i,e){
    if (!this.complete) {
        $(this).load(function(){
          loadEvent(e);
        });
    } else {
        loadEvent(e);
    }
  });
  function loadEvent(img) {
    var parent = $(img).parent();
    $(img).css('visibility','visible');
    $(parent).find('img.preload').remove();
  }
  
}
