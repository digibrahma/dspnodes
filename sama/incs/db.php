<?php
$dbLink = mysqli_connect("digibrahmadsp.cluster-ci0lgixaghcf.us-east-1.rds.amazonaws.com","digibrahma_dsp","digibrahma~17","digibrahma_dsp");

if (mysqli_connect_errno())
  {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  }

?>