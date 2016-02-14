<?php

$errorMessage = false;

require_once 'config.php';

function createGoalsData($prefix) {
    $data_simple = array(
        'goal' => array(
            'name' => $prefix . '_simple',
            'type' => 'url',
            'is_retargeting' => 0,
            'prev_goal_id' => 0,
            'conditions' => array(array("type"=>"action","url"=>$prefix)),
            'class' => 0,
        )
    );

    yield $data_simple;

    $data_step = array(
        'goal' => array(
            'name' => $prefix . '_step',
            'type' => 'step',
            'is_retargeting' => 0,
            'steps' => array(
                array(
                    'name' => $prefix . '-level1',
                    'type' => 'url',
                    'is_retargeting' => 0,
                    'conditions' => array(array('type' => 'action', 'url' => $prefix . 'level1-url')),
                    'class' => 0
                ),
                array(
                    'name' => $prefix . '-level2',
                    'type' => 'url',
                    'is_retargeting' => 0,
                    'conditions' => array(array('type' => 'action', 'url' => $prefix . 'level2-url')),
                    'class' => 0
                ),
                array(
                    'name' => $prefix . '-level3',
                    'type' => 'url',
                    'is_retargeting' => 0,
                    'conditions' => array(array('type' => 'action', 'url' => $prefix . 'level3-url')),
                    'class' => 0
                ),
            ),
            'class' => 0,
        )
    );

    yield $data_step;
}

function postGoal($data)
{
    global $COUNTER_ID;
    global $AUTH_TOKEN;
    $curl = curl_init("https://api-metrika.yandex.ru/management/v1/counter/{$COUNTER_ID}/goals?oauth_token={$AUTH_TOKEN}");
    curl_setopt_array($curl, array(
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
        CURLOPT_POSTFIELDS => json_encode($data)
    ));
    $response = curl_exec($curl);
    curl_close($curl);
}

try {
    if (isset($_POST['newgoal-prefix']) && $_POST['newgoal-prefix']) {
        foreach (createGoalsData($_POST['newgoal-prefix']) as $goal) {
            postGoal($goal);
        }
    }

} catch (\Exception $ex) {
    $errorMessage = $ex->getMessage();
}

?>
<!doctype html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>IkraTest - Goals</title>

    <link rel="stylesheet" href="//yandex.st/bootstrap/3.0.3/css/bootstrap.min.css">
</head>
<body>

<div class="container">
    <?php if ($errorMessage) { ?>
    <div class="alert alert-danger"><?= $errorMessage ?><br><?= $errorTrace ?></div>
    <?php } else { ?>
    <h3>Add goals</h3>
    <form method="POST">
        <input type="text" name="newgoal-prefix" placeholder="prefix"/>
        <input type="submit" value="Add goals" />
    </form>
    <div>
        <a href="https://metrika.yandex.ru/35335700?tab=goals">Go to metrika.yandex.ru >></a>
    </div>
    <?php } ?>
</div>

</body>
</html>
