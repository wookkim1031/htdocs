<?php
session_start();
include 'navbar.php';

if (isset($_SESSION["user_id"])) {
    $mysqli = require __DIR__ . "/database.php";
    $user_id = $_SESSION["user_id"];

    $stmt = $mysqli->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($user = $result->fetch_assoc()) {
        $_SESSION['role_id'] = $user['role_id'];

        $books_stmt = $mysqli->prepare("SELECT books.id, books.title, books.author, books.year, books.publisher FROM saved_items JOIN books ON saved_items.book_id = books.id WHERE saved_items.user_id = ?");
        $books_stmt->bind_param("i", $user_id);
        $books_stmt->execute();
        $books_result = $books_stmt->get_result();
        $saved_books = $books_result->fetch_all(MYSQLI_ASSOC);
        $books_stmt->close();


        $magazines_stmt = $mysqli->prepare("SELECT magazines.id, magazines.title, magazines.jahrgang, magazines.volumes, magazines.standort FROM saved_items JOIN magazines ON saved_items.magazine_id = magazines.id WHERE saved_items.user_id = ?");
        $magazines_stmt->bind_param("i", $user_id);
        $magazines_stmt->execute();
        $magazines_result = $magazines_stmt->get_result();
        $saved_magazines = $magazines_result->fetch_all(MYSQLI_ASSOC);
        $magazines_stmt->close();

    }
    $stmt->close();
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Profile</title>
    <link rel="stylesheet" type="text/css" href="style/user_dashboard.css">
    <link rel="icon" href="/librarysystem/image/IMSA-LOGO.png">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"> <!-- Font Awesome for icons -->
</head>
<body>
    <?php if(isset($user)) :?>
    <div class="profile-container">
        <div class="cover-photo">
            <h2>  User Profile </h2> 

        </div>
        <div class="profile-header">
            <div class="profile-picture"></div>
            <h2><?php echo $user['name']; ?></h2>
            <p><?php echo $user['email']; ?></p>
        </div>
        <div class="profile-tabs">
            <ul>
                <li><a href="#"><i class="fas fa-user"></i> About</a></li>
            </ul>
        </div>
        <div class="profile-content">
            <div class="non-hidden">
            <table>
                <tr>
                    <th>Name:</th>
                    <td><?php echo htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8'); ?></td>
                </tr>
                <tr>
                    <th>Email:</th>
                    <td><?php echo htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8'); ?></td>
                </tr>
                <tr>
                    <th>Telefonnummer:</th>
                    <td><?php echo htmlspecialchars($user['telephone'] ?? 'Nicht vorhanden', ENT_QUOTES, 'UTF-8'); ?></td>
                </tr>
            </table>
            <div class="non-hidden saved-books">
            
            <h3>Saved Books</h3>
            <hr>
            <?php if(!empty($saved_books)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Author</th>
                            <th>Year</th>
                            <th>Publisher</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($saved_books as $book): ?>
                            <tr>
                                <td><?= htmlspecialchars($book['title']) ?></td>
                                <td><?= htmlspecialchars($book['author']) ?></td>
                                <td><?= htmlspecialchars($book['year']) ?></td>
                                <td><?= htmlspecialchars($book['publisher']) ?></td>
                                <td>
                                    <form action="remove_saved_book.php" method="post">
                                        <input type="hidden" name="book_id" value="<?= $book['id'] ?>">
                                        <button type="submit">Remove</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                    <p class="saved-section">No Books saved.</p>
                <?php endif; ?>   
                
                <h3>Saved Magazines</h3>
                <hr>
                <?php if(!empty($saved_magazines)): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Jahrgang</th>
                                <th>Volumes</th>
                                <th>Standort</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($saved_magazines as $magazine): ?>
                                <tr>
                                    <td><?= htmlspecialchars($magazine['title']) ?></td>
                                    <td><?= htmlspecialchars($magazine['jahrgang']) ?></td>
                                    <td><?= htmlspecialchars($magazine['volumes']) ?></td>
                                    <td><?= htmlspecialchars($magazine['standort']) ?></td>
                                    <td>
                                        <form action="remove_saved_magazine.php" method="post">
                                            <input type="hidden" name="magazine_id" value="<?= $magazine['id'] ?>">
                                            <button type="submit">Remove</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="saved-section">No Magazines saved.</p>
                <?php endif; ?>
            </div>
            </div>
            <div class="edit-section hidden">
                <form action="update_profile.php" method="post">
                    <div class="form-group">
                        <label for="new_name">Update Name:</label>
                        <input type="text" id="new_name" name="new_name" value="<?php echo htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                    <div class="form-group">
                        <label for="new_telephone">Update Telephone:</label>
                        <input type="text" id="new_telephone" name="new_telephone" value="<?php echo htmlspecialchars($user['telephone'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                    <button type="submit" class="profile-button">Save</button>
                </form>
            </div>
            <button id="editButton" class="profile-button non-hidden">Edit</button> <br><br>
            <?php 
                    if (isset($_SESSION['role_id']) && $_SESSION['role_id'] == 2) {
                        echo '<a href="admin_searchbooks.php" class="admin_button">Edit Books and Magazines</a><br><br>';
                        echo '<a href="admin_users.php" class="admin_button"> Mangae Users</a>';
                    }
            ?>
        </div>
    </div>

    <script src="./js/dashboard.js"></script>

    <?php else: ?>

        <?php include 'login.php' ?>
    
    <?php endif; ?>
</body>
</html>