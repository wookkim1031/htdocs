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

    const books = document.querySelectorAll('.book-cover');

    books.forEach(book => {
        book.addEventListener('click', function() {
            const isbn = this.getAttribute('data-isbn');
            if (!isbn) return;
            fetchCoverImage(this, isbn);
        });
    });
});

function fetchCoverImage(bookElement, isbn) {
    const apiUrl = `https://www.googleapis.com/books/v1/volumes?q=isbn:${isbn}`;

    fetch(apiUrl)
        .then(response => response.json())
        .then(data => {
            if (data.items && data.items.length > 0) {
                const bookInfo = data.items[0].volumeInfo;
                if (bookInfo.imageLinks && bookInfo.imageLinks.thumbnail) {
                    updateBookCover(bookElement, bookInfo.imageLinks.thumbnail);
                }
            }
        })
        .catch(error => console.error('Error fetching cover image:', error));
}

function updateBookCover(bookElement, imageUrl) {
    // Assuming your bookElement has an <img> tag inside it
    const imgTag = bookElement.querySelector('img');
    imgTag.src = imageUrl;
}
