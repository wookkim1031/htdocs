function showPopup(bookId){
    var popup = document.getElementById('popup-' +bookId);
    popup.classList.add("open-popup");
}
function closePopup(bookId){
    var popup = document.getElementById('popup-' + bookId);
    popup.classList.remove("open-popup");
}

function showMagazinePopup(magazineId) {
    var magazinePopup = document.getElementById('magPopup-' + magazineId);
    magazinePopup.classList.add("open-Magpopup");
}

function closeMagazinePopup(magazineId) {
    var magazinePopup = document.getElementById('magPopup-' + magazineId);
    magazinePopup.classList.remove("open-Magpopup");
}

document.addEventListener('DOMContentLoaded', function() {
    document.querySelector('.search-bar').addEventListener('submit', function() {
        document.getElementById('loading').style.display = 'block';
    });

    // Assuming loading is hidden by default, else hide it here
    document.getElementById('loading').style.display = 'none';

    // Update cover images
    const bookCovers = document.querySelectorAll('.book-cover');
    bookCovers.forEach(coverDiv => {
        const isbn = coverDiv.getAttribute('data-isbn');
        const imageTag = coverDiv.querySelector('img');  // Target the img tag specifically
        if (isbn && imageTag) {
            const url = `https://www.googleapis.com/books/v1/volumes?q=isbn:${isbn}`;
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    if (data.items && data.items.length > 0 && data.items[0].volumeInfo.imageLinks && data.items[0].volumeInfo.imageLinks.thumbnail) {
                        imageTag.src = data.items[0].volumeInfo.imageLinks.thumbnail;
                    }
                })
                .catch(error => console.error('Error fetching cover image:', error));
        }
    });

})