const loadBooksByRestButton = document.getElementById( 'bookstore-load-books' );
if ( loadBooksByRestButton ) {
    loadBooksByRestButton.addEventListener( 'click', function () {
        const allBooks = new wp.api.collections.Book();
        allBooks.fetch().done(
            function ( books ) {
                console.log(books);
                const textarea = document.getElementById( 'bookstore-booklist' );
                books.forEach( function ( book ) {
                    textarea.value += book.title.rendered + ',' + book.link + ',\n'
                });
            }
        );
    });
}