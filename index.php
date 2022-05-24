<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta http-equiv="content-language" content="cz" />
    <meta http-equiv="content-type" content="text/html;charset=utf-8" />
    <meta name="copyright" content="podly" />
    <meta name="robots" content="index, follow" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <meta charset="UTF-8" />

    <title>Solar heating monitor</title>

    <link href="assets/styles.css" rel="stylesheet" />
    <link rel="icon" type="image/png" href="./graf.png" />

    <link rel="stylesheet" media="screen" href="styles/vlaCal-v2.1.css" type="text/css" />
    <link rel="stylesheet" media="screen" href="styles/vlaCal-v2.1-adobe_cs3.css" type="text/css" />
    <link rel="stylesheet" media="screen" href="styles/vlaCal-v2.1-apple_widget.css" type="text/css" />

    <script type="text/javascript" src="jslib/mootools-1.2-core-compressed.js"></script>
    <script type="text/javascript" src="jslib/vlaCal-v2.1-compressed.js"></script>

    <!-- You could also include the uncompressed versions for developing purposes:
	<script type="text/javascript" src="jslib/mootools-1.2-core.js"></script>
	<script type="text/javascript" src="jslib/vlaCal-v2.1.js"></script>-->


    <style>

    #chart {
      max-width: 1250px;
      margin: 10px auto;
    }

    body {
        cursor: default;
        text-align: left;
        font-family: calibri, arial, sans-serif;
        font-size: 13px;
        margin: 0;
        padding: 5px;
    }

    table th {
        vertical-align: top;
        }

    input {
        text-align: center;
        font-family: calibri, arial, sans-serif;
        font-size: 13px;
        background-color: white;
        border: 1px solid;
        border-color: #abadb3 #dbdfe6 #e3e9ef #e2e3ea;
        padding: 2px;
        }

    input:focus, input:hover  {
        border-color: #5794bf #b7d5ea #c7e2f1 #c5daed;
    }

    .pickerImg {
        position: absolute;
        margin-left: -16px;
        margin-top: 5px;
        cursor: pointer;
    }
    .infoBox {
        background-color: #fefdec;
        border: 1px solid #edebcd;
        padding: 6px;
        margin-bottom: 20px;
    }

    </style>




    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>


    <script type="text/javascript">
        window.addEvent('domready', function() {
            new vlaDatePicker('datum');
            });
    </script>



  </head>

  <body>

    <form action="index.php" method="get">
    Date : <input id="datum" name="datum" type="text" style="width: 80px;" maxlength="10" />
    <input type="submit" value="zobrazit">
    </form>

    <?php
        $datum = $_GET['datum'];

        if ($datum == ""){
        //------------------------------------------------------
        $pocet_souboru = 0;
            if ($cesta = opendir('./logs/')) {
            while (false !== ($soubor[$pocet_souboru] = readdir($cesta))) {
                $pocet_souboru = $pocet_souboru+1 ;
                }
            }
            closedir($cesta);
            sort($soubor);
            $soubor_pro_graf = $soubor[$pocet_souboru];
        }
        else {
        $hodnoty = explode ("/",$datum);
        $soubor_pro_graf = "TextData_".$hodnoty[2].$hodnoty[1].$hodnoty[0].".log";
        }

        echo 'Reading: <a href="./logs/'.$soubor_pro_graf.'">'.$soubor_pro_graf.'</a> <BR>';
        $soubor = $soubor_pro_graf;


        // SOLAR DATA - START ==========================================================


        //$soubor = "actual.log";


        if (( $soubor = fopen ( "./logs/".$soubor , "r" )) !== FALSE ) {
            while (($data = fgets($soubor)) !== FALSE ) {
                if (chop((explode ("	",$data)[20])) == "1,00") {

                    //echo "line: ".$data."<br>";

                    $data = StrTr ($data ,",",".");
                    $values = explode ("	",$data);

                    $casoveinfo = chop($values[0]);
                    $datumcas = explode (" ",$casoveinfo);
                    $cas      = explode (":",$datumcas[1]);
                    $cas_index = round ( $cas[0]*60 + $cas[1] + $cas[2]/60 );

                    $datum_cas[$cas_index] = chop($values[0]);
                    $datum_cas[$cas_index] = str_replace(".20", ".", $datum_cas[$cas_index]);
                    $delka_datumu = strlen($datum_cas[$cas_index]) ;
                    //$datum_cas[$cas_index] = mb_substr( $datum_cas[$cas_index], 0, $delka_datumu - 3);

                    $cidlo1[$cas_index] = chop($values[1]);
                    $aktualni_teplota_kol = $cidlo1[$cas_index];
                    $cidlo2[$cas_index] = chop($values[2]);
                    $cidlo3[$cas_index] = chop($values[3]);
                    $cidlo4[$cas_index] = chop($values[4]);
                    $prumer[$cas_index] = ($cidlo2[$cas_index] + $cidlo3[$cas_index]) / 2;
                    $cerpadlo[$cas_index] = chop($values[5]);
                    $topeni[$cas_index] = chop($values[6]);
                    $lineup[$cas_index] = 75;

                    if ($cerpadlo[$cas_index] > 99) $cerpadlo[$cas_index] = $cerpadlo[$cas_index-1];
                    if ($topeni[$cas_index] == 100) $topeni[$cas_index] = "5";


                    $cidlo1[$cas_index+1] = $cidlo1[$cas_index];
                    $cidlo2[$cas_index+1] = $cidlo2[$cas_index];
                    $cidlo3[$cas_index+1] = $cidlo3[$cas_index];
                    $cidlo4[$cas_index+1] = $cidlo4[$cas_index];
                    $prumer[$cas_index+1] = $prumer[$cas_index];
                    $cerpadlo[$cas_index+1] = $cerpadlo[$cas_index];
                    $cerpadlo[$cas_index+2] = 0 ;
                    $topeni[$cas_index+1] = $topeni[$cas_index];
                    $topeni[$cas_index+2] = 0 ;


                }
            }
        }

        fclose($soubor);

       // $categories = "'      ".implode($datum_cas,"      ', '")."',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '',' '', " ;

    ?>


    <!-- Appex chart -->
    <div id="chart"></div>

    <script>

        var options = {
          series: [
          {
              name: 'Solar pump   ',
              type: 'column',
              data: [ <?php echo implode($cerpadlo, ', '); ?> ]
            },
            
          {
              name: 'Fireplace pump   ',
              type: 'column',
              data: [ <?php echo implode($topeni, ', '); ?> ]
          },
            
          {
            name: "Solar collector",
            //data: [10, 41, 35, 51, 49, 62, 69, 91, 148]
           data: [ <?php echo implode($cidlo1, ', '); ?>]
            },
          {
            name: "Tank DW",
           // data: [10, 20, 41, 35, 51, 49, 62, 69, 91]
            data: [ <?php echo implode($cidlo2, ', '); ?>]
            },
          {
            name: 'Tank UP',
            //data: [110, 41, 35, 33, 51, 49, 62, 69, 91]
           data: [ <?php echo implode($cidlo3, ', '); ?>]
          }, 
          {
            name: "Fireplace",
            //data: [10, 41, 35, 51, 49, 62, 69, 91, 148]
           data: [ <?php echo implode($cidlo4, ', '); ?>]
            },
            
        ],
          chart: {
          height: 500,
          type: 'line',
          zoom: {
            enabled: true
          },
        },
        colors: ['#FF00FF', '#aaaaaa', '#800080', '#000cff', '#ff3333', '#555555'],
        dataLabels: {
          enabled: false
        },
        stroke: {
          width: [2, 2, 2, 2, 2, 2],
          curve: 'straight',
          dashArray: [0, 0, 0, 2, 2, 2]
        },
        title: {
          text: 'Temperatures',
          align: 'center'
        },
        legend: {
            position: 'top',
            horizontalAlign: 'center',
        },
        markers: { 
            tooltipHoverFormatter: function(seriesName, opts) { return seriesName + ': <strong>' + opts.w.globals.series[opts.seriesIndex][opts.dataPointIndex] + '</strong>' },
          size: 0,
          hover: {
            sizeOffset: 6
          }
        },
    
        
        xaxis: {
            //categories: [ <?php echo $categories; ?>],

            categories:  ['00:00',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ','01:00',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ','02:00',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ','03:00',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ','04:00',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ','05:00',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ','06:00',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ','07:00',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ','08:00',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ','09:00',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ','10:00',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ','11:00',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ','12:00',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ','13:00',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ','14:00',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ','15:00',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ','16:00',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ','17:00',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ','18:00',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ','19:00',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ','20:00',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ','21:00',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ','22:00',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ','23:00',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' '],
        },
        tooltip: {
          y: [
            {
              title: {
                formatter: function (val) {
                  return val
                }
              }
            },
            {
              title: {
                formatter: function (val) {
                  return val
                }
              }
            },
            {
              title: {
                formatter: function (val) {
                  return val;
                }
              }
            }
          ]
        },
        grid: {
          borderColor: '#f1f1f1',
        }
        };

        var chart = new ApexCharts(document.querySelector("#chart"), options);
        chart.render();


    </script>


  </body>
</html>
