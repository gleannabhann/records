<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
 ini_set("display_errors", true);
 error_reporting(E_ALL);

$ch = curl_init("http://records.gleannabhann.net/api/person_awards.php?id=245");
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$result = curl_exec($ch);
curl_close($ch);
$person = json_decode($result, TRUE);
echo '<table><tr><td>Award Name</td><td>Award Date</td></tr>';
foreach ($person["awards"] as $award)
{
   echo '<tr><td>' . $award["name_award"] . '</td><td>' .
   $award["date_award"] . '</td></tr>';
}
echo '</table>';
