// fetch_book_data.php
<?php
//cache the data because the amount if too large
function getBookDataFromCache($isbn) {
    $cacheFile = 'cache/' . $isbn . '.json';
    if (file_exists($cacheFile) && (time() - filemtime($cacheFile) < 86400)) { 
        return file_get_contents($cacheFile);
    }
    return false;
}

function saveBookDataToCache($isbn, $data) {
    $cacheFile = 'cache/' . $isbn . '.json';
    file_put_contents($cacheFile, json_encode($data));
}

function fetchBookDataFromAPI($isbn) {
    $url = "https://www.googleapis.com/books/v1/volumes?q=isbn:" . $isbn;
    $response = file_get_contents($url);
    $data = json_decode($response, true);
    if (isset($data['items'][0])) {
        saveBookDataToCache($isbn, $data['items'][0]);
        return $data['items'][0];
    }
    return null;
}

$isbn = $_GET['isbn'] ?? '';
if (!$isbn) {
    echo json_encode(['error' => 'No ISBN provided']);
    exit;
}

$cachedData = getBookDataFromCache($isbn);
if ($cachedData) {
    echo $cachedData; // Send cached data back to client
} else {
    $bookData = fetchBookDataFromAPI($isbn);
    if ($bookData) {
        echo json_encode($bookData);
    } else {
        echo json_encode(['error' => 'No data found for ISBN']);
    }
}
?>
