
function ieIdc() {
var care =   Cookies.get('idc_ie9');
const mask = document.getElementById('ie9_mask');
if(!care === "yes") {
  return false;
}

 const btn = mask.querySelector('button');
 btn.addEventListener('click',function(){
   mask.parentNode.removeChild(mask);
   Cookies.set('idc_ie9', 'yes');
 });

}
