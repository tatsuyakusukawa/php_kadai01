<?php
    session_start();
    $contact_array = array();

    require "validation.php";
    require "gpt_api.php";

    header("X-FRAME-OPTIONS:DENY");    

    function h($str){
        return htmlspecialchars($str,ENT_QUOTES,"UTF-8");
    }

        $pageFlag = 0;
        $errors = validation($_POST);
    
    
        if(!empty($_POST["confirm_button"]) && empty($errors)){
        $pageFlag = 1;

    }
    if(!empty($_POST["submit_button"])){
        $pageFlag = 2;
        try{
            $pdo = new PDO('mysql:dbname=kadai1;charset=utf8;host=localhost','root','');
        }catch(PDOException $e){
            echo $e->getMessage();
        }

        // DBにデータを保存
        $sql = "INSERT INTO `contacts` (`username`, `email`, `url`, `gender`, `age`, `contact`, `created_at`) VALUES (:username, :email, :url, :gender, :age , :contact, current_timestamp())" ;
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':username',$_POST["username"]);
        $stmt->bindValue(':email',$_POST["email"]);
        $stmt->bindValue(':url',$_POST["url"]);
        $stmt->bindValue(':gender',$_POST["gender"]);
        $stmt->bindValue(':age',$_POST["age"]);
        $stmt->bindValue(':contact',$_POST["contact"]);
        $stmt->execute();
        $pdo = null;


        $api_key =  "hoge"; // ここにAPIキーを入力
        $messages = array(
            array("role" => "system", "content" => "あなたは問い合わせ対応のプロです。優しく丁寧に対応してくださいね。"),
            array("role" => "user", "content" => h($_POST["contact"]))
        );

        $response = gpt_api($messages, $api_key);
        $response_decoded = json_decode($response, true);

        gpt_api($messages, $api_key);    


    }

    // DB接続

    try{
        $pdo = new PDO('mysql:dbname=kadai1;charset=utf8;host=localhost','root','');
    }catch(PDOException $e){
        echo $e->getMessage();
    }  

    // DBからデータを取得
    $sql = "SELECT * FROM `contacts`;";
    $contact_array = $pdo->query($sql);
    $pdo = null;

    ?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <title>Hello, world!</title>
  </head>
<body>

    <?php if($pageFlag === 0) : ?>
        <?php
            if(!isset($_SESSION["token"])){
                $token = bin2hex(random_bytes(16));
                $_SESSION["token"] = $token;
            }
            $token = $_SESSION["token"];
        ?>

        <?php if(!empty($errors) && !empty($_POST["confirm_button"])) : ?>
            <?php echo "<ul>"; ?>
            <?php
                foreach($errors as $error){
                    echo "<li>".$error."</li>";
                }
            ?>
            <?php echo "</ul>"; ?>
        <?php endif; ?>

        <div class="containar">
            <div class="row">
            <div class="col-md-6">
                <form method="POST" action="input_to_gpt.php" >
                <diV class="form-group"> 
                    <label for="username">氏名</label>
                    <input type="text" class="form-control" id="username" name="username" value="<?php if(!empty($_POST["username"])){echo h($_POST["username"]);} ?>">
                </diV>
                <diV class="form-group"> 
                    <label for="email">メールアドレス</label>
                    <input type="text" class="form-control" id="email" name="email" value="<?php if(!empty($_POST["email"])){echo h($_POST["email"]);} ?>">
                </diV>
                <diV class="form-group"> 
                    <label for="url">ホームページ</label>
                    <input type="text" class="form-control" id="url" name="url" value="<?php if(!empty($_POST["url"])){echo h($_POST["url"]);} ?>">
                </diV>

                性別
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="gender" id="gender1" value="0" <?php
                            if(!empty($_POST["gender"]) && $_POST["gender"]===  "0")
                            {echo `checked`;}
                        ?>>

                        <label class="form-check-label" for="gender1">男性</label>

                    </div>

                    <div class="form-check form-check-inline">

                        <input class="form-check-input" type="radio" name="gender" id="gender2" value="1" <?php
                            if(!empty($_POST["gender"]) && $_POST["gender"]===  "1")
                            {echo `checked`;}
                        ?>>
                        
                        <label class="form-check-label" for="gender2">女性</label>
                    
                        
                    </div>
                
                <br>
                <div class="form-group">
                    <label for="age">年齢</label>
                <select class="form-control" name="age">
                    <option value="">選択してください</option>
                    <option value="1">〜19歳</option>
                    <option value="2">20歳〜29歳</option>
                    <option value="3">30歳〜39歳</option>
                    <option value="4">40歳〜49歳</option>
                    <option value="5">50歳〜59歳</option>
                    <option value="6">60歳〜</option>
                </select>
                
                </div>
                
                <br>

                <div class="form-group"> 
                    <label for="contact">お問い合わせ内容</label>
                    <textarea name="contact" id="" cols="3" rows="10" class="form-control"><?php if(!empty($_POST["contact"])){echo h($_POST["contact"]);} ?></textarea>
                </diV>
                

                <br>
                <div class="form-group">
                    <input class="form-control-input" type="checkbox" name="caution" value="1">
                    <label class="form-control-input" for="caution">注意事項にチェックする</label>
                </div>
                <input class="btn btn-primary" type="submit" name="confirm_button" value="確認画面へ進む">
                <input type="hidden" name="token" value="<?php echo $token; ?>">
            </form>
        </div>
    </div>
    </div>


    <?php endif; ?>




    <?php if($pageFlag === 1) : ?>
        <?php
            if($_POST["token"] !== $_SESSION["token"]){
                echo "不正な投稿です";
                exit();
            }
        ?>

        確認画面
        <form method="POST" action="input_to_gpt.php" >
        <input type="submit" name="back" value="戻る">

        氏名：<?php echo h($_POST["username"]); ?><br>
        email：<?php echo h($_POST["email"]); ?><br>
        ホームページ：<?php echo h($_POST["url"]); ?><br>
        性別:<?php 
            if($_POST["gender"] === "0"){echo "男性";}
            if($_POST["gender"] === "1"){echo "女性";}
        ?><br>
        年齢：<?php 
            if($_POST["age"] === "1"){echo "〜19歳";}
            if($_POST["age"] === "2"){echo "20歳〜29歳";}
            if($_POST["age"] === "3"){echo "30歳〜39歳";}
            if($_POST["age"] === "4"){echo "40歳〜49歳";}
            if($_POST["age"] === "5"){echo "50歳〜59歳";}
            if($_POST["age"] === "6"){echo "60歳〜";}

        ?><br>
        お問い合わせ内容：<?php echo h($_POST["contact"]); ?><br>
        注意事項：<?php if($_POST["caution"] === "1"){echo "了承しました";}else{echo "了承していません";} ?><br>

        <input type="submit" name="submit_button" value="送信する">
        <input type="hidden" name="username" value="<?php echo h($_POST["username"]); ?>">
        <input type="hidden" name="email" value="<?php echo h($_POST["email"]);  ?>">                                                          
        <input type="hidden" name="url" value="<?php echo h($_POST["url"]); ?>">
        <input type="hidden" name="gender" value="<?php echo h($_POST["gender"]); ?>"> 
        <input type="hidden" name="age" value="<?php echo h($_POST["age"]); ?>"> 
        <input type="hidden" name="contact" value="<?php echo h($_POST["contact"]); ?>"> 
        <input type="hidden" name="caution" value="<?php echo h($_POST["caution"]); ?>"> 
        
        <input type="hidden" name="token" value="<?php echo h($_POST["token"]); ?>">
        </form>
    <?php endif; ?>

    <?php if($pageFlag === 2) : ?>
        <?php
            if($_POST["token"] !== $_SESSION["token"]){
                echo "不正な投稿です";
                exit();
            }
        ?>



    <p>送信が完了しました。 </p>
    <p>お問い合わせ内容については、3営業日以内にご返信いたします。  </p>

    <p> お問い合わせ内容：<?php echo h($_POST["contact"]); ?></p>
    <p>AIの回答:<?php echo  $response_decoded["choices"][0]["message"]["content"] . "\n"; ?></p>
    

    <?php unset($_SESSION["token"]); ?>

    <?php endif; ?>




<!-- Optional JavaScript; choose one of the two! -->

    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

    <!-- Option 2: Separate Popper and Bootstrap JS -->
    <!--
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
    -->


</body>
</html>