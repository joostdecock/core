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
setm('UpperBicepsCircumference', $P['parameters']['MEASUREMENTS']['field_biceps_circumference'] * $UF);
setm('ShoulderLength', $P['parameters']['MEASUREMENTS']['field_shoulder_length'] * $UF);
setm('ShoulderSlope', $P['parameters']['MEASUREMENTS']['field_shoulder_slope'] * $UF);

// Options
setp('CHEST_EASE', $P['parameters']['OPTIONS']['chest_ease'] * $UF);
setp('WAIST_EASE', $P['parameters']['OPTIONS']['natural_waist_ease'] * $UF);
setp('HIPS_EASE', $P['parameters']['OPTIONS']['trouser_waist_ease'] * $UF);
setp('BACK_NECK_CUTOUT', $P['parameters']['OPTIONS']['back_neck_cutout'] * $UF);

setp('NECK_INSET', $P['parameters']['OPTIONS']['neck_inset'] * $UF);
setp('SHOULDER_INSET', $P['parameters']['OPTIONS']['shoulder_inset'] * $UF);
setp('INSET', $P['parameters']['OPTIONS']['inset'] * $UF);
setp('BACK_INSET', $P['parameters']['OPTIONS']['back_inset'] * $UF);
setp('FRONT_DROP_BONUS', $P['parameters']['OPTIONS']['front_drop_bonus'] * $UF);
setp('FRONT_STYLE', $P['parameters']['OPTIONS']['front_style']);

setp('LENGTH_BONUS', $P['parameters']['OPTIONS']['length_bonus'] * $UF);
setp('HEM_STYLE', $P['parameters']['OPTIONS']['hem_style']);
setp('HEM_RADIUS', $P['parameters']['OPTIONS']['hem_radius'] * $UF);
setp('BUTTONS', $P['parameters']['OPTIONS']['buttons']);

setp('BACK_SCYE_DART', $P['parameters']['OPTIONS']['back_scye_dart'] * $UF);
setp('FRONT_SCYE_DART', $P['parameters']['OPTIONS']['front_scye_dart'] * $UF);
setp('CENTER_BACK_DART', $P['parameters']['OPTIONS']['center_back_dart'] * $UF);

// Fixed by Joost
setp('POCKET_WIDTH', 90);
setp('POCKET_HEIGHT', 15);
setp('POCKET_ANGLE', -5);

// Not set but calculated
setp('ARMHOLE_DEPTH', 290+(getm('ShoulderSlope')/2-27.5)+(getm('UpperBicepsCircumference')/10));

$chest = getm('ChestCircumference') + getp('CHEST_EASE');
$waist = getm('WaistCircumference') + getp('WAIST_EASE');
$hips = getm('HipsCircumference') + getp('HIPS_EASE');
$waist_re = $chest - $waist;
$hips_re = $chest - $hips;
if($hips_re <= 0) $hips_re = 0;
if($waist_re <= 0) $waist_re = 0;
setp('WAIST_REDUCTION', $waist_re);
setp('HIPS_REDUCTION', $hips_re);
setp('SCYE_DART', 5+$waist_re/10);
dmsg("waist reduction is $waist_re");
//dlog($P);
