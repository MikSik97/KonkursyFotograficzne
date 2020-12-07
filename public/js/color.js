function changeModalColor(color)
{
    var modals = document.getElementsByClassName("modal");
    for(i=0; i<modals.length; i++) {
            modals[i].style.backgroundColor = color;
        }
    }