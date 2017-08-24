
function ieIdc() {
 const mask = document.getElementById('ie9_mask'); 
 const btn = mask.querySelector('button');
 btn.addEventListener('click',function(){
   mask.parentNode.removeChild(mask);
   Cookies.set('idc_ie9', 'yes');
 });
  
}
