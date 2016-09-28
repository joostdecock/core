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

function buildSleeve() {
  GLOBAL $SLEEVECAPFACTOR;
  if(!$SLEEVECAPFACTOR) $SLEEVECAPFACTOR = 1;
  $preactive = activePart();
  activatePart('Sleeve');

  // Armhole + ease value
  $aseam = (getm('Armhole') + SLEEVECAP_EASE) * $SLEEVECAPFACTOR;
  addPoint(     1 , 0, 0);
  addPoint(     2 , px(1), getm('SleeveLengthToWrist'));
  addPoint(     3 , getm('UpperBicepsCircumference')+getp('BICEPS_EASE'), 0); 
  addPoint(  2030 , px(3), py(2));
  addPoint(     4 , px(3)*0.50, 0); 
  addPoint(   401 , px(3)*0.25, 0); 
  addPoint(   402 , px(3)*0.75, 0); 
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
  $width = getm('WristCircumference')+getp('CUFF_EASE');
  addPoint(    21 , px(4)-$width/2, py(2));
  addPntAr(    22 , xMirror(21,px(4)));
  addPntAr(    23 , lineIsect(11, 12, 5, 21));
  addPntAr(    24 , lineIsect(11, 12, 6, 22));
  activatePart($preactive);
}

function mmpPoints($part=FALSE, $model=FALSE) {
  if(!$part) $part = activePart();
  if(!$model) $model = activeModel();
  switch($part) {
    case 'FrontAndBackBlock':
      buildBody();
      tweakCollaropening();
      // Calculating exact location of the pitchpoint notch
      $result = splitArc(18,181,122,12,20);
      array_shift($result);
      addPntAr(20001, array_shift($result));
      addPntAr(20002, array_shift($result));
      setp('FRONT_PITCH_DISTANCE', arcLen(-5,501,182,18) + arcLen(18,20001,20002,20));
      // Full front armhole len
      setp('FRONT_ARMHOLE_LENGTH', arcLen(-5,501,182,18) + arcLen(18,181,122,12) + arcLen(12,121,161,16));
      //addPoint( 50 , px(10), py(-7));



      // Smoothing out curve (overwriting points)
      addPntAr(6668002,arcYsect(8001,8002,6031,6021,py(8002)));
      addPntAr(6666661, rotate(6661,8001,angle(8001,8002)+90));
      $flip = array(101,10,16,161,121,20,122,181,18,182,501,5,7,1040,1041,1042,1043,8,5000,6011,6021,6031,6100,6121,6122,6110,6300,6111,6112,6301,6302,6113,6114,6303,6401,6402,6403,6404,6405,6406,6407,6408,7001,7002,7003,6001,6445,6446,6447,6448,8001,8002,6661,6662,6663,6664,6665,6666,6667,6668,6669);
      foreach($flip as $pf) {
        $id = $pf*-1;
//        addPntAr($id,xFlip($pf));
      }
      // Calculating exact location of the pitchpoint notch
      setp('BACK_PITCH_DISTANCE', arcLen(-5,-501,-182,-18) + arcLen(-18,-181,-122,-20));
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
      if($lenq12 == $len) addPoint(801000, pxy(801), 'Front pitch point'); // Pitch exactly one border Q1 and Q2
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
  }
}

function mmpTitles($part=FALSE, $model=FALSE) {
  if(!$part) $part = activePart();
  if(!$model) $model = activeModel();
  switch($part) {
    case 'FrontAndBackBlock':
      addTitle('1', 'Body Block', 50, $part, $model);
    break;
    case 'Sleeve':
      addTitle('2', 'Sleeve Block|Left is back, Right is front', 50, $part, $model);
    break;
  }
}

function mmpPaths($part=FALSE) {
  if(!$part) $part = activePart();
  switch($part) {
    case 'Block':
      $placeholder = 'M 1';
      addPath($placeholder);
    break;
    case 'FrontAndBackBlock':
      $path1 = 'M 1 L 200 L 201 L 5 C 501 182 18 C 181 122 12 C 121 161 16 L 10 C 10 101 1 z';
      $path2 = 'M -17 L -200 L 201 L -5 C -501 -182 -18 C -181 -122 -12 C -121 -161 -16 L -10 C -9 -171 -17 z';
      $acrossback = 'M 11 L -11';
      $chestline = 'M 2 L -2';
      $waistline = 'M 3 L -3';
      addPath($path1);
      addPath($path2);
      addPath($acrossback, 'helpline');
      addPath($chestline, 'helpline');
      addPath($waistline, 'helpline');
    break;
    case 'Sleeve':
      $path = "M 5 C 5 57012 57011 C 57013 701 701 C 701 47013 47011 C 47012 41 4 C 42 48012 48011 C 48013 801 801 C 801 68013 68011 C 68012 6 6 L 22 L 21 z";
      addPath($path);
      break;
  }
}
 

function mmpNotches($part=FALSE, $model=FALSE) {
  if(!$part) $part = activePart();
  if(!$model) $model = activeModel();
  switch($part) {
    case 'FrontAndBackBlock':
      addNotch(2066601);
      addNotch(2066602);
      addNotch(-20);
    break;
    case 'Sleeve':
      addNotch(701001);
      addNotch(701002);
      addNotch(801000);
      addNotch(43000);
      addNotch(23);
      addNotch(24);
    break;
  }
}

function mmpSnippets($part=FALSE, $model=FALSE) {
  if(!$part) $part = activePart();
  if(!$model) $model = activeModel();
  switch($part) {
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

