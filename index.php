<?php
/*  Load time  


$time = microtime();
$time = explode(' ', $time);
$time = $time[1] + $time[0];
$start = $time;

            

$time = microtime();
            $time = explode(' ', $time);
            $time = $time[1] + $time[0];
            $finish = $time;
            $total_time = round(($finish - $start), 4);
            echo 'Page generated in '.$total_time.' seconds.';

    */





if (isset($_GET['action'])) {
    switch($_GET['action']){
        case 'getinfo':
            $stat1 = file('/proc/stat'); 
            //usleep(1000000);
            usleep(10000);

            $stat2 = file('/proc/stat'); 
            $info1 = explode(" ", preg_replace("!cpu +!", "", $stat1[0])); 
            $info2 = explode(" ", preg_replace("!cpu +!", "", $stat2[0])); 
            $dif = array(); 
            $dif['user'] = $info2[0] - $info1[0]; 
            $dif['nice'] = $info2[1] - $info1[1]; 
            $dif['sys'] = $info2[2] - $info1[2]; 
            $dif['idle'] = $info2[3] - $info1[3]; 
            $total = array_sum($dif); 


            $cpu = array(); 
            foreach($dif as $x=>$y) $cpu[$x] = round($y / $total * 100, 1);
            
            /*
            vars:
            $cpu['user'], $cpu['nice'], $cpu['sys'], $cpu['idle'] 



            user buff cached
             */


            $fh = fopen('/proc/meminfo','r');
            $mem = array();
            $stat = -1;

            while ($line = fgets($fh)) {
            if (preg_match('/^MemTotal:\s+(\d+)\skB$/', $line, $pieces)) {
                    $mem['MemTotal'] = (int)$pieces[1];
                    break;
                    }
            }

            while ($line = fgets($fh)) {
                $pieces = array();
                if (preg_match('/^Buffers:\s+(\d+)\skB$/', $line, $pieces)) {
                $mem['buffered'] = (int)$pieces[1];
                $mem['bufferedP'] = ((int)$pieces[1])/$mem['MemTotal'];
                $stat += 1;
                if($stat == 7)
                    break;
                }
                if (preg_match('/^Cached:\s+(\d+)\skB$/', $line, $pieces)) {
                $mem['cached'] = (int)$pieces[1];
                $mem['cachedP'] = ((int)$pieces[1])/$mem['MemTotal'];
                $stat += 2;
                if($stat == 7)
                    break;
                }
                if (preg_match('/^Active:\s+(\d+)\skB$/', $line, $pieces)) {
                $mem['active'] = (int)$pieces[1];
                $mem['activeP'] = ((int)$pieces[1])/$mem['MemTotal'];
                $stat += 4;
                if($stat == 7)
                    break; 
                }
            }
            fclose($fh);



            die(json_encode(array_merge($cpu, $mem)));

            break;
        default:

                break;
        }
}



/* 
totale: total (label= MemFree),sotto
usata dall'utente: MemTotal-(Cached+Buffers)
cached and buffered: Cached+Buffers

$fh = fopen('/proc/meminfo','r');
  $mem = 0;
  while ($line = fgets($fh)) {
    $pieces = array();
    if (preg_match('/^MemTotal:\s+(\d+)\skB$/', $line, $pieces)) {
      $mem = $pieces[1];
      break;
    }
  }
  fclose($fh);

  echo "$mem kB RAM found"; ?>

MemTotal:       255908 kB
MemFree:         69936 kB
Buffers:         15812 kB
Cached:         115124 kB
SwapCached:          0 kB
Active:          92700 kB
Inactive:        63792 kB


*/
?>


<!doctype html>
<html lang="en" class="loading">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>Server dashboard</title>

        <!-- CSS - Bootstrap -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
        <link href="https://fonts.googleapis.com/css?family=Quicksand" rel="stylesheet">
        <link href="assets/css/style.css" rel="stylesheet">

        <!-- Font Awsome  -->
        <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">

        <style>.pace {
  -webkit-pointer-events: none;
  pointer-events: none;

  -webkit-user-select: none;
  -moz-user-select: none;
  user-select: none;
}

.pace .pace-activity {
  display: block;
  position: fixed;
  z-index: 2000;
  top: 0;
  right: 0;
  width: 300px;
  height: 300px;
  background: #29d;
  -webkit-transition: -webkit-transform 0.3s;
  transition: transform 0.3s;
  -webkit-transform: translateX(100%) translateY(-100%) rotate(45deg);
  transform: translateX(100%) translateY(-100%) rotate(45deg);
  pointer-events: none;
}

.pace.pace-active .pace-activity {
  -webkit-transform: translateX(50%) translateY(-50%) rotate(45deg);
  transform: translateX(50%) translateY(-50%) rotate(45deg);
}

.pace .pace-activity::before,
.pace .pace-activity::after {
    -moz-box-sizing: border-box;
    box-sizing: border-box;
    position: absolute;
    bottom: 30px;
    left: 50%;
    display: block;
    border: 5px solid #fff;
    border-radius: 50%;
    content: '';
}

.pace .pace-activity::before {
    margin-left: -40px;
    width: 80px;
    height: 80px;
    border-right-color: rgba(0, 0, 0, .2);
    border-left-color: rgba(0, 0, 0, .2);
    -webkit-animation: pace-theme-corner-indicator-spin 3s linear infinite;
    animation: pace-theme-corner-indicator-spin 3s linear infinite;
}

.pace .pace-activity::after {
    bottom: 50px;
    margin-left: -20px;
    width: 40px;
    height: 40px;
    border-top-color: rgba(0, 0, 0, .2);
    border-bottom-color: rgba(0, 0, 0, .2);
    -webkit-animation: pace-theme-corner-indicator-spin 1s linear infinite;
    animation: pace-theme-corner-indicator-spin 1s linear infinite;
}



@-webkit-keyframes pace-theme-corner-indicator-spin {
  0% { -webkit-transform: rotate(0deg); }
  100% { -webkit-transform: rotate(359deg); }
}
@keyframes pace-theme-corner-indicator-spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(359deg); }
}
        </style>

        
        <!-- JS - Pace -->
        <script>
            window.paceOptions = {
                ajax: false,
                restartOnRequestAfter: false,
            };
        </script>
        <script src="assets/js/pace.min.js"></script>
        

        <!-- JS - JQuery -->
        <script src="https://code.jquery.com/jquery-3.2.1.min.js"  integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4="  crossorigin="anonymous"></script>

        <!-- JS - Tether -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js"></script>
        
        <!-- JS - Bootstrap -->
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" crossorigin="anonymous"></script>
        

        
    </head>
    <body class="loading">
        <div id="loader-container">
            <div id="animation-container">
                <div id="circleloader"></div>
            </div>
        </div>

        <div id="main-container" class="hidden">
            <div class="col-lg-2"></div>
            <div class="container col-sm-12 col-lg-8">
                <div id="header">
                    <h1 class="main-title">Pandry's Server Dashboard</h1>
                </div>
                </div>
                <div class="container col-sm-12 col-lg-8">
                <div class="row">
                
                

                    <!-- Color: 31d96a -->
                    <div class="panel col-lg-3">
                        <div class="panel-heading">
                            <div class="panel-title">
                                <a href="#" data-toggle="collapse" data-target="#CPUTable">
                                    <i class="panel-min-button fa fa-minus"></i>
                                </a>
                                <p class="panel-text">CPU Load
                                </p>
                            </div>
                        </div>
                        <div class="panel-body collapse show" id="CPUTable">
                            <div class="panel-body-content" id="cpuTableContent">
                                <p>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="panel col-lg-9">
                        <div class="panel-heading">
                            <div class="panel-title">
                                <a href="#" data-toggle="collapse" data-target="#RAMTable">
                                    <i class="panel-min-button fa fa-minus"></i>
                                </a>
                                <p class="panel-text">Occupied RAM</p>
                                
                            </div>
                        </div>
                        <div class="panel-body collapse show" id="RAMTable">
                            <div id="RAMGraphContainer" class="panel-body-content">
                                <svg  viewBox="0 -17 650 150">
                                    <rect x="5" y="5" width="640" height="110" stroke-width="3" class="gr-ram-xlg-surface" rx="2" />
                                    <g>
                                        <rect x="25" y="20" width="39" height="70" class="gr-ram-xlg-driver" stroke-width="0" rx="0" />
                                        <rect x="76" y="20" width="39" height="70" class="gr-ram-xlg-driver" stroke-width="0" rx="0" />
                                        <rect x="127" y="20" width="39" height="70" class="gr-ram-xlg-driver" stroke-width="0" rx="0" />
                                        <rect x="178" y="20" width="39" height="70" class="gr-ram-xlg-driver" stroke-width="0" rx="0" />
                                        <rect x="229" y="20" width="39" height="70" class="gr-ram-xlg-driver" stroke-width="0" rx="0" />
                                        <rect x="280" y="20" width="39" height="70" class="gr-ram-xlg-driver" stroke-width="0" rx="0" />
                                        <rect x="331" y="20" width="39" height="70" class="gr-ram-xlg-driver" stroke-width="0" rx="0" />
                                        <rect x="382" y="20" width="39" height="70" class="gr-ram-xlg-driver" stroke-width="0" rx="0" />
                                        <rect x="433" y="20" width="39" height="70" class="gr-ram-xlg-driver" stroke-width="0" rx="0" />
                                        <rect x="484" y="20" width="39" height="70" class="gr-ram-xlg-driver" stroke-width="0" rx="0" />
                                        <rect x="535" y="20" width="39" height="70" class="gr-ram-xlg-driver" stroke-width="0" rx="0" />
                                        <rect x="586" y="20" width="39" height="70" class="gr-ram-xlg-driver" stroke-width="0" rx="0" />
                                    </g>
                                    <g>
                                        <line x1="28" y1="105" x2="28" y2="116" stroke-width="4" ry="4" class="gr-ram-xlg-pin" stroke-linecap="round"/>
                                        <line x1="39" y1="105" x2="39" y2="116" stroke-width="4" ry="4" class="gr-ram-xlg-pin" stroke-linecap="round"/>
                                        <line x1="50" y1="105" x2="50" y2="116" stroke-width="4" ry="4" class="gr-ram-xlg-pin" stroke-linecap="round"/>
                                        <line x1="61" y1="105" x2="61" y2="116" stroke-width="4" ry="4" class="gr-ram-xlg-pin" stroke-linecap="round"/>
                                        <line x1="72" y1="105" x2="72" y2="116" stroke-width="4" ry="4" class="gr-ram-xlg-pin" stroke-linecap="round"/>
                                        <line x1="83" y1="105" x2="83" y2="116" stroke-width="4" ry="4" class="gr-ram-xlg-pin" stroke-linecap="round"/>
                                        <line x1="94" y1="105" x2="94" y2="116" stroke-width="4" ry="4" class="gr-ram-xlg-pin" stroke-linecap="round"/>
                                        <line x1="105" y1="105" x2="105" y2="116" stroke-width="4" ry="4" class="gr-ram-xlg-pin" stroke-linecap="round"/>
                                        <line x1="116" y1="105" x2="116" y2="116" stroke-width="4" ry="4" class="gr-ram-xlg-pin" stroke-linecap="round"/>
                                        <line x1="127" y1="105" x2="127" y2="116" stroke-width="4" ry="4" class="gr-ram-xlg-pin" stroke-linecap="round"/>
                                        <line x1="138" y1="105" x2="138" y2="116" stroke-width="4" ry="4" class="gr-ram-xlg-pin" stroke-linecap="round"/>
                                        <line x1="149" y1="105" x2="149" y2="116" stroke-width="4" ry="4" class="gr-ram-xlg-pin" stroke-linecap="round"/>
                                        <line x1="160" y1="105" x2="160" y2="116" stroke-width="4" ry="4" class="gr-ram-xlg-pin" stroke-linecap="round"/>
                                        <line x1="171" y1="105" x2="171" y2="116" stroke-width="4" ry="4" class="gr-ram-xlg-pin" stroke-linecap="round"/>
                                        <line x1="182" y1="105" x2="182" y2="116" stroke-width="4" ry="4" class="gr-ram-xlg-pin" stroke-linecap="round"/>
                                        <line x1="193" y1="105" x2="193" y2="116" stroke-width="4" ry="4" class="gr-ram-xlg-pin" stroke-linecap="round"/>
                                        <line x1="204" y1="105" x2="204" y2="116" stroke-width="4" ry="4" class="gr-ram-xlg-pin" stroke-linecap="round"/>
                                        <line x1="215" y1="105" x2="215" y2="116" stroke-width="4" ry="4" class="gr-ram-xlg-pin" stroke-linecap="round"/>
                                        <line x1="226" y1="105" x2="226" y2="116" stroke-width="4" ry="4" class="gr-ram-xlg-pin" stroke-linecap="round"/>
                                        <line x1="237" y1="105" x2="237" y2="116" stroke-width="4" ry="4" class="gr-ram-xlg-pin" stroke-linecap="round"/>
                                        <line x1="248" y1="105" x2="248" y2="116" stroke-width="4" ry="4" class="gr-ram-xlg-pin" stroke-linecap="round"/>
                                        <line x1="259" y1="105" x2="259" y2="116" stroke-width="4" ry="4" class="gr-ram-xlg-pin" stroke-linecap="round"/>
                                        <line x1="270" y1="105" x2="270" y2="116" stroke-width="4" ry="4" class="gr-ram-xlg-pin" stroke-linecap="round"/>
                                        <line x1="281" y1="105" x2="281" y2="116" stroke-width="4" ry="4" class="gr-ram-xlg-pin" stroke-linecap="round"/>
                                        <line x1="292" y1="105" x2="292" y2="116" stroke-width="4" ry="4" class="gr-ram-xlg-pin" stroke-linecap="round"/>
                                        <line x1="303" y1="105" x2="303" y2="116" stroke-width="4" ry="4" class="gr-ram-xlg-pin" stroke-linecap="round"/>
                                        <line x1="314" y1="105" x2="314" y2="116" stroke-width="4" ry="4" class="gr-ram-xlg-pin" stroke-linecap="round"/>
                                        <line x1="325" y1="105" x2="325" y2="116" stroke-width="4" ry="4" class="gr-ram-xlg-pin" stroke-linecap="round"/>
                                        <line x1="336" y1="105" x2="336" y2="116" stroke-width="4" ry="4" class="gr-ram-xlg-pin" stroke-linecap="round"/>
                                        <line x1="347" y1="105" x2="347" y2="116" stroke-width="4" ry="4" class="gr-ram-xlg-pin" stroke-linecap="round"/>
                                        <line x1="358" y1="105" x2="358" y2="116" stroke-width="4" ry="4" class="gr-ram-xlg-pin" stroke-linecap="round"/>
                                        <line x1="369" y1="105" x2="369" y2="116" stroke-width="4" ry="4" class="gr-ram-xlg-pin" stroke-linecap="round"/>
                                        <line x1="380" y1="105" x2="380" y2="116" stroke-width="4" ry="4" class="gr-ram-xlg-pin" stroke-linecap="round"/>
                                        <line x1="391" y1="105" x2="391" y2="116" stroke-width="4" ry="4" class="gr-ram-xlg-pin" stroke-linecap="round"/>
                                        <line x1="402" y1="105" x2="402" y2="116" stroke-width="4" ry="4" class="gr-ram-xlg-pin" stroke-linecap="round"/>
                                        <line x1="413" y1="105" x2="413" y2="116" stroke-width="4" ry="4" class="gr-ram-xlg-pin" stroke-linecap="round"/>
                                        <line x1="424" y1="105" x2="424" y2="116" stroke-width="4" ry="4" class="gr-ram-xlg-pin" stroke-linecap="round"/>
                                        <line x1="435" y1="105" x2="435" y2="116" stroke-width="4" ry="4" class="gr-ram-xlg-pin" stroke-linecap="round"/>
                                        <line x1="446" y1="105" x2="446" y2="116" stroke-width="4" ry="4" class="gr-ram-xlg-pin" stroke-linecap="round"/>
                                        <line x1="457" y1="105" x2="457" y2="116" stroke-width="4" ry="4" class="gr-ram-xlg-pin" stroke-linecap="round"/>
                                        <line x1="468" y1="105" x2="468" y2="116" stroke-width="4" ry="4" class="gr-ram-xlg-pin" stroke-linecap="round"/>
                                        <line x1="479" y1="105" x2="479" y2="116" stroke-width="4" ry="4" class="gr-ram-xlg-pin" stroke-linecap="round"/>
                                        <line x1="490" y1="105" x2="490" y2="116" stroke-width="4" ry="4" class="gr-ram-xlg-pin" stroke-linecap="round"/>
                                        <line x1="501" y1="105" x2="501" y2="116" stroke-width="4" ry="4" class="gr-ram-xlg-pin" stroke-linecap="round"/>
                                        <line x1="512" y1="105" x2="512" y2="116" stroke-width="4" ry="4" class="gr-ram-xlg-pin" stroke-linecap="round"/>
                                        <line x1="523" y1="105" x2="523" y2="116" stroke-width="4" ry="4" class="gr-ram-xlg-pin" stroke-linecap="round"/>
                                        <line x1="534" y1="105" x2="534" y2="116" stroke-width="4" ry="4" class="gr-ram-xlg-pin" stroke-linecap="round"/>
                                        <line x1="545" y1="105" x2="545" y2="116" stroke-width="4" ry="4" class="gr-ram-xlg-pin" stroke-linecap="round"/>
                                        <line x1="556" y1="105" x2="556" y2="116" stroke-width="4" ry="4" class="gr-ram-xlg-pin" stroke-linecap="round"/>
                                        <line x1="567" y1="105" x2="567" y2="116" stroke-width="4" ry="4" class="gr-ram-xlg-pin" stroke-linecap="round"/>
                                        <line x1="578" y1="105" x2="578" y2="116" stroke-width="4" ry="4" class="gr-ram-xlg-pin" stroke-linecap="round"/>
                                        <line x1="589" y1="105" x2="589" y2="116" stroke-width="4" ry="4" class="gr-ram-xlg-pin" stroke-linecap="round"/>
                                        <line x1="600" y1="105" x2="600" y2="116" stroke-width="4" ry="4" class="gr-ram-xlg-pin" stroke-linecap="round"/>
                                        <line x1="611" y1="105" x2="611" y2="116" stroke-width="4" ry="4" class="gr-ram-xlg-pin" stroke-linecap="round"/>
                                        <line x1="622" y1="105" x2="622" y2="116" stroke-width="4" ry="4" class="gr-ram-xlg-pin" stroke-linecap="round"/>
                                    </g>

                                    <g>
                                        <rect x="5" y="5" width="640" height="110" stroke-width="0" class="gr-ram-xlg-cachedmem" rx="2" />
                                        <rect x="5" y="5" width="480" height="110" stroke-width="0" class="gr-ram-xlg-bufferedmem" rx="2" />
                                        <rect x="5" y="5" width="256" height="110" stroke-width="0" class="gr-ram-xlg-userusedmem" rx="2" />
                                    </g>


                                </svg>
                            </div>
                        </div>
                    </div>


                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="panel-heading">
                            <div class="panel-title">
                                <a href="#" data-toggle="collapse" data-target="#RAMTable">
                                    <i class="panel-min-button fa fa-minus"></i>
                                </a>
                                <p class="panel-text">CPU Load Graph</p>
                            </div>
                        </div>
                        <div class="panel-body collapse show" id="RAMTable">
                            <div class="panel-body-content">
                                <p>Cool Graph man!!
                                </p>
                            </div>
                        </div>
                    </div>
                </div>






            </div>
        </div>
        <!-- JS - Normalize.css 
        <script src="https://cdnjs.cloudflare.com/ajax/libs/normalize/5.0.0/normalize.min.css"/>-->
        <!-- JS - d3.js -->
        <script type="text/javascript" src="assets/js/jquery.svg.min.js"></script>
        <script type="text/javascript" src="assets/js/jquery.svganim.min.js"></script>
         <script src="assets/js/progressbar.min.js"></script>
         <script src="assets/js/site.js"></script>
    </body>
</html>