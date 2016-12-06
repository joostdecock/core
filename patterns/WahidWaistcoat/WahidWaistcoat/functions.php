<?php 

function buildBody() {
  buildBlock();
  buildFrontAndBackBlock();
}

function buildBlock() {
  $preactive = activePart();
  // Using PI so assuming neck is perfectly circular, but adding 5mm because
  $cox = (getm('NeckCircumference')/3.1415)/2+5+getp('NECK_INSET');
  $coy = (getm('NeckCircumference')+getp('COLLAR_EASE'))/5 -8;
  activatePart('Block');
  addPoint(     0 , 0, 0);
  addPoint(     1 , px(0), getp('BACK_NECK_CUTOUT'), 'Point 1, Center back neck point');
  addPoint(     2 , px(0), py(0) + getp('ARMHOLE_DEPTH'));
  addPoint(     3 , px(0), py(1) + getm('CenterBackNeckToWaist'));
  addPoint(     5 , px(0) + mgetp('QuarterChest') + CHEST_EASE/4, py(2));
  addPoint(     6 , px(5), py(0));
  addPoint(     7 , px(5), py(3));
  addPoint(     9 , px(0) +  $cox, py(1));
  addPoint(    10 , px(9), 0);
  addPoint(    11 , px(0), py(1) + yDist(1,2)/2);
  addPoint(    12 , px(0) + getm('HalfAcrossBack') -getp('SHOULDER_INSET')*0.5, py(11));
  addPntAr(    12 , pShift(12,180,getp('INSET')));
  addPoint(    21 , px(0) + getm('HalfAcrossBack') -getp('SHOULDER_INSET')*0.5, py(11));
  addPntAr(    21 , pShift(21,180,getp('BACK_INSET')));
  addPoint(    13 , px(0), py(0) + getm('ShoulderSlope')/2);
  addPoint(    14 , px(12), py(13));
  addPoint(    15 , px(12) + getp('SHOULDER_INSET')*0.5, py(2));
  addPoint(    51 , px(21) + getp('SHOULDER_INSET')*0.5, py(2));
  $xval = sqrt(pow(getm('ShoulderLength')-getp('NECK_INSET'),2) - pow(getm('ShoulderSlope')/2,2));
  addPoint(    16 , px(9)+$xval, py(13));
  addPntAr(    16 , hopShift(16,10,getp('SHOULDER_INSET')));
  addPoint(    17 , px(0), py(1) + $coy);
  addPntAr(    19 , pShift(5, -90, 50));
  addPoint(    50 , px(15)/2, py(2)-20);
  addPoint(   101 , px(0) + xDist(1,9)*0.8, py(1));
  addPntAr(   121 , pShift(12, 90, 50));
  addPntAr(   122 , pShift(12, -90, 50));
  addPntAr(   211 , pShift(21, 90, 50));
  addPntAr(   212 , pShift(21, -90, 50));
  addPoint(   171 , px(0) + xDist(1,9)*0.8, py(17));
  addPntAr(   161 , pShift(16, angle(10,16)+90, 10));
  addPoint(   200 , 0, getm('CenterBackNeckToWaist') + getm('NaturalWaistToTrouserWaist') + getp('BACK_NECK_CUTOUT'), 'Trouser waist height');
  addPoint(   202 , px(200), py(200)+getp('LENGTH_BONUS'),'Lenght bonus');
  addPoint(   204 , px(5), py(202),'Lenght bonus');
  activatePart($preactive);
}

function buildFrontAndBackBlock() {
  $preactive = activePart();
  activatePart('FrontAndBackBlock');
  clonePoints('Block');
  $mirror = array(1,2,3,5,6,7,9,10,11,12,21,13,14,15,16,17,18,19,101,121,122,211,212,161,171,200,202,501,8001);
  $width = px(5); 
  foreach($mirror as $pm) {
    $id = $pm*-1;
    addPntAr($id,xMirror($pm, $width));
  }
  addPoint(   201, px(5),py(200));

  // V-neck point
  $overlap = 10; // 1cm passed center front
  addPoint(  300 , px(-2)+$overlap,py(-2)-80+getp('FRONT_DROP_BONUS'),'Neck cutout base point');
  if(getp('FRONT_STYLE') == 1 ) addPoint(  301 , px(-10), py(-17));
  else addPoint(  301 , px(-10), py(300));

  // Dart units
  $w8th = getp('WAIST_REDUCTION')/8;
  $h8th = getp('HIPS_REDUCTION')/8;
  // Back dart
  addPoint(  900 , px(5)*0.5,py(-7));
  addPoint(  901 , px(900)+$w8th/2,py(900));
  addPoint(  902 , px(900)-$w8th/2,py(900));
  addPntAr(  903 , pShift(901,90,yDist(-5,-7)*0.25));
  addPntAr(  904 , pShift(902,90,yDist(-5,-7)*0.25));
  addPntAr(  905 , pShift(901,-90,yDist(-7,201)*0.25));
  addPntAr(  906 , pShift(902,-90,yDist(-7,201)*0.25));
  addPntAr(  907 , pShift(900,90,yDist(-5,-7)));
  addPntAr(  908 , pShift(900,-90,yDist(-7,201)*0.75));
  addPoint(  909 , px(907)+$h8th/2,py(200));
  addPoint(  910 , px(908)-$h8th/2,py(200));
  addPoint(  911 , px(909), py(202));
  addPoint(  912 , px(910), py(202));
  addPntAr(  913 , pShift(909,90,25));
  addPntAr(  914 , pShift(910,90,25));
  // Hem
  addPoint(  302 , px(-202)+$overlap, py(-202));
  // Front dart
  $dart = array(900,901,902,903,904,905,906,907,908,909,910,911,912,913,914);
  foreach($dart as $pm) {
    $id1 = $pm+1000;
    addPntAr($id1,xMirror($pm, px(5)));
  }
  // Side dart
  addPoint (2900,px(-5),py(900));
  addPoint( 2901 , px(2900)+$w8th,py(2900));
  addPoint( 2902 , px(2900)-$w8th,py(2900));
  addPntAr( 2903 , pShift(2901,90,yDist(-5,-7)*0.25));
  addPntAr( 2904 , pShift(2902,90,yDist(-5,-7)*0.25));
  addPntAr( 2905 , pShift(2901,-90,yDist(-7,201)*0.25));
  addPntAr( 2906 , pShift(2902,-90,yDist(-7,201)*0.25));
  addPntAr( 2907 , pShift(2900,90,yDist(-5,-7)*0.75));
  addPoint( 2908 , px(2900)+$h8th/2,py(200));
  addPntAr( 2909 , pShift(2908,0,$h8th/2));
  addPntAr( 2910 , pShift(2908,180,$h8th/2));
  addPoint( 2911 , px(2909), py(202));
  addPoint( 2912 , px(2910), py(202));
  addPntAr( 2913 , pShift(2909,90,35));
  addPntAr( 2914 , pShift(2910,90,35));

  // Hem
  if(getp('HEM_STYLE')==1) { // Classic 
    $r = xDist(2911,302)/4;
    addPntAr(4001, pShift(302,90,$r/2));
    addPoint(4002, px(4001)-$r,py(4001)+$r);
    addPntAr(4003, pShift(4002,135,$r/4));
    addPntAr(4004, pxy(1911));
    addPntAr(4005, pxy(2911));
    // Extend dart
    addPntAr(1911,arcXsect(4002,4003,4004,4005,px(1911)));
    addPntAr(1912,arcXsect(4002,4003,4004,4005,px(1912)));
    // Split arc
    $result = splitArc(4002,4003,4004,4005,1912);
    array_shift($result);
    addPntAr(4006,array_shift($result));
    addPntAr(4007,array_shift($result));
    $result = splitArc(4005,4004,4003,4002,1911);
    array_shift($result);
    addPntAr(4008,array_shift($result));
    addPntAr(4009,array_shift($result));
  } else { // Modern / Rounded
    $r = getp('HEM_RADIUS');
    // Let's not arc into our dart
    if($r>xDist(1912,302)) $r = xDist(1912,302);
    addPntAr(4001, pShift(302,90,$r));
    addPntAr(4002, pShift(4001,-90,bezierCircle($r)));
    addPntAr(4004, pShift(302,180,$r));
    addPntAr(4003, pShift(4004,0,bezierCircle($r)));
  }
  // Buttons
  addPoint(5000, px(-2),py(300)+10);
  addPoint(5050, px(-2),py(4001)-10);
  $bc = getp('BUTTONS')-1;
  for($i=0;$i<$bc;$i++) addPntAr(5001+$i,pShift(5000,-90,($i+1)*yDist(5000,5050)/$bc));
  // Pockets
  $pw = getp('POCKET_WIDTH');
  $ph = getp('POCKET_HEIGHT');
  $pa = getp('POCKET_ANGLE');
  addPoint(7000, px(1900),py(1900)+yDist(1900,4001)*0.6-$ph/2);
  addPntAr(7001, arcYsect(1910,1914,1906,1902,py(7000)));
  addPntAr(7002, xMirror(7001,px(7000)));
  addPntAr(7003, pShift(7000,$pa,$pw/2));
  addPntAr(7004, pShift(7003,$pa-90,$ph));
  addPntAr(7005, pShift(7000,180+$pa,$pw/2));
  addPntAr(7006, pShift(7005,$pa-90,$ph));
  addPntAr(7007, lineIsect(1900,7000,7004,7006));
  addPntAr(7008, pxy(7000));
  addPntAr(7009, pxy(7007));
  $xdelta = xDist(7000,7001);
  $right = array(7000,7003,7004,7007);
  foreach($right as $mid) addPoint($mid,px($mid)+$xdelta,py($mid));
  $left = array(7008,7005,7006,7009);
  foreach($left as $mid) addPoint($mid,px($mid)-$xdelta,py($mid));
  addPntAr(7010, arcYsect(1910,1914,1906,1902,py(7007)));
  $angle = 90- angle(7001,7010);
  foreach($right as $mid) addPntAr($mid,rotate($mid,7001,$angle*-1));
  foreach($left as $mid) addPntAr($mid,rotate($mid,7008,$angle));

}

function mmpPoints($part=FALSE, $model=FALSE) {
  if(!$part) $part = activePart();
  if(!$model) $model = activeModel();
  switch($part) {
    case 'FrontAndBackBlock':
      buildBody();
    break;
    case 'WaistCoatBlock':
      clonePoints('FrontAndBackBlock');
      // Make back shoulder 1cm more sloped
      addPntAr(16, arcShift(16,161,211,21,10));
      addPntAr(161,hopShift(16,10,10));
      addPntAr(161,rotate(161,16,90));
      // Make Front shoulder 1cm more sloped
      addPntAr(-16, arcShift(-16,-161,-121,-12,10));
      // Equalise length of shoulder seam
      addPntAr(-16, hopShift(-10,-16,hopLen(10,16)));
      addPntAr(-161,hopShift(-16,-10,10));
      addPntAr(-161,rotate(-161,-16,-90));
      // Center back dart
      addPntAr(1, pShift(1,0,getp('CENTER_BACK_DART')));
      // Back scye dart
      addPntAr(1001, arcShift(21,211,161,16,getp('BACK_SCYE_DART')));
      addPntAr(1002, arcShift(21,212,51,5,getp('BACK_SCYE_DART')));
      $angle = angle(11,1001) - angle(11,1002);
      $torotate = array(16,161,10,101,1);
      foreach($torotate as $rp) addPntAr($rp,rotate($rp,11,-$angle));
      // Front scye dart
      addPoint(2001,px(-5),py(12));
      addPntAr(2002,array_shift(arcLineIsect(-5,-15,-122,-12,2001,1907)));
      addPntAr(2003,hopShift(2002,1907,getp('FRONT_SCYE_DART')));
      addpntAr(2004,rotate(2003,2002,90));
      $angle = angle(2002,1907) - angle(2004,1907);
      //duplicate double-used control points
      addPntAr(-2907,pxy(2907));
      $torotate = array(-15,-5,-2907,2903,2901,2905,2913,2909,2911,1911,1909,1913,1905,1901,1903,4005,4008,4009,7005,7006,7008,7009);
      foreach($torotate as $rp) addPntAr($rp,rotate($rp,1907,-$angle));
      // Move 1cm of back to front
      addPntAr(3000,rotate(16,161,90));
      addPntAr(3001,pxy(10));
      addPntAr(3002,pxy(16));
      addPntAr(3003,array_shift(arcLineIsect(16,161,211,21,161,3000)));
      addPntAr(3004,array_shift(arcLineIsect(1,101,10,10,161,3000)));
      $xdelta = px(-16)-px(3002);
      $ydelta = py(-16)-py(3002);
      $tomove = array(3001,3002,3003,3004);
      foreach($tomove as $pm) {
        $id = $pm+10;
        addPoint($id,px($pm)+$xdelta,py($pm)+$ydelta);
      }
      $angle = angle(3012,3011) - angle(-16,-10);
      foreach($tomove as $pm) {
        $id = $pm+10;
        $pm = $pm+10;
        addPntAr($id,rotate($pm,-16,360-$angle));
      }
      // New control points for back shoulder seam
      addPntAr(3030,rotate(16,3003,180));
      // Facing
      addPntAr(6000, hopShift(3012,3011,hopLen(3012,3011)/2));
      addPntAr(6001, pxy(1907));
      addPntAr(6002, rotate(3011,6000,-90));
      addPoint(6003, px(6001),py(-2));
      addPoint('grainbot',px(6001)+20, py(2911));
      addPoint('graintop',px('grainbot'), py(3012));
    break;
    case 'Back':
      clonePoints('WaistCoatBlock');
      addPoint('title',px(907)/2, py(907));
      addPoint('box',px(907)/4, py(211));
      addPoint('link',px('box'), py('box')+70);
      addPoint('graintop',px('box')-10, py(1)+10);
      addPoint('grainbot',px('graintop'), py(202)-10);
    break;
    case 'Front':
      clonePoints('WaistCoatBlock');
      addPoint('title',px(2907)+xDist(2907,1907)*0.45, py(2907));
    break;
    case 'PocketWelt':
      $pw = getp('POCKET_WIDTH');
      $ph = getp('POCKET_HEIGHT');
      addPoint(1,$pw*0.5,0);
      addPoint(2,px(1)+20,py(1)-$ph/2);
      addPoint(3,px(2),py(1)+$ph*2+20);
      addPoint(4,px(2),py(1)+$ph);
      addPoint(5,px(1)-$ph/2,py(1)-$ph/2);
      $flip = array(1,2,3,4,5);
      foreach($flip as $f) addPntAr(-1*$f,xFlip($f));
      addPoint('title',0,py(4)+15);
      addPntAr('graintop',pShift(2,-135,5));
      addPntAr('grainbot',pShift(3,135,5));
      addPntAr('graintop',rotate('graintop','grainbot',5));
    break;
    case 'PocketFacing':
      $pr = 20;
      $ph = getp('POCKET_HEIGHT');
      clonePoints('PocketWelt');
      addPoint(3, px(3),90);
      addPoint(4, px(3),py(3)-$pr);
      addPntAr(5, rotate(4,3,90));
      addPntAr(6, pShift(4,-90,bezierCircle($pr)));
      addPntAr(7, rotate(6,3,90));
      addPoint(8,px(1)-$ph/2,py(1)-$ph/2);
      $flip = array(3,4,5,6,7,8);
      foreach($flip as $f) addPntAr(-1*$f,xFlip($f));
    break;
    case 'PocketBag':
      clonePoints('PocketFacing');
      $pw = getp('POCKET_WIDTH');
      $ph = getp('POCKET_HEIGHT');
      addPoint(2,px(3),px(3)-110);
      $flip = array(2);
      foreach($flip as $f) addPntAr(-1*$f,xFlip($f));
    break;
    case 'PocketInterfacing':
      clonePoints('PocketWelt');
      addPntAr(3,pShift(3,90,15));
      addPntAr(-3,pShift(-3,90,15));
      addPntAr('title',pShift('title',90,10));
    break;
  }
}

function mmpTitles($part=FALSE, $model=FALSE) {
  if(!$part) $part = activePart();
  if(!$model) $model = activeModel();
  switch($part) {
    case 'Back':
      addTitle('1', 'Back|Cut 2 from fabric|Cut 2 from lining', 'title', $part, $model);
    break;
    case 'Front':
      addTitle('2', 'Front|Cut 2 from fabric|Cut 2 from lining/facing', 'title', $part, $model);
    break;
    case 'PocketWelt':
      addTitle('3', 'Pocket welt|Cut 2 from fabric', 'title', $part, $model);
    break;
    case 'PocketFacing':
      addTitle('4', 'Pocket facing|Cut 2 from fabric', 'title', $part, $model);
    break;
    case 'PocketBag':
      addTitle('5', 'Pocket bag|Cut 2 from lining', 'title', $part, $model);
    break;
    case 'PocketInterfacing':
      addTitle('6', 'Pocket interfacing|Cut 2 from interfacing', 'title', $part, $model);
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
      addPath('M 1');
    break;
    case 'NOFrontAndBackBlock':
      $backdart = 'M 912 L 910 C 914 906 902 C 904 907 907 C 907 903 901 C 905 913 911';
      $back = "M 1 L 202 L 912 L 2912 L 2910 C 2914 2906 2902 C 2904 2907 5 C 51 212 21 C 211 161 16 L 10 C 10 101 1 z";
      $frontdart = 'M 1912 L 1910 C 1914 1906 1902 C 1904 1907 1907 C 1907 1903 1901 C 1905 1913 1909 L 1911';
      if(getp('HEM_STYLE')==1) $hem = 'L 4002 C 4006 4007 1912 L 1911 C 4009 4008 4005';
      else $hem = 'C 4002 4003 4004 L 1912';
      $front = "M 300 L 4001 $hem L 2911 2909 C 2913 2905 2901 C 2903 2907 -5 C -15 -122 -12 C -121 -161 -16 L -10 C -9 301 300 z";
      $acrossback = 'M 11 L -11';
      $chestline = 'M 2 L -2';
      $waistline = 'M 3 L -3';
      if(getp('MODE') == 'grade') {
        addPath($back, 'gradeline');
        addPath($front, 'gradeline');
      } else {
        addPath($back);
        addPath($front);
        addPath($backdart, 'helpline');
        addPath($frontdart, 'helpline');
        addPath($acrossback, 'helpline');
        addPath($chestline, 'helpline');
        addPath($waistline, 'helpline');
        $placeholder = 'M 1';
        //addPath($placeholder);
      addPath('M 7000 L 7003 L 7004 L 7007 z','helpline lining');
      addPath('M 7008 L 7005 L 7006 L 7009 z','helpline canvas');
      }
    break;
    case 'WaistCoatBlock':
      addPath('M 1');
    break;
    case 'HIDEWaistCoatBlock':
      $backdart = 'L 910 C 914 906 902 C 904 907 907 C 907 903 901 C 905 913 911';
      $back = "M 1 C 1 11 2 L 202 L 912 $backdart L 2912 L 2910 C 2914 2906 2902 C 2904 2907 5 C 51 212 21 C 211 3030 3003 L 3004 C 3004 101 1 z";
      $frontdart = 'L 1910 C 1914 1906 1902 C 1904 1907 1907 C 1907 1903 1901 C 1905 1913 1909 L 1911';
      $front = "M 300 L 302 L 1912 $frontdart 2911 2909 C 2913 2905 2901 C 2903 -2907 -5 C -15 -122 -12 C -121 3012 3013 L 3014 C 3011 301 300 z";
      addPath($back,'helpline canvas');
      addPath($front,'helpline canvas');
      addPath('M 3001 L 3002 L 3003 L 3004 z','helpline');
      addPath('M 3011 L 3012 L 3013 L 3014 z','helpline');
    break;
    case 'Back':
      $backdart = 'M 912 L 910 C 914 906 902 C 904 907 907 C 907 903 901 C 905 913 911';
      $back = "M 1 C 1 11 2 L 202 L 912 L 2912 L 2910 C 2914 2906 2902 C 2904 2907 5 C 51 212 21 C 211 3030 3003 L 3004 C 3004 101 1 z";
      addPathWithSa($back);
      addPath($backdart);
      addPath('M grainbot L graintop', 'grainline');
    break;
    case 'Front':
      $frontdart = 'M 1912 L 1910 C 1914 1906 1902 C 1904 1907 1907 C 1907 1903 1901 C 1905 1913 1909 L 1911';
      if(getp('HEM_STYLE')==1) $hem = 'L 4002 C 4006 4007 1912 L 1911 C 4009 4008 4005';
      else $hem = 'C 4002 4003 4004 L 1912 L 1911';
      $front = "M 300 L 302 L 1912 $frontdart 2911 2909 C 2913 2905 2901 C 2903 -2907 -5 C -15 -122 -12 C -121 3012 3013 L 3014 C 3011 301 300 z";
      $front = "M 300 L 4001 $hem L 2911 2909 C 2913 2905 2901 C 2903 2907 -5 C -15 -122 -12 C -121 -161 -16 L -10 C -9 301 300 z";
      $facing = 'M 6000 C 6002 6003 6001';
      addPathWithSa($front);
      addPath($frontdart);
      addPath($facing, 'lining');
      addPath('M 7000 L 7003 L 7004 L 7007','helpline');
      addPath('M 7008 L 7005 L 7006 L 7009','helpline');
      addPath('M grainbot L graintop', 'grainline');
    break;
    case 'PocketWelt':
      addPath('M 2 L 3 L -3 L -2 z');
      addPath('M -1 L 1','helpline');
      addPath('M grainbot L graintop', 'grainline');
    break;
    case 'PocketFacing':
      addPath('M 2 L 4 C 6 7 5 L -5 C -7 -6 -4 L -2 z');
      addPath('M -1 L 1','helpline');
      addPath('M grainbot L graintop', 'grainline');
    break;
    case 'PocketBag':
      addPath('M 2 L 4 C 6 7 5 L -5 C -7 -6 -4 L -2 z','lining');
    break;
    case 'PocketInterfacing':
      addPath('M 2 L 3 L -3 L -2 z','interfacing');
      addPath('M -1 L 1','helpline');
    break;
  }
}
 

function mmpNotches($part=FALSE, $model=FALSE) {
  if(!$part) $part = activePart();
  if(!$model) $model = activeModel();
  switch($part) {
    case 'Back':
      addNotch(3003);  
      addNotch(3004);  
      addNotch(907);  
    break;
    case 'Front':
      addNotch(3011);  
      addNotch(3012);  
      addNotch(1907);  
    break;
    case 'PocketWelt':
      addNotch(-1);  
      addNotch(-4);  
      addNotch(-5);  
      addNotch(1);  
      addNotch(4);  
      addNotch(5);  
    break;
    case 'PocketFacing':
      addNotch(-1);  
      addNotch(1);  
      addNotch(-8);  
      addNotch(8);  
    break;
  }
}

function mmpSnippets($part=FALSE, $model=FALSE) {
  if(!$part) $part = activePart();
  if(!$model) $model = activeModel();
  switch($part) {
    case 'Back':
      addSnippet('scalebox', 'box');
      addSnippet('helplink', 'link');
    break;
    case 'Front':
    $bc = getp('BUTTONS')-1;
    for($i=0;$i<=$bc;$i++) {
      addMarker(5000+$i, 'button');
      addMarker(5000+$i, 'buttonhole',array('translate'=>'0,3', 'rotate'=>90));
    }
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
  msg('  Center back neck to natural waist: '.getm('CenterBackNeckToWaist').'mm');
  msg('                Chest circumference: '.getm('ChestCircumference').'mm');
  msg('                      Natural waist: '.getm('WaistCircumference').'mm');
  msg('     Natural waist to trouser waist: '.getm('NaturalWaistToTrouserWaist').'mm');
  msg('                 Neck circumference: '.getm('NeckCircumference').'mm');
  msg('                    Shoulder length: '.getm('ShoulderLength').'mm');
  msg('                     Shoulder slope: '.getm('ShoulderSlope').'mm');
  msg('        Trouser waist circumference: '.getm('HipsCircumference').'mm');

  msg();
  msg('and with the following options:');
  msg();

  msg('                         Chest ease: '.getp('CHEST_EASE').'mm');
  msg('                         Waist ease: '.getp('WAIST_EASE').'mm');
  msg('                          Hips ease: '.getp('HIPS_EASE').'mm');
  msg('                       Length bonus: '.getp('LENGTH_BONUS').'mm');
  msg('                          Hem style: '.getp('HEM_STYLE'));
  msg('                          Hem curve: '.getp('HEM_CURVE').'mm');
  msg('                   Back neck cutout: '.getp('BACK_NECK_CUTOUT').'mm');
  msg('                      Front buttons: '.getp('FRONT_BUTTONS'));
  msg();
  msg('And now...  REQUESTO PATRONUM!');
  msg('');
  msg('  > Crunching numbers');
}

