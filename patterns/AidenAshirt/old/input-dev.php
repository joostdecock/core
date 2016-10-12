<?php

addModel('developer', array('id' => 'Default developer model', 'added' => 'In input-dev.php'));
activateModel('developer');

// Joost goes here
setm('ChestCircumference', 1080);
setm('CenterBackNeckToWaist', 480);
setm('HalfAcrossBack', 225);
setm('NeckCircumference', 420);
setm('HipsCircumference', 950);
setm('NaturalWaistToTrouserWaist', 175);
setm('UpperBicepsCircumference', 335); // Needed to calculate armhole depth
setm('ShoulderLength', 150);
setm('ShoulderSlope', 55);

// Options
setp('STRETCH_FACTOR', 0.9);
setp('SHOULDER_STRAP_WIDTH', 40); // Width of the shoulder strap
setp('SHOULDER_STRAP_PLACEMENT', 0.4); // Middle of the shoulder strap, from neck to shoulder point in %
setp('NECKLINE_DROP', 125); // How deep is the unershirt cut out at the front
setp('NECKLINE_BEND', 1); // 0 = fully rounded neckline, 1 = most bendy neckline
setp('BACKLINE_BEND', 0.3); // In % (between 0 and 1) but will end up a value between 0.5 and 0.9
setp('LENGTH_BONUS', 30); // How much longer than trouser waist
setp('ARMHOLE_DROP', 0); // How much should we lower the armhole

// Needed for the basic block, but not relevant for this pattern
setp('BICEPS_EASE', 30);
setp('COLLAR_EASE', 15); // Collar ease. Not really relevant, but needed for the basic block

// Back neck cutout
setp('BACK_NECK_CUTOUT', 20); // Back cutout, probably best not to change this

// Not set but calculated
// Armhole Depth (using 55 as default shoulder slope)
// Note: does not include armhole drop
setp('ARMHOLE_DEPTH', 180 + (getm('ShoulderSlope') / 2 - 27.5) + (getm('UpperBicepsCircumference') / 10)); // 2cm higher than SingularShirt
