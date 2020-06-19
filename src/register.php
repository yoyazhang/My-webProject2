<?php
session_start();
require_once('../config.php');
require_once('../phpass-0.5/PasswordHash.php');

function validRegister(){
    $pdo = new PDO(DBCONNSTRING,DBUSER,DBPASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = 'SELECT UserName FROM traveluser';
    $result = $pdo->query($sql);
    while($row = $result->fetch()){
        if($row['UserName'] == $_POST['username']){
            echo '<script type="text/javascript">
              alert("已经有人用过这个名字啦，换一个吧~");
              </script>';
            return false;
        }
    }
    $pdo = null;
    return true;
}

function addUser(){
    $pdo = new PDO(DBCONNSTRING,DBUSER,DBPASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //count number of users
    $sqlNumber = 'SELECT COUNT(*) AS UserNum FROM traveluser';
    $result = $pdo->query($sqlNumber);
    $numRes = $result->fetch();
    $hasher = new PasswordHash(8, false);
    $hashedPassword = $hasher->HashPassword($_POST['password']);

    $sqlAddUser = "INSERT INTO traveluser VALUES (:UID,:email,:username,:pass,'1',:dateJoined,:lastModified)";
    $stmAddUser = $pdo->prepare($sqlAddUser);
    $stmAddUser->bindValue(':UID',$numRes['UserNum']+1);
    $stmAddUser->bindValue(':username',$_POST['username']);
    $stmAddUser->bindValue(':pass',$hashedPassword);
    $stmAddUser->bindValue(':email',$_POST['email']);
    $presentDate = date("Y-m-d H:i:s");
    $lastModifiedDate = date("Y-m-d H:i:s");
    $stmAddUser->bindValue(':dateJoined',$presentDate);
    $stmAddUser->bindValue(':lastModified',$lastModifiedDate);
    $stmAddUser->execute();

    $pdo = null;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>注册界面</title>
    <link href="../CSS/reset.css" rel="stylesheet">
    <link href="../CSS/Registry.css" rel="stylesheet">
</head>
<body>
<header>
    <h1>TRAVEL TO SHARE</h1>
    <div class = "slogan">See the World outside!</div>
</header>
<?php
if($_SERVER["REQUEST_METHOD"] == "POST"){
    if(validRegister()){
        addUser();
        echo '<script type="text/javascript">
              alert("注册成功！欢迎加入TTS的大家庭~");
              window.location.href="login.php";
              </script>';
    }
}
?>
<main>
    <h2>We have been waiting for you long!</h2>
    <form method="post" action="" role="form">
        <fieldset>
            <legend>REGISTER</legend>
            <div class="user">
                <label>
                    <img class="icon" src="../images/icons/用户.png">USER:
                    <input type="text" name="username" required>
                </label>
            </div>
            <div class="email">
                <label>
                    <img class="icon" src="../images/icons/邮箱.png">EMAIL:
                    <input type="email" name="email" required>
                </label>
            </div>
            <div class="password">
                <label>
                    <img class="icon" src="../images/icons/密码.png">PASSWORD:
                    <input type="password" name="password" required>
                </label><br>
            </div>
            <div class="password">
                <label>
                    PASSWORD DOUBLE-CHECK:
                    <input type="password" name="password2" required>
                </label>
            </div>
            <input type="submit" name="registrySubmit" value="SUBMIT">
        </fieldset>
    </form>
</main>
<footer>
    <div class="information">YoYa Zhang版权所有 &copy 2019-2020</div>
    <div class="easterEgg">沪公网安备19302010074号</div>
    <address>Email: <a href = "http://mail.fudan.edu.cn/">19302010074@fudan.edu.cn</a></address>
</footer>
<script type="text/javascript" src="../jquery-3.3.1.min.js"></script>
<script type="text/javascript" src="../JavaScript/verifyRegistry.js"></script>
</body>
</html>
