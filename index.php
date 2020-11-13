<?php

require_once('config.php');
require_once('functions.php');

$dbh = connectDb();

$sql = "SELECT * FROM plans WHERE status = 'notyet' ORDER BY due_date ASC";
$stmt = $dbh->prepare($sql);
$stmt->execute();
$notyet_plans = $stmt->fetchAll(PDO::FETCH_ASSOC);

$sql2 = "SELECT * FROM plans WHERE status = 'done' ORDER BY due_date DESC";
$stmt = $dbh->prepare($sql2);
$stmt->execute();
$done_plans = $stmt->fetchAll(PDO::FETCH_ASSOC);

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

    if (!$errors) {
        $sql = 'INSERT INTO plans (title, due_date) VALUES (:title, :due_date)';

        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':title', $title, PDO::PARAM_STR);
        $stmt->bindParam(':due_date', $due_date, PDO::PARAM_STR);
        $stmt->execute();

        header('Location: index.php');
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET')  {
    $id = $_GET['id'];

    $sql = "UPDATE plans SET status = 'done' WHERE id = :id";

    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    if (isset($id)) {
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
    <title>タスク管理アプリ 課題</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <h1>学習管理アプリ</h1>
    <div>
        <form action="" method="post">
            学習内容: <input type="text" name="title"><br>
            期限日: <input type="date" name="due_date">
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

    <h1>未達成</h1>
    <ul>
        <?php foreach ($notyet_plans as $plan) : ?>
            <?php if (date('Y-m-d') >= $plan['due_date']) : ?>
                <li class="expired">
                    <a href="index.php?id=<?= h($plan['id']) ?>">[完了]</a>
                    <a href="edit.php?id=<?= h($plan['id']) ?>">[編集]</a>
                    <?= h($plan['title']) ?>…完了期限: <?= h(date('Y/m/d', strtotime($plan['due_date']))) ?>
                </li>
            <?php else : ?>
                <li>
                    <a href="index.php?id=<?= h($plan['id']) ?>">[完了]</a>
                    <a href="edit.php?id=<?= h($plan['id']) ?>">[編集]</a>
                    <?= h($plan['title']) ?>…完了期限: <?= h(date('Y/m/d', strtotime($plan['due_date']))) ?>
                </li>
            <?php endif; ?>
        <?php endforeach; ?>
    </ul>
    <hr>

    <h1>達成済み</h1>
    <ul>
        <?php foreach ($done_plans as $plan) : ?>
            <li>
                <?= h($plan['title']) ?>
            </li>
        <?php endforeach; ?>
    </ul>
</body>

</html>