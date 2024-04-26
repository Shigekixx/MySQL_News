<?php
    $pdo = new PDO("mysql:host=localhost;dbname=Laravel_News;charaset=utf8","root","root");

    $id = $_GET['id'];//idを変数で置く
    //コメントを入力

    //サーバーのMETHODがPOSTのときの処理だよっていう目印
    if ($_SERVER["REQUEST_METHOD"]==="POST") {

        $comment = $_POST ["comment"];  //コメントのこと
        $limitC = 50; //コメントの文字数制限
        $errMsg = ''; // エラーメッセージ用変数
        // 入力された文字列の長さを取得する
        $titleLength = strlen($comment);
        
        //どっちも中身が埋まっていた場合
        if(empty($comment)){
            $errMsg = 'コメントが空欄です';
            echo $errMsg ;
        } else if ($limitC < $titleLength ){    //タイトルが長すぎる場合
            $errMsg = 'タイトルは50文字以下で表示してください';
            echo $errMsg;
        } else {
                
            // INSERT文を変数に格納
            // あらかじめMySQL内にテーブルとカラムを作成しておく必要がある
            $sql_register = "INSERT INTO news_comments (news_id,comments)  VALUES (:news_id,:comment)";
            //挿入する値は空のまま、SQL実行の準備をする
            $stmt = $pdo->prepare($sql_register);
            // 挿入する値を配列に格納する
            $params = array(':news_id'=> $id ,':comment' => $comment);
            //挿入する値が入った変数をexecuteにセットしてSQLを実行
            $stmt->execute($params);

            //リロードで再度送信はこれで解消
            header("location: News.php?id=$id");
             exit;
        }
    } else {

    } 
    // コメント削除機能
    // if (isset($_GET['deleteCommentId'])) {
    // $deleteCommentId = $_GET['deleteCommentId'];

    // 削除するコメントの投稿IDを取得し、それに紐づく投稿IDを特定
    $stmt = $pdo->prepare("SELECT news_id FROM news_comments WHERE id = :commentId");
    $stmt->execute([':commentId' => $deleteCommentId]);
    $comment = $stmt->fetch(PDO::FETCH_ASSOC);
    $currentPostId = $comment ? $comment['news_id'] : '';

    if ($currentPostId) {
        // コメントを削除する
        $stmt = $pdo->prepare("DELETE FROM comments WHERE id = :commentId");
        $stmt->execute([':commentId' => $deleteCommentId]);
        header("Location: News.php?id=$id");
        exit();
    }

?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">     
        <title>Document</title>
    </head>
    <body>
        <h1> <a href="index.php"> Laravel_News</a></h1>
        <h1>投稿詳細</h1>

       <!-- 投稿一覧での表示の仕方、ニュース詳細画面にリンクするためにどうすべきか -->
       <p>
            <?php 
            $sql_id = "SELECT id,title,news FROM news WHERE id =  $id ";

            // SQLステートメントを実行し、結果を変数に格納
            $stmt = $pdo->query($sql_id);
            // foreach文で配列の中身を一行ずつ出力
            foreach ($stmt as $row) {
            
            // データベースのフィールド名で出力
            echo $row['title'].'<br>'. $row['news'];
            
            // 改行を入れる
            echo '<br>';
            } ?> 
        </p> 

        <!-- コメント入力フォーム -->
        <form action = 'News.php?id=<?php echo $id; ?>' method = "post">
            <label for ="comment">コメント</label>
            <input type ="text" id ="comment" name ="comment">
            <br>
            <button type="submit">投稿</button>
        </form>

        <!-- コメントの表示 -->
        <!-- コメントと記事をリンクさせたい 表示方法も考えたい -->
        <p><?php $sql = "SELECT comments FROM news_comments WHERE news_id = $id ORDER BY id DESC ";
        
        // SQLステートメントを実行し、結果を変数に格納
        $stmt = $pdo->query($sql);
        
        // foreach文で配列の中身を一行ずつ出力
        foreach ($stmt as $row) {
        
        // データベースのフィールド名で出力
        echo  "<p> コメント:" .$row['comments']."</p>";
        
        // 改行を入れる
        echo '<br>';
        } ?> 

        </p> 
    <?php
        if (empty($params)) {
            echo "<p>まだコメントがありません。</p>";
        } else {
            $index = 0;
            while ($index < count($params)) {
                $comment = $params[$index];
                echo "<p>".$comment['commentText']."<a href=' News.php?id=$id"."&deleteCommentId=".$comment['id']."' onclick='return confirm(\"本当に削除しますか？\");'>削除</a></p>";
                $index++;
            }
        }
    ?>
    </body>

</html>
