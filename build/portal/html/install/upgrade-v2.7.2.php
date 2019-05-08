<?php

    /////////////////////////////////////////////////////////////////////////////////////
    //                            ADS-B RECEIVER PORTAL                                //
    // =============================================================================== //
    // Copyright and Licensing Information:                                            //
    //                                                                                 //
    // The MIT License (MIT)                                                           //
    //                                                                                 //
    // Copyright (c) 2015-2019 Joseph A. Prochazka                                     //
    //                                                                                 //
    // Permission is hereby granted, free of charge, to any person obtaining a copy    //
    // of this software and associated documentation files (the "Software"), to deal   //
    // in the Software without restriction, including without limitation the rights    //
    // to use, copy, modify, merge, publish, distribute, sublicense, and/or sell       //
    // copies of the Software, and to permit persons to whom the Software is           //
    // furnished to do so, subject to the following conditions:                        //
    //                                                                                 //
    // The above copyright notice and this permission notice shall be included in all  //
    // copies or substantial portions of the Software.                                 //
    //                                                                                 //
    // THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR      //
    // IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,        //
    // FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE     //
    // AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER          //
    // LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,   //
    // OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE   //
    // SOFTWARE.                                                                       //
    /////////////////////////////////////////////////////////////////////////////////////

    ///////////////////////
    // UPGRADE TO V2.7.2
    ///////////////////////

    // --------------------------------------------------------
    // Updates the version setting to 2.7.2.
    // Add indexes to the aircraft and positions tables.
    // --------------------------------------------------------

    $results = upgrade();
    exit(json_encode($results));

    function upgrade() {
        require_once($_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR."classes".DIRECTORY_SEPARATOR."common.class.php");
        require_once($_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR."classes".DIRECTORY_SEPARATOR."settings.class.php");

        $common = new common();
        $settings = new settings();

        try {

            if ($settings::db_driver == "mysql" || $settings::db_driver == "sqlite") {

                // Add an index to the aircraft table.
                $dbh = $common->pdoOpen();
                $sql = "CREATE INDEX IF NOT EXISTS idxIcao ON ".$settings::db_prefix."aircraft(icao)";
                $sth = $dbh->prepare($sql);
                $sth->execute();
                $sth = NULL;
                $dbh = NULL;

                // Add an index to the positions table.
                $dbh = $common->pdoOpen();
                $sql = "CREATE INDEX IF NOT EXISTS idxFlight ON ".$settings::db_prefix."positions(flight)";
                $sth = $dbh->prepare($sql);
                $sth->execute();
                $sth = NULL;
                $dbh = NULL;
            }

            // Update the version and patch settings..
            $common->updateSetting("version", "2.7.2");
            $common->updateSetting("patch", "");

            // The upgrade process completed successfully.
            $results['success'] = TRUE;
            $results['message'] = "Upgrade to v2.7.2 successful.";
            return $results;

        } catch(Exception $e) {
            // Something went wrong during this upgrade process.
            $results['success'] = FALSE;
            $results['message'] = $e->getMessage();
            return $results;
        }
    }
?>

