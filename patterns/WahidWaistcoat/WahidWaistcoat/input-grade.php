<?php 
addModel('developer1', array('id' => 'Default developer model 1', 'added' => 'In input-grade.php'));
activateModel('developer1');

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


addModel('developer2', array('id' => 'Default developer model 2 ', 'added' => 'In input-grade.php'));
activateModel('developer2');

// Joost goes here

setm('ChestCircumference', 980);
setm('CenterBackNeckToWaist', 430);
setm('HalfAcrossBack', 205);
setm('NeckCircumference', 400);
setm('WaistCircumference', 805);
setm('HipsCircumference', 940);
setm('NaturalWaistToTrouserWaist', 165);

setm('UpperBicepsCircumference', 315);
setm('ShoulderLength' , 140);
// Shoulder slope = 50.5cm - 45cm = 5.5cm = 55
setm('ShoulderSlope' , 50);

addModel('developer3', array('id' => 'Default developer model 1', 'added' => 'In input-grade.php'));
activateModel('developer3');

// Joost goes here

setm('ChestCircumference', 1280);
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


addModel('developer4', array('id' => 'Default developer model 2 ', 'added' => 'In input-grade.php'));
activateModel('developer4');

// Joost goes here

setm('ChestCircumference', 980);
setm('CenterBackNeckToWaist', 430);
setm('HalfAcrossBack', 205);
setm('NeckCircumference', 400);
setm('WaistCircumference', 805);
setm('HipsCircumference', 940);
setm('NaturalWaistToTrouserWaist', 165);

setm('UpperBicepsCircumference', 315);
setm('ShoulderLength' , 140);
// Shoulder slope = 50.5cm - 45cm = 5.5cm = 55
setm('ShoulderSlope' , 50);


// Options below

// Ease
setp('CHEST_EASE', 80);
setp('WAIST_EASE', 80);
setp('HIPS_EASE', 60);
setp('BACK_NECK_CUTOUT' , 25);
setp('NECK_INSET' , 10);
setp('SHOULDER_INSET' , 10);
setp('BACK_INSET' , 35);
setp('INSET' , 30);
setp('FRONT_DROP_BONUS', 0);
setp('FRONT_STYLE', 1);
setp('SCYE_DART_ALTER', 0);

setp('LENGTH_BONUS', 0);
setp('HEM_STYLE', 1);
setp('HEM_RADIUS', 60);
setp('BUTTONS', 5);

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
