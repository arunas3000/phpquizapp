<?php 

$con = mysqli_connect('localhost', 'id2135764_lenovo33', 'GoodLookin1', 'id2135764_phpquizes');

if (mysqli_connect_errno())
  {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  exit();
  }


//Helper functions so you don't have to write connection and mysqli functions everytime


function query($query) {
    
    global $con;
    
    
    return mysqli_query($con, $query);
    
    
}

function fetch_array($result){
    
      global $con;
    
    return mysqli_fetch_array($result);
    
}

function confirm($result) {
    
    global $con;
    
    if(!$result){
        die("Query failed" . mysqli_error($con));
    
            }
}

function escape($input_string) {
    
    global $con;
    
    return mysqli_real_escape_string($con, $input_string);
}
