let { h, render, Component } = preact;




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
    
    render(<App />, document.getElementById('PageSelector'));
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
  
  render(p,s) {
    
    return(
      <div className="modal-overlay">
        <div className="modal">
          <button onClick={this.closeApp}>Close App</button><br/>
            <input type="text" 
              value={this.state.searchValue} 
              onInput={(e) => this.setState({searchValue: e.target.value})}
            />
        {clearBtn}
        <div>{this.state.searchValue}</div>
        <PostList searchValue={this.state.searchValue} posts={this.state.posts}/>
        <SearchList searchValue={this.state.searchValue} posts={this.state.posts}/>
        </div>
      </div>  
      
    );
  }
  
  
  
  
}
