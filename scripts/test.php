<?php

// Turn off all error reporting
error_reporting(0);

$dir = getcwd();
chdir($dir);
chdir('..');

// Failure counter
$fail = 0;

// Does the info service even work?
p("Contacting API");
if(test('service=info') === 0) {
    $info = json_decode(`php index.php service=info`, 1);
    
    # Info service works, let's test it
    h1("Testing info service");
    foreach($info['patterns'] as $ns => $list) {
        h2("Testing $ns patterns");
        foreach($list as $id => $title) {
            p("Info on $title");
            $fail += test("service=info pattern=$id");
        }
    }
    // Do we have a draft service?
    if(in_array('draft', $info['services'])) {
        // We do, let's test it
        h1("Testing draft service");
        foreach($info['patterns'] as $pns => $patterns) {
            h2("Testing $pns patterns");
            foreach($patterns as $pid => $ptitle) {
                foreach($info['themes'] as $tns => $theme) {
                    foreach($theme as $tid => $tname) {
                        p("$ptitle draft, $tname theme");
                        $fail += test("service=draft pattern=$pid theme=$tname");
                    }
                }
            }
        }
    }
 
    // Do we have a compare service?
    if(in_array('compare', $info['services'])) {
        // We do, let's test it
        h1("Testing compare service");
        foreach($info['patterns'] as $pns => $patterns) {
            h2("Testing $pns patterns");
            foreach($patterns as $pid => $ptitle) {
                p("Comparing $ptitle");
                $fail += test("service=compare pattern=$pid");
            }
        }
    }
 
    // Do we have a sample service?
    if(in_array('sample', $info['services'])) {
        // We do, let's test it
        h1("Testing sample service");
        foreach($info['patterns'] as $pns => $patterns) {
            h2("Testing $pns patterns");
            foreach($patterns as $pid => $ptitle) {
                $pinfo = json_decode(`php index.php service=info pattern=$pid`, 1);
                h3("Sampling $ptitle");
                h3("  Measurements");
                foreach($pinfo['models']['groups'] as $group => $members) {
                    p("Sampling $group");
                    $fail += test("service=sample pattern=$pid mode=measurements samplerGroup=$group");
                }
                h3("  Options");
                foreach($pinfo['options'] as $oid => $option) {
                    if($option['type'] != 'chooseOne') {
                        p("Sampling $oid");
                        $fail += test("service=sample pattern=$pid mode=options option=$oid");
                    }
                }
            }
        }
    }
} else {
    echo "\n\nCould not get results from info service\n\n";
    $fail = true;
}

if($fail > 0) {
    h1("\033[31mTests failed :(\033[0m");
    h2("We encountered $fail warnings/failures.");
    echo "\n\n";
    exit(1);
} else {
    h1("\033[32mTests completed :)\033[0m");
    h2("No warning, no failures. Well done!");
    echo "\n\n";
    exit(0);
}




function test($cmd) 
{
    $dir = getcwd();
    chdir($dir);
    $fullCmd = "php index.php $cmd 2>scripts/test.log";
    exec($fullCmd, $output, $result);
    $errors = file_get_contents("scripts/test.log");
    if($result === 0 && strlen($errors) === 0) {
        ok();
        return 0;
    } else {
        if ($result === 0) warn();
        else ko();
        p("Error output:\n\n$errors");
        p("Please fix these.");
        p("Reproduce with this command:");
        h3("./freesewing $cmd 1>/dev/null");
        p();
        return 1;
    }
}

function h1($string)
{
    echo "\n\n\033[33m$string\033[0m";
    echo "\n\n\033[33m".str_pad('-', 72, '-')."\033[0m";
}

function h2($string)
{
    echo "\n\n\033[33m  $string\033[0m";
}

function h3($string)
{
    echo "\n\n\033[33m    $string\033[0m";
}

function p($string)
{
    echo "\n".str_pad('    '.$string, 69, ' ');
}

function ok()
{
    echo "\033[32m OK \033[0m";
}

function ko()
{
    echo "\033[31m Problem! \033[0m";
}

function warn()
{
    echo "\033[33m Warning \033[0m";
}
