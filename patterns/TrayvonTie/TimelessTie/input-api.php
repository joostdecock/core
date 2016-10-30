<?php 
addModel('customer', array('id' => 'Drupal', 'added' => 'In input-api.php'));
activateModel('customer');

$UF = $P['parameters']['UNITFACTOR'];
setm('NeckSize', $P['parameters']['MEASUREMENTS']['field_neck_size'] * $UF);
setm('NeckToWaist', $P['parameters']['MEASUREMENTS']['field_neck_to_waist'] * $UF);

setp('TIP', $P['parameters']['OPTIONS']['tie_bottom_width'] * $UF);
setp('KNOT', $P['parameters']['OPTIONS']['tie_central_width'] * $UF);

