<?php

addModel('customer', array('id' => 'Drupal', 'added' => 'In input-api.php'));
activateModel('customer');
// Fuck inches, unitfactor is 10;
$UF = 10;

// Measurements
setm('ChestCircumference', $P['parameters']['MEASUREMENTS']['field_chest'] * $UF);
setm('CenterBackNeckToWaist', $P['parameters']['MEASUREMENTS']['field_center_back_neck_to_waist'] * $UF);
setm('HalfAcrossBack', $P['parameters']['MEASUREMENTS']['field_across_back_width'] * $UF / 2);
setm('NeckCircumference', $P['parameters']['MEASUREMENTS']['field_neck_size'] * $UF);
setm('HipsCircumference', $P['parameters']['MEASUREMENTS']['field_trouser_waist_circum'] * $UF);
setm('NaturalWaistToTrouserWaist', $P['parameters']['MEASUREMENTS']['field_natural_waist_to_trouser_w'] * $UF);
setm('UpperBicepsCircumference', $P['parameters']['MEASUREMENTS']['field_biceps_circumference'] * $UF);
setm('ShoulderLength', $P['parameters']['MEASUREMENTS']['field_shoulder_length'] * $UF);
setm('ShoulderSlope', $P['parameters']['MEASUREMENTS']['field_shoulder_slope'] * $UF);

// Options
// Options
setp('STRETCH_FACTOR', $P['parameters']['OPTIONS']['stretch_factor'] / 100);
setp('SHOULDER_STRAP_WIDTH', $P['parameters']['OPTIONS']['shoulder_strap_width'] * $UF); // Width of the shoulder strap
setp('SHOULDER_STRAP_PLACEMENT', $P['parameters']['OPTIONS']['shoulder_strap_placement'] / 100); // Middle of the shoulder strap, from neck to shoulder point in %
setp('NECKLINE_DROP', $P['parameters']['OPTIONS']['neckline_drop'] * $UF); // How deep is the unershirt cut out at the front
setp('NECKLINE_BEND', $P['parameters']['OPTIONS']['neckline_bend'] / 100); // 0 = fully rounded neckline, 1 = most bendy neckline
setp('BACKLINE_BEND', $P['parameters']['OPTIONS']['backline_bend'] / 100); // In % (between 0 and 1) but will end up a value between 0.5 and 0.9
setp('LENGTH_BONUS', $P['parameters']['OPTIONS']['length_bonus'] * $UF); // How much longer than trouser waist
setp('ARMHOLE_DROP', $P['parameters']['OPTIONS']['armhole_drop'] * $UF); // How much should we lower the armhole

// Needed for the basic block, but not relevant for this pattern
setp('BICEPS_EASE', 30);
setp('COLLAR_EASE', 15); // Collar ease. Not really relevant, but needed for the basic block

// Back neck cutout
setp('BACK_NECK_CUTOUT', 20); // Back cutout, probably best not to change this

// Not set but calculated
// Armhole Depth
// Note: does not include armhole drop
setp('ARMHOLE_DEPTH', 180 + (getm('ShoulderSlope') / 2 - 27.5) + (getm('UpperBicepsCircumference') / 10)); // 2cm higher than SingularShirt
setp('BACK_NECK_CUTOUT', $P['parameters']['OPTIONS']['back_neck_cutout'] * $UF);
