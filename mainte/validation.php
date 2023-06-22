<?php

    function validation($req){
        $errors = [];

        if(empty($req["username"]) || 20 < mb_strlen($req["username"])){
            $errors[] = "名前を入力してください。氏名は20文字以内で入力してください。";
        }
        if(empty($req["email"]) || !filter_var($req["email"],FILTER_VALIDATE_EMAIL)){
            $errors[] = "メールアドレスを正しい形式で入力してください。";
        }
        if(empty($req["url"]) || !filter_var($req["url"],FILTER_VALIDATE_URL)){
            $errors[] = "ホームページを正しい形式で入力してください。";
        if(isset($req["gender"])){
            $errors[] = "性別を選択してください。";
        }
        if(empty($req["age"]) || 6 < $req["age"]){
            $errors[] = "年齢を選択してください。";
        }


        }
        if(empty($req["contact"]) || 200 < mb_strlen($req["contact"])){
            $errors[] = "お問い合わせ内容は200文字以内で入力してください。";
        }
        if(empty($req["caution"]) || $req["caution"] !== "1"){
            $errors[] = "注意事項をご確認ください。";
        }



        return $errors;
    }

?>
