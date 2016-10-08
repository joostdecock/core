<?php 
function sleevecapLen() {
  $preactive = activePart();
  activatePart('Sleeve');
  $len =  
    arcLen(5, 5, 57012, 57011) + 
    arcLen(57011, 57013, 701, 701) + 
    arcLen(701, 701, 47013, 47011) + 
    arcLen(47011, 47012, 41, 4) +
    arcLen(4, 42, 48012, 48011) +
    arcLen(48011, 48013, 801, 801) +
    arcLen(801, 801, 68013, 68011) +
    arcLen(68011, 68012, 6, 6); 
  activatePart($preactive);
  return $len;
}

function armholeLen() {
  $preactive = activePart();
  activatePart('FrontAndBackBlock');
  $len = ( 
      arcLen(16, 161, 121, 12) + 
      arcLen(12, 122, 181, 18) + 
      arcLen(18, 182, 501, 5) 
    ) * 2; 
  activatePart($preactive);
  return $len;
}

function tweakSleevecap() {
  GLOBAL $P;
  GLOBAL $SLEEVECAPITERATION;
  GLOBAL $SLEEVECAPFACTOR;
  $SLEEVECAPITERATION = 1;
  $SLEEVECAPFACTOR = 1;
  $delta = sleevecapDelta();
  msg();
  msg('      Calculating sleevecap with '.SLEEVECAP_EASE.'mm sleavecap ease.');
  while(abs($delta)>0.5 && $SLEEVECAPITERATION<150) {
    dmsg("      Iteration $SLEEVECAPITERATION, sleevecap length is $delta mm off");
    $fit = fitSLeevecap($delta);
    $delta = sleevecapDelta();
  }
  if($SLEEVECAPITERATION>144) msg("      Iteration $SLEEVECAPITERATION, sleevecap length is $delta mm off. I'm not happy, but it will have to do.");
  else msg("      Iteration $SLEEVECAPITERATION, sleevecap length is $delta mm off. I'm happy.");
  dmsg();
  dmsg("          Armhole length: ".round(armholeLen(),2)."mm");
  dmsg("        Sleevecap length: ".round(sleevecapLen(),2)."mm");
}

function sleevecapDelta() {
  $len = sleevecapLen();
  $target = armholeLen() + SLEEVECAP_EASE;
  return round($len - $target, 2);
}

function fitSleevecap($delta) {
  GLOBAL $SLEEVECAPFACTOR;
  GLOBAL $SLEEVECAPITERATION;
  GLOBAL $P;
  $SLEEVECAPITERATION++;
  // Adjusting sleevecap factor
  if($SLEEVECAPITERATION>18) {
    if($delta>0) $SLEEVECAPFACTOR = $SLEEVECAPFACTOR *0.9; 
    else if($delta<0) $SLEEVECAPFACTOR = $SLEEVECAPFACTOR *1.1; 
  } else if($SLEEVECAPITERATION>10) {
    if($delta>0) $SLEEVECAPFACTOR = $SLEEVECAPFACTOR *0.95; 
    else if($delta<0) $SLEEVECAPFACTOR = $SLEEVECAPFACTOR *1.05; 
  } else {
    if($delta>0) $SLEEVECAPFACTOR = $SLEEVECAPFACTOR *0.99; 
    else if($delta<0) $SLEEVECAPFACTOR = $SLEEVECAPFACTOR *1.01; 
  }
  buildSleeve();
}
function tweakCollaropening() {
  GLOBAL $P;
  GLOBAL $COLLAROPENINGITERATION;
  GLOBAL $COLLAROPENINGFACTOR;
  $COLLAROPENINGITERATION = 1;
  $COLLAROPENINGFACTOR = 1;
  $delta = collaropeningDelta();
  msg();
  msg('      Calculating collar opening with '.COLLAR_EASE.'mm collar ease.');
  while(abs($delta)>0.5 && $COLLAROPENINGITERATION<150) {
    dmsg("      Iteration $COLLAROPENINGITERATION, collar opening length is $delta mm off");
    $fit = fitCollaropening($delta);
    $delta = collaropeningDelta();
  }
  if($COLLAROPENINGITERATION>149) msg("      Iteration $COLLAROPENINGITERATION, collar opening length is $delta mm off. I'm not happy, but it will have to do.");
  else msg("      Iteration $COLLAROPENINGITERATION, collar opening length is $delta mm off. I'm happy.");
  dmsg();
  dmsg("          Collar opening length: ".round(collaropeningLen(),2)."mm");
  dmsg("                  Collar length: ".round(getm('NeckCircumference')+getp('COLLAR_EASE'),2)."mm");
}
function collaropeningDelta() {
  $len = collaropeningLen();
  $target = getm('NeckCircumference')+getp('COLLAR_EASE');
  return round($len - $target, 2);
}
function collaropeningLen() {
  $preactive = activePart();
  activatePart('FrontAndBackBlock');
  $len =  
    arcLen(10, 9, 171, 17)*2 + 
    arcLen(1, 101, 10, 10)*2;  
  activatePart($preactive);
  return $len;
}

function fitCollaropening($delta) {
  GLOBAL $COLLAROPENINGFACTOR;
  GLOBAL $COLLAROPENINGITERATION;
  GLOBAL $P;
  $COLLAROPENINGITERATION++;
  // Adjusting sleevecap factor
  if($delta>0) $COLLAROPENINGFACTOR = $COLLAROPENINGFACTOR *0.97; 
  else if($delta<0) $COLLAROPENINGFACTOR = $COLLAROPENINGFACTOR *1.03; 
  buildBody();
}


function collarstandLen() {
  $preactive = activePart();
  activatePart('CollarStand');
  $len = ( 
      arcLen(42, 43, 6, 61) + 
      arcLen(61, 62, 33, 32) + 
      hopLen(32, 31) 
    ) * 2; 
  activatePart($preactive);
  return $len;
}
function collarstandDelta() {
  $len = collarstandLen();
  $extra = getp('BUTTONHOLE_PLACKET_WIDTH')/2 + getp('BUTTON_PLACKET_WIDTH')/2;
  $target = getm('NeckCircumference')+getp('COLLAR_EASE')+$extra;
  return round($len - $target, 2);
}

function fitCollarstand($delta) {
  GLOBAL $COLLARSTANDFACTOR;
  GLOBAL $COLLARSTANDITERATION;
  GLOBAL $P;
  $COLLARSTANDITERATION++;
  // Adjusting collarstand factor
  if($delta>0) $COLLARSTANDFACTOR = $COLLARSTANDFACTOR *0.98; 
  else if($delta<0) $COLLARSTANDFACTOR = $COLLARSTANDFACTOR *1.02; 
  buildCollarstand();
}
function tweakCollarstand() {
  GLOBAL $P;
  GLOBAL $COLLARSTANDITERATION;
  GLOBAL $COLLARSTANDFACTOR;
  $COLLARSTANDITERATION = 1;
  $COLLARSTANDFACTOR = 1;
  $delta = collarstandDelta();
  msg();
  msg('      Calculating collar stand length with '.COLLAR_EASE.'mm collar ease.');
  while(abs($delta)>0.5 && $COLLARSTANDITERATION<150) {
    dmsg("      Iteration $COLLARSTANDITERATION, collar stand length is $delta mm off");
    $fit = fitCollarstand($delta);
    $delta = collarstandDelta();
  }
  if($COLLARSTANDITERATION>149) msg("      Iteration $COLLARSTANDITERATION, collar stand length is $delta mm off. I'm not happy, but it will have to do.");
  else msg("      Iteration $COLLARSTANDITERATION, collar stand length is $delta mm off. I'm happy.");
  dmsg();
  dmsg("          Collar stand length: ".round(collarstandLen(),2)."mm");
  dmsg("                  Collar length: ".round(getm('NeckCircumference')+getp('COLLAR_EASE'),2)."mm");
}

function buildBody() {
  buildBlock();
  buildFrontAndBackBlock();
}

function buildCollarstand() {
  GLOBAL $COLLARSTANDFACTOR;
  if(!$COLLARSTANDFACTOR) $COLLARSTANDFACTOR = 1;
  $preactive = activePart();
  activatePart('CollarStand');
  $base = $COLLARSTANDFACTOR* (getm('NeckCircumference')/2+getp('COLLAR_EASE')/2);
      $extra = getp('BUTTONHOLE_PLACKET_WIDTH')/2 + getp('BUTTON_PLACKET_WIDTH')/2;
      addPoint( 1, 0,0);
      addPoint( 2, $base,0);
      addPoint(21,px(2)+$extra/2,py(2));
      addPoint( 3, px(2),getp('COLLAR_STAND_WIDTH'));
      addPoint(31,px(3)+$extra/2,py(3));
      addPoint( 4,px(1),py(3));
      addPoint( 5,px(2),py(3)/2);
      addPntAr(21,rotate(21,5,getp('COLLAR_STAND_CURVE')));
      addPntAr(21,pShift(21,90,getp('COLLAR_STAND_BEND')/2));
      addPntAr(31,rotate(31,5,getp('COLLAR_STAND_CURVE')));
      addPntAr(31,pShift(31,90,getp('COLLAR_STAND_BEND')/2));
      addPntAr(22,rotate(2,5,getp('COLLAR_STAND_CURVE')));
      addPntAr(22,pShift(22,90,getp('COLLAR_STAND_BEND')/2));
      addPntAr(32,rotate(3,5,getp('COLLAR_STAND_CURVE')));
      addPntAr(32,pShift(32,90,getp('COLLAR_STAND_BEND')/2));
      addPntAr(23,pShift(22,getp('COLLAR_STAND_CURVE')+180,10));
      addPntAr(33,pShift(32,getp('COLLAR_STAND_CURVE')+180,10));
      addPntAr(52,pShift(5,90,getp('COLLAR_STAND_BEND')));
      addPntAr(12,pShift(1,90,getp('COLLAR_STAND_BEND')));
      addPntAr(42,pShift(4,90,getp('COLLAR_STAND_BEND')));
      addPoint(43,px(2)*0.4,py(42));
      addPoint( 6,px(2)*0.6,py(3));
      addPoint(61,px(2)*0.75,py(3));
      addPoint(62,px(2)*0.95,py(3));
      addPoint( 7,px(6),py(1));
      addPoint(71,px(2)*0.75,py(1));
      addPoint(72,px(2)*0.95,py(1));
      addPoint(13,px(43),py(12));
      addPntAr(51,hopShift(21,31,hopLen(21,31)/2));
      addPntAr(55,hopShift(22,32,hopLen(22,32)/2));
      addPoint(56,0,py(3)/2);
      addPoint(57,px(61),py(56));
      $flip = array(2,3,5,6,7,12,13,21,22,23,31,32,33,42,43,51,52,55,57,61,62,71,72);
      foreach($flip as $pf) {
        $id = $pf*-1;
        addPntAr($id,xFlip($pf));
      }
      // Move buttonhole to not be centered
      addPntAr(55,pShift(55,getp('COLLAR_STAND_CURVE')+180,4));
  activatePart($preactive);
}
function collarLen() {
  $preactive = activePart();
  activatePart('Collar');
  $len = (arcLen(5, 6, 4, 4) * 2);
  activatePart($preactive);
  return $len;
}
function collarDelta() {
  $len = collarLen();
  $target = getm('NeckCircumference')+getp('COLLAR_EASE')-getp('COLLAR_GAP');
  return round($len - $target, 2);
}

function fitCollar($delta) {
  GLOBAL $COLLARFACTOR;
  GLOBAL $COLLARITERATION;
  GLOBAL $P;
  $COLLARITERATION++;
  // Adjusting collar factor
  if($delta>0) $COLLARFACTOR = $COLLARFACTOR *0.98; 
  else if($delta<0) $COLLARFACTOR = $COLLARFACTOR *1.02; 
  buildCollar();
}
function tweakCollar() {
  GLOBAL $P;
  GLOBAL $COLLARITERATION;
  GLOBAL $COLLARFACTOR;
  $COLLARITERATION = 1;
  $COLLARFACTOR = 1;
  $delta = collarDelta();
  msg();
  msg('      Calculating collar length with '.COLLAR_EASE.'mm collar ease and a '.COLLAR_GAP.'mm collar gap');
  while(abs($delta)>0.5 && $COLLARITERATION<150) {
    dmsg("      Iteration $COLLARITERATION, collar length is $delta mm off");
    $fit = fitCollar($delta);
    $delta = collarDelta();
  }
  if($COLLARITERATION>149) msg("      Iteration $COLLARITERATION, collar length is $delta mm off. I'm not happy, but it will have to do.");
  else msg("      Iteration $COLLARITERATION, collar length is $delta mm off. I'm happy.");
  dmsg();
  dmsg("          Collar length: ".round(collarLen(),2)."mm");
  dmsg("    Collar stand length: ".round(getm('NeckCircumference')+getp('COLLAR_EASE'),2)."mm");
  dmsg("             Collar gap: ".round(getp('COLLAR_GAP'),2)."mm");
  msg();
}
function buildCollar() {
  GLOBAL $COLLARFACTOR;
  if(!$COLLARFACTOR) $COLLARFACTOR = 1;
  $preactive = activePart();
  // Length of Yoke cutout needed for notches
  activatePart('Yoke');
  $len = arcLen(1,101,10,10);
  activatePart('Collar');
      $base = $COLLARFACTOR * (getm('NeckCircumference')/2+getp('COLLAR_EASE')/2);
      addPoint( 0 , 0, 0);
      addPoint( 1 , $base,0);  
      addPoint( 2 , px(1),-1*(getp('COLLAR_STAND_WIDTH')+getp('COLLAR_ROLL')+getp('COLLAR_BEND')));
      addPoint( 3 , px(0),py(2));
      addPoint( 4 , px(1)-getp('COLLAR_GAP')/2,0);
      addPoint( 5 , 0,-1*getp('COLLAR_BEND'));
      addPoint( 6 , px(1)*0.8,py(5));
      addPntAr(401 , pShift(4,180,20));
      addPntAr(402 , rotate(401,4,getp('COLLAR_ANGLE')));
      addPoint( 7 , 0,py(2)-getp('COLLAR_FLARE'));
      addPntAr(701 , pShift(7,0,20));
      addPntAr( 8 ,lineIsect(7,701,4,402),'Collar tip');
      addPoint( 9 , px(1)*0.7,py(3));
      addPntAr( 10, pShift(5,90,hopLen(5,3)/2));
      addPoint( 11, px(6),py(10));
      addPoint( 12, px(8)/2,py(3));
      addPntAr( 13, arcShift(5,6,4,4,$len));
      $flip = array(1,2,4,6,8,9,11,12,13);
      foreach($flip as $pf) {
        $id = $pf*-1;
        addPntAr($id,xFlip($pf));
      }
  activatePart($preactive);
}


function buildBlock() {
  GLOBAL $COLLAROPENINGFACTOR;
  if(!$COLLAROPENINGFACTOR) $COLLAROPENINGFACTOR = 1;
  $preactive = activePart();
  // This is by the book
  //$cox = (getm('NeckCircumference')+getp('COLLAR_EASE'))/5 + 7;
  // Using PI so assuming neck is perfectly circular, but adding 5mm because
  $cox = (getm('NeckCircumference')/3.1415)/2+5;
  $coy = (getm('NeckCircumference')+getp('COLLAR_EASE'))/5 -8;
  // Now let's tweak
  //$cox = $cox * $COLLAROPENINGFACTOR;
  $coy = $coy * $COLLAROPENINGFACTOR;

  activatePart('Block');
  addPoint(     0 , 0, 0);
  addPoint(     1 , px(0), getp('BACK_NECK_CUTOUT'), 'Point 1, Center back neck point');
  addPoint(     2 , px(0), py(1) + getp('ARMHOLE_DEPTH'));
  addPoint(     3 , px(0), py(1) + getm('CenterBackNeckToWaist'));
  addPoint(     4 , px(0), py(1) + GARMENT_LENGTH);
  addPoint(     5 , px(0) + mgetp('QuarterChest') + CHEST_EASE/4, py(2));
  addPoint(     6 , px(5), py(0));
  addPoint(     7 , px(5), py(3));
  addPoint(     7001 , px(0) + mgetp('QuarterWaist') + WAIST_EASE/4, py(3));
  addPoint(     8 , px(5), py(4));
  addPoint(     8001 , px(0) + mgetp('QuarterHips') + HIPS_EASE/4, py(4)-getp('LENGTH_BONUS'));
  addPoint(     9 , px(0) +  $cox, py(1));
  addPoint(    10 , px(9), py(1) - getp('BACK_NECK_CUTOUT'));
  addPoint(    11 , px(0), py(1) + yDist(1,2)/2);
  addPoint(    12 , px(0) + getm('HalfAcrossBack'), py(11));
  // Old style by the book
  // addPoint(    13 , px(0), py(1) + getp('ARMHOLE_DEPTH')/8 - 7.5);
  // New style,based on shoulder length and slope
  addPoint(    13 , px(0), py(0) + getm('ShoulderSlope')/2);
  addPoint(    14 , px(12), py(13));
  addPoint(    15 , px(12), py(2));
  // Old style by the book
  // addPoint(    16 , px(12) + 18, py(13));
  // New style,based on shoulder length and slope
  // Don't know much about history
  // Don't know much trigonometry
  $xval = sqrt(pow(getm('ShoulderLength'),2) - pow(getm('ShoulderSlope')/2,2));
  addPoint(    16 , px(9)+$xval, py(13));
  addPoint(    17 , px(0), py(1) + $coy);
  addPntAr(    18 , pShift(15, 45, 30));
  addPoint(    50 , px(15)/2, py(2)-20);
  addPoint(   101 , px(0) + xDist(1,9)*0.8, py(1));
  addPntAr(   121 , pShift(12, 90, 40));
  addPntAr(   122 , pShift(12, -90, 40));
  addPoint(   171 , px(0) + xDist(1,9)*0.8, py(17));
  addPntAr(   181 , pShift(18, 135, 25));
  addPntAr(   182 , pShift(18, -45, 25));
  addPntAr(   501 , pShift(5, 180, 10));
  addPntAr(   161 , pShift(16, angle(10,16)+90, 10));
  addPoint(   200 , 0, getm('CenterBackNeckToWaist') + getm('NaturalWaistToTrouserWaist') + getp('BACK_NECK_CUTOUT'), 'Trouser waist height');
  activatePart($preactive);
}

function buildFrontAndBackBlock() {
  GLOBAL $COLLAROPENINGFACTOR;
  if(!$COLLAROPENINGFACTOR) $COLLAROPENINGFACTOR = 1;
  $preactive = activePart();
  activatePart('FrontAndBackBlock');
  clonePoints('Block');
  $width = px(5); 
  addPntAr(-99999 , xMirror(  0, $width));
  addPntAr(    -1 , xMirror(  1, $width));
  addPntAr(    -2 , xMirror(  2, $width));
  addPntAr(    -3 , xMirror(  3, $width));
  addPntAr(    -4 , xMirror(  4, $width));
  addPntAr(    -5 , xMirror(  5, $width));
  addPntAr(    -6 , xMirror(  6, $width));
  addPntAr(    -7 , xMirror(  7, $width));
  addPntAr(    -8 , xMirror(  8, $width));
  addPntAr(    -8001 , xMirror(  8001, $width));
  addPntAr(    -9 , xMirror(  9, $width));
  addPntAr(   -10 , xMirror( 10, $width));
  addPntAr(   -11 , xMirror( 11, $width));
  addPntAr(   -12 , xMirror( 12, $width));
  addPntAr(   -13 , xMirror( 13, $width));
  addPntAr(   -14 , xMirror( 14, $width));
  addPntAr(   -15 , xMirror( 15, $width));
  addPntAr(   -16 , xMirror( 16, $width));
  addPntAr(   -17 , xMirror( 17, $width));
  addPntAr(   -18 , xMirror( 18, $width));
  addPntAr(  -101 , xMirror(101, $width));
  addPntAr(  -121 , xMirror(121, $width));
  addPntAr(  -122 , xMirror(122, $width));
  addPntAr(  -171 , xMirror(171, $width));
  addPntAr(  -181 , xMirror(181, $width));
  addPntAr(  -182 , xMirror(182, $width));
  addPntAr(  -501 , xMirror(501, $width));
  addPntAr(  -161 , xMirror(161, $width));
  // Cutting out armhole a bit deeper at the front
  $frontextra = 5;
  addPntAr(   -12 , pShift(-12, 0, $frontextra));
  addPntAr(   -121 , pShift(-121, 0, $frontextra));
  addPntAr(   -122 , pShift(-122, 0, $frontextra));
  // Need to figure out what curve the back pitch point will intersect with
  $backarmpitchY = py(15) - 110;
  if($backarmpitchY == py(12)) addPoint(20,px(12),py(12), 'Back pitch point'); // Smack between curves
  else if($backarmpitchY < py(12)) addPntAr(20, arcYsect(12, 121, 161, 16, $backarmpitchY), 'Back pitch point'); // Top curve
  else if($backarmpitchY > py(12)) addPntAr(20, arcYsect(18, 181, 122, 12, $backarmpitchY), 'Back pitch point'); // Bottom curve
  // Need to figure out what curve the front pitch point will intersect with
  $frontarmpitchY = py(-15) - 80;
  if($frontarmpitchY == py(-12)) addPoint(-20,px(-12),py(-12), 'Front pitch point'); // Smack between curves
  else if($frontarmpitchY < py(-12)) addPntAr(-20, arcYsect(-12, -121, -161, -16, $frontarmpitchY), 'Front pitch point'); // Top curve
  else if($frontarmpitchY > py(-12)) addPntAr(-20, arcYsect(-18, -181, -122, -12, $frontarmpitchY), 'Front pitch point'); // Bottom curve

  // Setting the armhole curve length
  $alen = ( arcLen(16, 161, 121, 12) + arcLen(12, 122, 181, 18) + arcLen(18, 182, 501, 5) ) * 2; 
  setm('Armhole', $alen);
  activatePart($preactive);
}

function buildSleeve() {
  GLOBAL $SLEEVECAPFACTOR;
  if(!$SLEEVECAPFACTOR) $SLEEVECAPFACTOR = 1;
  $preactive = activePart();
  activatePart('Sleeve');

  // Armhole + ease value
  $aseam = (getm('Armhole') + SLEEVECAP_EASE) * $SLEEVECAPFACTOR;
  addPoint(     1 , 0, 0);
  // Add 3cm to length for wrinkles
  addPoint(     2 , px(1), getm('SleeveLengthToWrist')-getp('CUFF_LENGTH')+30);
  addPoint(     3 , getm('UpperBicepsCircumference')+getp('BICEPS_EASE'), 0); 
  addPoint(  2030 , px(3), py(2));
  addPoint(     4 , px(3)*0.50, 0); 
  addPoint(   401 , px(3)*0.25, 0); 
  addPoint(   402 , px(3)*0.75, 0); 
  addPoint(   403 , px(401), py(2)); 
  addPoint(   404 , px(402), py(2)); 
  addPoint(   405 , px(4), py(2)); 
  addPoint(     5 , px(1), $aseam/3); 
  addPoint(     6 , px(3), py(5)); 
  addPoint(     7 , px(401), py(5)); 
  addPoint(     8 , px(402), py(5)); 
  addPoint(    50 , px(4), py(5)); 
  addPoint(    51 , px(4)-50, py(50)+50); 
  addPoint(    52 , px(51), py(51)+70); 
  addPoint(   701 , px(7), $aseam/6 - 15, 'Back Pitch Point');
  // I am moving this 5mm out for some extra room
  addPoint(   801 , px(8) + 5, $aseam/6, 'Front Pitch Point');
  // Angles of the segments of the sleevecap
  // Prevents from calculating them more than once
  $angle5701 = angle(5,701);
  $angle4701 = angle(4,701);
  $angle6801 = angle(6,801);
  $angle4801 = angle(4,801);
  addPntAr(  5701 , hopShift(5,701,hopLen(5,701)/2));
  addPntAr( 57011 , pShift(5701,$angle5701+90,5));
  addPntAr(  4701 , hopShift(4,701,hopLen(4,701)/2));
  addPntAr( 47011 , pShift(4701,$angle4701+90,15));
  addPntAr(  6801 , hopShift(6,801,hopLen(6,801)/2));
  // This bumps in 10 by the book, I'm making it 15
  addPntAr( 68011 , pShift(6801,$angle6801-90,15));
  addPntAr(  4801 , hopShift(4,801,hopLen(4,801)/2));
  // This bumps out 20 by the book, I'm making it 23
  addPntAr( 48011 , pShift(4801,$angle4801-90,23));

  addPntAr( 57012 , pShift(57011,$angle5701,25));
  addPntAr( 57013 , pShift(57011,$angle5701,-25));
  addPntAr( 47012 , pShift(47011,$angle4701,25));
  addPntAr( 47013 , pShift(47011,$angle4701,-25));
  addPntAr( 68012 , pShift(68011,$angle6801,25));
  addPntAr( 68013 , pShift(68011,$angle6801,-25));
  addPntAr( 48012 , pShift(48011,$angle4801,25));
  addPntAr( 48013 , pShift(48011,$angle4801,-25));
 
  addPntAr(    41 , pShift(4, 0, -25));
  addPntAr(    42 , pShift(4, 0, 25));
  addPntAr(    43 , arcShift(4, 42, 48012, 48011, 5), 'Crown Point');

  addPoint(    11 , px(1), py(5) + hopLen(2,5)/2 - 25, 'Elbow point');
  addPoint(    12 , px(3), py(11));
  // What is the usable cuff width?
  if(getp('CUFF_STYLE') < 4) $cuffwidth = getm('WristCircumference')+getp('CUFF_EASE') + 20;
  else if(getp('CUFF_STYLE') == 6) $cuffwidth = getm('WristCircumference')+getp('CUFF_EASE') + 30;
  else $cuffwidth = getm('WristCircumference')+getp('CUFF_EASE') + 30 - getp('CUFF_LENGTH')/2;
  // Sleeve width is cuff width plus cuff drape
  $drape = getp('CUFF_DRAPE');
  $width = $cuffwidth + $drape;
  addPoint(    21 , px(4)-$width/2, py(2));
  addPntAr(    22 , xMirror(21,px(4)));
  addPntAr(    23 , lineIsect(11, 12, 5, 21));
  addPntAr(    24 , lineIsect(11, 12, 6, 22));
  addPoint(   2403 , px(21)+hopLen(21,22)*0.25, py(2)); 
  addPoint(   2404 , px(21)+hopLen(21,22)*0.75, py(2)); 
  addPntAr(   406 , pShift(2403, -90 , 3)); 
  addPntAr(   407 , pShift(2404, 90 , 3)); 
  $wavelen = hopLen(21,22)/8;
  addPntAr(  4061 , pShift(406, 0 , $wavelen)); 
  addPntAr(  4062 , pShift(406, 180 , $wavelen)); 
  addPntAr(  4071 , pShift(407, 0 , $wavelen)); 
  addPntAr(  4072 , pShift(407, 180 , $wavelen)); 
  addPntAr(    25 , pxy(406));
  addPntAr(    26 , pShift(2403,90,getp('SLEEVE_PLACKET_LENGTH')+getp('SLEEVE_PLACKET_WIDTH')*0.25),'Placket cut');
  addPntAr(    27 , pShift(405,90,80));
  // Pleats
  if(getp('CUFF_PLEATS') == 1) { // Single pleat
    addPntAr(    28 , pShift(405,180,$drape/2));
    addPntAr(    29 , pShift(28,90,80));
    addPntAr(    30 , pShift(405,180,$drape));
    addPntAr(    31 , pShift(30,90,80));
  } else { // Double pleat
    addPntAr(    28 , pShift(405,180,$drape*0.3));
    addPntAr(    29 , pShift(28,90,80));
    addPntAr(    30 , pShift(405,180,$drape*0.6));
    addPntAr(    31 , pShift(30,90,80));
    // Position please in the middle between seam and placket
    $len = arcLen(21,21,4062,25) - getp('SLEEVE_PLACKET_WIDTH');
    addPntAr(    32 , pShift(21,0,$len/2));
    addPntAr(    33 , pShift(32,90,80));
    addPntAr(    34 , pShift(32,0,$drape*0.2));
    addPntAr(    35 , pShift(34,90,80));
    addPntAr(    36 , pShift(32,180,$drape*0.2));
    addPntAr(    37 , pShift(36,90,80));
    addNotch(34);
    addNotch(36);
  }
  // Moving grainline a bit
  AddPoint(5001, px(4)+25,py(4)+10); 
  AddPoint(5002, px(5001),py(405)-10); 
  activatePart($preactive);
}

function mmpPoints($part=FALSE, $model=FALSE) {
  if(!$part) $part = activePart();
  if(!$model) $model = activeModel();
  switch($part) {
    case 'Block':
    break;
    case 'FrontAndBackBlock':
    break;
    case 'FrontRight':
      buildBody();
      tweakCollaropening();
      clonePoints('FrontAndBackBlock');
      $width = getp('BUTTON_PLACKET_WIDTH')/2;
      if(getp('BUTTON_PLACKET_STYLE') == 1) $width2 = $width*3; // Classic placket
      else $width2 = $width*5;
      addPntAr( 2040, pShift(-4,180,$width));
      addPntAr( 2011, pShift(-11,180,$width));
      addPntAr( 2041, array_shift(arcLineIsect(-17,-171,-9,-10,2011,2040)));
      addPntAr( 2042, pShift(-17,0,$width));
      addPntAr( 2043, pShift(-17,0,$width2));
      addPntAr( 2044, pShift(-4,0,$width2));
      addPntAr( 2045, pShift(-4,0,$width));
      if(getp('BUTTON_PLACKET_STYLE') == 2) addPntAr( 2046, pShift(-4,0,$width*3));
      // Need to split this curve for a seperate placket
      $result = splitArc(-10,-9,-171,-17,2041); 
      array_shift($result);
      addPntAr( 2051, array_shift($result));
      addPntAr( 2052, array_shift($result));
      addPntAr( 2053, array_shift($result));
      // And split other side of curve for folding back
      $result = splitArc(-17,-171,-9,-10,2041); 
      array_shift($result);
      addPntAr( 2151, array_shift($result));
      addPntAr( 2152, array_shift($result));
      addPntAr( 2153, array_shift($result));
      addPntAr( -2051 , xMirror(2151,px(2042)));
      addPntAr( -2052 , xMirror(2152,px(2042)));
      addPntAr( -2053 , xMirror(2153,px(2042)));
      addPntAr( -2017 , xMirror(-17,px(2042)));
      addPntAr( -2054, xMirror(-2051,px(-2053)));
      addPntAr( -2055, xMirror(-2052,px(-2053)));
      addPntAr( -2056, xMirror(-2017,px(-2053)));

      $btnlen = getp('GARMENT_LENGTH') - getp('LENGTH_BONUS') - getp('BUTTON_FREE_LENGTH') -py(-17);
      $btndist = $btnlen / (getp('FRONT_BUTTONS'));
      addPoint (3000 , px(-4), $btnlen+py(-17), 'button start');
      if(getp('BUTTON_PLACKET_TYPE')==1) addNotch(3000, 'button');
      for($i=1;$i<getp('FRONT_BUTTONS');$i++) {
        $pid = 3000+$i;
        addPntAr( $pid, pShift(3000, 90, $btndist*$i), 'Button');
        if(getp('BUTTON_PLACKET_TYPE')==1) addNotch($pid, 'button');
      }
      if(getp('EXTRA_TOP_BUTTON')) {
        $extrapid = $pid +1;
        addPntAr( $extrapid, pShift($pid,90,$btndist/2), 'Extra button');
        if(getp('BUTTON_PLACKET_TYPE')==1) addNotch($extrapid, 'button');
      }
      // Shaping of side seam
      if (getp('WAIST_REDUCTION') <= 100) { // Only shape side seams
        $in = getp('WAIST_REDUCTION')/4;
        $hin = (getp('HIPS_REDUCTION'))/4;
      } else { // Back darts too
        $in = (getp('WAIST_REDUCTION')*0.6)/4;
        $hin = (getp('HIPS_REDUCTION')*0.6)/4;
      }
        addPntar( 6001, pShift(5,-90,yDist(5,3)*0.2));
        addPoint( 6011, px(5)+$in,py(3)-yDist(5,3)/2);
        addPoint( 6021, px(5)+$in,py(3));
        addPoint( 6031, px(5)+$in,py(3)+yDist(3,8001)/2);
        addPntAr( 8002 , pShift(-8001,90,yDist(6031,-8001)/4));
        addPoint( 8003 , px(-4),py(-8001));
        addPntAr( 5001 , pShift(5,-90,yDist(5,6011)/4));
      // Hem shape
      if(isPoint(2044)) addPntAr( 6660 , pxy(2044));
      else addPntAr( 6660 , pxy(-4));
      addPoint( 6663 , px(-8001)-getp('HIP_FLARE')/4,py(-8001)+getp('LENGTH_BONUS')-getp('HEM_CURVE'));
      addPntAr( 6662 , pShift(6663,90,yDist(-8001,6663)*0.3));
      addPntAr( 6661 , pShift(-8001,-90,yDist(-8001,6663)*0.3));
      //addPntAr( 8002 , pShift(8001,90,yDist(6031,-8001)/4));
      switch(getp('HEM_STYLE')) {
        case 1: // Straight hem
          addPntAr(6664, pxy(6663));
          addPoint(6665, px(6663), py(6663)+getp('HEM_CURVE'));
          addPntAr(6666, pxy(6665));
          addPntAr(6667, pxy(6666));
          addPntAr(6668, pShift(6666,180,xDist(6660,6666)*0.1));
          addPntAr(6669, pxy(6668));
        break;
        case 2: // Baseball hem
          addPntAr(6664, pShift(6663,180,xDist(6660,6663)*0.3));
          addPntAr(6665, pShift(6660,0,xDist(6660,6663)*0.7));
          addPntAr(6666, pShift(6660,0,xDist(6660,6663)*0.2));
          addPntAr(6667, pxy(6666));
          addPntAr(6668, pShift(6666,180,xDist(6660,6666)*0.1));
          addPntAr(6669, pxy(6668));
        break;
        case 3: // Slashed hem
          addPoint(6664, px(6663), py(6663)+getp('HEM_CURVE'));
          addPoint(6665, px(6663), py(6663)+getp('HEM_CURVE'));
          addPntAr(6666, pShift(6664,180,xDist(6660,6663)*0.3));
          addPntAr(6667, pxy(6666));
          addPntAr(6668, pShift(6666,180,xDist(6660,6666)*0.1));
          addPntAr(6669, pxy(6668));
        break;
      }
      // Smoothing out curve (overwriting points)
      addPntAr(8002,arcYsect(-8001,8002,6031,6021,py(8002)));
      addPntAr(6661, rotate(6661,-8001,angle(-8001,8002)+90));
      // Needed to block out seam allowance
      addPoint( 20401 , px(2043)+0.25,py(2043)-11);
      addPntAr( 20402 , pShift(20401,0,11));
      addPoint( 20403 , px(20401),py(-4)+11);
      addPoint( 20404 , px(20402),py(20403));
      addPoint( 50 , px(-10), py(-7));
    break;
    case 'ButtonPlacket':
      clonePoints('FrontRight');
      addNotch(3000, 'button');
      for($i=1;$i<getp('FRONT_BUTTONS');$i++) {
        $pid = 3000+$i;
        addNotch($pid, 'button');
      }
      if(getp('EXTRA_TOP_BUTTON')) {
        $extrapid = $pid +1;
        addNotch($extrapid, 'button');
      }
      addPoint(505050,px(2042),py(-3));
    break;
    case 'FrontLeft':
      clonePoints('FrontAndBackBlock');
      // Cutting armhole a bit deeper to match front side
      $frontextra = 10;
      addPntAr( 12, pShift(12,180,$frontextra));
      addPntAr( 121, pShift(121,180,$frontextra));
      addPntAr( 122, pShift(122,180,$frontextra));
      // Front pitch point
      addPntAr( 20, arcYsect(12,122,181,18,py(-20)), 'Front pitch point');
      $width = getp('BUTTONHOLE_PLACKET_WIDTH');
      switch(getp('BUTTONHOLE_PLACKET_STYLE')) {
        case 1: // Classic placket
          $edge = getp('BUTTONHOLE_PLACKET_FOLD_WIDTH');
          addPntAr(4000, pShift(4,180,$edge*2), 'New center front');
          addPntAr(4001, pShift(4000,0,$width/2), 'Fold here');
          addPntAr(4002, pShift(4000,0,$width/2+$edge), 'Stitches');
          addPntAr(4003, pShift(4000,0,$width/2-$edge), 'Stitches');
          addPntAr(4004, pShift(4000,180,$width/2), 'Fold here');
          addPntAr(4005, pShift(4000,180,$width/2+$edge), 'Stitches');
          addPntAr(4006, pShift(4000,180,$width/2-$edge), 'Stitches');
          addPntAr(4007, pShift(4004,180,$width), 'Edge');
          for($i=0;$i<8;$i++){
            $pid = 4100+$i;
            $oid = 4000+$i;
            addPoint($pid,px($oid),py(17));
          }
          addPntAr(4108, arcXsect(17,171,9,10,px(4102)));
          addPoint(4008, px(4108),py(4001));
          $result = splitArc(17,171,19,10,4108);
          array_shift($result);
          addPntAr(41081, array_shift($result));
          addPntAr(41082, array_shift($result));
          $result = splitArc(10,9,171,17,4108);
          array_shift($result);
          addPntAr(41091, array_shift($result));
          addPntAr(41092, array_shift($result));
          // Needed to block out seam allowance
          addPntAr( 4010 , pShift(4007,-90,11));
          // Just to avoid cutting off half of the stroke width
          addPntAr( 4010 , pShift(4010,180,0.25));
          addPntAr( 4011 , pShift(4010,180,11));
          addPntAr( 4110 , pShift(4107,90,11));
          // Just to avoid cutting off half of the stroke width
          addPntAr( 4110 , pShift(4110,180,0.25));
          addPntAr( 4111 , pShift(4110,180,11));
        break;
        case 2: // Seamless
          $edge = getp('BUTTONHOLE_PLACKET_FOLD_WIDTH');
          addPntAr(4000, pxy(4), 'New center front');
          addPntAr(4001, pShift(4000,180,$width/2), 'Fold here');
          addPntAr(4002, pShift(4000,180,$width*1.5), 'Fold again');
          addPntAr(4007, pShift(4000,180,$width*2.5), 'Edge');
          addPoint(4100, px(4000), py(17), 'New center front');
          addPoint(4101, px(4001), py(17), 'Fold here');
          addPoint(4102, px(4002), py(17), 'Fold again');
          addPoint(4107, px(4007), py(17), 'Edge');
          addPntAr(4108, arcXsect(4100,171,9,10,px(4100)+$width/2));
          addPoint(4008, px(4108),py(4001));
          $result = splitArc(4100,171,9,10,4108);
          array_shift($result);
          addPntAr(41081, array_shift($result));
          addPntAr(41082, array_shift($result));
          addPntAr(41083, xMirror(4108,px(4101)));
          addPntAr(41084, xMirror(41081,px(4101)));
          addPntAr(41085, xMirror(41082,px(4101)));
          addPntAr(41086, xMirror(4100,px(4101)));
          addPntAr(41087, xMirror(41084,px(4102)));
          addPntAr(41088, xMirror(41085,px(4102)));
          addPntAr(41089, xMirror(41086,px(4102)));
          $result = splitArc(10,9,171,4100,4108);
          array_shift($result);
          addPntAr(41091, array_shift($result));
          addPntAr(41092, array_shift($result));

          // Needed to block out seam allowance
          addPntAr( 4010 , pShift(4007,-90,11));
          // Just to avoid cutting off half of the stroke width
          addPntAr( 4010 , pShift(4010,180,0.25));
          addPntAr( 4011 , pShift(4010,180,11));
          addPntAr( 4110 , pShift(4107,90,11));
          // Just to avoid cutting off half of the stroke width
          addPntAr( 4110 , pShift(4110,180,0.25));
          addPntAr( 4111 , pShift(4110,180,11));
        break;
      }

      $btnlen = getp('GARMENT_LENGTH') - getp('LENGTH_BONUS') - getp('BUTTON_FREE_LENGTH') -py(17);
      $btndist = $btnlen / (getp('FRONT_BUTTONS'));
      addPoint (3000 , px(4000), $btnlen+py(17), 'button start');
      if(getp('BUTTONHOLE_PLACKET_TYPE')==1) addMarker(3000, 'buttonhole',array('rotate'=>90));
      for($i=1;$i<getp('FRONT_BUTTONS');$i++) {
        $pid = 3000+$i;
        addPntAr( $pid, pShift(3000, 90, $btndist*$i), 'Button');
        if(getp('BUTTONHOLE_PLACKET_TYPE')==1) addNotch($pid, 'buttonhole');
      }
      if(getp('EXTRA_TOP_BUTTON')) {
        $extrapid = $pid +1;
        addPntAr( $extrapid, pShift($pid,90,$btndist/2), 'Extra button');
        if(getp('BUTTONHOLE_PLACKET_TYPE')==1) addNotch($extrapid, 'buttonhole');
      }
      // Shaping of side seam
      if (getp('WAIST_REDUCTION') <= 100) { // Only shape side seams
        $in = getp('WAIST_REDUCTION')/4;
        $hin = (getp('HIPS_REDUCTION'))/4;
      } else { // Back darts too
        $in = (getp('WAIST_REDUCTION')*0.6)/4;
        $hin = (getp('HIPS_REDUCTION')*0.6)/4;
      }
        addPntar( 6001, pShift(5,-90,yDist(5,3)*0.2));
        addPoint( 6011, px(5)-$in,py(3)-yDist(5,3)/2);
        addPoint( 6021, px(5)-$in,py(3));
        addPoint( 6031, px(5)-$in,py(3)+yDist(3,8001)*0.5);
        addPntAr( 8002 , pShift(8001,90,yDist(6031,8001)/4));
        addPoint( 8003 , px(4),py(8001));
        addPntAr( 5001 , pShift(5,-90,yDist(5,6011)/4));
      // Hem shape
      if(isPoint(4007)) addPntAr( 6660 , pxy(4007));
      else addPntAr( 6660 , pxy(-4));
      addPoint( 6663 , px(8001)+getp('HIP_FLARE')/4,py(8001)+getp('LENGTH_BONUS')-getp('HEM_CURVE'));
      addPntAr( 6662 , pShift(6663,90,yDist(8001,6663)*0.3));
      addPntAr( 6661 , pShift(8001,-90,yDist(8001,6663)*0.3));
      //addPntAr( 8002 , pShift(8001,90,yDist(6031,-8001)/4));
      switch(getp('HEM_STYLE')) {
        case 1: // Straight hem
          addPntAr(6664, pxy(6663));
          addPoint(6665, px(6663), py(6663)+getp('HEM_CURVE'));
          addPntAr(6666, pxy(6665));
          addPntAr(6667, pxy(6666));
          addPntAr(6668, pShift(6666,180,xDist(6660,6666)*0.1));
          addPntAr(6669, pxy(6668));
        break;
        case 2: // Baseball hem
          addPntAr(6664, pShift(6663,180,xDist(6660,6663)*0.3));
          addPntAr(6665, pShift(6660,0,xDist(6660,6663)*0.7));
          addPntAr(6666, pShift(6660,0,xDist(6660,6663)*0.2));
          addPntAr(6667, pxy(6666));
          addPntAr(6668, pShift(6666,0,xDist(6660,6666)*0.1));
          addPntAr(6669, pxy(6668));
        break;
        case 3: // Slashed hem
          addPoint(6664, px(6663), py(6663)+getp('HEM_CURVE'));
          addPoint(6665, px(6663), py(6663)+getp('HEM_CURVE'));
          addPntAr(6666, pShift(6664,180,xDist(6660,6663)*0.3));
          addPntAr(6667, pxy(6666));
          addPntAr(6668, pShift(6666,180,xDist(6660,6666)*0.1));
          addPntAr(6669, pxy(6668));
        break;
      }
      // Smoothing out curve (overwriting points)
      addPntAr(8002,arcYsect(8001,8002,6031,6021,py(8002)));
      addPntAr(6661, rotate(6661,8001,angle(8001,8002)+90));
      // Calculating exact location of the pitchpoint notch
      $result = splitArc(18,181,122,12,20);
      array_shift($result);
      addPntAr(20001, array_shift($result));
      addPntAr(20002, array_shift($result));
      setp('FRONT_PITCH_DISTANCE', arcLen(-5,501,182,18) + arcLen(18,20001,20002,20));
      // Full front armhole len
      setp('FRONT_ARMHOLE_LENGTH', arcLen(-5,501,182,18) + arcLen(18,181,122,12) + arcLen(12,121,161,16));
      addPoint( 50 , px(10), py(-7));
    break;
    case 'ButtonPlacket':
      clonePoints('FrontRight');
      addNotch(3000, 'button');
      for($i=1;$i<getp('FRONT_BUTTONS');$i++) {
        $pid = 3000+$i;
        addNotch($pid, 'button');
      }
      if(getp('EXTRA_TOP_BUTTON')) {
        $extrapid = $pid +1;
        addNotch($extrapid, 'button');
      }
      addPoint(505050,px(4102),py(3));
    break;
    case 'ButtonholePlacket':
      clonePoints('FrontLeft');
      addNotch(3000, 'buttonhole');
      for($i=1;$i<getp('FRONT_BUTTONS');$i++) {
        $pid = 3000+$i;
        addNotch($pid, 'buttonhole');
      }
      if(getp('EXTRA_TOP_BUTTON')) {
        $extrapid = $pid +1;
        addNotch($extrapid, 'buttonhole');
      }
      addPoint(505050,px(4102),py(3));
    break;
    case 'Back':
      clonePoints('FrontAndBackBlock');
      addPoint( 5000 , px(12)*0.666, py(12));
      $hin = (getp('HIPS_REDUCTION'))/4;
      if (getp('WAIST_REDUCTION') <= 100) { // Only shape side seams
        $in = getp('WAIST_REDUCTION')/4;
      } else { // Back darts too
        $in = (getp('WAIST_REDUCTION')*0.6)/4;
        $dart = (getp('WAIST_REDUCTION')*0.4)/4;
        $hdart = (getp('HIPS_REDUCTION')*0.4)/4;
        addPoint( 6100, px(3)+(px(5)-$in)*0.55, py(3));
        addPoint( 6300, px(6100), py(200)-yDist(3,200)*0.15);
        addPntAr( 6121, pShift(6100,0,$dart));
        addPntAr( 6122, pShift(6100,180,$dart));
        addPoint( 6110, px(6100),py(3)-yDist(5,3)*0.75);
        addPoint( 6111, px(6121),py(3)-yDist(5,3)*0.2);
        addPoint( 6112, px(6122),py(3)-yDist(5,3)*0.2);
        addPoint( 6113, px(6121),py(3)+yDist(5,3)*0.2);
        addPoint( 6114, px(6122),py(3)+yDist(5,3)*0.2);
      }
      // Side shaping
      addPntar( 6001, pShift(5,-90,yDist(5,3)*0.2));
      addPoint( 6011, px(5)-$in,py(3)-yDist(5,3)/2);
      addPoint( 6021, px(5)-$in,py(3));
      addPoint( 6031, px(5)-$in,py(3)+yDist(3,200)/2);
      // Hem shape
      addPoint( 6660 , 0,py(8));
      addPoint( 6663 , px(8001)+getp('HIP_FLARE')/4,py(8001)+getp('LENGTH_BONUS')-getp('HEM_CURVE'));
      addPntAr( 6662 , pShift(6663,90,yDist(8001,6663)*0.3));
      addPntAr( 6661 , pShift(8001,-90,yDist(8001,6663)*0.3));
      addPntAr( 8002 , pShift(8001,90,yDist(6031,-8001)/4));
      switch(getp('HEM_STYLE')) {
        case 1: // Straight hem
          addPntAr(6664, pxy(6663));
          addPoint(6665, px(6663), py(6663)+getp('HEM_CURVE'));
          addPntAr(6666, pxy(6665));
          addPntAr(6667, pxy(6666));
          addPntAr(6668, pShift(6666,180,xDist(6660,6666)*0.1));
          addPntAr(6669, pxy(6668));
        break;
        case 2: // Baseball hem
          addPntAr(6664, pShift(6663,180,xDist(6660,6663)*0.3));
          addPntAr(6665, pShift(6660,0,xDist(6660,6663)*0.7));
          addPntAr(6666, pShift(6660,0,xDist(6660,6663)*0.2));
          addPntAr(6667, pxy(6666));
          addPntAr(6668, pShift(6666,180,xDist(6660,6666)*0.1));
          addPntAr(6669, pxy(6668));
        break;
        case 3: // Slashed hem
          addPoint(6664, px(6663), py(6663)+getp('HEM_CURVE'));
          addPoint(6665, px(6663), py(6663)+getp('HEM_CURVE'));
          addPntAr(6666, pShift(6664,180,xDist(6660,6663)*0.3));
          addPntAr(6667, pxy(6666));
          addPntAr(6668, pShift(6666,180,xDist(6660,6666)*0.1));
          addPntAr(6669, pxy(6668));
        break;
      }
      // Smoothing out curve (overwriting points)
      addPntAr(8002,arcYsect(8001,8002,6031,6021,py(8002)));
      addPntAr(6661, rotate(6661,8001,angle(8001,8002)+90));
      $flip = array(101,10,16,161,121,20,122,181,18,182,501,5,7,1040,1041,1042,1043,8,5000,6011,6021,6031,6100,6121,6122,6110,6300,6111,6112,6301,6302,6113,6114,6303,6401,6402,6403,6404,6405,6406,6407,6408,7001,7002,7003,6001,6445,6446,6447,6448,8001,8002,6661,6662,6663,6664,6665,6666,6667,6668,6669);
      foreach($flip as $pf) {
        $id = $pf*-1;
        addPntAr($id,xFlip($pf));
      }
      // Calculating exact location of the pitchpoint notch
      setp('BACK_PITCH_DISTANCE', arcLen(-5,-501,-182,-18) + arcLen(-18,-181,-122,-20));
      addPoint( 50, px(2),py(2)+69);
      addPoint( 51, px(2),py(-20));
      addPoint( 52, px(2),py(6660));
    break;
    case 'Yoke':
      clonePoints('FrontAndBackBlock');
      $flip = array(101,10,16,161,121,20,122,181,18,182,501,5,7,1040,1041,1042,1043,8);
      foreach($flip as $pf) {
        $id = $pf*-1;
        addPntAr($id,xFlip($pf));
      }
      addPoint(1011, px(11),py(20));
      addPntAr( 4001 , rotate(17,1011,-45));
      addPntAr( 4002 , lineIsect(1011,4001,10,16));
      addPntAr(-4002 , xFlip(4002));
      addPntAr( 4003 , rotate(1011,1,45));
      addPntAr( 4004 , lineIsect(1,4003,-20,20));
      addPntAr(-4004 , xFlip(4004));
      addPntAr( 4005 , pShift(1,-90,hopLen(1,1011)/2));
      addPntAr( 4006 , pShift(4005,0,66.6));
      addPntAr( 4007 , array_shift(arcLineIsect(20,121,161,16,4005,4006))); 
      addPntAr( 4008 , array_shift(arcLineIsect(-20,-121,-161,-16,4005,4006)));
      if(getp('SPLIT_YOKE')) addPoint(40050,px(-4002),py(4005));
      else addPoint(40050,px(4005),py(4005));

    break;
    case 'Sleeve':
      buildSleeve();
      tweakSleeveCap();
      // Where exactly should the pitch point notch go?
      // Front
      $lenq1 = arcLen(6,6,68012,68011);
      $lenq2 = arcLen(68011,68013,801,801);
      $lenq12 = $lenq1+$lenq2;
      $len = getp('FRONT_PITCH_DISTANCE');
      if($lenq12 == $len) addPoint(801000, pxy(801), 'Front pitch point'); // Pitch exactly one borden Q1 and Q2
      else if($lenq12>$len) { // Pitch in Q1
        addPntAr(801000, arcShift(68011,68013,801,801,$len-$lenq1),'Front pitch point');
      } else { // Pitch in Q2
        addPntAr(801000, arcShift(801,801,48013,48011,$len-$lenq12),'Front pitch point');
      }
      // Shoulder point
      $len = getp('FRONT_ARMHOLE_LENGTH');
      $lenq3 = arcLen(801,801,48013,48011);
      $lenq4 = arcLen(48011,48012,42,43);
      $lenq14 = $lenq12 + $lenq3 + $lenq4;
      if($len == $lenq14)  addPntAr(43000, pxy(43), 'Shoulder point'); // Shoulder point exactly where intended
      else if ($len > $lenq14) { // Shoulder point more towards the back
        addPntAr(43000, arcShift(43,41,47012,47011, $len-$lenq14));
      } else { // Shoulder point more towards the front
        addPntAr(43000, arcShift(43,42,48012,48011, $lenq14-$len));
      }

      // Back
      $lenq1 = arcLen(5,5,57012,57011);
      $lenq2 = arcLen(57011,57013,701,701);
      $lenq12 = $lenq1+$lenq2;
      $len = getp('BACK_PITCH_DISTANCE');
      if($lenq12 == $len) addPoint(701000, pxy(701), 'Front pitch point'); // Pitch exactly one borden Q1 and Q2
      else if($lenq12>$len) { // Pitch in Q1
        addPntAr(701000, arcShift(57011,57013,701,701,$len-$lenq1),'Back pitch point');
        addPntAr(701001, arcShift(57011,57013,701,701,$len-$lenq1+2.5),'Back pitch point Notch 1');
        addPntAr(701002, arcShift(57011,57013,701,701,$len-$lenq1-2.5),'Back pitch point Notch 2');
      } else { // Pitch in Q2
        addPntAr(701000, arcShift(701,701,47013,47011,$len-$lenq12),'Back pitch point');
        addPntAr(701001, arcShift(701,701,47013,47011,$len-$lenq12+2.5),'Back pitch point Notch 1');
        addPntAr(701002, arcShift(701,701,47013,47011,$len-$lenq12-2.5),'Back pitch point Notch 2');
      }
    break;
    case 'CollarStand':
      buildCollarstand();
      tweakCollarstand();
    break;
    case 'Collar':
      buildCollar();
      tweakCollar();
    break;
    case 'UnderCollar':
      clonePoints('Collar');
      addPntAr(5,pShift(5,90,3));
      addPntAr(6,pShift(6,90,2));
      addPntAr(-6,pShift(-6,90,2));
    break;
    case 'BarrelCuff':
      addPoint(0,0,0);
      addPoint(1,getm('WristCircumference')/2+getp('CUFF_EASE')/2,0,'Button line');  
      addPntAr(2,pShift(1,0,10));
      addPoint(3,px(1),getp('CUFF_LENGTH'));
      addPoint(4,px(2),py(3));
      addPoint(5,getm('WristCircumference')/2-getp('CUFF_EASE')/2+20,0, 'Narrow button line');
      addPoint(6, px(1), py(3)*0.45,'Single button');
      addPoint(7, px(1), py(3)*0.3,'Double button, A');
      addPoint(8, px(1), py(3)*0.7,'Double button, B');
      addPoint(9, px(5), py(3)*0.45,'Single button');
      addPoint(10, px(5), py(3)*0.3,'Double button, A');
      addPoint(11, px(5), py(3)*0.7,'Double button, B');
      addPoint(12, px(2), py(3)*0.25);
      addPntAr(13, pShift(2,180,hopLen(2,12)));
      addPntAr(14, pShift(13,0,bezierCircle(hopLen(2,12))));
      addPntAr(15, pShift(12,90,bezierCircle(hopLen(2,12))));
      $flip = array(1,2,3,4,6,7,8,12,13,14,15);
      foreach($flip as $pf) {
        $id = $pf*-1;
        addPntAr($id,xFlip($pf));
      }
      // Shift for buttonholes
      addPntAr(-6,pShift(-6,0,4));
      addPntAr(-7,pShift(-7,0,4));
      addPntAr(-8,pShift(-8,0,4));
      if(getp('BARRELCUFF_BUTTONS') == 2) {
        addMarker(7,'button');
        addMarker(8,'button');
        addMarker(-7,'buttonhole',array('rotate'=>90));
        addMarker(-8,'buttonhole',array('rotate'=>90));
        if(getp('BARRELCUFF_NARROW_BUTTON') == 1) {
          addMarker(10,'button');
          addMarker(11,'button');
        }
      } else {
        addMarker(6,'button');
        addMarker(-6,'buttonhole',array('rotate'=>90));
        if(getp('BARRELCUFF_NARROW_BUTTON') == 1) addMarker(9,'button');
      }
      addPoint(50,px(-3)/2,py(6));
    break;
    case 'FrenchCuff':
      addPoint(0,0,0);
      addPoint(1,getm('WristCircumference')/2+getp('CUFF_EASE')/2,0,'Button line');  
      addPntAr(2,pShift(1,0,15));
      addPoint(3,px(1),getp('CUFF_LENGTH'));
      addPoint(4,px(2),py(3));
      addPoint(6, px(1), py(3)*0.45,'First buttonhole');
      addPntAr(7, pShift(6,-90,hopLen(6,3)*2), 'Second buttonhole');
      // Shift buttonholes
      addPntAr(6,pShift(6,180,4));
      addPntAr(7,pShift(7,180,4));
      addPoint(12, px(2), py(3)*0.25);
      addPntAr(13, pShift(2,180,hopLen(2,12)));
      addPntAr(14, pShift(13,0,bezierCircle(hopLen(2,12))));
      addPntAr(15, pShift(12,90,bezierCircle(hopLen(2,12))));
      addPoint(16,px(2),py(3)*2);
      addPntAr(17,yMirror(12,py(4)));
      addPntAr(18,yMirror(13,py(4)));
      addPntAr(19,yMirror(14,py(4)));
      addPntAr(20,yMirror(15,py(4)));
      addPntAr(21,yMirror(1,py(4)));
      $flip = array(1,2,3,4,6,7,8,12,13,14,15,16,17,18,19,20,21);
      foreach($flip as $pf) {
        $id = $pf*-1;
        addPntAr($id,xFlip($pf));
      }
      // Need to move foldline a bit and buttonholes at back
      // This way first half coverd the seam
      $foldshift = 1.5;
      addPntAr(4, pShift(4,-90,$foldshift));
      addPntAr(-4, pShift(-4,-90,$foldshift));
      addPntAr(7, pShift(7,-90,$foldshift*2));
      addPntAr(-7, pShift(-7,-90,$foldshift*2));
      addMarker(6,'buttonhole',array('rotate'=>90));
      addMarker(7,'buttonhole',array('rotate'=>90));
      addMarker(-6,'buttonhole',array('rotate'=>90));
      addMarker(-7,'buttonhole',array('rotate'=>90));
      addPoint(50, px(-8),py(-3));
    break;
    case 'CuffGuard':
      if (getp('SLEEVE_PLACKET_WIDTH')>=20) {
        $width = 10;
        $btndist = 5;
        $fold = 8;
      } else {
        $btndist = getp('SLEEVE_PLACKET_WIDTH')/4;
        $width = getp('SLEEVE_PLACKET_WIDTH')/2;
        $fold = $width-1.5;
      }
      addPoint(0,0,0);
      addPoint(1,getp('SLEEVE_PLACKET_LENGTH')+10+getp('SLEEVE_PLACKET_WIDTH')*0.25,0);
      addPntAr(2,pShift(1,-90,$fold));
      addPntAr(3,pShift(2,-90,$width));
      addPntAr(4,pShift(3,-90,$width));
      addPntAr(5,pShift(4,-90,$fold));
      addPntAr(6,pShift(0,-90,$fold));
      addPntAr(7,pShift(6,-90,$width));
      addPntAr(8,pShift(7,-90,$width));
      addPntAr(9,pShift(8,-90,$fold));
      addPntAr(10,pShift(0,0,10));
      addPntAr(11,pShift(9,0,10));
      addPoint(12,px(1)/2,py(3)+$btndist);
      addMarker(12,'button');
      addPoint(50,px(1)/4,py(3));
    break;
    case 'CuffPlacket':
      // HERE
      if (getp('SLEEVE_PLACKET_WIDTH')>=20) {
        $width = 10;
        $btndist = 5;
        $fold = 8;
      } else {
        $btndist = getp('SLEEVE_PLACKET_WIDTH')/4;
        $width = getp('SLEEVE_PLACKET_WIDTH')/2;
        $fold = $width-1.5;
      }
      addPoint(0 , 0, 0);
      // 1.5 for fixed part, 0.7 for fold = 2.2 times 
      addPoint(1,getp('SLEEVE_PLACKET_LENGTH')+10+getp('SLEEVE_PLACKET_WIDTH')*2.2,0);
      addPntAr(2,pShift(1,-90,$fold));
      addPntAr(3,pShift(2,-90,getp('SLEEVE_PLACKET_WIDTH')));
      addPntAr(4,pShift(3,-90,getp('SLEEVE_PLACKET_WIDTH')+2));
      addPntAr(5,pShift(4,-90,$fold));
      addPoint(6,getp('SLEEVE_PLACKET_LENGTH')+getp('SLEEVE_PLACKET_WIDTH')*1.5+10,0);
      addPntAr(7,pShift(6,-90,$fold));
      addPntAr(8,pShift(7,-90,getp('SLEEVE_PLACKET_WIDTH')));
      addPntAr(9,pShift(8,-90,getp('SLEEVE_PLACKET_WIDTH')+2));
      addPntAr(10,pShift(9,-90,$fold));
      addPoint(11,getp('SLEEVE_PLACKET_LENGTH')+10,$fold);
      addPntAr(12,pShift(11,-90,getp('SLEEVE_PLACKET_WIDTH')));
      addPntAr(13,pShift(3,-90,getp('SLEEVE_PLACKET_WIDTH')/3));
      addPntAr(14,pShift(13,180,getp('SLEEVE_PLACKET_WIDTH')*0.75));
      addPoint(15,px(12)+getp('SLEEVE_PLACKET_WIDTH')*0.35,py(3)+2*getp('SLEEVE_PLACKET_WIDTH')/3);
      addPoint(16,px(15),py(5));
      addPoint(17,px(15)+getp('SLEEVE_PLACKET_WIDTH')*0.5,py(15));
      addPoint(18,py(0),py(16));
      addPoint(19,py(0),py(2));
      addPoint(20,py(0),py(3));
      addPoint(21,py(0),py(4));
      addPntAr(22,lineIsect(2,8,3,7));
      addPoint(23,px(15),py(4));
      addPoint(24,px(0)+10,py(0));
      addPoint(25,px(0)+10,py(5));
      addPntAr(26,yMirror(22,py(2)));
      addPntAr(27,yMirror(22,py(3)));
      addPntAr(28,lineIsect(6,1,7,26));
      addPntAr(29,lineIsect(13,14,8,27));
      addPoint(30,px(11)/2+5,py(22));
      addMarker(30,'buttonhole',array('rotate'=>90));
      addPoint(50,px(11)/4,py(12));

    break;
  }
}

function mmpTitles($part=FALSE, $model=FALSE) {
  if(!$part) $part = activePart();
  if(!$model) $model = activeModel();
  switch($part) {
    case 'FrontRight':
      addTitle('1', 'Front Right|Cut 1', 50, $part, $model);
    break;
    case 'ButtonPlacket':
      addTitle('1b', 'Button Placket|Cut 1', 505050, $part, $model);
    break;
    case 'FrontLeft':
      addTitle('2', 'Front Left|Cut 1', 50, $part, $model);
    break;
    case 'ButtonholePlacket':
      addTitle('2b', 'Buttonhole Placket|Cut 1', 505050, $part, $model);
    break;
    case 'Back':
      addTitle('3', "Back|Cut 1", 50, $part, $model);
    break;
    case 'Yoke':
      if(getp('SPLIT_YOKE')) addTitle('4', "Yoke|Cut 4", 40050, $part, $model);
      else  addTitle('4', "Yoke|Cut 2", 40050, $part, $model);
    break;
    case 'Sleeve':
      addTitle('5', "Sleeve|Cut 2|Left is back, right is front", 50, $part, $model);
    break;
    case 'CollarStand':
      addTitle('6', "Collar Stand|Cut 2 from fabric|Cut 2 from interfacing", 56, $part, $model);
    break;
    case 'Collar':
      addTitle('7', "Collar|Cut 1 from fabric|Cut 1 from interfacing", 10, $part, $model);
    break;
    case 'UnderCollar':
      addTitle('8', "Under Collar|Cut 1 from fabric|Cut 1 from interfacing", 10, $part, $model);
    break;
    case 'BarrelCuff':
      addTitle('9', "Barrel Cuff|Cut 4 from fabric|Cut 2 from interfacing", 50, $part, $model);
    break;
    case 'FrenchCuff':
      addTitle('9', "French Cuff|Cut 4 from fabric|Cut 2 from interfacing", 50, $part, $model);
    break;
    case 'CuffGuard':
      addTitle('10', "Cuff Guard|Cut 2", 50, $part, $model);
    break;
    case 'CuffPlacket':
      addTitle('11', "Cuff Placket|Cut 2", 50, $part, $model);
    break;
  }
}

function mmpPaths($part=FALSE) {
  if(!$part) $part = activePart();
  switch($part) {
    case 'Block':
      $placeholder = 'M 1';
      addPath($placeholder);
      /*
      $path = 'M 1 L 4 L 8 L 5 C 501 182 18 C 181 122 12 C 121 161 16 L 10 C 10 101 1 z';
      $acrossback = 'M 11 L 12';
      $chestline = 'M 2 L 5';
      $waistline = 'M 3 L 7';
      $neckfront = 'M 10 C 9 171 17';
      $frame = 'M 1 L 0 L 6 L 5';
      if(getp('MODE') == 'grade') {
      } else {
        addPath($path);
        addPath($neckfront);
        addPath($acrossback, 'helpline');
        addPath($chestline, 'helpline');
        addPath($waistline, 'helpline');
        addPath($frame, 'helpline');
      }
       */
    break;
    case 'FrontAndBackBlock':
      $placeholder = 'M 1 ';
      addPath($placeholder);
      /*
      $path1 = 'M 1 L 4 L 8 L 5 C 501 182 18 C 181 122 12 C 121 161 16 L 10 C 10 101 1 z';
      $path2 = 'M -17 L -4 L -8 L -5 C -501 -182 -18 C -181 -122 -12 C -121 -161 -16 L -10 C -9 -171 -17 z';
      $acrossback = 'M 11 L -11';
      $chestline = 'M 2 L -2';
      $waistline = 'M 3 L -3';
      $frame1 = 'M 1 L 0 L 6 L 5';
      $frame2 = 'M -17 L -99999 L -6 ';
      if(getp('MODE') == 'grade') {
      } else {
        addPath($path1);
        addPath($path2);
        addPath($acrossback, 'helpline');
        addPath($chestline, 'helpline');
        addPath($waistline, 'helpline');
        addPath($frame1, 'helpline');
        addPath($frame2, 'helpline');
      }
       */
    break;
    break;
    case 'FrontRight':
      if(getp('HEM_STYLE') == 1) {
        $hem = 'M 6667 ';
        addPath('M 8001 M -5 C 5001 6011 6021 C 6031 8002 -8001 C 6661 6662 6663 L 6667','felled');
      } else { 
        $hem = 'M 6663 C 6664 6665 6669 ';
        addPath('M 8001 M -5 C 5001 6011 6021 C 6031 8002 -8001 C 6661 6662 6663','felled');
      }
      if(getp('BUTTON_PLACKET_TYPE')==2) {
        $hem .= 'L 2040 ';
        $path = 'M 2041 L 2040 ';
      } else {
       $hem .= 'L 6660';
       switch(getp('BUTTON_PLACKET_STYLE')) {
          case 1: // Classic placket
            $path = 'M -17 L -2017 C -2051 -2052 -2053 L 2043 L 2044 ';
            $plackethelp = 'M 2153 2040';
          break;
          case 2: // Seamless
            $path = 'M -17 L -2017 C -2051 -2052 -2053 C -2055 -2054 -2056 L 2043 L 2044 ';
            $placketfold = 'M -2053 2046';
          break;
       }
      }
      addPath($hem,'hemmed');
     $path .= 'L 6669 C 6668 6667 6666 C 6665 6664 6663 C 6662 6661 -8001 C 8002 6031 6021 C 6011 5001 -5 C -501 -182 -18 C -181 -122 -12 C -121 -161 -16 L -10 ';
     if(getp('BUTTON_PLACKET_TYPE')==2) $path .= 'C 2051 2052 2041 z';
     else $path .= 'C -9 -171 -17 z';
      $acrossback = 'M -12 L -11';
      $chestline = 'M 5 L -2';
      $waistline = 'M -7 L -3';
      $hipsline = 'M -8001 L 8003';
      addPathWithSA($path);
      if(getp('BUTTON_PLACKET_TYPE')==1) {
        addPath('M -4 L -17', 'helpline');
        addPath('M 2042 L 2045', 'foldline');
        if(getp('BUTTON_PLACKET_STYLE')==2) addPath('M 20401 L 20402 L 20404 L 20403 z', 'white');
      }
      if($placketfold) addPath($placketfold, 'foldline');
      if($plackethelp) addPath($plackethelp, 'helpline');
      addPath($acrossback, 'helpline');
      addPath($chestline, 'helpline');
      addPath($waistline, 'helpline');
      addPath($hipsline, 'helpline');
    break;
    case 'ButtonPlacket':
      $path = 'M 2153 C 2152 2151 -17 L -2017 C -2051 -2052 -2053 ';
      if(getp('BUTTON_PLACKET_STYLE')==2) $path .= 'C -2055 -2054 -2056 ';
      addPath('M 2040 L 6660','hemmed');
      $path .= 'L 2043 L 2044 L 2040 z';

      addPathWithSA($path);
      if(getp('BUTTON_PLACKET_STYLE')==2) {
        addPath('M 20401 L 20402 L 20404 L 20403 z', 'white');
        addPath('M -2053 L 2046', 'foldline');
      }
      addPath('M -4 L -17', 'helpline');
      addPath('M 2042 L 2045', 'foldline');
    break;
    case 'FrontLeft':
      if(getp('HEM_STYLE') == 1) {
        $hem = 'M 6667 ';
        addPath('M -5 C 5001 6011 6021 C 6031 8002 8001 C 6661 6662 6663 L 6667', 'felled');
      } else {
        if(getp('BUTTONHOLE_PLACKET_TYPE')==2 && getp('BUTTONHOLE_PLACKET_STYLE')==2) $hem = 'M 6663 C 6664 6665 4008 ';
        else $hem = 'M 6663 C 6664 6665 6669 ';
        addPath('M -5 C 5001 6011 6021 C 6031 8002 8001 C 6661 6662 6663', 'felled');
      }
      if(getp('BUTTONHOLE_PLACKET_TYPE')==1) { // Grown on
        $hem .= 'L 6660 ';
        if(getp('BUTTONHOLE_PLACKET_STYLE')==1) $path = 'M 17 L 4107 L 4007 '; // Classic placket
        else $path = 'M 17 L 41086 C 41084 41085 41083 C 41088 41087 41089 L 4107 L 4007 '; // Seamless
      } else { // Sewn on
        $hem .= 'L 4008';
        $path = 'M 4008 ';
      }
      addPath($hem,'hemmed');
      if(getp('BUTTONHOLE_PLACKET_TYPE')==2 && getp('BUTTONHOLE_PLACKET_STYLE')==2) { 
        if(getp('HEM_STYLE')) $path .= 'L 6669 C 6668 6667 6666 ';
      } else $path .= 'L 6669 C 6668 6667 6666 ';
      $path .= 'C 6665 6664 6663 C 6662 6661 8001 C 8002 6031 6021 C 6011 5001 5 C 501 182 18 C 181 122 12 C 121 161 16 L 10 ';
      if(getp('BUTTONHOLE_PLACKET_TYPE')==2) $path .= 'C 41091 41092 4108 z';
      else $path .= 'C 9 171 17 z';
      $acrossback = 'M 11 L 12';
      $chestline = 'M 2 L -5';
      $waistline = 'M 3 L -7';
      $hipsline = 'M 8001 L 8003';
      addPathWithSa($path);
      if(getp('BUTTONHOLE_PLACKET_TYPE')==1) { // Grown on
        switch(getp('BUTTONHOLE_PLACKET_STYLE')) {
          case 1: // Classic placket
            addPath('M 4110 L 4111 L 4011 L 4010 z', 'white');
            addPath('M 4105 L 4005', 'helpline');
            addPath('M 4104 L 4004', 'foldline');
            addPath('M 4106 L 4006', 'helpline');
            addPath('M 4103 L 4003', 'helpline');
            addPath('M 4102 L 4002', 'helpline');
            addPath('M 4101 L 4001', 'foldline');
            addPath('M 4100 L 4000', 'helpline');
          break;
          case 2: // Seamless
            addPath('M 4100 L 4000', 'helpline');
            addPath('M 4101 L 4001', 'helpline');
            addPath('M 4102 L 4002', 'helpline');
            addPath('M 4110 L 4111 L 4011 L 4010 z', 'white');
          break;
        }
      }
      addPath($acrossback, 'helpline');
      addPath($chestline, 'helpline');
      addPath($waistline, 'helpline');
      addPath($hipsline, 'helpline');
    break;
    case 'ButtonholePlacket':
      if(getp('BUTTONHOLE_PLACKET_STYLE')==1) $path = 'M 4108 C 41082 41081 17 L 4107 L 4007 L 4008 z'; // Classic placket
      else $path = 'M 4108 C 41082 41081 4100 L 41086 C 41084 41085 41083 C 41088 41087 41089 L 4107 L 4007 L 4008 z'; // Seamles
      addPath('M 4008 L 6660','hemmed');
      addPathWithSa($path);
      addPath('M 4110 L 4111 L 4011 L 4010 z', 'white');
      if(getp('BUTTONHOLE_PLACKET_STYLE')==1) { // Classic placket
        addPath('M 4105 L 4005', 'helpline');
        addPath('M 4104 L 4004', 'foldline');
        addPath('M 4106 L 4006', 'helpline');
        addPath('M 4103 L 4003', 'helpline');
        addPath('M 4102 L 4002', 'helpline');
        addPath('M 4101 L 4001', 'foldline');
        addPath('M 4100 L 4000', 'helpline');
      } else {
        addPath('M 41083 L 4002', 'foldline');
        addPath('M 4101 L 4001', 'foldline');
        addPath('M 4100 L 4000', 'helpline');
      }
    break;
    case 'Back':
      if(getp('HEM_STYLE') == 1 ) $hem = 'M -6667 L 6667';
      else $hem = 'M -6663 C -6664 -6665 -6666 C -6667 -6668 -6669 L 6660 L 6669 C 6668 6667 6666 C 6665 6664 6663';
      addPath($hem, 'hemmed');
        $path1 = 'M -20 C -122 -181 -18 C -182 -501 -5 C -6001 -6011 -6021 C -6031 -8002 -8001 C -6661 -6662 -6663 C -6664 -6665 -6666 C -6667 -6668 -6669 L 6660 ';
        $path1 .= 'L 6669 C 6668 6667 6666 C 6665 6664 6663 C 6662 6661 8001 C 8002 6031 6021 C 6011 6001 5 C 501 182 18 C 181 122 20 z';
        addPathWithSa($path1);
        if (getp('WAIST_REDUCTION') > 100) {
          $darts ='M 6300 C 6300 6302 6114 6122 C 6112 6110 6110 C 6110 6111 6121 C 6113 6300 6300 z ';
          $darts.='M -6300 C -6300 -6114 -6122 C -6112 -6110 -6110 C -6110 -6111 -6121 C -6113 -6300 -6300 z ';
          addPath($darts);
        }
      $acrossback = 'M 11 L -11';
      $chestline = 'M 5 L -5';
      $waistline = 'M 6021 L -6021';
      $hipsline = 'M 8001 L -8001';
      addPath($chestline, 'helpline');
      addPath($waistline, 'helpline');
      addPath($hipsline, 'helpline');
      addPath('M 51 L 52', 'grainline');
    break;
    case 'Yoke':
      if(getp('SPLIT_YOKE') == 1) $path = 'M 1 C -101 -10 -10 L -16 C -161 -121 -20 L 11 z';
      else $path = 'M 1 C -101 -10 -10 L -16 C -161 -121 -20 L 20 C 121 161 16 L 10 C 10 101 1 z';
      addPathWithSa($path);
      if(getp('SPLIT_YOKE') == 1) $grainhelp = 'M 11 L -4002 M -4004 L 1 M 4008 L 4005';
      else $grainhelp = 'M 1 L 1011 M 4002 L 1011 L -4002 M -4004 L 1 L 4004 M 4007 L 4008';
      addPath($grainhelp, 'helpline');
    break;
    case 'Sleeve':
      $help = "M 1 L 2 L 2030 L 3 z M 4 L 405 M 401 L 403 M 402 L 404 M 5 L 6 L 801 L 4 L 701 L 5 M 11 L 12";
      $path = "M 5 C 5 57012 57011 C 57013 701 701 C 701 47013 47011 C 47012 41 4 C 42 48012 48011 C 48013 801 801 C 801 68013 68011 C 68012 6 6 L 22 L 21 z";
      addPath('M 5 C 5 57012 57011 C 57013 701 701 C 701 47013 47011 C 47012 41 4 C 42 48012 48011 C 48013 801 801 C 801 68013 68011 C 68012 6 6 L 22', 'felled');
      addPathWithSa($path);
      addPath('M 5001 L 5002', 'grainline');
      addPath('M 26 L 2403');
      // First pleat
      addPath('M 405 L 27 M 29 L 28', 'foldline');
      addPath('M 30 L 31', 'helpline');
      if(isPoint(35)){ // Second pleat
        addPath('M 34 L 35 M 33 L 32', 'foldline');
        addPath('M 36 L 37', 'helpline');
      }
      break;
    case 'CollarStand':
      $path = 'M 42 C 43 6 61 C 62 33 32 L 31 C 51 21 22 C 23 72 71 C 7 13 12 C -13 -7 -71 C -72 -23 -22 C -21 -51 -31 L -32 C -33 -62 -61 C -6 -43 -42 z';
      addPathWithSa($path);
      $help = 'M 22 L 32 M -22 L -32';
      addPath($help,'helpline');
      addPath('M 57 L -57', 'grainline');
    break;
    case 'Collar':
      $path = 'M 5 C 6 4 4 L 8 C 8 9 12 L -12 C -9 -8 -8 L -4 C -4 -6 5 z';  
      addPathWithSa($path);
      addPath('M 11 L -11', 'grainline');
      addPath('M 5 L 3', 'helpline');
    break;
    case 'UnderCollar':
      $path = 'M 5 C 6 4 4 L 8 C 8 9 12 L -12 C -9 -8 -8 L -4 C -4 -6 5 z';  
      addPathWithSa($path);
      addPath('M 11 L -11', 'grainline');
      addPath('M 5 L 3', 'helpline');
    break;
    case 'BarrelCuff':
      switch(getp('CUFF_STYLE')) {
        case 1:
          addPathWithSa('M 0 L 13 C 14 15 12 L 4 L -4 L -12 C -15 -14 -13 z');
        break;
        case 2:
          addPathWithSa('M 0 L 13 L 12 L 4 L -4 L -12 L -13 z');
        break;
        case 3:
          addPathWithSa('M 0 L 2 L 4 L -4 L -2 z');
        break;
      }
      addPath('M 1 L 3 M -1 L -3', 'helpline');
    break;
    case 'FrenchCuff':
      switch(getp('CUFF_STYLE')) {
        case 4:
          addPathWithSa('M 0 L 13 C 14 15 12 L 17 C 20 19 18 L -18 C -19 -20 -17 L -12 C -15 -14 -13 z');
        break;
        case 5:
          addPathWithSa('M 0 L 13 L 12 L 17 L 18 L -18 L -17 L -12 L -13 z');
        break;
        case 6:
          addPathWithSa('M 0 L 2 L 16 L -16 L -2 z');
        break;
      }
      addPath('M 4 L -4', 'helpline');
    break;
    case 'CuffGuard':
      addPath('M 0 L 1 L 5 L 9 z');
      addPath('M 2 L 6 M 7 L 3 M 4 L 8', 'foldline');
    break;
    case 'CuffPlacket':
      // HERE
      addPath('M 0 L 1 L 13 L 14 L 17 L 15 L 16 L 18 z');
      addPath('M 19 L 2 M 7 L 22 L 8 M 3 L 20 M 2 L 22 L 3 M 21 L 23 M 7 L 28 M 8 L 29', 'foldline');
      addPath('M 11 L 12 M 24 L 25','helpline');
    break;
  }
}
 

function mmpNotches($part=FALSE, $model=FALSE) {
  if(!$part) $part = activePart();
  if(!$model) $model = activeModel();
  switch($part) {
    case 'CollarStand':
      addMarker(55,'buttonhole',array('rotate' => 90-getp('COLLAR_STAND_CURVE')));
      addNotch(-55,'button');
    break;
    case 'Collar':
    break;
    case 'FrontRight':
      addNotch(-10);
      addNotch(-16);
      addNotch(-20);
    break;
    case 'FrontLeft':
      addNotch(10);
      addNotch(16);
      addNotch(20);
    break;
    case 'Yoke':
      if(getp('SPLIT_YOKE') == 0) {
        addNotch(10);
        addNotch(16);
      }
      addNotch(-10);
      addNotch(-16);
    break;
    case 'Sleeve':
      addNotch(701001);
      addNotch(701002);
      addNotch(801000);
      addNotch(43000);
      addNotch(23);
      addNotch(24);
      addNotch(30);
      addNotch(405);
    break;
  }
}

function mmpSnippets($part=FALSE, $model=FALSE) {
  if(!$part) $part = activePart();
  if(!$model) $model = activeModel();
  switch($part) {
    case 'Back':
    break;
    case 'FrontAndBackBlock':
    break;
    case 'Sleeve':
      addSnippet('scalebox', 51);
      addSnippet('helplink', 52);
    break;
  }
}


/*
 * Prints messages for user in pattern comments
 */
function katie($model) {
  GLOBAL $P;
  $halfacrossback = getm('HalfAcrossBack')*2;
  msg('Hi, my name is Katie, I am a robot.');
  msg('I will make the '.$P['parameters']['PATTERN'].' pattern for model '.$P['parameters']['MODELNAME'].'.');
  msg('To do so, I will use these measurements:');
  msg();
  msg('                  Across back width: '.$halfacrossback.'mm');
  msg('               Biceps circumference: '.getm('UpperBicepsCircumference').'mm');
  msg('  Center back neck to natural waist: '.getm('CenterBackNeckToWaist').'mm');
  msg('                Chest circumference: '.getm('ChestCircumference').'mm');
  msg('                      Natural waist: '.getm('WaistCircumference').'mm');
  msg('     Natural waist to trouser waist: '.getm('NaturalWaistToTrouserWaist').'mm');
  msg('                 Neck circumference: '.getm('NeckCircumference').'mm');
  msg('                    Shoulder length: '.getm('ShoulderLength').'mm');
  msg('                     Shoulder slope: '.getm('ShoulderSlope').'mm');
  msg('           Shoulder to elbow length: '.getm('CrownPointToElbowLength').'mm');
  msg('                      Sleeve length: '.getm('SleeveLengthToWrist').'mm');
  msg('        Trouser waist circumference: '.getm('HipsCircumference').'mm');
  msg('                Wrist circumference: '.getm('WristCircumference').'mm');

  msg();
  msg('and with the following options:');
  msg();

  msg('                     Sleevecap ease: '.getp('SLEEVECAP_EASE').'mm');
  msg('                         Chest ease: '.getp('CHEST_EASE').'mm');
  msg('                         Waist ease: '.getp('WAIST_EASE').'mm');
  msg('                          Hips ease: '.getp('HIPS_EASE').'mm');
  msg('                        Biceps ease: '.getp('BICEPS_EASE').'mm');
  msg('                       Length bonus: '.getp('LENGTH_BONUS').'mm');
  msg('                          Hem style: '.getp('HEM_STYLE'));
  msg('                          Hem curve: '.getp('HEM_CURVE').'mm');
  msg('                          Hip flare: '.getp('HIP_FLARE').'mm');
  msg('                   Back neck cutout: '.getp('BACK_NECK_CUTOUT').'mm');
  msg('                        Collar ease: '.getp('COLLAR_EASE').'mm');
  msg('                Button placket type: '.getp('BUTTON_PLACKET_TYPE'));
  msg('               Button placket style: '.getp('BUTTON_PLACKET_STYLE'));
  msg('               Button placket width: '.getp('BUTTON_PLACKET_WIDTH').'mm');
  msg('            Buttonhole placket type: '.getp('BUTTONHOLE_PLACKET_TYPE'));
  msg('           Buttonhole placket style: '.getp('BUTTONHOLE_PLACKET_STYLE'));
  msg('           Buttonhole placket width: '.getp('BUTTONHOLE_PLACKET_WIDTH').'mm');
  msg('      Buttonhole placket fold width: '.getp('BUTTONHOLE_PLACKET_FOLD_WIDTH').'mm');
  msg('                      Front buttons: '.getp('FRONT_BUTTONS'));
  msg('                   Extra top button: '.getp('EXTRA_TOP_BUTTON'));
  msg('                 Button free length: '.getp('BUTTON_FREE_LENGTH').'mm');
  msg('                         Split yoke: '.getp('SPLIT_YOKE'));
  msg('                 Collar stand width: '.getp('COLLAR_STAND_WIDTH').'mm');
  msg('                  Collar stand bend: '.getp('COLLAR_STAND_BEND').'mm');
  msg('                 Collar stand curve: '.getp('COLLAR_STAND_CURVE').' degrees');
  msg('                         Collar gap: '.getp('COLLAR_GAP').'mm');
  msg('                        Collar bend: '.getp('COLLAR_BEND').'mm');
  msg('                       Collar flare: '.getp('COLLAR_FLARE').'mm');
  msg('                       Collar angle: '.getp('COLLAR_ANGLE').' degrees');
  msg('                        Collar roll: '.getp('COLLAR_ROLL').'mm');
  msg('                         Cuff style: '.getp('CUFF_STYLE').'mm');
  msg('                         Cuf length: '.getp('CUFF_LENGTH').'mm');
  msg('                 Barrelcuff buttons: '.getp('BARRELCUFF_BUTTONS'));
  msg('           Barrelcuff narrow button: '.getp('BARRELCUFF_NARROW_BUTTON'));
  msg('                          Cuff ease: '.getp('CUFF_EASE').'mm');
  msg('                         Cuff drape: '.getp('CUFF_DRAPE').'mm');
  msg('               Sleeve placket width: '.getp('SLEEVE_PLACKET_WIDTH').'mm');
  msg('              Sleeve placket length: '.getp('SLEEVE_PLACKET_LENGTH').'mm');
  msg();
  msg('And now...  REQUESTO PATRONUM!');
  msg('');
  msg('  > Crunching numbers');
}

function oneMoreThing() {
  emailPatternInfo();
}

function emailPatternInfo() {
  $user = getDrupalUserInfo(getp('USERID'));
  $mail = $user['mail'];
  $go = '/home/joost/go/bin/postman';
  $template = '/mmp/apiroot/patterns/lib/SingularShirt/pattern';
  $csv = '/mmp/downloadroot/'.getp('HASH').'/postman.csv';
  $sender = '"MakeMyPattern <jobot@makemypattern.com>"';
  $subject = 'Info leaflet for your SingularShirt pattern '.getp('HASH');
  $cmd = "$go -html $template.html -text $template.txt -csv $csv -sender $sender -subject \"$subject\" -server makemypattern.com -port 25 -user foo -password bar";
  // Make things more readable
  $cs[1] = 'Rounded barrel cuff';
  $cs[2] = 'Chamfer barrel cuff';
  $cs[3] = 'Straight barrel cuff';
  $cs[4] = 'Rounded French cuff';
  $cs[5] = 'Chamfer French cuff';
  $cs[6] = 'Straight French cuff';
  $hs[1] = 'Straight hem';
  $hs[2] = 'Baseball hem';
  $hs[3] = 'Slashed hem';
  $ps[1] = 'Classic';
  $ps[2] = 'Seamless';
  $yesno[0] = 'No';
  $yesno[1] = 'Yes';
  $pt[1] = 'Grown on';
  $pt[2] = 'Sewn on';
  $bcb[1] = 'Single button';
  $bcb[2] = 'Two buttons';
  $bnb[0] = 'No extra button';
  $bnb[1] = 'With extra narrow button';
  $sy[0] = 'No split yoke';
  $sy[1] = 'With a split yoke';
  $acrossback = getm('HalfAcrossBack')*2;
  $postman = 'Email,Pattern,Model,ModelidHash,Mabw,Mbc,Mcbtnw,Mcc,Mnw,Mnwttw,Mnc,Mshl,Mss,Mstel,Msvl,Mtwc,Mwc,Ose,Oche,Owe,Ohe,Obe,Olb,Ohs,Ohc,Ohf,Obnc,Ocle,Obpt,Obps,Obpw,Obhpt,Obhps,Obhpw,Obhpfw,Ofb,Oetb,Obfl,Osy,Ocsw,Ocsb,Ocsc,Ocg,Ocb,Ocf,Oca,Ocr,Ocs,Ocl,Obb,Obnb,Ocfe,Ocd,Ospw,Ospl'.
  "\n$mail,"
  .getp('PATTERN')
  .','.getp('MODELNAME')
  .','.getp('MODELID')
  .','.getp('HASH')
  .','.$acrossback/10
  .','.getm('UpperBicepsCircumference')/10
  .','.getm('CenterBackNeckToWaist')/10
  .','.getm('ChestCircumference')/10
  .','.getm('WaistCircumference')/10
  .','.getm('NaturalWaistToTrouserWaist')/10
  .','.getm('NeckCircumference')/10
  .','.getm('ShoulderLength')/10
  .','.getm('ShoulderSlope')/10
  .','.getm('CrownPointToElbowLength')/10
  .','.getm('SleeveLengthToWrist')/10
  .','.getm('HipsCircumference')/10
  .','.getm('WristCircumference')/10
  .','.getp('SLEEVECAP_EASE')/10
  .','.getp('CHEST_EASE')/10
  .','.getp('WAIST_EASE')/10
  .','.getp('HIPS_EASE')/10
  .','.getp('BICEPS_EASE')/10
  .','.getp('LENGTH_BONUS')/10
  .','.$hs[getp('HEM_STYLE')]
  .','.getp('HEM_CURVE')/10
  .','.getp('HIP_FLARE')/10
  .','.getp('BACK_NECK_CUTOUT')/10
  .','.getp('COLLAR_EASE')/10
  .','.$pt[getp('BUTTON_PLACKET_TYPE')]
  .','.$ps[getp('BUTTON_PLACKET_STYLE')]
  .','.getp('BUTTON_PLACKET_WIDTH')/10
  .','.$pt[getp('BUTTONHOLE_PLACKET_TYPE')]
  .','.$ps[getp('BUTTONHOLE_PLACKET_STYLE')]
  .','.getp('BUTTONHOLE_PLACKET_WIDTH')/10
  .','.getp('BUTTONHOLE_PLACKET_FOLD_WIDTH')/10
  .','.getp('FRONT_BUTTONS')
  .','.$yesno[getp('EXTRA_TOP_BUTTON')]
  .','.getp('BUTTON_FREE_LENGTH')/10
  .','.$sy[getp('SPLIT_YOKE')]
  .','.getp('COLLAR_STAND_WIDTH')/10
  .','.getp('COLLAR_STAND_BEND')/10
  .','.getp('COLLAR_STAND_CURVE')
  .','.getp('COLLAR_GAP')/10
  .','.getp('COLLAR_BEND')/10
  .','.getp('COLLAR_FLARE')/10
  .','.getp('COLLAR_ANGLE')
  .','.getp('COLLAR_ROLL')/10
  .','.$cs[getp('CUFF_STYLE')]
  .','.getp('CUFF_LENGTH')/10
  .','.$bcb[getp('BARRELCUFF_BUTTONS')]
  .','.$bnb[getp('BARRELCUFF_NARROW_BUTTON')]
  .','.getp('CUFF_EASE')/10
  .','.getp('CUFF_DRAPE')/10
  .','.getp('SLEEVE_PLACKET_WIDTH')/10
  .','.getp('SLEEVE_PLACKET_LENGTH')/10;
  if($mail == 'joost@decock.org') {
    if (!$handle = fopen($csv, 'a'))  { dlog("Could not open $csv"); exit; }
    if (fwrite($handle, $postman) === FALSE)  { dlog("Could not write to $csv"); exit; }
    fclose($handle);
    dlog($cmd);
    shell_exec($cmd);
  }
}

