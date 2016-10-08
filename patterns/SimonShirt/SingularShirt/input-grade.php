<?php 
addModel('Joost', array('id' => 'joost', 'added' => 'In input-grade.php'));
  activateModel('Joost');
  setm('TrouserWaistCircumference', 1060);
  setm('UppermostLegCircumference', 600);
  setm('WaistlineToWaistlineCrossseamLength', 670);

addModel('test1', array('id' => 'joost', 'added' => 'In input-grade.php'));
  activateModel('test1');
  setm('TrouserWaistCircumference', 940);
  setm('UppermostLegCircumference', 567.5);
  setm('WaistlineToWaistlineCrossseamLength', 645);

addModel('MrHanne', array('id' => 'mrhanne', 'added' => 'In input-grade.php'));
  activateModel('MrHanne');
  setm('TrouserWaistCircumference', 820);
  setm('UppermostLegCircumference', 535);
  setm('WaistlineToWaistlineCrossseamLength', 620);
  

setp('WAISTBAND_ELASTIC_WIDTH', 40);
setp('SPEEDO_RISE', 35);
setp('VERTICAL_STRETCH_FACTOR', 0.9);
setp('HORIZONTAL_STRETCH_FACTOR', 0.8);

