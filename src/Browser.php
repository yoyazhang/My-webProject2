<?php
session_start();
require_once('../config.php');
include_once('outputNavLink.php');
include_once('outputPageBrowser.php');
include_once('outputCouReg.php');

function outputBrowserPics() {
    $query = $_GET['query'];
    $CityCode = $_GET['Cities'];
    $ISO = $_GET['CouRegs'];
    $content = $_GET['Content'];

    if($query==null && $CityCode == 'default'&&$ISO == 'default'&&$content=='default'){
        return;
    }elseif($query == null){
        $mode = 'multiple';
    }else{
        $mode = 'single';
    }

    try {
        $pdo = new PDO(DBCONNSTRING,DBUSER,DBPASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        if(isset($_GET['page']) ){
            $page = intval( $_GET['page'] );
        }
        else{
            $page = 1;
        }
        if($mode == 'single'){
            if($_GET['way']== 'city'){
                $numSQL ='select COUNT(*) AS amount from travelimage WHERE CityCode = :query';
            }elseif($_GET['way']== 'countryRegion'){
                $numSQL ='select COUNT(*) AS amount from travelimage WHERE Country_RegionCodeISO = :query';
            }elseif($_GET['way']== 'content'){
                $numSQL ='select COUNT(*) AS amount from travelimage WHERE Content = :query';
            }elseif($_GET['way'] == 'Filter'){
                $numSQL ='select COUNT(*) AS amount from travelimage WHERE Title like "%'.$query.'%"';
            }
        }else{
            $numSQL ='select COUNT(*) AS amount from travelimage WHERE ';
            $numSQL = getSQL($numSQL,$content,$CityCode,$ISO);

        }

        $numRes = $pdo->prepare($numSQL);
        if($mode == 'single' && $_GET['way']!='Filter'){
            $numRes->bindValue(':query',$query);
        }

        $numRes->execute();
        $row = $numRes->fetch();
        $amount = $row['amount'];
        $currentPageAmount = $amount - ($page-1)*9;

        $PageSize = 9;
        $rowSize = 3;
        if( $amount ){
            if( $amount % $PageSize ){                                  //取总数据量除以每页数的余数
                $totalPage = (int)($amount / $PageSize) + 1;           //如果有余数，则页数等于总数据量除以每页数的结果取整再加一
            }else{
                $totalPage = $amount / $PageSize;                      //如果没有余数，则页数等于总数据量除以每页数的结果
            }
        }
        else{
            $totalPage = 0;
        }
        if($amount == 0){
            echo '<script type="text/javascript">alert("Oops! Nothing is found,change some keywords and try again");</script>';
            return;
        }
        if($page == $totalPage){
            if( $currentPageAmount % $rowSize ){
                $totalRow = (int)($currentPageAmount / $rowSize) + 1;
                $hasMore = true;
                $moreNum = $currentPageAmount % $rowSize;
            }else{
                $totalRow = $currentPageAmount / $rowSize;
                $hasMore = false;
            }
        }
        else{
            if( $currentPageAmount % $rowSize ){
                $totalRow = (int)(9 / $rowSize) + 1;
                $hasMore = true;
                $moreNum = 9 % $rowSize;
            }else{
                $totalRow = 9 / $rowSize;
                $hasMore = false;
            }
        }
        $startNum = 9*($page-1);
        if($mode == 'single'){
            if($_GET['way']== 'city'){
                $sql ='select ImageID,PATH from travelimage WHERE CityCode = :query LIMIT '.$startNum.',9';
            }elseif($_GET['way']== 'countryRegion'){
                $sql ='select ImageID,PATH from travelimage WHERE Country_RegionCodeISO = :query LIMIT '.$startNum.',9';
            }elseif($_GET['way']== 'content'){
                $sql ='select ImageID,PATH from travelimage WHERE Content = :query LIMIT '.$startNum.',9';
            }elseif($_GET['way'] == 'Filter'){
                $sql ='select ImageID,PATH from travelimage WHERE Title like "%'.$query.'%" LIMIT '.$startNum.',9';
            }
        }
        else{
            $sql ='select ImageID,PATH from travelimage WHERE ';
            $sql = getSQL($sql,$content,$CityCode,$ISO);
            $sql .= ' LIMIT '.$startNum.',9';
        }

        $result = $pdo->prepare($sql);
        if($mode == 'single'&&$_GET['way']!='Filter'){
            $result->bindValue(':query',$query);
        }

        $result->execute();

        outputTable($totalRow,$moreNum,$hasMore,$result);
        if($mode == 'single'){
            outputPageLink($page,$totalPage,$_GET['way'],$query);
        }else{
            outputMulPage($page,$totalPage,$content,$CityCode,$ISO);
        }
        $pdo = null;
    }catch (PDOException $e) {
        die( $e->getMessage() );
    }
}

function getSQL($originalSQL,$content,$CityCode,$ISO){
    if($content != 'default'){
        $originalSQL .= 'Content = "'.$content.'"';
        if($ISO != 'default'){
            $originalSQL .= ' AND Country_RegionCodeISO = "'.$ISO.'"';
            if($CityCode != 'default'){
                $originalSQL .= ' AND CityCode = "'.$CityCode.'"';
            }
        }
        elseif($CityCode != 'default'){
            $originalSQL .= ' AND CityCode = "'.$CityCode.'"';
        }
    }
    else{
        $originalSQL .= 'Country_RegionCodeISO = "'.$ISO.'"';
        if($CityCode != 'default'){
            $originalSQL .= ' AND CityCode = "'.$CityCode.'"';
        }
    }
    return $originalSQL;
}
function outputTable($totalRow,$moreNum,$hasMore,$result){
    echo '<section class="results">';
    echo '<table id="BrowserPic">';
    for($i = 0;$i < $totalRow-1; $i++){
        echo '<tr>';
        for($j = 0;$j < 3;$j++){
            $row = $result->fetch();
            outputSinglePic($row);
        }
        echo '</tr>';
    }
    if($hasMore){
        echo '<tr>';
        for($j = 0;$j < $moreNum;$j++){
            $row = $result->fetch();
            outputSinglePic($row);
        }
        echo '</tr>';
    }
    else{
        echo '<tr>';
        for($j = 0;$j < 3;$j++){
            $row = $result->fetch();
            outputSinglePic($row);
        }
        echo '</tr>';
    }
    echo '</table>';
    echo '</section>';
}
function outputSinglePic($row) {
    echo '<td>';
    $img = '<img class="normalPic" src="../images/normal/medium/'.$row['PATH'].'">';
    echo constructPicLink($row['ImageID'], $img);
    echo '</td>';
}
function outputHotContent(){
    try {
        $pdo = new PDO(DBCONNSTRING,DBUSER,DBPASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = 'SELECT Content,COUNT(Content) AS instnum FROM travelimage GROUP BY Content ORDER BY instnum DESC LIMIT 0,5';
        $result = $pdo->query($sql);

        while($row = $result->fetch()){
            echo '<li>';
            echo  constructFilterLink('content',$row['Content'],$row['Content']);
            echo '</li>';
        }

        $pdo = null;
    }catch (PDOException $e) {
        die( $e->getMessage() );
    }
}
function outputHotCities(){
    try {
        $pdo = new PDO(DBCONNSTRING,DBUSER,DBPASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = 'SELECT AsciiName,GeoNameID,COUNT(travelimage.CityCode) AS instnum FROM geocities  LEFT JOIN travelimage ON travelimage.CityCode =geocities.GeoNameID GROUP BY GeoNameID ORDER BY instnum DESC LIMIT 0,6';
        $result = $pdo->query($sql);

        while($row = $result->fetch()){
            echo '<li>';
            echo  constructFilterLink('city',$row['GeoNameID'],$row['AsciiName']);
            echo '</li>';
        }

        $pdo = null;
    }catch (PDOException $e) {
        die( $e->getMessage() );
    }
}
function outputHotCouReg(){
    try {
        $pdo = new PDO(DBCONNSTRING,DBUSER,DBPASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = 'SELECT Country_RegionName,ISO,COUNT(travelimage.Country_RegionCodeISO) AS instnum FROM geocountries_regions LEFT JOIN travelimage ON travelimage.Country_RegionCodeISO =geocountries_regions.ISO GROUP BY ISO ORDER BY instnum DESC LIMIT 0,4';
        $result = $pdo->query($sql);
        while($row = $result->fetch()){
            echo '<li>';
            echo  constructFilterLink('countryRegion',$row['ISO'],$row['Country_RegionName']);
            echo '</li>';
        }
        $pdo = null;
    }catch (PDOException $e) {
        die( $e->getMessage() );
    }
}

function constructFilterLink($way,$query,$label){
    $link = '<a href="../src/Browser.php?way='.$way.'&query='.$query.'">';
    $link .= $label;
    $link .= '</a>';
    return $link;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>浏览页</title>
    <link href="../CSS/reset.css" rel="stylesheet">
    <link href="../CSS/HeaderNavMainFooterPic.css" rel="stylesheet">
    <link href="../CSS/Browser.css" rel="stylesheet">
</head>
<body onload="squareClip()" onresize="squareClip()">
<header>
    <div class="title">
        <h1>Travel To Share</h1>
        <div class = "slogan">See the World outside!</div>
    </div>
    <nav>
        <ul id="navPublic">
            <li><a href="../index.php"><img class="icon" src="../images/icons/首页-未选中.png">Home</a></li>
            <li><a href="Browser.php" id="currentPage"><img class="icon" src="../images/icons/浏览-选中.png">Browser</a></li>
            <li><a href="Search.php"><img class="icon" src="../images/icons/搜索-未选中.png">Search</a></li>
            <?php LoginOrOut('Browser');?>
        </ul>
    </nav>
</header>
<main>
    <aside>
        <div id="FilterSearch">
            <form>
                <h2>Filter</h2>
                <input type="text" name="query" id="nameText"><input type="submit" name="way" value="Filter" id="nameSearch">
            </form>
        </div>
        <div id="HotContent">
            <h2>Hot Content</h2>
            <ul><?php outputHotContent();?></ul>
        </div>
        <div id="HotCountry">
            <h2>Hot Countries</h2>
            <ul><?php outputHotCouReg();?></ul>
        </div>
        <div id="HotCity">
            <h2>Hot Cities</h2>
            <ul><?php outputHotCities();?></ul>
        </div>
    </aside>
    <div class="FilterResults">
        <section class="filter">
            <form action="" method="get" role="form">
                <fieldset>
                    <legend>Filter</legend>
                    <select name="Content" required>
                        <option value="default" selected>-Content-</option>
                        <option value="scenery">Scenery</option>
                        <option value="city">City</option>
                        <option value="people">People</option>
                        <option value="animal">Animal</option>
                        <option value="building">Building</option>
                        <option value="wonder">Wonder</option>
                        <option value="other">Other</option>
                    </select>
                    <select name="CouRegs" id="Countries" onchange="setCity(this, this.form.Cities)" required>
                        <option value="default" selected>-Countries-</option>
                        <?php outputCouRegBro();?>
                    </select>
                    <select name="Cities" id="Cities">
                        <option value="default" selected>-Cities-</option>
                    </select>
                    <input type="submit" name="filter" value="FILTER">
                </fieldset>
            </form>
        </section>
        <?php if($_GET['query'] != null || $_GET['Cities']!= null) outputBrowserPics(); ?>
    </div>
</main>
<footer>
    <div class="information">YoYa Zhang版权所有 &copy 2019-2020</div>
    <div class="easterEgg">沪公网安备19302010074号</div>
    <address>Email: <a href = "http://mail.fudan.edu.cn/">19302010074@fudan.edu.cn</a></address>
</footer>
<script type="text/javascript" src="../jquery-3.3.1.min.js"></script>
<script type="text/javascript" src="../JavaScript/ImgClip.js"></script>
<script type="text/javascript" src="../JavaScript/Filter.js"></script>
</body>
</html>
