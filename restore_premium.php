<?php
require 'config.php';

// Change back to the pollinations ones chosen earlier for a premium dark background
$pdo->exec("UPDATE equipments SET image_path = 'uploads/pollination_basketball_fixed.jpg' WHERE name LIKE '%Basketball%'");
$pdo->exec("UPDATE equipments SET image_path = 'uploads/pollination_football.jpg' WHERE name LIKE '%Football%'");

// Just to be completely consistent, I will also make sure the pingpong items use the dark background ones if requested, but they only said "this two". I'll leave the pingpong items as they were or restore them to pollination as well just in case. Let me only do what they asked: "for this two" (basketball and football).
echo "Updated back to premium chosen photos.\n";
