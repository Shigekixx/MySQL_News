<?php
    $pdo = new PDO("mysql:host=localhost;dbname=Laravel_News;charaset=utf8","root","root");

    //サーバーのMETHODがPOSTのときの処理だよっていう目印
    if ($_SERVER["REQUEST_METHOD"]==="POST") {

        $title = $_POST ["title"];  //タイトルのこと
        $news = $_POST ["news"];  //記事のこと
        
        // 制限値 タイトルを30文字以下にしたい
        $limitA= 30;
        // エラーメッセージ用変数
        $errMsg = '';
        // 入力された文字列の長さを取得する
        $titleLength = strlen($title);

        //どっちも中身が埋まっていない場合
        if(empty($title) && empty($news)){
            $errMsg = 'タイトル・本文が空欄です';
            echo $errMsg ;
        } else if (empty($title)){
            $errMsg = 'タイトルが空欄です';
            echo $errMsg;
        } else if(empty($news)){
            $errMsg = '本文が空欄です';
            echo $errMsg;
        } else if ($limitA < $titleLength ){    //タイトルが長すぎる場合
            $errMsg = 'タイトルは30文字以下で表示してください';
            echo $errMsg;
        } else {

            // INSERT文を変数に格納
            // あらかじめMySQL内にテーブルとカラムを作成しておく必要がある
            $sql = "INSERT INTO news (title, news) VALUES (:title, :news)";
            //挿入する値は空のまま、SQL実行の準備をする
            $stmt = $pdo->prepare($sql);
            // 挿入する値を配列に格納する
            $params = array(':title' => $title, ':news' => $news);
            //挿入する値が入った変数をexecuteにセットしてSQLを実行
            $stmt->execute($params);

            //リロードで再度送信はこれで解消
            header("location:http://localhost:8888/");
            exit;
        }
    } else {
        
    }
    
?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">     
        <title>Document</title>


    <style>
            .dialog::backdrop {
            backdrop-filter: blur(8px);
        }

        .dialog {
            box-shadow: 0px 20px 36px 0px rgba(0, 0, 0, 0.6);
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        @keyframes fadeOut {
            from {
                opacity: 1;
            }
            to {
                opacity: 0;
            }
        }

        .dialog {
            display: block;
            position: fixed;
            inset-inline: 0;
            inset-block: 0;

            animation-name: fadeOut;
            animation-fill-mode: forwards;
            animation-duration: 200ms;
            animation-timing-function: ease-out;
        }

        .dialog[open] {
            animation-name: fadeIn;
            animation-fill-mode: forwards;
            animation-duration: 200ms;
            animation-timing-function: ease-out;
        }
        </style>

    </head>

    <body>

        <!-- //ナビゲーションバーのリンク -->
        <h1><a href="index.php"> Laravel_News</a></h1>
        <h2>さぁ、新しいニュースを投稿しよう</h2>

        <form action = "./" method = "post" onSubmit="return confirmSubmit()">
            <label for ="title">タイトル</label>
            <input type ="text" id ="title" name ="title">
            <br>
            <label for ="news">投稿内容</label>
            <input type ="text" id ="news" name ="news">
            <br>
            <button id="openButton" type="button">投稿</button>
           
            <dialog id="modalDialog" class="dialog">
                <div id="dialog-container">
                    <header>
                    <div>本当に送信しますか？</div>
                    </header>
                    <form method="dialog">
                        <button type="submit" value="OK">Ok</button>
                        <button id="closeButton" type="button" value="CANCEL">Cancel</button>
                        
                    </form>
                </div>
            </dialog>
        </form>

        <h1>投稿一覧</h1> 
        <!-- 投稿一覧での表示の仕方、ニュース詳細画面にリンクするためにどうすべきか -->
        <p><?php $sql = "SELECT id,title,news FROM news ORDER BY id DESC";

            // SQLステートメントを実行し、結果を変数に格納
            $stmt = $pdo->query($sql);
            
            // foreach文で配列の中身を一行ずつ出力
            foreach ($stmt as $row) {
            
            // データベースのフィールド名で出力
            echo "<p><a href='News.php?id=".$row['id']."'>タイトル:" .$row['title']."</a><br>投稿内容:".$row ['news']."</p>";
            
            // 改行を入れる
            echo '<br>';
            } ?> 
        </p> 

        <script>
            const openButton = document.getElementById('openButton');
            const modalDialog = document.getElementById('modalDialog');

            // モーダルを開く
                openButton?.addEventListener('click', async () => {
                modalDialog.removeAttribute("style")

                modalDialog.showModal();
                // モーダルダイアログを表示する際に背景部分がスクロールしないようにする
                document.documentElement.style.overflow = "hidden";
            });
            
                const closeButton = document.getElementById('closeButton');

                // モーダルを閉じる
                closeButton?.addEventListener('click', () => {
                modalDialog.close();
                // モーダルを解除すると、スクロール可能になる
                document.documentElement.removeAttribute("style");
                });

                modalDialog.addEventListener("close", async(e) => {
                // アニメーションが終了すると、スタイルを適用する
                await waitDialogAnimation(e.target)
                modalDialog.style.display = "none"
            })

            // アニメーションが完了するまで待機する
                const waitDialogAnimation = (dialog) => Promise.allSettled(
                Array.from(dialog.getAnimations()).map(animation => animation.finished)
            );

        </script>  
    </body>

</html>

