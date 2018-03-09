let { h, render, Component } = preact;


let appDom; 

function domready(fn) {
  if (document.attachEvent ? document.readyState === "complete" : document.readyState !== "loading"){
    fn();
  } else {
    document.addEventListener('DOMContentLoaded', fn);
  }
}

domready(function(){
  let appDiv = document.createElement('div');
  appDiv.setAttribute('id','PageSelector');
  document.querySelector('body').appendChild(appDiv);
  document.querySelector('button#add-post-id').addEventListener('click',function(e){
    e.preventDefault();
    
    appDom = render(<App />, document.getElementById('PageSelector'));
    return false;
  });
  
});


class App extends Component {
  constructor(props) {
    super(props);
    this.state = {
      searchValue : '',
      posts : allPosts,
      scrollPos: 0
    };
  }
  componentWillMount() {
    this.state.scrollPos =  window.pageYOffset || document.documentElement.scrollTop;
  }
  closeApp(e) {
    e.preventDefault();
    render('', document.getElementById('PageSelector'), appDom);
  }
  
  render(props,state) {
    let clearBtn = '';
    if(state.searchValue) {
      clearBtn = <button value={''} onClick={this.linkState('searchValue')}>Clear</button>;
    }
    return(
      <div className="modal-overlay">
        <div className="modal">
          <button onClick={this.closeApp}>Close App</button><br/>
            <input type="text" 
              value={state.searchValue} 
              onInput={this.linkState('searchValue')}
            />
        {clearBtn}
        <div>{state.searchValue}</div>
        <PostList searchValue={state.searchValue} posts={state.posts}/>
        <SearchList searchValue={state.searchValue} posts={state.posts}/>
        </div>
      </div>  
      
    );
  }
}
function PostItem(props) {
  
  return(
    <div className="post-section">
      <h2>
    </div>
  )
}
class PostList extends Component {
  constructor(props) {
    super(props);
    this.state = {
      sections:['post','project','page']
    }
  }
  render(props,state) {
    if(props.searchValue) {return false}
    
    let list = state.sections.map(function(e,i){
      let key = e;
      let filtered = props.posts.filter(function(e){
        return e.type === key;
      });
      if(!filtered.length){return false};
      //return <PostSection items={filtered} header={key} />
    });
    
    
    return(<div className="post-items">{list}</div>);
    
    
  }
