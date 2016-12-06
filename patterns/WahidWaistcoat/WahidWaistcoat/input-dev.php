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

setm('UpperBicepsCircumference', 335);
setm('ShoulderLength' , 150);
// Shoulder slope = 50.5cm - 45cm = 5.5cm = 55
setm('ShoulderSlope' , 55);


// Options below

// Ease
setp('CHEST_EASE', 20);
setp('WAIST_EASE', 80);
setp('HIPS_EASE', 40);
setp('BACK_NECK_CUTOUT' , 25);
setp('NECK_INSET' , 20);
setp('SHOULDER_INSET' , 20);
setp('BACK_INSET' , 25);
setp('INSET' , 30);
setp('FRONT_DROP_BONUS', 0);
setp('FRONT_STYLE', 1);
setp('BACK_SCYE_DART', 5);
setp('FRONT_SCYE_DART', 10);
setp('CENTER_BACK_DART', 5);

setp('LENGTH_BONUS', 15);
if($_GET['hem']==1) setp('HEM_STYLE', 1);
else setp('HEM_STYLE', 2);
setp('HEM_RADIUS', 60);
setp('BUTTONS', 6);

// Fixed by Joost
setp('POCKET_WIDTH', 100);
setp('POCKET_HEIGHT', 20);
setp('POCKET_ANGLE', -5);

// Armhole Depth (using 55 as default shoulder slope)
setp('ARMHOLE_DEPTH', 290+(getm('ShoulderSlope')/2-27.5)+(getm('UpperBicepsCircumference')/10));

// Not set but calculated
$chest = getm('ChestCircumference') + getp('CHEST_EASE');
$waist = getm('WaistCircumference') + getp('WAIST_EASE');
$hips = getm('HipsCircumference') + getp('HIPS_EASE');
$waist_re = $chest - $waist;
$hips_re = $chest - $hips;
if($hips_re <= 0) $hips_re = 0;
if($waist_re <= 0) $waist_re = 0;
setp('WAIST_REDUCTION', $waist_re);
setp('SCYE_DART', 5+$waist_re/10);
setp('HIPS_REDUCTION', $hips_re);
