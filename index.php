<?php
session_start();
require_once('config.php');
function outputHomePics() {
    try {
        $pdo = new PDO(DBCONNSTRING,DBUSER,DBPASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = 'SELECT Title, Description,PATH,travelimage.ImageID, COUNT(travelimagefavor.ImageID) AS instnum FROM travelimage LEFT JOIN travelimagefavor ON travelimage.ImageID =travelimagefavor.ImageID GROUP BY travelimage.ImageID ORDER BY instnum DESC LIMIT 0,9';
        $result = $pdo->query($sql);
        for($i = 0;$i < 3; $i++){
            echo '<tr>';
            for($j = 0;$j < 3;$j++){
                $row = $result->fetch();
                if($row['Description'] == null){
                    $row['Description'] = "The author is so lazy that he/she doesn't give any detailed description about this Photo.TUT";
                }
                outputSinglePic($row);
            }
            echo '</tr>';
        }

        $pdo = null;
    }catch (PDOException $e) {
        die( $e->getMessage() );
    }
}
function outputRandomPics(){
    try{
        $pdo = new PDO(DBCONNSTRING,DBUSER,DBPASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = 'select * from travelimage  order by rand() limit 9';
        $result = $pdo->query($sql);

        for($i = 0;$i < 3; $i++){
            echo '<tr>';
            for($j = 0;$j < 3;$j++){
                $row = $result->fetch();
                if($row['Description'] == null){
                    $row['Description'] = "The author is so lazy that he/she doesn't give any detailed description about this Photo.TUT";
                }
                outputSinglePic($row);
            }
            echo '</tr>';
        }
        $pdo = null;
    }catch (PDOException $e) {
        die( $e->getMessage() );
    }
}
function outputSinglePic($row) {
    echo '<td>';
    echo '<figure>';
    $img = '<img class="normalPic" src="images/normal/medium/'.$row['PATH'].'">';
    echo constructPicLink($row['ImageID'], $img);
    echo '<figcaption>';
    echo '<h3>'.$row['Title'].'</h3>';
    echo '<div class="details">'.$row['Description'].'</div>';
    echo '</figcaption></figure></td>';
}

/*
  Constructs a link given the genre id and a label (which could
  be a name or even an image tag
*/
function constructPicLink($id, $label) {
    $link = '<a href="src/PictureInformation.php?id='.$id.'">';
    $link .= $label;
    $link .= '</a>';
    return $link;
}

function LoginOrOut(){
    if($_SESSION['Username']){
        echo '<li id="theDoor"><h2 id="myZone"><img class="icon" src="images/icons/个人中心.png">MyZone</h2>';
        echo '<ul id="navPersonal">';
        echo '<li><a href="src/upload.php?id='.$_SESSION['UID'].'"><img class="icon" src="images/icons/上传-未选中.png">Upload</a></li>';
        echo '<li><a href="src/MyPhotos.php?id='.$_SESSION['UID'].'"><img class="icon" src="images/icons/我的照片-未选中.png">MyPhotos</a></li>';
        echo '<li><a href="src/Favorites.php?id='.$_SESSION['UID'].'"><img class="icon" src="images/icons/收藏-未选中.png">MyFavorites</a></li>';
        echo '<li><a href="src/logout.php"><img class="icon" src="images/icons/用户.png">LogOut</a></li>';
        echo '</ul>';
        echo '</li>';
    }
    else{
        echo '<li><a href="src/login.php"><img class="icon" src="images/icons/用户.png">LogIn</a></li>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>首页</title>
    <script type="text/javascript" src="jquery-3.3.1.min.js"></script>
    <script src="bootstrap-3.3.7-dist/js/bootstrap.min.js"></script>

    <link href="bootstrap-3.3.7-dist/css/bootstrap.css" rel="stylesheet">
    <link href="CSS/reset.css" rel="stylesheet">
    <link href="CSS/HeaderNavMainFooterPic.css" rel="stylesheet">
    <link href="CSS/Home.css" rel="stylesheet">

</head>
<body onload="squareClip()" onresize="squareClip()">
<header>
    <div class="title">
        <h1><a name="header">Travel To Share</a></h1>
        <div class = "slogan">See the World outside!</div>
    </div>
    <nav>
        <ul id="navPublic">
            <li><a href="index.php" id="currentPage"><img class="icon" src="images/icons/首页-选中.png">Home</a></li>
            <li><a href="src/Browser.php"><img class="icon" src="images/icons/浏览-未选中.png">Browser</a></li>
            <li><a href="src/Search.php"><img class="icon" src="images/icons/搜索-未选中.png">Search</a></li>
            <?php LoginOrOut();?>
        </ul>
    </nav>
</header>
<main>

        <div class="container">
            <div id="myCarousel" class="carousel slide">
                <!-- 轮播（Carousel）指标 -->
                <ol class="carousel-indicators">
                    <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
                    <li data-target="#myCarousel" data-slide-to="1"></li>
                    <li data-target="#myCarousel" data-slide-to="2"></li>
                </ol>
                <!-- 图片 -->
                <div class="carousel-inner">
                    <div class="item active">
                        <a href="src/PictureInformation.php?id=41"><img src="images/normal/medium/222223.jpg" alt="First slide"></a>
                        <div class="carousel-caption">
                            <a href="src/PictureInformation.php?id=41">A nice picture in black and white</a>
                        </div>
                    </div>
                    <div class="item">
                        <a href="src/PictureInformation.php?id=49"><img src="images/normal/medium/9494464567.jpg" alt="Second slide"></a>
                        <div class="carousel-caption">
                            <a href="src/PictureInformation.php?id=49"> Venice,  a place full of adventure and love scenario</a></div>
                    </div>
                    <div class="item">
                        <a href="src/PictureInformation.php?id=7"><img src="images/normal/medium/5856697109.jpg" alt="Third slide"></a>
                        <div class="carousel-caption">
                            <a href="src/PictureInformation.php?id=7">A beautiful town near the lake! </a>
                        </div>
                    </div>
                </div>
                <!-- 轮播（Carousel）导航 -->
                <a class="left carousel-control" href="#myCarousel" role="button" data-slide="prev">
                    <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
                    <span class="sr-only">Previous</span>
                </a>
                <a class="right carousel-control" href="#myCarousel" role="button" data-slide="next">
                    <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                    <span class="sr-only">Next</span>
                </a>
            </div>
        </div>

    <section class="homePics">
        <table>
            <?php
            if($_GET['mode']==1){
                outputRandomPics();
            }
            else{
                outputHomePics();
            }
            ?>
        </table>
    </section>
    <aside>
        <button id="toTop"><a href="#header"><img src="images/icons/向上.png"></a></button>
        <button id="refresh"><img src="images/icons/刷新.png"></button>
    </aside>
</main>
<footer>
    <div class="contact">
        <div><a href="#">About us</a></div>
        <div><a href="#">Contact us</a></div>
    </div>
    <div class="Information">
        <div class="introduction">YoYa Zhang版权所有 &copy 2019-2020</div>
        <div class="easterEgg">沪公网安备19302010074号</div>
        <address>Email: <a href = "http://mail.fudan.edu.cn/">19302010074@fudan.edu.cn</a></address>
    </div>
</footer>
<script type="text/javascript" src="JavaScript/ImgClip.js"></script>

<script type="text/javascript" src="JavaScript/GetRandomPic.js"></script>
</body>
</html>
