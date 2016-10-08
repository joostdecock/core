<?php 
addModel('developer', array('id' => 'Default developer model', 'added' => 'In input-dev.php'));
activateModel('developer');

/*
setm('ChestCircumference', 960);
setm('ArmholeDepth', 220);
setm('CenterBackNeckToWaist', 455);
setm('HalfAcrossBack', 200);
setm('NeckCircumference', 390);
setm('WaistCircumference', 840);
setm('HipsCircumference', 950);
setm('NaturalWaistToHip', 200);

setm('SleeveLengthToWrist', 700);
setm('CrownPointToElbowLength', 410);
setm('UpperBicepsCircumference', 355);
 */
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

// Set options
setp('SLEEVECAP_EASE', 5);

setp('CHEST_EASE', 80);
setp('WAIST_EASE', 100);
setp('HIPS_EASE', 80);
setp('BICEPS_EASE', 30);

// More shirt to tuck in your trousers. Can also be negative for less
setp('LENGTH_BONUS' , 150);


// Hem style
// 1 = straight
// 2 = baseball
// 3 = slashed 
setp('HEM_STYLE' , 1);

// Curved hem for styles 2 and 3
// Can never be more than LENGTH_BONUS
setp('HEM_CURVE' ,80);

// Go out a bit on the hips to precent belly fluff on show
// CONSTRAINT: HIP_FLARE cannot be more than LENGTH_BONUS - HEM_CURVE or it will be ignored
setp('HIP_FLARE' , 20);


// Back neck cutout
setp('BACK_NECK_CUTOUT' , 20);

// Armhole Depth (used to be a measurement) (using 55 as default shoulder slope)
setp('ARMHOLE_DEPTH', 200+(getm('ShoulderSlope')/2-27.5)+(getm('UpperBicepsCircumference')/10));


// Collar ease
setp('COLLAR_EASE' , 15);

// BUTTON PLACKET TYPE
// 1 = Grown on
// 2 = Sewn on
setp('BUTTON_PLACKET_TYPE' , 1);

// BUTTONHOLE PLACKET STYLE
// 1 = Classic placket
// 2 = Seamless (French style)
// if sewn on, this is always 1
setp('BUTTON_PLACKET_STYLE' , 2);
setp('BUTTON_PLACKET_WIDTH' , 20);

// BUTTONHOLE PLACKET TYPE
// 1 = Grown on
// 2 = Sewn on
setp('BUTTONHOLE_PLACKET_TYPE' , 1);

// BUTTONHOLE PLACKET STYLE
// 1 = Classic placket
// 2 = Seamless (French style)
setp('BUTTONHOLE_PLACKET_STYLE' , 2);

setp('BUTTONHOLE_PLACKET_WIDTH' , 35);
setp('BUTTONHOLE_PLACKET_FOLD_WIDTH' , 6.35);

// Number of buttons, not including collar or optional top button
setp('FRONT_BUTTONS', 7);

// Extra button between top button and collar
setp('EXTRA_TOP_BUTTON', 1);

// Stop buttons before waist
setp('BUTTON_FREE_LENGTH' , 10);

// Split yoke or not
setp('SPLIT_YOKE', 1);

// Collar stand width
setp('COLLAR_STAND_WIDTH', 35);

// Collar stand bend, home much the collar stand is not straight but bended
setp('COLLAR_STAND_BEND', 5);

// Collar stand curve, angled of the collar at the end
setp('COLLAR_STAND_CURVE', 5);

// Collar gap, distance between collar edges at center front
setp('COLLAR_GAP', 25);

// Collar bend, bend at the collar stand side
setp('COLLAR_BEND', 15);

// Collar flare, flare out (like bend) at the edge side
setp('COLLAR_FLARE', 10);

// Collar angle, angle of collar tips to the grainline
setp('COLLAR_ANGLE', 85);

// How much is the collar wider than the stand at center back?
setp('COLLAR_ROLL', 7);

// Cuff style
// 1 = Rounded barrel cuff
// 2 = Chamfer barrel cuff
// 3 = Straight barrel cuff
// 4 = Rounded French cuff
// 5 = Chamfer French cuff
// 6 = Straight French cuff
setp('CUFF_STYLE', 2);

// Cuff length
setp('CUFF_LENGTH', 65);

// Cuff button rows, either 1 or 2
setp('BARRELCUFF_BUTTONS', 1);

// Cuff narrow button, 1 means add extra button(s) to close cuff narrowly
// Applies only to barrel cuffs
setp('BARRELCUFF_NARROW_BUTTON', 1);

// Cuff ease
setp('CUFF_EASE', 35);

// Cuff drape
setp('CUFF_DRAPE', 40);
  // 1, 2, or 3 pleats?
if(getp('CUFF_DRAPE') <= 30) setp('CUFF_PLEATS',1);
else setp('CUFF_PLEATS',2);

// Sleeve placket width
setp('SLEEVE_PLACKET_WIDTH', 25);

// Sleeve placket length
setp('SLEEVE_PLACKET_LENGTH', 145);


// Not set but calculated
setp('GARMENT_LENGTH', getp('LENGTH_BONUS')+getm('CenterBackNeckToWaist')+getm('NaturalWaistToTrouserWaist'));
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
