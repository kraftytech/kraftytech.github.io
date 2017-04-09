<?php
date_default_timezone_set('America/Sao_Paulo');
$tz=timezone_open("America/Sao_Paulo");
$tz_utc=timezone_open("UTC");
?>

<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="utf-8">
        <title>Pluvi.On | Stations Summary</title>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
        <style type="text/css">
            @import url('https://fonts.googleapis.com/css?family=Montserrat');
            h1{
            font-size: 30px;
            color: #fff;
            text-transform: uppercase;
            font-weight: 300;
            text-align: left;
            margin-bottom: 15px;
            font-family: 'Montserrat', sans-serif;
            }
            table{
            width:100%;
            table-layout: fixed;
            }
            .tbl-header{
            background-color: rgba(255,255,255,0.3);
            }
            .tbl-content{
            overflow-x:auto;
            margin-top: 0px;
            border: 1px solid rgba(255,255,255,0.3);
            }
            th{
            padding: 20px 15px;
            text-align: left;
            font-weight: 500;
            font-size: 12px;
            color: #fff;
            text-transform: uppercase;
            }
            td{
            padding: 15px;
            text-align: left;
            vertical-align:middle;
            font-weight: 300;
            font-size: 12px;
            color: #fff;
            border-bottom: solid 1px rgba(255,255,255,0.1);
            }


            /* demo styles */

            @import url(http://fonts.googleapis.com/css?family=Roboto:400,500,300,700);
            body{
            background: -webkit-linear-gradient(left, #6881B6, #25b7c4);
            background: linear-gradient(to right, #6881B6, #25b7c4);
            font-family: 'Roboto', sans-serif;
            }
            section{
            margin: 50px;
            }


            /* follow me template */
            .made-with-love {
            margin-top: 40px;
            padding: 10px;
            clear: left;
            text-align: center;
            font-size: 10px;
            font-family: arial;
            color: #fff;
            }
            .made-with-love i {
            font-style: normal;
            color: #F50057;
            font-size: 14px;
            position: relative;
            top: 2px;
            }
            .made-with-love a {
            color: #fff;
            text-decoration: none;
            }
            .made-with-love a:hover {
            text-decoration: underline;
            }


            /* for custom scrollbar for webkit browser*/

            ::-webkit-scrollbar {
                width: 6px;
            } 
            ::-webkit-scrollbar-track {
                -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3); 
            } 
            ::-webkit-scrollbar-thumb {
                -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3); 
            }
            .alert {
                color:black;
            }
        </style>
    </head>

    <body>
        <script>
            // '.tbl-content' consumed little space for vertical scrollbar, scrollbar width depend on browser/os/platfrom. Here calculate the scollbar width .
            $(window).on("load resize ", function() {
            var scrollWidth = $('.tbl-content').width() - $('.tbl-content table').width();
            $('.tbl-header').css({'padding-right':scrollWidth});
            }).resize();
        </script>
        <section>
            <h1>
                <img style="align:left" src="https://static.wixstatic.com/media/91926f_d0a260aa459c485fa0833684f8aa34e6~mv2.png/v1/fill/w_80,h_80,al_c,usm_0.66_1.00_0.01/91926f_d0a260aa459c485fa0833684f8aa34e6~mv2.png"/>
                Stations Summary
            </h1>
            <div class="tbl-header">
                <table cellpadding="0" cellspacing="0" border="0">
                    <thead>
                        <tr>
                            <th>Station</th>
                            <th>Date/Time</th>
                            <th>Temperature</th>
                            <th>Precipitation</th>
                            <th>Elapsed Time</th>
                        </tr>
                    </thead>
                </table>
            </div>
            <div class="tbl-content">
                <table cellpadding="0" cellspacing="0" border="0">
                    <tbody>

                        <?php
                        $url_channel ='https://api.thingspeak.com/users/pluvion/channels.json';
                        $content_channel = file_get_contents($url_channel);
                        $json_channel = json_decode($content_channel, true);
                        
                        $now_utc = new DateTime();
                        $now_utc->setTimezone($tz_utc);

                        $timetout = 21*60;

                        foreach($json_channel['channels'] as $item_channel) {

                            $url = 'http://api.thingspeak.com/channels/'. $item_channel['id'] .'/feeds.json?results=1';
                            $content = file_get_contents($url);
                            $json = json_decode($content, true);

                            echo "<tr>";

                            echo "<td class='stationName'>";
                            echo $item_channel['name'];
                            echo "</td>";

                            foreach($json['feeds'] as $item) {

                                $dt = new DateTime($item['created_at'], $tz_utc);
                                $elapsed = $now_utc->getTimestamp() - $dt->getTimestamp();
                                $diff = $now_utc->diff($dt);
                                $dt->setTimezone($tz);
                                $clazz = ($elapsed > $timetout)? "alert":"";

                                echo "<td class='$clazz'>";
                                    echo $dt->format("d/m/Y H:i");
                                echo "</td>";

                                echo "<td class='$clazz'>";
                                    echo $item['field1'];
                                    echo ' &deg;C';
                                echo "</td>";

                                echo "<td class='$clazz'>";
                                    echo $item['field4'];
                                    echo ' mm';
                                echo "</td>";

                                echo "<td class='$clazz'>";
                                    echo $diff->format('%d Day %h Hours %i Minute %s Seconds');
                                echo "</td>";
                            }

                            echo "</tr>";
                        }
                    ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th class="alert">* Stations with problems</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </section>
    </body>

</html>