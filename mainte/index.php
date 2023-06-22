<?php
    require "db_connect.php";

    // ユーザー入力無し query
    $sql = "select * from contacts where id = 1"; // SQL文
    $stmt = $pdo->query($sql); // SQL文の実行結果が$stmtに入る　ステートメントの略です
    $result = $stmt->fetchall(); // fetchallで全件取得

    echo "<pre>";
    var_dump($result);
    echo "</pre>";

    // ユーザー入力有り prepare, bind, execute
    $sql = "select * from contacts where id = :id"; // SQL文 プレースホルダ
    $stmt = $pdo->prepare($sql); // プリペアードステートメント
    $stmt->bindValue("id", 2, PDO::PARAM_INT); // 紐付け
    $stmt->execute(); // 実行
    $result = $stmt->fetchall(); // 全件取得

    echo "<pre>";
    var_dump($result);
    echo "</pre>";

    // トランザクション
    $pdo->beginTransaction(); // トランザクション開始
    try{
        $stmt = $pdo->prepare($sql); // プリペアードステートメント
        $stmt->bindValue("id", 3, PDO::PARAM_INT); // 紐付け
        $stmt->execute(); // 実行
        $pdo->commit(); // コミット
    }catch(PDOException $e){
        $pdo->rollback(); // ロールバック
    }
    
?>