<?php
require_once "functions.php";
if(isset($_GET['s'])){
  /*
  mres is instead of escapestring
  */
  $value = mres($_GET['s']);
  $results = requestData($value);
  /*
  Too make everything more effective we only send the data that we need
  */
  echo generateReturnData($results);
}
?>
