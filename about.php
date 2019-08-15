<?php				
	session_start();
	require_once('all_fns.php');
	tmetadata(); 
	if (empty($_GET['quest'])) { $_GET['quest'] = ""; }
?>
	<div class="menu">TransAtlasDB Summary</div>
	<table width=80% ><tr><td valign="top" width=280pt>
<?php
	//create query for DB display
	if ($_GET['quest'] == 'samples') { //if samples
?>
	<div class="metamenu"><a href="about.php">Organisms</a></div>
	<div class="metamenu"><a href="about.php?quest=animal">Animals</a></div>
	<div class="metactive"><a href="about.php?quest=samples">Samples</a></div>
	<div class="metamenu"><a href="about.php?quest=samplesprocessed">Samples Processed</a></div>
	<div class="metamenu"><a href="about.php?quest=database">Database content</a></div>
	</td><td valign="top">
	<?php
		$result = $db_conn->query("select species, tissue, count(*) Count from bird_libraries where species='gallus' group by species, tissue");
		$result2 = "null";
		echo '<div class="dift"><p>Summary of Samples.</p>';
		about_display($result, $result2);
	} elseif ($_GET['quest'] == 'samplesprocessed') { // if samplesprocessed
?>
	<div class="metamenu"><a href="about.php">Organisms</a></div>
	<div class="metamenu"><a href="about.php?quest=animal">Animals</a></div>
	<div class="metamenu"><a href="about.php?quest=samples">Samples</a></div>
	<div class="metactive"><a href="about.php?quest=samplesprocessed">Samples Processed</a></div>
	<div class="metamenu"><a href="about.php?quest=database">Database content</a></div>
	</td><td valign="top">
	<?php
		$result = $db_conn->query("select a.species Organism, format(count(a.library_id),0) Recorded, format(count(c.library_id),0) Processed , format(count(d.library_id),0) Genes, format(count(e.library_id),0) Variants from bird_libraries a left outer join vw_sampleinfo c on a.library_id = c.library_id left outer join GeneStats d on c.library_id = d.library_id left outer join VarSummary e on c.library_id = e.library_id where a.species = 'gallus' group by a.species");
		$result2 = $db_conn->query("select format(count(a.library_id),0), format(count(c.library_id),0), format(count(d.library_id),0), format(count(e.library_id),0) from bird_libraries a left outer join vw_sampleinfo c on a.library_id = c.library_id left outer join GeneStats d on c.library_id = d.library_id left outer join VarSummary e on c.library_id = e.library_id where a.species='gallus'"); #FINAL ROW
		echo '<div class="dift"><p>Summary of Samples processed.</p>';
		about_display($result, $result2);
	} elseif ($_GET['quest'] == 'database') { //if database
?>
	<div class="metamenu"><a href="about.php">Organisms</a></div>
	<div class="metamenu"><a href="about.php?quest=animal">Animals</a></div>
	<div class="metamenu"><a href="about.php?quest=samples">Samples</a></div>
	<div class="metamenu"><a href="about.php?quest=samplesprocessed">Samples Processed</a></div>
	<div class="metactive"><a href="about.php?quest=database">Database content</a></div>
	</td><td valign="top">
	<?php
		$result = $db_conn->query("select species Organism, format(sum(genes),0) Genes, format(sum(totalvariants),0) Variants from vw_sampleinfo group by species");
		$result2 = $db_conn->query("select format(sum(genes),0) Genes, format(sum(totalvariants ),0) Variants from vw_sampleinfo"); #FINAL ROW
		echo '<div class="dift"><p>Summary of Database Content.</p>';
		about_display($result, $result2);
	} elseif ($_GET['quest'] == 'animal') { //if animal
?>
	<div class="metamenu"><a href="about.php">Organisms</a></div>
	<div class="metactive"><a href="about.php?quest=animal">Animals</a></div>
	<div class="metamenu"><a href="about.php?quest=samples">Samples</a></div>
	<div class="metamenu"><a href="about.php?quest=samplesprocessed">Samples Processed</a></div>
	<div class="metamenu"><a href="about.php?quest=database">Database content</a></div>
	</td><td valign="top">
	<?php
		$result = $db_conn->query("select species Organism, bird_id Bird_ID, format(count(library_id),0) Library_ID from bird_libraries where species='gallus' group by species, bird_id");
		$result2 = $db_conn->query("select format(count(distinct bird_id),0) Bird_ID, format(count(distinct library_id),0) Library_ID from bird_libraries where species='gallus' group by species"); #FINAL ROW
		echo '<div class="dift"><p>Summary of Animals .</p>';
		about_display($result, $result2);
	} else { //if organisms
?>
	<div class="metactive"><a href="about.php">Organisms</a></div>
	<div class="metamenu"><a href="about.php?quest=animal">Animals</a></div>
	<div class="metamenu"><a href="about.php?quest=samples">Samples</a></div>
	<div class="metamenu"><a href="about.php?quest=samplesprocessed">Samples Processed</a></div>
	<div class="metamenu"><a href="about.php?quest=database">Database content</a></div>
	</td><td valign="top">
	<?php
		$result = $db_conn->query("select species Organism, count(*) as Count from bird_libraries where species='gallus' group by species");
		$result2 = $db_conn->query("select count(*) from bird_libraries"); #FINAL ROW
		echo '<div class="dift"><p>Summary of Organisms.</p>';
		about_display($result, $result2);
	}

	if ($db_conn->errno) {
		echo "<div>";
		echo "<span><strong>Error with query.</strong></span>";
		echo "<span><strong>Error number: </strong>$db_conn->errno</span>";
		echo "<span><strong>Error string: </strong>$db_conn->error</span>";
		echo "</div>";
	}
?>
<!-- QUERY -->

</div>
</td></tr>
</table>
  </div>
<?php
  $db_conn->close();
?>

</body>
</html>
