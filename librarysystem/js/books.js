function openPopup(id) {
    var popup = document.getElementById('popup-' + id);
    popup.classList.add("open-popup");

}

function closePopup(id) {
    var popup = document.getElementById('popup-' + id);
    popup.classList.remove("open-popup");
}

function toggleFilter(filter) {
    var filterSection = document.querySelector('.' + filter + '-filter');
    if (filterSection.style.display === 'none' || filterSection.style.display === '') {
        filterSection.style.display = 'block';
    } else {
        filterSection.style.display = 'none';
    }
}


function applyStatusFilter(statusId) {
    const url = new URL(window.location.href);
    url.searchParams.set('status', statusId);
    window.location.href = url.toString();
}

document.addEventListener('DOMContentLoaded', function() {
    const statusButtons = document.querySelectorAll('.status-button');
    statusButtons.forEach(function(button) {
      button.addEventListener('click', function() {
        const statusId = this.getAttribute('data-status');
        applyStatusFilter(statusId);
      });
    });
  });
  
function scrollToTop() {
    window.scrollTo({top: 0, behavior: 'smooth'});
}

function scrollToBottom() {
    window.scrollTo({
        top: document.body.scrollHeight,
        behavior: 'smooth'
    });
} 

document.addEventListener("DOMContentLoaded", () => {
    const closeButton = document.querySelector('.close-btn');
    if(closeButton) {
        closeButton.addEventListener('click', function() {
            this.parentElement.style.display ='none';
        });
    }
});

window.addEventListener("scroll", function() {
    var scrollToTopBtn = document.getElementById("scrollToTopBtn");
    var scrollToBottomBtn = document.getElementById("scrollToBottomBtn");
    if (window.scrollY > 20) {
        scrollToTopBtn.style.display = "block";
    } else {
        scrollToTopBtn.style.display = "none";
    }
    var maxScroll = document.documentElement.scrollHeight - window.innerHeight;

    if (window.scrollY < maxScroll - 20) {
        scrollToBottomBtn.style.display = "block";
    } else {
        scrollToBottomBtn.style.display = "none";
    }
    
});


window.onload = function() {
    document.getElementById('loading-screen').style.display = 'none';
  }

  window.addEventListener('load', function() {
    var loadingScreen = document.getElementById('loading-screen');
    var nonLoadingScreen = document.getElementById('non-loading-screen');
    
    loadingScreen.style.display = 'none';
    nonLoadingScreen.style.opacity = '1';
    nonLoadingScreen.style.pointerEvents = 'auto';
  });

function resetFiltersAndReload() {
    window.location.href = 'http://localhost/librarysystem/books.php';
}

var resetButton = document.getElementById('resetFiltersButton');
resetButton.addEventListener('click', function() {
    resetFiltersAndReload();
}); 

window.addEventListener('DOMContentLoaded', (event) => {
    if (window.location.search !== '') {
        history.replaceState(null, '', window.location.pathname);
    }
});