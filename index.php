<?php
  session_start();
  require_once("/home/modupe/public_html/atlas/atlas_fns.php");
?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>TranscriptAtlas GG6</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">
        
        <!-- Styles -->
        <link rel="STYLESHEET" type="text/css" href="stylesheet.css">
        
    </head>
    <body>
        <div class="flex-center position-ref full-height">
            <div class="content">
                <div class="title m-b-md">
                    TranscriptAtlas GG6
                </div>
                <div class="linksheader">
                    <a href="http://raven.anr.udel.edu/~modupe/atlas"><img src=".images/atlas_main.png" alt="Transcriptome Atlas" align="bottom" height="50"></a>
                    <a href="about.php">About</a>
                    <!-- <a href="import.php">Data Import</a> #not included -->
                    <a href="sqlquery.php">SQL Query</a>
		    <a href="metadata.php">MetaData</a>
                    <a href="expression.php">Genes Expression</a>
                    <!-- <a href="variants.php">Variants</a> # not included -->
                    <a href="https://modupeore.github.com/TransAtlasDB" target="_blank">GitHub</a>
                </div>
                <br>
                <p>Schmidt Lab samples information for Galgal6</p>
            </div>
        </div>
    </body>
</html>

