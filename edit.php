<?php
require_once('config.php');
require_once('functions.php');

$id = $_GET['id'];

$dbh = connectDb();

$sql = 'SELECT * FROM plans WHERE id = :id';
$stmt = $dbh->prepare($sql);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();

$plan = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $due_date = $_POST['due_date'];
    $errors = [];

    if ($title == '') {
        $errors['title'] = '学習内容を入力してください';
    }

    if ($due_date == '') {
        $errors['due_date'] = '期限日を入力してください';
    }

    if ($title == $plan['title'] && $due_date == $plan['due_date']) {
        $errors['title&due_date'] = '変更内容がありません';
    }

    if (!$errors) {
        $sql = 'UPDATE plans SET title = :title, due_date = :due_date WHERE id = :id';
        
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_STR);
        $stmt->bindParam(':title', $title, PDO::PARAM_STR);
        $stmt->bindParam(':due_date', $due_date, PDO::PARAM_STR);
        $stmt->execute();

        header('Location: index.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>編集画面</title>
</head>

<body>
    <h1>編集</h1>
    <div>
        <form action="" method="post">
            学習内容: <input type="text" name="title" value="<?= h($plan['title']) ?>"><br>
            期限日: <input type="date" name="due_date" value="<?= h($plan['due_date']) ?>">
            <input type="submit" value="追加">
            <span style="color:red;">
                <?php if ($errors) : ?>
                    <ul>
                        <?php foreach ($errors as $error) : ?>
                            <li><?= h($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </span>
        </form>
    </div>
    <a href="index.php">戻る</a>
</body>

</html>