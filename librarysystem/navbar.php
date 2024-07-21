<!DOCTYPE html>
<html>
    <head>
        <title> Home</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="icon" href="/librarysystem/image/IMSA-LOGO.png">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Arimo:wght@400;700&family=Libre+Baskerville:ital@1&family=Oswald&family=PT+Serif:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <link rel="stylesheet" type="text/css" href="style/navbar.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js"></script>
    </head>
    <body>
        <header>
            <div class="navbar">   
                <div class="title-navbar"> <a href="index.php"> Bibilothek <br> Medizinische Statistik </a> </div>
                <ul class="links">
                    <li><a href="books.php">DATENBANK <br> BUCH</a></li>
                    <li><a href="magazines.php">DATENBANK <br> MAGAZINE</a></li>
                    <li><a href="https://www.ukaachen.de/kliniken-institute/institut-fuer-medizinische-statistik/institut/">INSTITUT <br> SEITE</a></li>
                    <?php if (isset($_SESSION["role_id"]) && $_SESSION["role_id"] == 2) : ?>
                        <li><a href="admin_searchbooks.php"> Edit <br> Books-Magazine </a></li>
                        <li><a href="admin_users.php"> Edit <br> Users </a></li>
                        <li><a href="admin_manage.php"> Datenbank <br> Edit </a></li>
                    <?php endif; ?>
                </ul>

                <ul class="logout-button">
                    <?php if (isset($_SESSION['user_id'])) :?>
                        <li class="account"><a href="user_dashboard.php"><img src="/librarysystem/image/user.png" alt="user-account">  ACCOUNT </a></li>
                        <li class="logout"><a href="logout.php"><img src="/librarysystem/image/logout.png" alt="user-account"> LOGOUT</a></li>
                        
                    <?php else :?>
                        <li class="login"><a href="login.php"><img src="/librarysystem/image/logout.png" alt="login"> Login</a></li>
                    <?php endif; ?>
                </ul>
                    <div class="toggle_btn">
                        <i class="fa-solid fa-bars"></i> 
                    </div>
            </div>
            </div>

            <div class="dropdown_menu"> 
                <li><a href="books.php">Buch Datenbank</a></li>
                <li><a href="magazines.php">Magazine Datenbank</a></li>
                <li><a href="https://www.ukaachen.de/kliniken-institute/institut-fuer-medizinische-statistik/institut/">Institut Seite</a></li>
                <?php if (isset($_SESSION["role_id"]) && $_SESSION["role_id"] == 2) : ?>
                        <li><a href="admin_searchbooks.php"> Eedit Buch-Magazine </a></li>
                        <li><a href="admin_users.php"> Edit Users </a></li>
                        <li><a href="admin_manage.php"> Datenbank <br> Edit </a></li>
                <?php endif; ?>
                <?php if (isset($_SESSION['user_id'])) :?>
                        <li class="account"><a href="user_dashboard.php"><img src="/librarysystem/image/user.png" alt="user-account">  Account </a></li>
                        <li class="logout"><a href="logout.php"><img src="/librarysystem/image/logout.png" alt="user-account"> Logout </a></li>
                    <?php else :?>
                        <li class="login"><a href="login.php"><img src="/librarysystem/image/logout.png" alt="login"> Login</a></li>
                    <?php endif; ?>
            </div>
        </header>

        <script>
            const toggleBtn = document.querySelector('.toggle_btn')
            const toggleBtnIcon = document.querySelector('.toggle_btn i')
            const dropDownMenu = document.querySelector('.dropdown_menu')

            toggleBtn.onclick =function() {
                dropDownMenu.classList.toggle('open')

                toggleBtnIcon.classList = isOpen
                    ? '"fas fa-xmark' 
                    : 'fa-solid fa-bars'
            }
        </script>
    </body>
</html>