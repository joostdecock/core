<?php 
require $_SERVER['APIROOT'].'patterns/lib/AidenAshirt/functions.php'; 
// Load pattern info in options 
$P['options']['id'] = 'AidenAshirt';
$P['options']['title'] = 'Aiden A-shirt';
$P['options']['helplink'] = 'makemypattern.com/pattern/aiden-shirt/';
$P['options']['version'] = 1.1;
$P['options']['date'] = 20160822;
$P['options']['author'] = 'Joost De Cock';

// Setting up parts
addPart('Block');
addPart('FrontAndBackBlock');
addPart('Front');
addPart('Back');

// Don't include model measurements when grading
if($P['parameters']['MODE'] != 'grade') {
  $models = allModels();
  katie($models[0]);
}
// Calculate some stuff specific to models but not parts
foreach(allModels() as $model) {
    msetp('HalfChest', (getm('ChestCircumference', $model)/2) * getp('STRETCH_FACTOR'), $model);
    msetp('QuarterChest', (getm('ChestCircumference', $model)/4) * getp('STRETCH_FACTOR'), $model);
    msetp('QuarterWaist', (getm('WaistCircumference', $model)/4) * getp('STRETCH_FACTOR'), $model);
    msetp('QuarterHips', (getm('HipsCircumference', $model)/4) * getp('STRETCH_FACTOR'), $model);
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

movePartHor('Front', array('Back'));
setPatternWidth(array('Front','Back'));
setPatternHeight(array('Front'));


if(@$_GET['dump']==1) dmsg(print_r($P, 1));
