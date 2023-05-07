<!DOCTYPE html>
<html>
    <head>
        <title> Home</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <link rel="stylesheet" type="text/css" href="style/home.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js"></script>
    </head>
    <body>
        <header>
            <div class="navbar">   
                <div class="logo"> <a href="index.php">Bibilothek</a> </div>
                <ul class="links">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="login.php">login</a></li>
                    <li><a href="searchbooks.php">search Books</a></li>
                    <li><a href="profile.php">profile</a></li>
                </ul>

                <a href="#" class="action_btn">Get Started</a>
                    <div class="toggle_btn">
                        <i class="fa-solid fa-bars"></i>
                    </div>
            </div>

            <div class="dropdown_menu">
            <li><a href="index.php">Home</a></li>
                    <li><a href="login.php">login</a></li>
                    <li><a href="searchbooks.php">search Books</a></li>
                    <li><a href="profile.php">profile</a></li>
                    <li><a href="#" class="action_btn"> Get Started</a></li>
            </div>
        </header>

        <script>
            const toggleBtn = document.querySelector('.toggle_btn')
            const toggleBtnIcon = document.querySelector('.toggle_btn i')
            const dropDownMenu = document.querySelector('.dropdown_menu')

            toggleBtn.onclick =function() {
                dropDownMenu.classList.toggle('open')

                toggleBtnIcon.classList = isOpen
                    ? '"fas fa-xmark' //xmark not working
                    : 'fa-solid fa-bars'
            }
        </script>
    </body>
</html>