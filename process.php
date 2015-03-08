<?php

require 'TTH.php';
$TTH = new TTH();

$input = $_POST['input'];
echo $TTH->getHash($input);