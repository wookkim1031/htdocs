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
});

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('loading').style.display = 'none';
})

document.addEventListener('DOMContentLoaded', function() {
    const books = document.querySelectorAll('.book-cover');
    books.forEach(book => {
        const isbn = book.getAttribute('data-isbn');
        if (isbn) {
            const url = `https://www.googleapis.com/books/v1/volumes?q=isbn:${isbn}`;
            
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    if (data.items && data.items.length > 0 && data.items[0].volumeInfo.imageLinks && data.items[0].volumeInfo.imageLinks.thumbnail) {
                        const coverImageUrl = data.items[0].volumeInfo.imageLinks.thumbnail;
                        book.querySelector('.book-cover').src = coverImageUrl;
                    }
                })
                .catch(error => console.error('Error fetching cover image:', error));
        }
    });
})