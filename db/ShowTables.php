<!DOCTYPE html>
<html>
<head>
    <title>Reast_debug Db</title>
    <style>
        table {border-collapse: collapse; }
        table, th, td { border: 1px #000 solid; }
    </style>
</head>
<body>
    <h1>Reast_debug データベース</h1>

    <h2>user</h2>
    <table>
        <tr><th>id</th><th>user_name</th><th>password</th><th>created_at</th></tr>
    <?php
    try {
        // データベース接続
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ];
        $pdo = new PDO('sqlite:./db/blog.db', null, null, $options);

        $selSql = 'SELECT id, user_name, password, created_at FROM user ORDER BY id;';
        // PDO::query SQLを実行して結果をPDOStatementオブジェクトで受け取ります
        $selRet = $pdo->query($selSql);
        // 結果のPDOStatementオブジェクトから全体を配列で受け取ります
        $rows = $selRet->fetchAll();
        foreach ($rows as $row) {
            // 結果を行毎に処理
            $selRow = '<tr>';
            $selRow .= '<td>'.htmlspecialchars($row['id']).'</td>';
            $selRow .= '<td>'.htmlspecialchars($row['user_name']).'</td>';
            $selRow .= '<td>'.htmlspecialchars($row['password']).'</td>';
            $selRow .= '<td>'.htmlspecialchars($row['created_at']).'</td></tr>';
            $selRow .= '<td>'.htmlspecialchars($row['register_token']).'</td></tr>';
            $selRow .= '<td>'.htmlspecialchars($row['register_token_sent_at']).'</td></tr>';
            echo $selRow;
        }
        $selRet = null;
    } catch (Exception $e) {
        echo $e->getMessage();
        print_r($pdo->errorInfo());
    }
    ?>
    </table>

    <h2>status</h2>
    <table>
        <tr><th>id</th><th>user_id</th><th>body</th><th>created_at</th></tr>
    <?php
    try {
        $selSql = 'SELECT id, user_id, body, created_at FROM status ORDER BY created_at;';
        $selRet = $pdo->query($selSql);
        $rows = $selRet->fetchAll();
        foreach ($rows as $row) {
            $selRow = '<tr>';
            $selRow .= '<td>'.htmlspecialchars($row['id']).'</td>';
            $selRow .= '<td>'.htmlspecialchars($row['user_id']).'</td>';
            $selRow .= '<td>'.htmlspecialchars($row['body']).'</td>';
            $selRow .= '<td>'.htmlspecialchars($row['created_at']).'</td></tr>';
            echo $selRow;
        }
        $selRet = null;
    } catch (Exception $e) {
        echo $e->getMessage();
        print_r($pdo->errorInfo());
    }
    ?>
    </table>

    <h2>following</h2>
    <table>
        <tr><th>user_id</th><th>following_id</th><th>created_at</th></tr>
    <?php
    try {
        $selSql = 'SELECT user_id, following_id, created_at
                   FROM following
                   ORDER BY user_id, following_id;';
        $selRet = $pdo->query($selSql);
        $rows = $selRet->fetchAll();
        foreach ($rows as $row) {
            $selRow = '<tr>';
            $selRow .= '<td>'.htmlspecialchars($row['user_id']).'</td>';
            $selRow .= '<td>'.htmlspecialchars($row['following_id']).'</td>';
            $selRow .= '<td>'.htmlspecialchars($row['created_at']).'</td></tr>';
            echo $selRow;
        }
        $selRet = null;
    } catch (Exception $e) {
        echo $e->getMessage();
        print_r($pdo->errorInfo());
    }
    ?>
    </table>
    <h2>Images</h2>
    <table>
        <tr><th>id</th><th>file_name</th><th>uploaded_on</th></tr>
    <?php
    try {
        $selSql = 'SELECT id, file_name, uploaded_on
                   FROM Images
                   ORDER BY id, file_name;';
        $selRet = $pdo->query($selSql);
        $rows = $selRet->fetchAll();
        foreach ($rows as $row) {
            $selRow = '<tr>';
            $selRow .= '<td>'.htmlspecialchars($row['id']).'</td>';
            $selRow .= '<td>'.htmlspecialchars($row['file_name']).'</td>';
            $selRow .= '<td>'.htmlspecialchars($row['uploaded_on']).'</td></tr>';
            echo $selRow;
        }
        $selRet = null;
    } catch (Exception $e) {
        echo $e->getMessage();
        print_r($pdo->errorInfo());
    }
    ?>
    </table>
</body>
</html>