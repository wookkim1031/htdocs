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

// Add an event listener to status buttons
document.addEventListener('DOMContentLoaded', function() {
    const statusButtons = document.querySelectorAll('.status-button');
    statusButtons.forEach(function(button) {
      button.addEventListener('click', function() {
        const statusId = this.getAttribute('data-status');
        applyStatusFilter(statusId);
      });
    });
  });
  
// JavaScript function to scroll to the top of the page
function scrollToTop() {
    document.body.scrollTop = 0; // For Safari
    document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE, and Opera
}

// Show/hide the scroll-to-top button based on the scroll position
window.addEventListener("scroll", function() {
    var scrollToTopBtn = document.getElementById("scrollToTopBtn");
    if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
        scrollToTopBtn.style.display = "block";
    } else {
        scrollToTopBtn.style.display = "none";
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

  /*reset button */
function resetFiltersAndReload() {
    window.location.href = 'http://localhost/librarysystem/books.php';
}

var resetButton = document.getElementById('resetFiltersButton');
resetButton.addEventListener('click', function() {
    resetFiltersAndReload();
}); 