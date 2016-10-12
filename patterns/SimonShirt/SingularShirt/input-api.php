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
setp('LENGTH_BONUS', $P['parameters']['OPTIONS']['length_bonus'] * $UF);
setp('HEM_STYLE', $P['parameters']['OPTIONS']['hem_style']);
setp('HEM_CURVE', $P['parameters']['OPTIONS']['hem_curve'] * $UF);
setp('HIP_FLARE', $P['parameters']['OPTIONS']['hip_flare'] * $UF);
setp('BACK_NECK_CUTOUT', $P['parameters']['OPTIONS']['back_neck_cutout'] * $UF);
setp('COLLAR_EASE', $P['parameters']['OPTIONS']['collar_ease'] * $UF);
setp('BUTTON_PLACKET_TYPE', $P['parameters']['OPTIONS']['button_placket_type']);
setp('BUTTON_PLACKET_STYLE', $P['parameters']['OPTIONS']['button_placket_style']);
setp('BUTTON_PLACKET_WIDTH', $P['parameters']['OPTIONS']['button_placket_width'] * $UF);
setp('BUTTONHOLE_PLACKET_TYPE', $P['parameters']['OPTIONS']['buttonhole_placket_type']);
setp('BUTTONHOLE_PLACKET_STYLE', $P['parameters']['OPTIONS']['buttonhole_placket_style']);
setp('BUTTONHOLE_PLACKET_WIDTH', $P['parameters']['OPTIONS']['buttonhole_placket_width'] * $UF);
setp('BUTTONHOLE_PLACKET_FOLD_WIDTH', $P['parameters']['OPTIONS']['buttonhole_placket_fold_width'] * $UF);
setp('FRONT_BUTTONS', $P['parameters']['OPTIONS']['number_of_buttons']);
setp('EXTRA_TOP_BUTTON', $P['parameters']['OPTIONS']['extra_top_button']);
setp('BUTTON_FREE_LENGTH', $P['parameters']['OPTIONS']['button_free_length'] * $UF);
setp('SPLIT_YOKE', $P['parameters']['OPTIONS']['split_yoke']);
setp('COLLAR_STAND_WIDTH', $P['parameters']['OPTIONS']['collar_stand_height'] * $UF);
setp('COLLAR_STAND_BEND', $P['parameters']['OPTIONS']['collar_stand_bend'] * $UF);
setp('COLLAR_STAND_CURVE', $P['parameters']['OPTIONS']['collar_stand_curve']);
setp('COLLAR_GAP', $P['parameters']['OPTIONS']['collar_gap'] * $UF);
setp('COLLAR_BEND', $P['parameters']['OPTIONS']['collar_bend'] * $UF);
setp('COLLAR_FLARE', $P['parameters']['OPTIONS']['collar_flare'] * $UF);
setp('COLLAR_ANGLE', $P['parameters']['OPTIONS']['collar_angle']);
setp('COLLAR_ROLL', $P['parameters']['OPTIONS']['collar_roll'] * $UF);
setp('CUFF_STYLE', $P['parameters']['OPTIONS']['cuff_style']);
setp('CUFF_LENGTH', $P['parameters']['OPTIONS']['cuff_length'] * $UF);
setp('BARRELCUFF_BUTTONS', $P['parameters']['OPTIONS']['barrelcuff_buttons']);
setp('BARRELCUFF_NARROW_BUTTON', $P['parameters']['OPTIONS']['barrelcuff_narrow_button']);
setp('CUFF_EASE', $P['parameters']['OPTIONS']['cuff_ease'] * $UF);
setp('CUFF_DRAPE', $P['parameters']['OPTIONS']['cuff_drape'] * $UF);
setp('SLEEVE_PLACKET_WIDTH', $P['parameters']['OPTIONS']['sleeve_placket_width'] * $UF);
setp('SLEEVE_PLACKET_LENGTH', $P['parameters']['OPTIONS']['sleeve_placket_length'] * $UF);

// Not set but calculated
if (getp('CUFF_DRAPE') <= 30) {
    setp('CUFF_PLEATS', 1);
} else {
    setp('CUFF_PLEATS', 2);
}
// Armhole Depth (used to be a measurement) (using 55 as default shoulder slope)
setp('ARMHOLE_DEPTH', 200 + (getm('ShoulderSlope') / 2 - 27.5) + (getm('UpperBicepsCircumference') / 10));
setp('GARMENT_LENGTH', getp('LENGTH_BONUS') + getm('CenterBackNeckToWaist') + getm('NaturalWaistToTrouserWaist'));
$chest = getm('ChestCircumference') + getp('CHEST_EASE');
$waist = getm('WaistCircumference') + getp('WAIST_EASE');
$hips = getm('HipsCircumference') + getp('HIPS_EASE');
$waist_re = $chest - $waist;
$hips_re = $chest - $hips;
if ($hips_re <= 0) {
    $hips_re = 0;
}
if ($waist_re <= 0) {
    $waist_re = 0;
}
setp('WAIST_REDUCTION', $waist_re);
setp('HIPS_REDUCTION', $hips_re);

dlog($P);
