<?php

    // Escape program (block hacker)
    function h(string $str): string {
        // block sql injection
        $str = str_replace("'", "'", $str);
        $str = str_replace('"', '"', $str);
        // block xss attack
        return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
    }

    // Set timezone
    date_default_timezone_set("Asia/Tokyo");

    // Database values
    $comment_array = array(); /* arrary = list */
    
    $pdo = null;
    $stmt = null;
    $error_msg = array();
    

    // Coonet DB
    try{
        /* PDO('mysql:host=localhost;dbname=your_database_name', "login_user", "user_passwd") */
        $pdo = new PDO('mysql:host=localhost;dbname=bbs-tutorial', "root", "");
    } catch (PDOException $e) {
        // If error echo error
        echo $e->getMessage();
    }

    // If isn't empty = true
    $submit_status = $_POST["submitButton"];
    if(!empty($submit_status)){

        $postDate = date("Y-m-d H:i:s");

        // This get from name tag
        // $_POST = formからデータを取得
        // formのmethod=POSTなので取得できる
        $input_username = h($_POST["username"]);
        $input_comment = h($_POST["comment"]);

        if(empty($input_username)){
            $errorMsg_username = "You need to write username";
            $error_msg["username"] = $errorMsg_username;
        }

        if(empty($input_comment)){
            $errorMsg_comment = "You need to write comment";
            $error_msg["comment"] = $errorMsg_comment;
        }

       // If no error msg
       if(empty($error_msg)){
            try{
                // INSERT data to sql
                // set sql cmd
                $stmt = $pdo->prepare("INSERT INTO `bbs-table` ( `username`, `comment`, `postDate`) VALUES (:username, :comment, :postDate);");

                // add data
                $stmt->bindParam(":username", $input_username);
                $stmt->bindParam(":comment", $input_comment);
                $stmt->bindParam(":postDate", $postDate);

                // Run save data
                $stmt->execute();
            } catch (PDOException $e) {
                echo $e->getMessage();
            }
        }else{
            foreach($error_msg as $err):
                echo "<script> alert("."'".$err."'"."); </script>";
            endforeach;
        }

    }

    // Get data from database
    $sql = "SELECT `id`, `username`, `comment`, `postDate` FROM `bbs-table`;";
    $comment_array = $pdo->query($sql);

    // End database conection
    $pdo = null;


?>




<!--  Start html  -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP message board</title>
    <link rel="stylesheet" href="./style.css">
</head>
<body>
    <h1 class="title">PHP message board</h1>
    <hr>
    <div class="boardWrapper">
        <section>

            <!--  for comment in comment_array:  (python)  -->
            <?php foreach($comment_array as $comment): ?>

            <article>
                <div class="wrapper">
                    <div class="nameArea">
                        <span>Name:</span>
                        <p class="username"><?php echo $comment["username"]; ?></p>
                        <time>:<?php echo $comment["postDate"]; ?></time>
                    </div>
                </div>
                <p class="comment"><?php echo $comment["comment"]; ?></p>
            </article>

            <!-- You need to end for -->
            <?php endforeach; ?>

        </section>

        <form class="formWrapper" method="POST">
            <div>
                <input type="submit" value="Submit" name="submitButton">
                <label for="">Name :</label>
                <input type="text" name="username">
            </div>
            <div>
                <textarea name="comment" class="commentTextArea"></textarea>
            </div>
        </form>
    </div>
</body>
</html>
