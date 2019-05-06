<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 01.05.2019
 * Time: 19:22
 */
$connection_string=("host=//localhost
port=5432
dbname=twitter 
user=postgres 
password=qepiqooo12Q");
$con = pg_connect($connection_string)
or die ("cannot connect server ");



