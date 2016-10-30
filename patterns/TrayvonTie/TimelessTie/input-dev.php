<?php 
addModel('developer', array('id' => 'Default developer model', 'added' => 'In input-dev.php'));
activateModel('developer');

setm('NeckSize', 42 * 10);
setm('NeckToWaist', 52 * 10);

setp('TIP', 7 * 10);
setp('KNOT', 3.5 * 10);

dlog($P);
