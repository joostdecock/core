<?php 
require $_SERVER['APIROOT'].'patterns/lib/BodyblockJoost/functions.php'; 
// Load pattern info in options 
$P['options']['id'] = 'BodyblockJoost';
$P['options']['title'] = 'Body Block Joost';
$P['options']['helplink'] = 'makemypattern.com/pattern/bodyblock-joost/';
$P['options']['version'] = 0.1;
$P['options']['date'] = 20160223;
$P['options']['author'] = 'Joost De Cock';

// Setting up parts
addPart('Block');
addPart('FrontAndBackBlock');
addPart('Sleeve');

// Don't include model measurements when grading
if($P['parameters']['MODE'] != 'grade') {
  $models = allModels();
  katie($models[0]);
}
// Calculate some stuff specific to models but not parts
foreach(allModels() as $model) {
    msetp('HalfChest', getm('ChestCircumference', $model)/2, $model);
    msetp('QuarterChest', getm('ChestCircumference', $model)/4, $model);
    msetp('QuarterWaist', getm('WaistCircumference', $model)/4, $model);
    msetp('QuarterHips', getm('HipsCircumference', $model)/4, $model);
}
// And now...
foreach(allParts() as $part) {
  activatePart($part);
  foreach(allModels() as $model) {
    activateModel($model);
    mmpPoints();
    mmpTitles();
    mmpNotches();
    mmpSnippets();
  }
  mmpPaths();
}

// Layout
calculatePathBounderies();
pileParts();

movePartHor('Sleeve', array('FrontAndBackBlock'));
setPatternWidth(array('FrontAndBackBlock', 'Sleeve'));
setPatternHeight(array('FrontAndBackBlock'),30);


if(@$_GET['dump']==1) dmsg(print_r($P, 1));
