let MostrarBotonOcultoPalMenu = document.getElementById('MostrarBotonOcultoPalMenu');

document.addEventListener('scroll', () => {
    if (document.documentElement.scrollTop > 99) {
        MostrarBotonOcultoPalMenu.checked = true;
    }else{
        MostrarBotonOcultoPalMenu.checked = false;
    }
})