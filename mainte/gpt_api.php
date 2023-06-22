<?php
function gpt_api($messages, $api_key) {
    // OpenAI API URL
    $url = "https://api.openai.com/v1/chat/completions";
    // リクエストヘッダー
    $headers = array(
        "Content-Type: application/json",
        "Authorization: Bearer " . $api_key
    );

    // リクエストボディ
    $data = array(
        "model" => "gpt-3.5-turbo",
        "messages" => $messages,
        "max_tokens" => 256, // 応答の最大トークン数（≒文字数）を設定
    );

    // cURLを初期化
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // APIにリクエストを送信し、応答を取得
    $response = curl_exec($ch);

    // cURLを閉じる
    curl_close($ch);

    return $response;
}

?>