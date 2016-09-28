<?php 
addModel('customer', array('id' => 'Drupal', 'added' => 'In input-api.php'));
activateModel('customer');
// Fuck inches, unitfactor is 10;
$UF = 10;

// Measurements
setm('ChestCircumference', $P['parameters']['MEASUREMENTS']['field_chest'] * $UF);
setm('CenterBackNeckToWaist', $P['parameters']['MEASUREMENTS']['field_center_back_neck_to_waist'] * $UF);
setm('HalfAcrossBack', $P['parameters']['MEASUREMENTS']['field_across_back_width'] * $UF /2);
setm('NeckCircumference', $P['parameters']['MEASUREMENTS']['field_neck_size'] * $UF);
setm('WaistCircumference', $P['parameters']['MEASUREMENTS']['field_natural_waist'] * $UF);
setm('HipsCircumference', $P['parameters']['MEASUREMENTS']['field_trouser_waist_circum'] * $UF);
setm('NaturalWaistToTrouserWaist', $P['parameters']['MEASUREMENTS']['field_natural_waist_to_trouser_w'] * $UF);
setm('SleeveLengthToWrist', $P['parameters']['MEASUREMENTS']['field_sleeve_length'] * $UF);
setm('CrownPointToElbowLength', $P['parameters']['MEASUREMENTS']['field_shoulder_to_elbow_length'] * $UF);
setm('UpperBicepsCircumference', $P['parameters']['MEASUREMENTS']['field_biceps_circumference'] * $UF);
setm('WristCircumference', $P['parameters']['MEASUREMENTS']['field_wrist_circumference'] * $UF);
setm('ShoulderLength', $P['parameters']['MEASUREMENTS']['field_shoulder_length'] * $UF);
setm('ShoulderSlope', $P['parameters']['MEASUREMENTS']['field_shoulder_slope'] * $UF);

// Options

setp('SLEEVECAP_EASE', $P['parameters']['OPTIONS']['sleevecap_ease'] * $UF);
setp('CHEST_EASE', $P['parameters']['OPTIONS']['chest_ease'] * $UF);
setp('WAIST_EASE', $P['parameters']['OPTIONS']['natural_waist_ease'] * $UF);
setp('HIPS_EASE', $P['parameters']['OPTIONS']['trouser_waist_ease'] * $UF);
setp('BICEPS_EASE', $P['parameters']['OPTIONS']['biceps_ease'] * $UF);
setp('COLLAR_EASE', $P['parameters']['OPTIONS']['collar_ease'] * $UF);
setp('CUFF_EASE', $P['parameters']['OPTIONS']['cuff_ease'] * $UF);
setp('BACK_NECK_CUTOUT', $P['parameters']['OPTIONS']['back_neck_cutout'] * $UF);
// Not set but calculated
setp('ARMHOLE_DEPTH', 200+(getm('ShoulderSlope')/2-27.5)+(getm('UpperBicepsCircumference')/10));

$chest = getm('ChestCircumference') + getp('CHEST_EASE');
$waist = getm('WaistCircumference') + getp('WAIST_EASE');
$hips = getm('HipsCircumference') + getp('HIPS_EASE');
$waist_re = $chest - $waist;
$hips_re = $chest - $hips;
if($hips_re <= 0) $hips_re = 0;
if($waist_re <= 0) $waist_re = 0;
setp('WAIST_REDUCTION', $waist_re);
setp('HIPS_REDUCTION', $hips_re);

dlog($P);
