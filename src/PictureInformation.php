<?php
session_start();
require_once('../config.php');
include_once('outputNavLink.php');
try {
    $pdo = new PDO(DBCONNSTRING,DBUSER,DBPASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = 'SELECT travelimage.ImageID, Title, Description,Content,PATH, traveluser.UserName,geocountries_regions.Country_RegionName,CityCode from travelimage NATURAL JOIN traveluser JOIN geocountries_regions ON geocountries_regions.ISO= travelimage.Country_RegionCodeISO WHERE travelimage.ImageId =:id';
    $sqlFavor = 'SELECT COUNT(*) AS kudos FROM travelimagefavor WHERE travelimagefavor.ImageID = :id';
    $id =  $_GET['id'];
    $statement = $pdo->prepare($sql);
    $statement->bindValue(':id', $id);
    $statement->execute();

    $favorNumber = $pdo->prepare($sqlFavor);
    $favorNumber->bindValue(':id',$id);
    $favorNumber->execute();

    $row = $statement->fetch(PDO::FETCH_ASSOC);
    $favorRes = $favorNumber->fetch(PDO::FETCH_ASSOC);
    if($row['CityCode'] != null){
        $sqlCity = 'SELECT geocities.AsciiName FROM geocities JOIN travelimage ON CityCode = GeoNameId WHERE travelimage.ImageId = :id';
        $cityRes = $pdo->prepare($sqlCity);
        $cityRes->bindValue(':id',$id);
        $cityRes->execute();
        $row2 = $cityRes->fetch(PDO::FETCH_ASSOC);
        $row['AsciiName'] = $row2['AsciiName'];
    }
    else{
        $row['AsciiName'] = "(Unknown)";
    }
    if($row['Description'] == null){
        $row['Description'] = "The author is so lazy that he/she doesn't give any detailed description about this Photo.TUT";
    }

    $pdo = null;
}
catch (PDOException $e) {
    die( $e->getMessage() );
}

function outputKudos($favorRes){
    try {
        $pdo = new PDO(DBCONNSTRING,DBUSER,DBPASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = 'SELECT COUNT(*) AS LIKES FROM travelimagefavor WHERE travelimagefavor.UID = :UID AND travelimagefavor.ImageID = :id';
        $statement = $pdo->prepare($sql);
        $statement->bindValue(':UID',$_SESSION['UID']);
        $statement->bindValue(':id',$_GET['id']);
        $statement->execute();
        $row1 = $statement->fetch();
        if($row1['LIKES'] == 0){
            echo '<input type="button" id="kudos" name="kudos" value="KUDOS '.$favorRes['kudos'].'">';
        }
        else{
            echo '<input type="button" id="collected" name="kudos" value="COLLECTED">';
        }
        $pdo = null;
    }
    catch (PDOException $e) {
        die( $e->getMessage() );
    }
}
function addTOFavor(){
    try {
        $pdo = new PDO(DBCONNSTRING,DBUSER,DBPASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        //是否已经有了
        $ifHad = 'SELECT COUNT(*) AS hasany FROM travelimagefavor WHERE ImageID = :id and UID = :uid';
        $outcome = $pdo->prepare($ifHad);
        $outcome->bindValue(':uid',$_SESSION['UID']);
        $outcome->bindValue(':id',$_GET['id']);
        $outcome->execute();
        $result2 = $outcome->fetch();
        if($result2['hasany'] != 0){
            return;
        }
        //现在favor里有几个行数了
        $sqlNumber = 'SELECT MAX(FavorID) AS lastDigit FROM travelimagefavor';
        $result = $pdo->query($sqlNumber);
        $numRes = $result->fetch();
        //添加！
        $sql = "INSERT INTO travelimagefavor VALUES (:favorId,:UID,:imageID)";
        $statement = $pdo->prepare($sql);
        $statement->bindValue(':favorId',$numRes['lastDigit']+1);
        $statement->bindValue(':UID',$_SESSION['UID']);
        $statement->bindValue(':imageID',$_GET['id']);
        $statement->execute();
        $pdo = null;
    }
    catch (PDOException $e) {
        die( $e->getMessage() );
    }
}
function removeFromFavor(){
    global $row;
    try {
        $pdo = new PDO(DBCONNSTRING,DBUSER,DBPASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = 'DELETE FROM travelimagefavor WHERE ImageID = :id and UID = :uid';
        $statement = $pdo->prepare($sql);
        $statement->bindValue(':id',$row['ImageID']);
        $statement->bindValue(':uid',$_SESSION['UID']);
        $statement->execute();
        $pdo = null;
    }
    catch (PDOException $e) {
        die( $e->getMessage() );
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>图片详情</title>
    <link href="../CSS/reset.css" rel="stylesheet">
    <link href="../CSS/HeaderNavMainFooterPic.css" rel="stylesheet">
    <link href="../CSS/PicInformation.css" rel="stylesheet">
</head>
<body>

<header>
    <div class="title">
        <h1><a name="header">Travel To Share</a></h1>
        <div class = "slogan">See the World outside!</div>
    </div>
    <nav>
        <ul id="navPublic">
            <li><a href="../index.php"><img class="icon" src="../images/icons/首页-未选中.png">Home</a></li>
            <li><a href="Browser.php"><img class="icon" src="../images/icons/浏览-未选中.png">Browser</a></li>
            <li><a href="Search.php"><img class="icon" src="../images/icons/搜索-未选中.png">Search</a></li>
            <?php LoginOrOut('');?>
        </ul>
    </nav>
</header>
<main>
    <div class="picture">
        <figure>
            <img src="../images/normal/medium/<?php echo $row['PATH']; ?>">
            <figcaption>
                <div class="PicDetails">
                    <h3>Title:</h3>
                    <p class="details"><?php echo $row['Title']; ?></p>
                </div>
                <div class="PicDetails">
                    <h3>Content:</h3>
                    <p class="details"><?php echo $row['Content']; ?></p>
                </div>
                <div class="PicDetails">
                    <h3>City:</h3>
                    <p class="details"><?php echo $row['AsciiName'];?></p>
                </div>
                <div class="PicDetails">
                    <h3>Country:</h3>
                    <p class="details"><?php echo $row['Country_RegionName'];?></p>
                </div>
                <div class="PicDetails">
                    <h3>Photographer:</h3>
                    <p class="details"><?php echo $row['UserName'];?></p>
                </div>
            </figcaption>
        </figure>
    </div>
    <div class="PicDescription">
        <h3>Description：</h3>
        <p><?php echo $row['Description']; ?></p>

    </div>
    <?php
    if($_GET['status'] == 1){
        addTOFavor();
    }
    elseif($_GET['status'] == 0){
        removeFromFavor();
    }
    ?>
    <?php outputKudos($favorRes)?>

</main>
<footer>
    <div class="information">YoYa Zhang版权所有 &copy 2019-2020</div>
    <div class="easterEgg">沪公网安备19302010074号</div>
    <address>Email: <a href = "http://mail.fudan.edu.cn/">19302010074@fudan.edu.cn</a></address>
</footer>
<script type="text/javascript" src="../jquery-3.3.1.min.js"></script>
<script type="text/javascript" src="../JavaScript/LikeOrNot.js"></script>
</body>
</html>
