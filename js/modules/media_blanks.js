function mediaBlanks() {
  const sets = [ ...document.querySelectorAll('.media-item') ];
  if(localStorage.getItem('dark_mode') == "yes") {
    sets.forEach(function(e,i) {
      let img = e.querySelector('img');
      if(img && img.classList.contains('preload-image')) {
        img.setAttribute('src', img.getAttribute('data-src'));
      }
    });
    return false ; 
  }
  let observer = new IntersectionObserver(onChange);
  
  const innerContents = [];
  sets.forEach(function(e,i){
    innerContents.push({
      html: e.innerHTML,
      styles: e.getAttribute('style') || ""
    });
    e.setAttribute('data-intersector-key', i);
    let img = e.querySelector('img');
    if(img && img.classList.contains('preload-image')) {
      img.setAttribute('src', img.getAttribute('data-src'));
    }
  });

  function onChange(changes) {

   changes.forEach(change => {
     var key = parseInt(change.target.getAttribute('data-intersector-key'));
     if(change.isIntersecting) {
       returnContents(change.target)

     } else {
       innerContents[key]['html'] = change.target.innerHTML;
       innerContents[key]['styles'] = change.target.getAttribute('style') || "";
      // change.target.style.height = change.target.offsetHeight+'px';
       change.target.setAttribute('style', "color:black; visibility:hidden;border: 0 !important; padding-top: 0 !important; padding-bottom: 0 !important; height: "+change.target.offsetHeight+"px");
       change.target.classList.add('offScreen');
       change.target.innerHTML = '';
     }
   });
  }

  function returnContents(thetarget) {
    var key = parseInt(thetarget.getAttribute('data-intersector-key'));
    thetarget.innerHTML = innerContents[key]['html'];
    thetarget.style.height = "";
    thetarget.setAttribute('style',innerContents[key]['styles']);
    thetarget.classList.remove('offScreen');
    let img = thetarget.querySelector('img');

    if(!img) {
      return;
    }
    if(img.classList.contains('preload-image')) {
      img.setAttribute('src', img.getAttribute('data-src'));
    }
  }


  sets.forEach(set => observer.observe(set));

  window.addEventListener('resize', function(){
    leader();
  });

  window.addEventListener('resize', function(){
    trailer();
  });

  var leader = _.debounce(function(){
    sets.forEach(e => {
      returnContents(e);
      observer.unobserve(e)
    });
  },400,{leading:true,trailing:false});

  var trailer = _.debounce(function(){
    sets.forEach(set => observer.observe(set));
  },400,{leading:false,trailing:true});

}


//This is unused
/*
function contentFill(key) {
  let item = App.mediaContent[key];
  let imgClass = item.type;
  if(imgClass == 'episode' || imgClass === 'show') {
    imgClass = 'tv';
  }
  if(imgClass == 'album' || imgClass == 'track') {
    imgClass = 'cd';
  }
  let imgURL = item.img;
  if(!imgURL) {
    imgURL = App.URL.siteDir+'/assets/imgs/blank_'+imgClass+'.png'
  }
  if(imgURL.indexOf('https://') !== 0) {
    imgURL = App.URL.siteDir+'/image_proxy.php?url='+encodeURIComponent(imgURL);
  }

  let info = infoSwitch(item);
  return (`

    <div class="img-container">
      <div class="media-image type-${imgClass}">
        <img alt="${item.title}" src="${imgURL}" />
      </div>
    </div>
    <div class="info">
    ${info}
    </div>

    `)
}

function titleFormatter(title, classString) {
  let classes = classString
  let longTitle = '';
  if(!classString) {
    classes = '';
  }
  if(title.length >= 40) {
    longTitle = 'long-title';
  }
  if(title.length < 15) {
    longTitle = 'short-title';
  }
  return (`<h2 class="${longTitle} ${classes}">${title}</h2>`);

}
function artistNames(i) {
  if(i.length === 1) {
   return `<span class="name">${i[0]}</span>`;
  }
  let string = '';
  i.forEach(function(e,k){
    let sep = ', ';
    if(k === 0) {
      sep = '';
    }
    if(k === (i.length - 1)) {
      sep = ' & ';
    }
    string += sep+'<span class="name">'+e+'</span>';

  });
  return string;
}
function bookStatusMaker(i) {
  if(i.percent) {
   return 'Finished '+i.percent+'%';
  }
  if(i.status === 'read') {
   return 'Finished reading';
  }
  if(i.status === 'currently-reading') {
   return 'Started reading';
  }
}

function infoSwitch(item) {
  switch(item.type) {
    case 'movie':
      let title = titleFormatter(item.title);
      return(`
        <div class="extra">Watched</div>
        ${titleFormatter(item.title)}
        `)
      break;
    case 'episode':
      return (`
        <div class="extra">Watched</div>
        ${titleFormatter(item.title, 'single')}
        <div class="show-title">${item.show.title}</div>
      `);
      break;
    case 'show':
      return(`
        <div class="extra">Watched ${item.bingeCount} episodes</div>
        ${titleFormatter(item.show.title)}
      `)
      break;
    case 'album' :
      return(`
        <div class="extra">Listened to</div>
        ${titleFormatter(item.album.title)}
        <h3 class="byline">by ${artistNames(item.album.artists)}</h3>
      `)
      break;
    case 'track' :
      let extra = '<div class="extra">Listened to</div>';
      let s = ''
      if(item.listenCount > 1) {
        if(item.listenCount > 2) {
          s = 's';
        }
        extra = (`
          <div class="extra">
          ${item.listenCount} repeat${s}
          </div>
          `)
      }

      return(`
        ${extra}
        ${titleFormatter(item.title, 'single')}
        <h3 class="byline">by ${artistNames(item.album.artists)}</h3>
        `)
      break;
    case 'book':
      return(`
        <div class="extra">${bookStatusMaker(item)}</div>
        ${titleFormatter(item.title)}
        <h3 class="byline">by ${artistNames(item.authors)}</h3>

      `)
      break;

    default:
      return `${titleFormatter(item.title)}`;
  }
}
*/
