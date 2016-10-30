<?php 
function mmpPoints($part=FALSE, $model=FALSE) {
  if(!$part) $part = activePart();
  if(!$model) $model = activeModel();
  $length = getm('NeckToWaist')*2+getm('NeckSize')+150;
  $halflength = $length/2;
  $SA = 10;
  $fSA = $SA * 3;
  // Width of the tip on the end of the tie
  $backtip = KNOT+((TIP-KNOT)/2);
  switch($part) {
    case 'Interfacing-tip':
      addPoint(     1, 0, 0, 'Tip');
      addPoint(     2, 0, $halflength, 'Center join, tip side');
      addPoint(     3, -TIP/2,TIP/2, 'Left tip corner');
      addPoint(     4, TIP/2,TIP/2, 'Right tip corner');
      addPoint(     5, -KNOT/2,$halflength+(KNOT/2), 'Join left, tip side');
      addPoint(     6, KNOT/2,$halflength-(KNOT/2), 'Join right, tip side');
      addPoint(     7, 0,$halflength+$SA, 'Center join, with SA');
      addPoint(     8, -KNOT/2,$halflength+(KNOT/2)+$SA, 'Join left, with SA');
      addPoint(     9, KNOT/2,$halflength-(KNOT/2)+$SA, 'Join right, with SA');
      addPoint(    50, 0,$halflength/3, 'Title anchor');
    break;
    case 'Interfacing-tail':
      addPoint(     1, 0, 0, 'Tip');
      addPoint(     2, 0, $halflength, 'Center join, tip side');
      addPoint(     3, -$backtip/2,$backtip/2, 'Left tip corner');
      addPoint(     4, $backtip/2,$backtip/2, 'Right tip corner');
      addPoint(     5, -KNOT/2,$halflength+(KNOT/2), 'Join left, tip side');
      addPoint(     6, KNOT/2,$halflength-(KNOT/2), 'Join left, tip side');
      addPoint(     7, 0,$halflength+$SA, 'Center join, with SA');
      addPoint(     8, -KNOT/2,$halflength+(KNOT/2)+$SA, 'Join left, with SA');
      addPoint(     9, KNOT/2,$halflength-(KNOT/2)+$SA, 'Join right, with SA');
      addPoint(    50, 0,$halflength/3, 'Title anchor');
    break;
    case 'Fabric-tip':
      addPoint(     1, 0,-10, 'Tip');
      addPoint(     2, 0,$halflength+$SA+10, 'Center join, tip side');
      addPoint(     3, -TIP-$SA*3,TIP+$SA*3-10, 'Left tip corner');
      addPoint(     4, TIP+$SA*3,TIP+$fSA-10, 'Right tip corner');
      addPoint(     5, -KNOT-$fSA,$halflength+$SA+10, 'Join left tip side');
      addPoint(     6, KNOT+$fSA,$halflength+$SA+10, 'Joing right, tip side');
      addPoint(     7, 0,$halflength+$SA+10+$SA, 'Center join, with SA');
      // Point     under 45 degrees, 6.66 is not important
      addPoint(     8, 6.66,$halflength+$SA+10+$SA-6.66, 'Intersection helper point');
      addPntAr(     9, lineIsect(pxy(7), pxy(8), pxy(4), pxy(6)), 'Join right, with SA');
      addPntAr(    10, lineIsect(pxy(7), pxy(8), pxy(3), pxy(5)), 'Join left, with SA');
      addPoint(    20, 0,0, 'Tip zero');
      addPntAr(   21 , pShift(20, -45, 15));
      addPntAr(   22 , pShift(20, -135, 15));
      
      addPoint(    50, 0,$halflength/3, 'Title anchor');
      addPoint(    51, -49,py(3), 'Helplink anchor');
    break;
    case 'Fabric-tail':
      addPoint(     1, 0,-10, 'Tip');
      addPoint(     2, 0,$halflength+$SA+10, 'Center join, tip side');
      addPoint(     3, -$backtip-$fSA,$backtip+$fSA-10, 'Left tip corner');
      addPoint(     4, $backtip+$fSA,$backtip+$fSA-10, 'Right tip corner');
      addPoint(     5, -KNOT-$fSA,$halflength+$SA+10, 'Join left tip side');
      addPoint(     6, KNOT+$fSA,$halflength+$SA+10, 'Joing right, tip side');
      addPoint(     7, 0,$halflength+$SA+10+$SA, 'Center join, with SA');
      // Point under 45 degrees, 6.66 is not important
      addPoint(     8, 6.66,$halflength+$SA+10+$SA-6.66, 'Intersection helper point');
      addPntAr(     9, lineIsect(pxy(7), pxy(8), pxy(4), pxy(6)), 'Join right, with SA');
      addPntAr(    10, lineIsect(pxy(7), pxy(8), pxy(3), pxy(5)), 'Join left, with SA');
      addPoint(    20, 0,0, 'Tip zero');
      addPntAr(   21 , pShift(20, -45, 12));
      addPntAr(   22 , pShift(20, -135, 12));
      addPoint(    50, 0,$halflength/3, 'Title anchor');
    break;
    case 'Lining-tip':
      addPoint(     1, 0,-10, 'Tip');
      addPoint(     2, 0,TIP*5+$SA+10, 'Lining center end');
      addPoint(     3, -TIP-$fSA,TIP+$fSA-10, 'Left tip corner');
      addPoint(     4, TIP+$fSA,TIP+$fSA-10, 'Right tip corner');
      addPoint(     5, -KNOT-$fSA,$halflength+$SA+10, 'Join left tip side');
      addPoint(     6, KNOT+$fSA,$halflength+$SA+10, 'Joing right, tip side');
      addPoint(     7, 6.66,py(2), 'Intersection helper point');
      addPntAr(     9, lineIsect(pxy(2), pxy(7), pxy(4), pxy(6)), 'Join right, with SA');
      addPntAr(    10, lineIsect(pxy(2), pxy(7), pxy(3), pxy(5)), 'Join left, with SA');
      addPoint(    20, 0,0, 'Tip zero');
      addPoint(    50, 0,TIP*2, 'Title anchor');
      addPoint(    51, -50,py(50)+65, 'Infobox anchor');
    break;
    case 'Lining-tail':
      addPoint(     1, 0,-10, 'Tip');
      addPoint(     2, 0,$backtip*5+$SA+10, 'Lining center end');
      addPoint(     3, -$backtip-$fSA,$backtip+$fSA-10, 'Left tip corner');
      addPoint(     4, $backtip+$fSA,$backtip+$fSA-10, 'Right tip corner');
      addPoint(     5, -KNOT-$fSA,$halflength+$SA+10, 'Join left tip side');
      addPoint(     6, KNOT+$fSA,$halflength+$SA+10, 'Joing right, tip side');
      addPoint(     7, 6.66,py(2), 'Intersection helper point');
      addPntAr(     9, lineIsect(pxy(2), pxy(7), pxy(4), pxy(6)), 'Join right, with SA');
      addPntAr(    10, lineIsect(pxy(2), pxy(7), pxy(3), pxy(5)), 'Join left, with SA');
      addPoint(    20, 0,0, 'Tip zero');
      addPoint(    50, 0,TIP*2, 'Title anchor');
    break;
    case 'Loop':
      addPoint(     1, 0,0, 'Top left');
      addPoint(     2, $backtip*2+40, 34, 'Bottom right');
      addPoint(     3, px(1), py(2), 'Bottom left');
      addPoint(     4, px(2), py(1), 'Top right');
      addPoint(     50, px(2)/2,py(2)/2, 'Title anchor');
    break;
  }
}

function mmpTitles($part=FALSE, $model=FALSE) {
  if(!$part) $part = activePart();
  if(!$model) $model = activeModel();
  switch($part) {
    case 'Interfacing-tip':
      addTitle('1', 'Interfacing tip| |Cut 1', 50, $part, $model);
    break;
    case 'Interfacing-tail':
      addTitle('2', 'Interfacing tail| |Cut 1', 50, $part, $model);
    break;
    case 'Fabric-tip':
      addTitle('3', 'Fabric tip| |Cut 1|Good side down', 50, $part, $model);
    break;
    case 'Fabric-tail':
      addTitle('4', 'Fabric tail| |Cut 1|Good side down', 50, $part, $model);
    break;
    case 'Lining-tip':
      addTitle('5', 'Lining tip| |Cut 1|Good side down', 50, $part, $model);
    break;
    case 'Lining-tail':
      addTitle('6', 'Lining tail| |Cut 1|Good side down', 50, $part, $model);
    break;
    case 'Loop':
      addTitle('7', 'Loop| |Cut 1|Good side down', 50, $part, $model);
    break;
  }
}

function mmpPaths($part=FALSE) {
  if(!$part) $part = activePart();
  switch($part) {
    case 'Interfacing-tip':
      $path = 'M 1 L 4 L 9 L 8 L 3 z'; 
    break;
    case 'Interfacing-tail':
      $path = 'M 1 L 4 L 9 L 8 L 3 z'; 
    break;
    case 'Fabric-tip':
      $path = 'M 1 L 4 L 9 L 10 L 3 z'; 
    break;
    case 'Fabric-tail':
      $path = 'M 1 L 4 L 9 L 10 L 3 z'; 
    break;
    case 'Lining-tip':
      $path = 'M 1 L 4 L 9 L 10 L 3 z'; 
    break;
    case 'Lining-tail':
      $path = 'M 1 L 4 L 9 L 10 L 3 z'; 
    break;
    case 'Loop':
      $path = 'M 1 L 4 L 2 L 3 z'; 
    break;
  }
  addPath($path,'fitline');
}
 

function mmpNotches($part=FALSE, $model=FALSE) {
  if(!$part) $part = activePart();
  if(!$model) $model = activeModel();
  switch($part) {
    case 'Interfacing-tip':
    case 'Interfacing-tail':
      addNotch(2);
    break;
    case 'Lining-tip':
    case 'Lining-tail':
      addNotch(20);
    break;
    case 'Fabric-tip':
    case 'Fabric-tail':
      addNotch(2);
      addNotch(21);
      addNotch(22);
    break;
  }
}

function mmpSnippets($part=FALSE, $model=FALSE) {
  if(!$part) $part = activePart();
  if(!$model) $model = activeModel();
  switch($part) {
    case 'Lining-tip':
      addSnippet('scalebox', 51);
    break;
    case 'Fabric-tip':
      addSnippet('helplink', 51);
    break;
  }
}


/*
 * Prints messages for user in pattern comments
 */
function robotMsg($model) {
  msg('Hi there, I am Leela, and I will be your robot today.');
  msg('Our special of the day is Timeless Tie pattern');
  msg('I will serve it for you with these measurements:');
  msg();
  msg('  Neck size: '.getm('NeckSize', $model).'mm');
  msg('  Neck to waist: '.getm('NeckToWaist', $model).'mm');
  msg();
  msg('and these options:');
  msg();
  msg('      Tip width: '.TIP.'mm');
  msg('      Knot width: '.KNOT.'mm');
  msg();
  msg('Here we go...');
  msg('');
  msg('  > Sprinkling pixels');
}

