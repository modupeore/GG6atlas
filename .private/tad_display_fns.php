<?php //About page display
function about_display($result, $result2){
    $num_rows = $result->num_rows;
    echo '<form action="" method="post">';
    echo '<table class="metadata"><tr>';    
    $j = 0;
    while ($j < $result->field_count) { 
        $meta = $result->fetch_field_direct($j);
        echo '<th class="metadata" id="' . $meta->name . '">'.$meta->name.'</th>';
        $j++;
    }
    echo '</tr>';
    for ($i = 0; $i < $num_rows; $i++) {
      if ($i % 2 == 0) {
          echo "<tr class=\"odd\">";
      } else {
          echo "<tr class=\"even\">";
      }
      $row = $result->fetch_assoc();
      $j = 0;
      while ($j < $result->field_count) {
        $meta = $result->fetch_field_direct($j);
        echo '<td headers="' . $meta->name . '" class="metadata"><center>' . $row[$meta->name] . '</center></td>';
        $j++;
      }
      echo "</tr>";
    }
    if ($result2 != "null") {
        $j = 0;
        echo '<tr style="background-color:#f6fbf1;"><td class="metadata" align="right"><font style="font-size:100%;font-weight:bolder;letter-spacing:1px;">Total</font></td>';
        $row = $result2->fetch_assoc();
        while ($j < $result2->field_count) { 
            $meta = $result2->fetch_field_direct($j);
            echo '<td align="right" class="metadata" id="' . $meta->name . '"><font style="font-size:100%;font-weight:bolder;letter-spacing:1px;">'.$row[$meta->name].'</font></td>';
            $j++;
        }
        echo '</tr>';
    }
    echo '</table></form>';
}
?>

<?php
function db_display($result){
    $num_rows = $result->num_rows;
    echo '<form action="" method="post">';
    echo '<table class="metadata"><tr>';    
    $meta = $result->fetch_field_direct(0); echo '<th class="metadata" id="' . $meta->name . '">Sample Id</th>';
    $meta = $result->fetch_field_direct(1); echo '<th class="metadata" id="' . $meta->name . '">Animal Id</th>';
    $meta = $result->fetch_field_direct(2); echo '<th class="metadata" id="' . $meta->name . '">Organism</th>';
    $meta = $result->fetch_field_direct(3); echo '<th class="metadata" id="' . $meta->name . '">Tissue</th>';
    $meta = $result->fetch_field_direct(4); echo '<th class="metadata" id="' . $meta->name . '">Person</th>';
    $meta = $result->fetch_field_direct(5); echo '<th class="metadata" id="' . $meta->name . '">Organization</th>';
    $meta = $result->fetch_field_direct(6); echo '<th class="metadata" id="' . $meta->name . '">Animal Description</th>';
    $meta = $result->fetch_field_direct(7); echo '<th class="metadata" id="' . $meta->name . '">Sample Description</th>';
    $meta = $result->fetch_field_direct(8); echo '<th class="metadata" id="' . $meta->name . '">Date</th>';
    
    echo '</tr>';
    for ($i = 0; $i < $num_rows; $i++) {
      if ($i % 2 == 0) {
          echo "<tr class=\"odd\">";
      } else {
          echo "<tr class=\"even\">";
      }
      $row = $result->fetch_assoc();
      $j = 0;
      while ($j < $result->field_count) {
        $meta = $result->fetch_field_direct($j);
        echo '<td headers="' . $meta->name . '" class="metadata"><center>' . $row[$meta->name] . '</center></td>';
        $j++;
      }
      echo "</tr>";
    }
    echo '</table></form>';
}
?>

<?php //Delete data
function db_delete($table, $db_conn) {
    if (!empty($_REQUEST['accept'])) {
        echo '';
        $all_stat = $db_conn->query("select sampleid from Sample where sampleid = '".$_POST['samplename']."'");
        $total_rows = $all_stat->num_rows;
        if ($total_rows >= 1) {
            $darray = array('Sample Information');
        
            $all_stat = $db_conn->query("select sampleid from MapStats where sampleid = '".$_POST['samplename']."'");
            $total_rows = $all_stat->num_rows; if ($total_rows >=1) { array_push($darray,'Alignment Information'); }
        
            $all_stat = $db_conn->query("select sampleid from GeneStats where sampleid = '".$_POST['samplename']."'");
            $total_rows = $all_stat->num_rows; if ($total_rows >= 1) { array_push($darray,'Expression Information'); }
        
            $all_stat = $db_conn->query("select sampleid from VarSummary where sampleid = '".$_POST['samplename']."'");
            $total_rows = $all_stat->num_rows; if ($total_rows >= 1) { array_push($darray,'Variant Information'); }
        
            echo '<form action="" method="post"><table class="lines">';
            echo '<tr><td colspan="2"><center>Select the details to remove for sampleid "'.$_POST['samplename'].'"</center></td></tr>';
            foreach ($darray as $index => $ddd) {
                echo '<tr><th class="lines"><strong>'.$ddd.'</strong><th><td><input type="checkbox" name="data_delete[]" value="'.$index.'"></td></tr>';
            }
            echo '<tr><td colspan="2"><center><input type="submit" name="removed" class="import" value="are you sure?"/></center></td></tr>';
            echo '</table></form>';
        } else {print 'SampleID "'.$_POST['samplename'].'" does not exist in the database'; }
    $count = count($darray);
    return $darray;
    }
}
?>


<?php //Accept input
function db_accept($table, $db_conn) {
    if (!empty($_REQUEST['accept'])) {
        echo '';
  ?>
        <form action="" method="post">
            <table class="lines">
                    <tr>
                        <th class="lines"><strong>Sample Name</strong></th>
                        <td class="lines"><input type="hidden" class="port" name="samplename"<?php if(!empty($db_conn)){echo 'value="'.$_POST['samplename'].'"/>'.$_POST['samplename'];}?></td> <!--sample name-->
                    </tr><tr>
                        <th class="lines"><strong>Sample description</strong></th>
                        <td class="lines"><input type="hidden" class="port" name="sampledesc"<?php if(!empty($db_conn)){echo 'value="'.$_POST['sampledesc'].'"/>'.$_POST['sampledesc'];}?></td> <!--sample desc -->
                    </tr><tr>
                        <th class="lines"><strong>Animal ID</strong></th>
                        <td class="lines"><input type="hidden" class="port" name="animalid"<?php if(!empty($db_conn)){echo 'value="'.$_POST['animalid'].'"/>'.$_POST['animalid'];}?></td>   <!--animalid-->
                    </tr><tr>
                        <th class="lines"><strong>Animal Description</strong></th>
                        <td class="lines"><input type="hidden" class="port" name="animaldesc"<?php if(!empty($db_conn)){echo 'value="'.$_POST['animaldesc'].'"/>'.$_POST['animaldesc'];}?></td> <!--animalid-->
                    </tr><tr>
                        <th class="lines"><strong>Organism</strong></th>
                        <td class="lines"><input type="hidden" class="port" name="organism"<?php if(!empty($db_conn)){echo 'value="'.$_POST['organism'].'"/>'.$_POST['organism'];}?></td>   <!--organism-->
                    </tr><tr>
                        <th class="lines"><strong>Organism Part</strong></th>
                        <td class="lines"><input type="hidden" class="port" name="part"<?php if(!empty($db_conn)){echo 'value="'.$_POST['part'].'"/>'.$_POST['part'];}?></td>   <!--part-->
                    </tr><tr>
                        <th class="lines"><strong>First Name</strong></th>
                        <td class="lines"><input type="hidden" class="port" name="firstname"<?php if(!empty($db_conn)){echo 'value="'.$_POST['firstname'].'"/>'.$_POST['firstname'];}?></td>    <!--first name-->
                    </tr><tr>
                        <th class="lines"><strong>Middle Initial</strong></th>
                        <td class="lines"><input type="hidden" class="port" name="middle"<?php if(!empty($db_conn)){echo 'value="'.$_POST['middle'].'"/>'.$_POST['middle'];}?></td> <!--middle-->
                    </tr><tr>
                        <th class="lines"><strong>Last Name</strong></th>
                        <td class="lines"><input type="hidden" class="port" name="lastname"<?php if(!empty($db_conn)){echo 'value="'.$_POST['lastname'].'"/>'.$_POST['lastname'];}?></td>   <!--last-->
                    </tr><tr>
                        <th class="lines"><strong>Organization</strong></th>
                        <td class="lines"><input type="hidden" class="port" name="organization"<?php if(!empty($db_conn)){echo 'value="'.$_POST['organization'].'"/>'.$_POST['organization'];}?></td>   <!--org-->
                    </tr>
                    <tr><td colspan="2"><center><input type="submit" name="reset" class="import" value="reject"/><input type="submit" name="verified" class="import" value="accept"/></center></td></tr>
                </table></form>
    <?php
    }
}
?>

<?php 
function meta_display($result) {
    $num_rows = $result->num_rows;
    echo '<br><table class="metadata"><tr>';
    echo '<th align="left" width=40pt bgcolor="white"><font size="2" color="red">Select All</font><input type="checkbox" id="selectall" onClick="selectAll(this)" /></th>';
    $meta = $result->fetch_field_direct(0); echo '<th class="metadata" id="' . $meta->name . '">Library Id</th>';
    $meta = $result->fetch_field_direct(1); echo '<th class="metadata" id="' . $meta->name . '">Animal Id</th>';
    $meta = $result->fetch_field_direct(2); echo '<th class="metadata" id="' . $meta->name . '">Organism</th>';
    $meta = $result->fetch_field_direct(3); echo '<th class="metadata" id="' . $meta->name . '">Tissue</th>';
    $meta = $result->fetch_field_direct(4); echo '<th class="metadata" id="' . $meta->name . '">Sample Description</th>';
    $meta = $result->fetch_field_direct(5); echo '<th class="metadata" id="' . $meta->name . '">Date</th>';
    $meta = $result->fetch_field_direct(6); echo '<th class="metadata" id="' . $meta->name . '">Gene Status</th>';
    $meta = $result->fetch_field_direct(7); echo '<th class="metadata" id="' . $meta->name . '">RawCount Status</th>';
    $meta = $result->fetch_field_direct(8); echo '<th class="metadata" id="' . $meta->name . '">Variant Status</th></tr>';

    for ($i = 0; $i < $num_rows; $i++) {
        if ($i % 2 == 0) {
            echo "<tr class=\"odd\">";
        } else {
            echo "<tr class=\"even\">";
        }
        $row = $result->fetch_assoc();
        echo '<td><input type="checkbox" name="meta_data[]" value="'.$row['library_id'].'"></td>';
        $j = 0;
        while ($j < $result->field_count) {
            $meta = $result->fetch_field_direct($j);
            if ($row[$meta->name] == "done"){
                echo '<td headers="' . $meta->name . '" class="metadata"><center><img src=".images/done.png" style="display:block;" width="20pt" height="20pt" ></center></td>';
            } else {
                echo '<td headers="' . $meta->name . '" class="metadata"><center>' . $row[$meta->name] . '</center></td>';
            }
            $j++;
        }
        echo "</tr>";
    }
    echo "</table></form>";
}
?>


<?php 
function metavw_display($result) {
    $num_rows = $result->num_rows;
    echo '<br><table class="metadata"><tr style="font-size:1.8vh;">';
    echo '<th align="left" width=40pt bgcolor="white"></th><th class="metadata" colspan=5>Analysis Summary</th><th class="metadata" colspan=3 style="color:#306269;">Mapping Metadata</th><th class="metadata" colspan=2 style="color:#306937;">Expression Metadata</th><th class="metadata" colspan=4 style="color:#693062;">Variant Metadata</th></tr><tr>';
    echo '<th align="left" width=40pt bgcolor="white"><font size="2" color="red">Select All</font><input type="checkbox" id="selectall" onClick="selectAll(this)" /></th>';
    $meta = $result->fetch_field_direct(0); echo '<th class="metadata" id="' . $meta->name . '">Library ID</th>';
    $meta = $result->fetch_field_direct(1); echo '<th class="metadata" id="' . $meta->name . '">Total Fastq reads</th>';
    $meta = $result->fetch_field_direct(2); echo '<th class="metadata" id="' . $meta->name . '">Alignment Rate</th>';
    $meta = $result->fetch_field_direct(3); echo '<th class="metadata" id="' . $meta->name . '">Genes</th>';
    $meta = $result->fetch_field_direct(4); echo '<th class="metadata" id="' . $meta->name . '">Variants</th>';
    $meta = $result->fetch_field_direct(5); echo '<th class="metadata" style="color:#306269;" id="' . $meta->name . '">Mapping Tool</th>';
    $meta = $result->fetch_field_direct(6); echo '<th class="metadata" style="color:#306269;" id="' . $meta->name . '">Annotation file format</th>';
    $meta = $result->fetch_field_direct(7); echo '<th class="metadata" style="color:#306269;" id="' . $meta->name . '">Date</th>';
    $meta = $result->fetch_field_direct(8); echo '<th class="metadata" style="color:#306937;" id="' . $meta->name . '">Differential Expression Tool</th>';
    $meta = $result->fetch_field_direct(9); echo '<th class="metadata" style="color:#306937;" id="' . $meta->name . '">Read Counts Tool</th>';
    $meta = $result->fetch_field_direct(10); echo '<th class="metadata" style="color:#306937;" id="' . $meta->name . '">Date</th>';
    $meta = $result->fetch_field_direct(11); echo '<th class="metadata" style="color:#693062;" id="' . $meta->name . '">Variant Tool</th>';
    $meta = $result->fetch_field_direct(12); echo '<th class="metadata" style="color:#693062;" id="' . $meta->name . '">Variant Annotation Tool</th>';
    $meta = $result->fetch_field_direct(13); echo '<th class="metadata" style="color:#693062;" id="' . $meta->name . '">Date</th>';
    

    for ($i = 0; $i < $num_rows; $i++) {
        if ($i % 2 == 0) {
            echo "<tr class=\"odd\">";
        } else {
            echo "<tr class=\"even\">";
        }
        $row = $result->fetch_assoc();
        echo '<td><input type="checkbox" name="meta_data[]" value="'.$row['library_id'].'"></td>';
        $j = 0;
        while ($j < $result->field_count) {
            $meta = $result->fetch_field_direct($j);
            if ($row[$meta->name] == "done"){
                echo '<td headers="' . $meta->name . '" class="metadata"><center><img src=".images/done.png" style="display:block;" width="10%" height="10%" ></center></td>';
            } else {
                echo '<td headers="' . $meta->name . '" class="metadata"><center>' . $row[$meta->name] . '</center></td>';
            }
            $j++;
        }
        echo "</tr>";
    }
    echo "</table></form>";
}
?>
<?php
function tabs_to_table($input) {
    //define replacement constants
    define('TAB_REPLACEMENT', "</center></td><td class='metadata'><center>");
    define('NEWLINE_BEGIN', "<tr%s><td class='metadata'><center>");
    define('NEWLINE_END', "</center></td></tr>");
    define('TABLE_BEGIN', "<table class='metadata'><tr><th class='metadata'>");
    define('TABLE_END', "</center></td></tr></table>");
    define('TAB_HEADER', "</th><th class='metadata'>");
    define('HEADER_END', "</th></tr>");

    //split the rows
    $rows = preg_split  ('/\n/'  , $input); $header = array_slice($rows,0,1); $rest = array_splice($rows,1);
    foreach ($header as $index => $row) {
        $row = preg_replace ('/\t/', TAB_HEADER , $row);
        $output = $row . HEADER_END;
    }      
    foreach ($rest as $index => $row) {
        $row = preg_replace  ('/\t/'  , TAB_REPLACEMENT  , $row);
        $output .= sprintf(NEWLINE_BEGIN, ($index%2?"":' class="odd"')) . $row . NEWLINE_END;
    }
    $input = TABLE_BEGIN. $output . "</table>";
    return ($input);
}
?>
