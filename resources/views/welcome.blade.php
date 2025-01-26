<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>NYTimes Best Sellers</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer>
        async function fetchBestSellers(event) {
            event.preventDefault();

            const author = document.getElementById('author').value;
            const resultsContainer = document.getElementById('results');
            resultsContainer.innerHTML = '<p class="text-gray-500">Loading...</p>';

            try {
                const response = await fetch(`/api/v1/nyt/best-sellers?author=${encodeURIComponent(author)}`);
                if (!response.ok) throw new Error('Failed to fetch data.');

                const data = await response.json();
                if (data.error) throw new Error(data.error);

                if (data.results && data.results.length > 0) {
                    resultsContainer.innerHTML = data.results.map(book => `
                        <div class="p-4 border rounded shadow mb-4 bg-white">
                            <h2 class="text-xl font-bold text-gray-800">${book.title || 'Untitled'}</h2>
                            <p class="text-sm text-gray-600"><strong>Author:</strong> ${book.author || 'Unknown'}</p>
                            <p class="text-sm text-gray-600"><strong>Description:</strong> ${book.description || 'No description available.'}</p>
                            <p class="text-sm text-gray-600"><strong>Publisher:</strong> ${book.publisher || 'Unknown'}</p>
                            <p class="text-sm text-gray-600"><strong>Price:</strong> $${book.price || 'N/A'}</p>
                            <p class="text-sm text-gray-600"><strong>ISBNs:</strong> ${
                                book.isbns
                                    ? book.isbns.map(isbn => `${isbn.isbn13 || isbn.isbn10}`).join(', ')
                                    : 'N/A'
                            }</p>
                            ${book.reviews[0]?.book_review_link ? `
                                <a href="${book.reviews[0].book_review_link}" target="_blank" class="text-blue-500 hover:underline">
                                    Read Review
                                </a>` : '<p class="text-sm text-gray-600">No review available.</p>'}
                        </div>
                    `).join('');
                } else {
                    resultsContainer.innerHTML = '<p class="text-gray-500">No results found for the given author.</p>';
                }
            } catch (error) {
                resultsContainer.innerHTML = `<p class="text-red-500">${error.message}</p>`;
            }
        }
    </script>
</head>
<body class="antialiased bg-gray-100 min-h-screen">
    <div class="max-w-7xl mx-auto p-6">
        <h1 class="text-3xl font-bold text-center text-gray-900 mb-8">NYTimes Best Sellers Search</h1>
        <form id="searchForm" class="bg-white p-6 rounded-lg shadow-md" onsubmit="fetchBestSellers(event)">
            <div class="mb-4">
                <label for="author" class="block text-gray-700 font-medium">Author</label>
                <input type="text" id="author" name="author" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" placeholder="Enter an author">
            </div>
            <div class="mb-4">
                <label for="title" class="block text-gray-700 font-medium">Title</label>
                <input type="text" id="title" name="title" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" placeholder="Enter a book title">
            </div>
            <div class="mb-4">
                <label for="isbn" class="block text-gray-700 font-medium">ISBN</label>
                <input type="text" id="isbn" name="isbn" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" placeholder="Enter an ISBN">
            </div>
            <button type="submit" class="w-full bg-green-500 text-white py-2 px-4 rounded-md hover:bg-red-600 transition">Search</button>
        </form>
        <div id="results" class="mt-8"></div>
    </div>
</body>
</html>
