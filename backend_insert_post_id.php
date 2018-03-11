<?php
function add_insert_post_id_button() {
  ?>
  <button id="add-post-id" class="button " type="button"  >Insert Post ID</button>
  <?php
}
add_action('media_buttons', 'add_insert_post_id_button');
add_action('admin_footer-post.php', 'insert_post_id_script');
add_action('admin_footer-post-new.php', 'insert_post_id_script');

function insert_post_id_script() {
  $all_posts = get_posts(
    array(
      'post_type' => ['post','page','project'],
      'posts_per_page'   => -1,
    )
  );

  $p_scripts = array_map(function($e){
    $item = [];
    $item['id'] = $e->ID;
    $item['title'] = $e->post_title;
    $item['type'] = $e->post_type;
    return $item;
  },$all_posts);




?>
<script>
var allPosts = <?php echo json_encode($p_scripts);?>;
var scrollPos = 0;

function assignID(e) {
  var id = e.getAttribute('data-id');
  if(!id){return false};
  var tarea = document.querySelector('textarea#content'),
      cStart = tarea.selectionStart,
      cEnd = tarea.selectionEnd,
      content = tarea.value,
      beforeInsert = content.substring(0,cStart),
      afterInsert = content.substring(cEnd);

    tarea.value = beforeInsert+id+afterInsert;
    tarea.focus();
    tarea.selectionStart = cStart+id.length;
    tarea.selectionEnd = cStart+id.length;

    closeModal();

}

function closeModal() {
  var modal = document.querySelector('.page-id-modal-backdrop');
  modal.parentNode.removeChild(modal);
  document.querySelector('body').classList.remove('modal-open');
  window.scrollTo(0, scrollPos);
}

function domready(fn) {
  if (document.attachEvent ? document.readyState === "complete" : document.readyState !== "loading"){
    fn();
  } else {
    document.addEventListener('DOMContentLoaded', fn);
  }
}

domready(function(){
  document.querySelector('button#add-post-id').addEventListener('click',createModal);

  function createModal(e) {
    e.preventDefault();
    scrollPos = window.pageYOffset || document.documentElement.scrollTop;
    var theBody = document.querySelector('body')
    theBody.classList.add('modal-open');
    var modalContainer = document.createElement('div');
    modalContainer.setAttribute('class', 'page-id-modal-backdrop');
    modalContainer.innerHTML = (
      `
      <div  class="page-id-modal-backdrop">
        <div id="page-id-modal">
          <div class="modal-search">
            <div class="inner">
              <input id="page-search-bar" placeholder="Search a title..." type="text" class="regular-text"/>
            </div>
          </div>
          <div class="modal-body items">


          </div>
          <div class="modal-body search-items" style="display:none;"></div>
          <div class="modal-footer">
            <button class="button cancel" type="button">Cancel</button>
          </div>
        </div>

      </div>
      `
    );
    theBody.appendChild(modalContainer);
    var sections = ['post','project','page'];
    var baseItems = document.querySelector('#page-id-modal .modal-body.items');
    sections.forEach(function(e,i){
      var type = e;
      var sectionClass = '';
      if(i === 0) {
         sectionClass = 'opened';
      }
      var fItems = allPosts.filter(function(e,i){
        return e.type === type;
      });
      if(!fItems.length){return}
      var divs = fItems.map(function(e,i){
        return (
          `<div class="post-item" onClick="assignID(this)" data-id=${e.id}>${e.title}</div>`
        )
      });
      var section = document.createElement('div');

      section.innerHTML = (
        `
        <h2 class="section-header ${sectionClass}"><span class="text">${type+'s'}</span> <span class="visibility-toggle"></span> </h2>
      <div class="post-items ${sectionClass}">  ${divs.join("")} </div>
        `
      );
      baseItems.appendChild(section);
    });

    document.querySelector('#page-id-modal button.cancel').addEventListener('click',function(e){
      e.preventDefault();
      closeModal();
    });
    var headers = document.querySelectorAll("#page-id-modal h2.section-header");
    Array.prototype.forEach.call(headers, function(e, i){
      e.addEventListener('click',function(e){
        this.classList.toggle('opened');
        this.nextElementSibling.classList.toggle('opened');
        this.blur();
        return false;
      });
    });

    document.querySelector('#page-id-modal #page-search-bar').addEventListener('input',function(e){
      var needle = e.target.value.toLowerCase();
      var regItems = document.querySelector('#page-id-modal .items');
      var searchItems =  document.querySelector('#page-id-modal .search-items');
      if(!needle){
        regItems.style.display = 'block';
        searchItems.style.display = 'none';
        searchItems.innerHTML = '';
        return false;
      }
      regItems.style.display = 'none';
      searchItems.style.display = 'block';
      searchItems.innerHTML = '';
      var filtered = allPosts.filter(function(e,i){
        let search = e.title.toLowerCase();
        return search.includes(needle);
      });
      if(!filtered.length){
        searchItems.innerHTML = '<h2 class="no-matches">No matching posts found</h2>';
        return false;
      }
      var items = filtered.map(function(e,i){
        var search = e.title.toLowerCase(),
            start = search.indexOf(needle),
            sec1 = e.title.substring(0,start),
            sec2 = e.title.substring(start, start+needle.length),
            sec3 = e.title.substring(start+needle.length);
        return(`<div class="post-item" onClick="assignID(this)" data-id=${e.id}>
                  <span class="type-pill">${e.type}</span>
                  ${sec1}<b>${sec2}</b>${sec3}
                </div>`);
      });
      searchItems.innerHTML = items.join('')

    });


    return false;
  }


});
</script>
<style>
.page-id-modal-backdrop {
  position:fixed;
  left: 0;
  right: 0;
  top: 0;
  bottom: 0;
  background: rgba(0,0,0,.7);
  z-index: 99999;
}
#page-id-modal {
  position:absolute;
  left: 50%;
  top: 50%;
  transform: translate(-50%,-50%);
  background:white;
  width: calc(100% - 24px);
  height: calc(100% - 24px);
  max-width: 600px;
  max-height: 500px;
  display:flex;
  flex-direction: column;
}

#page-id-modal .modal-search {
  padding: 12px;
  border-bottom: 1px solid #ddd;
  position:relative;
}
#page-id-modal .modal-search  input, #page-id-modal .modal-search > .inner {
  width: 100%;

}
#page-id-modal .modal-footer {
  border-top: 1px solid #ddd;
  padding:12px;
  text-align: right;
}
#page-id-modal .modal-body {
  overflow-x: hidden;
  overflow-y: auto;
  -webkit-overflow-scrolling: touch;
  flex: 1;
}
#page-id-modal h2.section-header {
  margin: 0;
  line-height: 1;
  position:relative;
  display:flex;
  border-bottom:1px solid #ccc;
  position:relative;
  cursor: pointer;
}

#page-id-modal h2.section-header > span.text {
  padding: 12px;
  overflow:hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  font-size:14px;
  flex:1;
  text-transform: capitalize;
}
#page-id-modal h2.section-header > span.visibility-toggle {
  position:relative;
  flex-basis: 48px;
  border: 0;
  background:none;
  cursor:pointer;
}
#page-id-modal h2.section-header.opened > span.visibility-toggle {
  transform: rotate(180deg);
}
#page-id-modal h2.section-header > span.visibility-toggle:before {
  display:block;
  content:'';
  position: absolute;
  left: 50%;
  top: 50%;
  transform: translate(-50%,-50%);
  border: 6px solid transparent;
  border-bottom-width: 0;
  border-top-color: #72777c;
}
#page-id-modal .post-item {
  padding: 8px 12px;
  overflow:hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  cursor:pointer;
  background-color:#f1f1f1;
  border-bottom: 1px solid #ddd;
}
#page-id-modal .post-items {
  display:none;
}
#page-id-modal .post-items.opened {
  display:block;
}
#page-id-modal .post-item:last-child {
  border-bottom-color:#ccc;
}
#page-id-modal .post-item:hover {
  background:#ddd;
}
#page-id-modal .post-item .type-pill {
  display:inline-block;
  margin-right: 4px;
  background:#aaa;
  vertical-align: middle;
  border-radius: 3px;
  padding:  4px;
  font-size: 10px;
  color:white;
  line-height: 1;
  text-transform: uppercase;
}
#page-id-modal .search-items h2.no-matches {
  margin: 0;
  padding: 12px;
  font-size: 14px;
}
</style>
<?php
}
?>
