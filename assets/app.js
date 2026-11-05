const toggle=document.getElementById('menuToggle');
const sidebar=document.getElementById('sidebar');
toggle?.addEventListener('click',()=>sidebar?.classList.toggle('open'));
document.addEventListener('click',e=>{
 if(window.innerWidth<=900 && sidebar?.classList.contains('open') && !sidebar.contains(e.target) && !toggle?.contains(e.target))sidebar.classList.remove('open');
});
