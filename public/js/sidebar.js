const scroll = document.getElementById('menu');

scroll.onmouseover = () => {
    scroll.style.overflowY = 'auto'
}

scroll.onmouseout = () => {
    scroll.style.overflowY = 'hidden'
}