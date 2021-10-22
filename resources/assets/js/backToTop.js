const backToTopBtn = document.createElement('Button')
backToTopBtn.innerHTML = 'Back to top'
backToTopBtn.id = 'backToTopBtn'
document.body.appendChild(backToTopBtn)

backToTopBtn.addEventListener('click', topFunction)
// When the user clicks on the button, scroll to the top of the document
function topFunction () {
  document.body.scrollTop = 0
  document.documentElement.scrollTop = 0
}

window.onscroll = scrollFunction

function scrollFunction () {
  if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
    backToTopBtn.style.display = 'block'
  } else {
    backToTopBtn.style.display = 'none'
  }
}
