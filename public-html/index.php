<?php

// это вебхук

require __DIR__ . "/CommentsBot.php";
$params = new stdClass();
$params->APIKey = '1311343166:AAGWdjwE2kVkfTC-5zfE--v_etTFo0kapJY';
$params->adminIDChat = ['440046277'];
$params->menegerOrdersIDChat = ['440046277', '440046277'];
$pp = new CommentsBot($params);





