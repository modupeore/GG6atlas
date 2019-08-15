<?php				
	session_start();
	require_once('all_fns.php');
	if (empty($_GET['quest'])) { $_GET['quest'] = ""; }
	if (empty($tissues)) {$tissues = "";}
	if(empty($_POST['tissue'])) {$_POST['tissue'] = ""; }
	if(empty($_POST['genexp'])) {$_POST['genexp'] = ""; }
?>
<?PHP
	//Database Attributes
	$table = "vw_sampleinfo";
	@$species=$_GET['organism'];
	
	if ($_GET['quest'] == 'fragments') {
		tfragment(); 
?>
		<div class="menu">TransAtlasDB Samples - Expression Information</div>
			<table width=80%><tr><td width="280pt">
			<div class="metamenu"><a href="expression.php">Gene Expression Summary</a></div>
			<div class="metactive"><a href="expression.php?quest=fragments">Samples-Gene Expression</a></div>
			<br><br><br></td><td>
			<div class="dift">
				<p>View expression (FPKM) summaries of samples and genes.<br>
				This provides a tab-delimited ".txt" file to easily compare the genes FPKM values across different samples.</p>
<?php
		if(isset($species)){
			$query="SELECT library_id FROM $table where species='$species' order by library_id"; 
		}else{ $query ="SELECT library_id FROM $table where species is null order by tissue"; }
		
		if (!empty($_REQUEST['salute'])) {
			$_SESSION[$table]['tissue'] = $_POST['tissue'];
			$_SESSION[$table]['organism'] = $_POST['organism'];
			$_SESSION[$table]['search'] = $_POST['search'];
			$_SESSION[$table]['criteria'] = $_POST['criteria'];
			$_SESSION[$table]['genexp'] = $_POST['genexp'];
		}		
?>
<div class="question">
  <form action="" method="post">
    <p class="pages"><span>Select Organism: </span>
    <select name="organism" onchange="reload(this.form)">
		<option value="" selected disabled >Select Organism</option>
		<?php
			foreach ($db_conn->query("select distinct species from $table") as $row) {
				if($row["species"]==@$species){ echo "<option selected value='$row[species]'>$row[species]</option><br>"; }
				else { echo '<option value="'.$row['species'].'">'. $row['species'].'</option>';}
			}
		?>
	</select></p>

       <p class="pages"><span>Search based on: </span>
        <select name="criteria" id="criteria">
                <option value='--gene'>GeneName</option>
                <option value='--ensembl'>Ensembl IDs</option>
                </select></p>

	<p class="pages"><span>Specify your gene name / ensembl IDs: </span>
	<?php
		if (!empty($_SESSION[$table]['search'])) {
		  echo '<input type="text" name="search" id="genename" size="35" value="' .$_SESSION[$table]['search']. '"/></p>';
		} else {
		  echo '<input type="text" name="search" id="genename" size="35" placeholder="Enter Gene Name(s)" /></p>';
		}
	?>
	
	<p class="pages"><span>Samples of interest: </span>
	<select name="tissue[]" id="tissue" size=3 multiple="multiple">
		<option value="" selected disabled >Select Sample(s)</option>
		<?php
			foreach ($db_conn->query($query) as $row) {
				echo "<option value='$row[library_id]'>$row[library_id]</option>";
			}
		?>
		</select></p>
	
	<p class="pages"><span>Expression Values: </span>
	<select name="genexp" id="genexp">
		<option value="" selected disabled >Select Expression Values</option>
		<option value='-fpkm'>FPKM</option>
		<option value='-tpm'>TPM</option>
		</select></p>
	
<center><input type="submit" name="salute" value="View Results"></center>
</form>
</div>
  </td></tr></table>

<?php
		if (!empty($_POST['salute'])) {
			echo '<div class="menu">Results</div><div class="xtra">';
			$queryforoutput = "yes";
			$output = "genexp_".$explodedate.".txt";
			if (!empty($_POST['tissue'])) { foreach ($_POST["tissue"] as $tissue){ $tissues .= $tissue. ","; } $tissues = rtrim($tissues,","); }
			if (!empty($_POST['search'])) { $genenames = rtrim($_POST['search'],","); }
			
			if ((!empty($_POST['tissue'])) && (!empty($_POST['organism'])) && (!empty($_POST['search']))) {          
				$pquery = "perl jedimaster.pl -genexp $_POST[genexp] -species '$_POST[organism]' $_POST[criteria] '".strtoupper("$genenames")."' --samples '$tissues' -o $output";
			} elseif ((!empty($_POST['tissue'])) && (!empty($_POST['organism']))) {          
				$pquery = "perl jedimaster.pl -genexp $_POST[genexp] -species '$_POST[organism]' --samples '$tissues' -o $output";
			} elseif ((!empty($_POST['search'])) && (!empty($_POST['organism']))) {          
				$pquery = "perl jedimaster.pl -genexp $_POST[genexp] -species '$_POST[organism]' $_POST[criteria] '".strtoupper("$genenames")."' -o $output";
			} elseif (!empty($_POST['organism'])) {          
				$pquery = "perl jedimaster.pl -genexp $_POST[genexp] -species '$_POST[organism]' -o $output";
			}else {
				$queryforoutput = "no";
				echo "<center>Forgot something ?</center>";
			}
			//print $pquery;
			if ($queryforoutput == "yes") {
				$values = strtoupper(ltrim($_POST[genexp],"-")); 
				print "Viewing the $values results";
				shell_exec($pquery);
				if (file_exists("OUTPUT/$output")){
					echo '<form action="" method="post">';
					echo '<p class="gened">Download the results below. ';
					$newbrowser = "results.php?file=OUTPUT/$output&name=genes-stats.txt";
					echo '<input type="button" class="browser" value="Download Results" onclick="window.open(\''. $newbrowser .'\')"></p>';
					echo '</form>';
		
					// Get Tab delimted text from file
					$handle = fopen("OUTPUT/$output", "r");
					$contents = fread($handle, filesize("OUTPUT/$output")-1);
					fclose($handle);
					// Start building the HTML file
					print(tabs_to_table($contents));
				} else {
					echo '<center>No result based on search criteria.</center>';
				}
			}
		}
?>
  </div>
<?php
	} else {
		texpression(); 
?>
		<div class="menu">TransAtlasDB Expression Information</div>
			<table width=80%><tr><td width="280pt">
			<div class="metactive"><a href="expression.php">Gene Expression Summary</a></div>
			<div class="metamenu"><a href="expression.php?quest=fragments">Samples-Gene Expression</a></div>
			<br><br></td><td>
			<div class="dift"><p> View expression (FPKM) summaries of specified genes.</p>
<?php

		if(isset($species)){
			$query="SELECT DISTINCT tissue FROM $table where species='$species' order by tissue"; 
		}else{ $query ="SELECT DISTINCT tissue FROM $table where species is null order by tissue"; }
	
		if (!empty($_REQUEST['salute'])) {
			$_SESSION[$table]['tissue'] = $_POST['tissue'];
			$_SESSION[$table]['organism'] = $_POST['organism'];
			$_SESSION[$table]['search'] = $_POST['search'];
			$_SESSION[$table]['criteria'] = $_POST['criteria'];
		}
?>

  <div class="question">
  <form action="" method="post">
    <p class="pages"><span>Select Organism: </span>
    <select name="organism" onchange="reload(this.form)">
		<option value="" selected disabled >Select Organism</option>
		<?php
			foreach ($db_conn->query("select distinct species from $table") as $row) {
				if($row["species"]==@$species){ echo "<option selected value='$row[species]'>$row[species]</option><br>"; }
				else { echo '<option value="'.$row['species'].'">'. $row['species'].'</option>';}
			}
		?>
	</select></p>

     <p class="pages"><span>Search based on: </span>
       <select name="criteria" id="criteria">
         <option value='--gene'>GeneName</option>
         <option value='--ensembl'>Ensembl IDs</option>
       </select></p>

    <p class="pages"><span>Specify your gene name: </span>
	<?php
		if (!empty($_SESSION[$table]['search'])) {
		  echo '<input type="text" name="search" id="genename" size="35" value="' .$_SESSION[$table]['search']. '"/></p>';
		} else {
		  echo '<input type="text" name="search" id="genename" size="35" placeholder="Enter Gene Name(s)" /></p>';
		}
	?>
	
	<p class="pages"><span>Tissue(s) of interest: </span>
	<select name="tissue[]" id="tissue" size=3 multiple="multiple">
		<option value="" selected disabled >Select Tissue(s)</option>
		<?php
			foreach ($db_conn->query($query) as $row) {
				echo "<option value='$row[tissue]'>$row[tissue]</option>";
			}
		?>
		</select></p>
	
	<p class="pages"><span>Expression Values: </span>
	<select name="genexp" id="genexp">
		<option value="" selected disabled >Select Expression Values</option>
		<option value='-fpkm'>FPKM</option>
		<option value='-tpm'>TPM</option>
		</select></p>
<center><input type="submit" name="salute" value="View Results"></center>
</form>
</div>
  </td></tr></table>

<?php

	if (!empty($_POST['salute'])) {
		echo '<div class="menu">Results</div><div class="xtra">';
		if ((!empty($_POST['tissue'])) && (!empty($_POST['organism'])) && (!empty($_POST['search']))) {          
			$output = "avgexp_".$explodedate.".txt";
			foreach ($_POST["tissue"] as $tissue){ $tissues .= $tissue. ","; } $tissues = rtrim($tissues,",");
			$genenames = rtrim($_POST['search'],",");
			$pquery = "perl jedimaster.pl -avgexp $_POST[genexp] -species '$_POST[organism]' $_POST[criteria] '".strtoupper("$genenames")."' --tissue '$tissues' -o $output";
			//print $pquery;
			shell_exec($pquery);
			if (file_exists("OUTPUT/$output")){
				echo '<form action="" method="post">';
				echo '<p class="gened">Download the results below. ';
				$newbrowser = "results.php?file=OUTPUT/$output&name=genes-stats.txt";
				echo '<input type="button" class="browser" value="Download Results" onclick="window.open(\''. $newbrowser .'\')"></p>';
				echo '</form>';
			
				// Get Tab delimted text from file
				$handle = fopen("OUTPUT/$output", "r");
				$contents = fread($handle, filesize("OUTPUT/$output")-1);
				fclose($handle);
				// Start building the HTML file
				print(tabs_to_table($contents));
			} else {
				echo '<center>No result based on search criteria.</center>';
			}
		} else {
			echo "<center>Forgot something ?</center>";
		}
	}
?>
  </div>
<?php
}
  $db_conn->close();
?>
</body>
</html>
