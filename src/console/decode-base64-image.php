<?php

$filename = "image.txt";
$data = file_get_contents($filename);
$data = str_replace("\n", "", $data);
$data = base64_decode($data);
file_put_contents("image.png", $data);
