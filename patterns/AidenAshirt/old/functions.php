<?php 

function buildBlock() {
  $preactive = activePart();
  // Using PI so assuming neck is perfectly circular, but adding 5mm because
  $cox = (getm('NeckCircumference')/3.1415)/2+5;
  $coy = (getm('NeckCircumference')+getp('COLLAR_EASE'))/5 -8;
  // Now let's tweak
  activatePart('Block');
  addPoint(     0 , 0, 0);
  addPoint(     1 , px(0), getp('BACK_NECK_CUTOUT'), 'Point 1, Center back neck point');
  addPoint(     2 , px(0), py(1) + getp('ARMHOLE_DEPTH'));
  addPoint(     3 , px(0), py(1) + getm('CenterBackNeckToWaist'));
  addPoint(     5 , px(0) + mgetp('QuarterChest'), py(2));
  addPoint(     6 , px(5), py(0));
  addPoint(     7 , px(5), py(3));
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
  // New style,based on shoulder length and slope
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
  $preactive = activePart();
  activatePart('FrontAndBackBlock');
  clonePoints('Block');
  $mirror = array(1,2,3,5,6,7,9,10,11,12,13,14,15,16,17,18,101,121,122,161,171,181,182,200,501,8001);
  $width = px(5); 
  foreach($mirror as $pm) {
    $id = $pm*-1;
    addPntAr($id,xMirror($pm, $width));
  }
  addPoint(   201, px(5),py(200));
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
  addPntAr( 2066601, pShift(20,90,2.5), 'Back pitch point Top Notch');
  addPntAr( 2066602, pShift(20,-90,2.5), 'Back pitch point Bottom Notch');
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

function mmpPoints($part=FALSE, $model=FALSE) {
  if(!$part) $part = activePart();
  if(!$model) $model = activeModel();
  switch($part) {
    case 'FrontAndBackBlock':
      buildBlock();
      buildFrontAndBackBlock();
    break;
    case 'Front':
      clonePoints('FrontAndBackBlock');
      // Shoulders
      addPntAr('NeckBottom', pShift(1,-90,getp('NECKLINE_DROP')));
      addPntAr('ShoulderStrapCenter', hopShift(10,16,hopLen(10,16)*getp('SHOULDER_STRAP_PLACEMENT')*getp('STRETCH_FACTOR')));
      addPntAr('ShoulderStrapRight', hopShift('ShoulderStrapCenter',16,getp('SHOULDER_STRAP_WIDTH')/2));
      addPntAr('ShoulderStrapLeft', hopShift('ShoulderStrapCenter',10,getp('SHOULDER_STRAP_WIDTH')/2));
      addPntAr('ShoulderX', rotate(16,'ShoulderStrapRight',-90));
      addPntAr('ShoulderX', lineIsect('ShoulderStrapRight','ShoulderX',5,2));
      addPntAr('ShoulderStrapRightcpa', hopShift('ShoulderStrapRight','ShoulderX',hopLen('ShoulderStrapRight','ShoulderX')/2));
      addPntAr('ShoulderStrapRightcpb', hopShift(5,'ShoulderX',hopLen(5,'ShoulderX')/-1.5));
      addPntAr('ShoulderStrapLeftcpa', pShift('ShoulderStrapRightcpa',angle(10,16),getp('SHOULDER_STRAP_WIDTH')));
      addPoint('ShoulderStrapLeftcpb', px('ShoulderStrapLeftcpa'),py('NeckBottom'));
      addPntAr('ShoulderStrapLeftMax', lineIsect('ShoulderStrapLeft','ShoulderStrapLeftcpa','NeckBottom','ShoulderStrapLeftcpb'));
      // Adjustable arc for cutout
      addPntAr('ShoulderStrapLeftcpa', hopShift('ShoulderStrapLeft','ShoulderStrapLeftMax',hopLen('ShoulderStrapLeft','ShoulderStrapLeftMax')*getp('NECKLINE_BEND')));

      // Hips
      addPoint('HipsPoint', mgetp('QuarterHips'), py(200));
      addPoint('HemPoint', px('HipsPoint'),py('HipsPoint') + getp('LENGTH_BONUS'));
      addPntAr('HipsPointcp', pShift('HipsPoint',90,yDist('WaistPoint','HipsPoint')/3));
      addPoint('HemCF', px(0),py('HemPoint'));
      // Waist
      // Actually, screw the waist. This is typically stretch, and it doesn't look nice to come in at the waist
      addPoint('WaistPoint', px('HipsPoint'), py(3));
      addPntAr('WaistPointcpa', pShift('WaistPoint',90,yDist(-5,-7)/3));
      addPntAr('WaistPointcpb', pShift('WaistPoint',-90,yDist(-7,201)/2));
      addPntAr('5cp', pShift(5,-90,yDist(-5,-7)/3));
      // Armhole drop
      if(getp('ARMHOLE_DROP') > 0) {
        // Move point 5 along curve
        addPntAr('old5', pxy(5));
        addPntAr(5, arcYsect(5,5,'WaistPointcpa','WaistPoint',py(-5)+getp('ARMHOLE_DROP')));
        // Update other points accordingly
        addPoint(2, px(2),py(2)+getp('ARMHOLE_DROP'));
        addPntAr('ShoulderStrapRightcpb',pShift('ShoulderStrapRightcpb',angle('old5',5)+180,hopLen('old5',5)));
        addPntAr('ShoulderX', lineIsect('ShoulderStrapRight','ShoulderX',5,2));
      addPntAr('ShoulderStrapRightcpa', hopShift('ShoulderStrapRight','ShoulderX',hopLen('ShoulderStrapRight','ShoulderX')/2));
      }
      // Helper points for cut-on-fold (cof) line
      addPntAr('cof1', pShift('NeckBottom',-90,20));
      addPntAr('cof2', pShift('cof1',0,20));
      addPntAr('cof3', pShift('HemCF',90,20));
      addPntAr('cof4', pShift('cof3',0,20));
      // Helper points for grainline (gl)
      addPntAr('gl1', pShift('cof2',0,40));
      addPntAr('gl2', pShift('cof4',0,40));
      // Anchor points for scalebox/helplink
      addPntAr('scalebox', pShift(50,-110,75));
      addPntAr('helplink', pShift('scalebox',-90,75));
      $armholeLengthFront = arclen('ShoulderStrapRight','ShoulderStrapRightcpa','ShoulderStrapRightcpb',-5);
      $neckholeLengthFront = arclen('ShoulderStrapLeft','ShoulderStrapLeftcpa','ShoulderStrapLeftcpb',-5);
      setp('ARMHOLE_LEN_FRONT', $armholeLengthFront);
      setp('NECKHOLE_LEN_FRONT', $neckholeLengthFront);
      // No seam allowance at neck and armhole
      // This requires some extra points to draw paths 
      addPntAr('5sa1',arcShift(5,'5','WaistPointcpa','WaistPoint',10));
      addPntAr('5sa2',pShift('5sa1',180,100));
      addPntAr('HemCFsa1',pShift('HemCF',0,10));
      addPntAr('HemCFsa2',pShift('HemCFsa1',90,100));
      addPntAr('ShoulderStrapLeftsa', pShift('ShoulderStrapLeft',angle('ShoulderStrapLeftMax','ShoulderStrapLeft')+180,10));
      addPntAr('ShoulderStrapRightsa', pShift('ShoulderStrapRight',angle('ShoulderStrapRightcpa','ShoulderStrapRight')+180,10));
      addPntAr('HemCFsa3',pShift('HemCF',-90,20));
      addPntAr('HemPointsa1',pShift('HemPoint',-90,20));
      addPntAr('HemPointsa1',pShift('HemPointsa1',0,10));
      addPntAr('HemPointsa2',pShift('HemPoint',0,10));
    break;
    case 'Back':
      clonePoints('Front');
      // Drop back neck 0.5 cm
      addPntAr(-13,pShift(-13,-90,5));
      // Bring over points
      foreach(array('ShoulderStrapCenter','ShoulderStrapRight','ShoulderStrapLeft','WaistPoint','WaistPointcpa','WaistPointcpb','HipsPointcp','HipsPoint','HemPoint','HemCF') as $id) addPntAr("Back$id", xMirror($id,px(-5)));
      addPntAr('helper1', rotate('BackShoulderStrapCenter','BackShoulderStrapLeft',90));
      addPoint('helper2', 0,py(-13));
      addPntAr('BackShoulderStrapLeftMax', lineIsect('helper1','BackShoulderStrapLeft',-13,'helper2'));
      addPntAr('BackShoulderStrapLeftcp', hopShift('BackShoulderStrapLeftMax',-13,hopLen('BackShoulderStrapLeftMax',-13)*0.4));
      addPntAr('helper1', rotate('BackShoulderStrapCenter','BackShoulderStrapRight',-90));
      if(getp('ARMHOLE_DROP') > 0) {
        // Move point -5 along curve
        addPntAr('old-5', pxy(-5));
        addPntAr(-5, arcYsect(-5,-5,'BackWaistPointcpa','BackWaistPoint',py(-5)+getp('ARMHOLE_DROP')));
        // Update -2 accordingly
        addPoint(-2, px(-2),py(-2)+getp('ARMHOLE_DROP'));
      }
      addPntAr('BackShoulderStrapRightMax', lineIsect('helper1','BackShoulderStrapRight',-5,-2));
      // BACKLINE_BEND should stay between 0.5 and 0.9, so let's make sure of that.
      $backlineBend = 0.5+getp('BACKLINE_BEND')*0.4;
      addPntAr('BackShoulderStrapcp1', hopShift('BackShoulderStrapRight','BackShoulderStrapRightMax',hopLen('BackShoulderStrapRight','BackShoulderStrapRightMax')*$backlineBend));
      addPntAr('BackShoulderStrapcp2', hopShift(-5,'BackShoulderStrapRightMax',hopLen(-5,'BackShoulderStrapRightMax')*$backlineBend));
      // Helper points for cut-on-fold (cof) line
      addPntAr('cof1', pShift(-13,-90,20));
      addPntAr('cof2', pShift('cof1',180,20));
      addPntAr('cof3', pShift('BackHemCF',90,20));
      addPntAr('cof4', pShift('cof3',180,20));
      // Helper points for grainline (gl)
      addPntAr('gl1', pShift('cof2',180,40));
      addPntAr('gl2', pShift('cof4',180,40));
      // Anchor points for ribbing text
      addPntAr('text', pShift(-5,-45,75));
      $armholeLengthBack = arclen('BackShoulderStrapRight','BackShoulderStrapcp1','BackShoulderStrapcp2',-5);
      $neckholeLengthBack = arclen(-13,'BackShoulderStrapLeftcp','BackShoulderStrapLeftMax','BackShoulderStrapLeft');
      setp('ARMHOLE_LEN', round(getp('ARMHOLE_LEN_FRONT')/10 + $armholeLengthBack/10,1));
      setp('NECKHOLE_LEN', round((getp('NECKHOLE_LEN_FRONT')/10 + $neckholeLengthBack/10)*2,1));
      addText('
TO FINISH ARMHOLES AND NECK OPENING:|
====================================|
Cut two 6cm wide and '.round(getp('ARMHOLE_LEN'),0).'cm long strips to finish the armholes|
Cut one 6cm wide and '.round(getp('NECKHOLE_LEN'),0).'cm long trip to finish the neck opening','text');
      // No seam allowance at neck and armhole
      // This requires some extra points to draw paths 
      addPntAr('5sa1',arcShift(-5,-5,'BackWaistPointcpa','BackWaistPoint',10));
      addPntAr('5sa2',pShift('5sa1',0,100));
      addPntAr('BackHemCFsa1',pShift('BackHemCF',180,10));
      addPntAr('BackHemCFsa2',pShift('BackHemCFsa1',90,100));
      addPntAr('BackShoulderStrapLeftsa', pShift('BackShoulderStrapLeft',angle('BackShoulderStrapLeftMax','BackShoulderStrapLeft')+180,10));
      addPntAr('BackShoulderStrapRightsa', pShift('BackShoulderStrapRight',angle('BackShoulderStrapcp1','BackShoulderStrapRight')+180,10));
      addPntAr('BackHemCFsa3',pShift('BackHemCF',-90,20));
      addPntAr('BackHemPointsa1',pShift('BackHemPoint',-90,20));
      addPntAr('BackHemPointsa1',pShift('BackHemPointsa1',180,10));
      addPntAr('BackHemPointsa2',pShift('BackHemPoint',180,10));
    break;
  }
}

function mmpTitles($part=FALSE, $model=FALSE) {
  if(!$part) $part = activePart();
  if(!$model) $model = activeModel();
  switch($part) {
    case 'Front':
      addTitle('1', 'Front|Cut 1 on fold', 50, $part, $model);
    break;
    case 'Back':
      addTitle('2', 'Back|Cut 1 on fold', 'BackShoulderStrapRightMax', $part, $model);
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
      $placeholder = 'M 1';
      addPath($placeholder);
    break;
    case 'Front':
      $front = 'M NeckBottom C ShoulderStrapLeftcpb ShoulderStrapLeftcpa ShoulderStrapLeft L ShoulderStrapRight C ShoulderStrapRightcpa ShoulderStrapRightcpb 5 C 5 WaistPointcpa WaistPoint L HemPoint L HemCF z';
      $frontsa = 'M 5sa2 L 5sa1 C 5sa1 WaistPointcpa WaistPoint L HemPoint L HemCFsa1 L HemCFsa2 z';
      $extrasa = 'M ShoulderStrapLeftsa L ShoulderStrapRightsa L ShoulderStrapRight L ShoulderStrapLeft z M HemCF L HemCFsa3 L HemPointsa1 HemPointsa2 z';
      addPathWithSa($frontsa);
      addPath($extrasa,'saline');
      addPath($front);
      addPath('M cof1 L cof2 L cof4 L cof3','grainline');
      addPath('M gl1 L gl2','grainline');
      break;
    case 'Back':
      /*
      $path1 = 'M 1 L 200 L 201 L 5 C 501 182 18 C 181 122 12 C 121 161 16 L 10 C 10 101 1 z';
      $path2 = 'M -17 L -200 L 201 L -5 C -501 -182 -18 C -181 -122 -12 C -121 -161 -16 L -10 C -9 -171 -17 z';
      $acrossback = 'M 11 L -11';
      $chestline = 'M 2 L -2';
      $waistline = 'M 3 L -3';
      addPath($path1, 'helpline');
      addPath($path2, 'helpline');
      addPath($acrossback, 'helpline');
      addPath($chestline, 'helpline');
      addPath($waistline, 'helpline');
       */
      $back = 'M -13 C BackShoulderStrapLeftcp BackShoulderStrapLeftMax BackShoulderStrapLeft L BackShoulderStrapRight C BackShoulderStrapcp1 BackShoulderStrapcp2 -5 C -5 BackWaistPointcpa BackWaistPoint L BackHemPoint L BackHemCF z';
      $backsa = 'M 5sa2 L 5sa1 C 5sa1 BackWaistPointcpa BackWaistPoint L BackHemPoint L BackHemCFsa1 L BackHemCFsa2 z';
      $extrasa = 'M BackShoulderStrapLeftsa L BackShoulderStrapRightsa L BackShoulderStrapRight L BackShoulderStrapLeft z M BackHemCF L BackHemCFsa3 L BackHemPointsa1 BackHemPointsa2 z';
      addPathWithSa($backsa);
      addPath($extrasa,'saline');
      addPath($back);
      addPath('M cof1 L cof2 L cof4 L cof3','grainline');
      addPath('M gl1 L gl2','grainline');
      break;
  }
}
 

function mmpNotches($part=FALSE, $model=FALSE) {
  if(!$part) $part = activePart();
  if(!$model) $model = activeModel();
  switch($part) {
    case 'FrontAndBackBlock':
    break;
    case 'Front':
    break;
  }
}

function mmpSnippets($part=FALSE, $model=FALSE) {
  if(!$part) $part = activePart();
  if(!$model) $model = activeModel();
  switch($part) {
    case 'Front':
      addSnippet('scalebox', 'scalebox');
      addSnippet('helplink', 'helplink');
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
  msg('     Natural waist to trouser waist: '.getm('NaturalWaistToTrouserWaist').'mm');
  msg('                 Neck circumference: '.getm('NeckCircumference').'mm');
  msg('                    Shoulder length: '.getm('ShoulderLength').'mm');
  msg('                     Shoulder slope: '.getm('ShoulderSlope').'mm');
  msg('        Trouser waist circumference: '.getm('HipsCircumference').'mm');

  msg();
  msg('and with the following options:');
  msg();

  msg('                     Stretch factor: '.(getp('STRETCH_FACTOR')*100).'%');
  msg('               Shoulder strap width: '.getp('SHOULDER_STRAP_WIDTH').'mm');
  msg('               Shoulder strap width: '.getp('SHOULDER_STRAP_WIDTH').'mm');
  msg('           Shoulder strap placement: '.(getp('SHOULDER_STRAP_PLACEMENT')*100).'%');
  msg('                      Neckline drop: '.getp('NECKLINE_DROP').'mm');
  msg('                      Neckline bend: '.(getp('NECKLINE_BEND')*100).'%');
  msg('                      Backline bend: '.(getp('BACKLINE_BEND')*100).'%');
  msg('                       Length bonus: '.getp('LENGTH_BONUS').'mm');
  msg();
  msg('And now...  REQUESTO PATRONUM!');
  msg('');
  msg('  > Crunching numbers');
}

