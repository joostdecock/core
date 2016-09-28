<?php 
addModel('developer', array('id' => 'Default developer model', 'added' => 'In input-dev.php'));
activateModel('developer');

// Joost goes here

setm('ChestCircumference', 1080);
setm('CenterBackNeckToWaist', 480);
setm('HalfAcrossBack', 225);
setm('NeckCircumference', 420);
setm('WaistCircumference', 885);
setm('HipsCircumference', 1040);
setm('NaturalWaistToTrouserWaist', 175);

setm('SleeveLengthToWrist', 700);
setm('CrownPointToElbowLength', 410);
setm('UpperBicepsCircumference', 335);
setm('WristCircumference', 190);

setm('ShoulderLength' , 150);
// Shoulder slope = 50.5cm - 45cm = 5.5cm = 55
setm('ShoulderSlope' , 55);


// Options below

// Ease
setp('SLEEVECAP_EASE', 5);
setp('CHEST_EASE', 80);
setp('WAIST_EASE', 100);
setp('HIPS_EASE', 80);
setp('BICEPS_EASE', 30);
setp('COLLAR_EASE' , 15);
setp('CUFF_EASE', 45);

// Back neck cutout
setp('BACK_NECK_CUTOUT' , 20);

// Armhole Depth (using 55 as default shoulder slope)
setp('ARMHOLE_DEPTH', 200+(getm('ShoulderSlope')/2-27.5)+(getm('UpperBicepsCircumference')/10));

// Not set but calculated
$chest = getm('ChestCircumference') + getp('CHEST_EASE');
$waist = getm('WaistCircumference') + getp('WAIST_EASE');
$hips = getm('HipsCircumference') + getp('HIPS_EASE');
$waist_re = $chest - $waist;
$hips_re = $chest - $hips;
if($hips_re <= 0) $hips_re = 0;
if($waist_re <= 0) $waist_re = 0;
setp('WAIST_REDUCTION', $waist_re);
setp('HIPS_REDUCTION', $hips_re);
dmsg("waist reduction: $waist_re");
