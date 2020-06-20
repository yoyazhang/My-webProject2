<?php
require_once('../config.php');

function LoginOrOut($nowPage){
    if(isset($_SESSION['Username'])){
        echo '<li id="theDoor"><h2 id="myZone"><img class="icon" src="../images/icons/个人中心.png">MyZone</h2>';
        echo '<ul id="navPersonal">';
        if($nowPage == 'upload'){
            echo '<li><a href="upload.php?uid='.$_SESSION['UID'].'" id="currentPage"><img class="icon" src="../images/icons/上传-选中.png">Upload</a></li>';
        }
        else{
            echo '<li><a href="upload.php?uid='.$_SESSION['UID'].'"><img class="icon" src="../images/icons/上传-未选中.png">Upload</a></li>';
        }
        if($nowPage == 'MyPhotos'){
            echo '<li><a href="MyPhotos.php?uid='.$_SESSION['UID'].'" id="currentPage"><img class="icon" src="../images/icons/我的照片.png">MyPhotos</a></li>';
        }
        else{
            echo '<li><a href="MyPhotos.php?uid='.$_SESSION['UID'].'"><img class="icon" src="../images/icons/我的照片-未选中.png">MyPhotos</a></li>';
        }
        if($nowPage == 'Favorites'){
            echo '<li><a href="Favorites.php?uid='.$_SESSION['UID'].'" id="currentPage"><img class="icon" src="../images/icons/收藏-选中.png">MyFavorites</a></li>';
        }
        else{
            echo '<li><a href="Favorites.php?uid='.$_SESSION['UID'].'"><img class="icon" src="../images/icons/收藏-未选中.png">MyFavorites</a></li>';
        }
        echo '<li><a href="logout.php"><img class="icon" src="../images/icons/用户.png">LogOut</a></li>';
        echo '</ul>';
        echo '</li>';
    }
    else{
        echo '<li><a href="login.php"><img class="icon" src="../images/icons/用户.png">LogIn</a></li>';
    }
}
function constructPicLink($id, $label) {
    $link = '<a href="../src/PictureInformation.php?id='.$id.'">';
    $link .= $label;
    $link .= '</a>';
    return $link;
}
?>
