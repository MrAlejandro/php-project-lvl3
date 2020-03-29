<?php

$statusCode = 200;
$keywords = 'HTML,CSS,JavaScript,SQL,PHP,jQuery,XML,DOM,Bootstrap,Python,Java,Web development,W3C,tutorials,programming,training,learning,quiz,primer,lessons,references,examples,exercises,source code,colors,demos,tips';
$description = 'Well organized and easy to understand Web building tutorials with lots of examples of how to use HTML, CSS, JavaScript, SQL, PHP, Python, Bootstrap, Java and XML.';
$h1 = 'Thank You For Helping Us!';

return [
    $statusCode,
    [],
    <<<HTML
<!doctype html>
<html lang="en-US" style="height: 100%;" class="no-touch"><head>
    <title>HTML Examples</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="Keywords" content="$keywords">
    <meta name="Description" content="$description">
    </head>
    <body style="position: relative; min-height: 100%; top: 0px;" data-gr-c-s-loaded="true">
        <h1>$h1</h1>
    </body>
</html>
HTML,
    [
        'statusCode' => $statusCode,
        'keywords' => $keywords,
        'description' => $description,
        'h1' => $h1,
    ]
];
