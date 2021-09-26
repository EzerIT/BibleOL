<?php
# Store the path to the active exam.
$full_name = "/var/www/BibleOL/exam/" . $exam;
# Load the config file into a variable.
$xml = simplexml_load_file($full_name . "/config.xml") or die("error");

# Declare the array that will store the
# names of the exercises that make up this exam.
$exercise_list = array();
# Declare the array that will store the
# values of the different features for
# the exam.
$feature_values = array();

# Store the proper format for HTML datetime-local.
$datetime_format = "/^[0-9]{4}-[0-9]{2}-[0-9]{2}T[0-9]{2}:[0-9]{2}$/";

# Execute once form has been submitted.
# In this case it would be when the changes
# are saved.
# NEED FIX
# This section should be taken care of in
# a separate file (exams/save_exam.php).
if(isset($_POST["submit"])) {
	# Store all exam feature values in an array.
	foreach($_POST as $key => $value){
		$feature_values[] = $value;
	}

	# Store description.
	$xml->description = trim(array_shift($feature_values));

	# Iterate through the exercises in the exam.
	foreach ($xml->exercise as $x){
		# Store the exercise in the form of an associative array.
		$array = json_decode(json_encode((array) $x), TRUE);
		# Iterate through the features of the exercise.
		foreach ($array as $key => $value){
			# If the current feature is not exercisename.
			if($key != "exercisename"){
				# then remove the next feature value in the array
				# and store it.
				$removed = array_shift($feature_values);
				# Assign the stored feature value to the current feature.
				$x->$key = $removed;
			}
		}
	}

	# Rewrite the config file with the updated version.
	$xml->asXML($full_name . "/config.xml");

	$data = array(
		'exam_name' => $xml->examname,
		'ownerid' => $xml->teacher_id,
		'pathname' => 'exam/' . $xml->examname,
		'examcode' => $xml->asXML(),
		'examcodehash' => hash("md5", $xml)
	);

	$query = $this->db->query('SELECT id FROM bol_exam WHERE exam_name="'.$xml->examname.'"');
	if ($query->result()){
		$this->db->set($data);
		$this->db->where('exam_name', $xml->examname);
		$this->db->update('bol_exam');
	}
	else {
		$this->db->insert('bol_exam', $data);
	}

	redirect("/exams");

}

?>


<head>
<style>

#exercise {
	margin-top: 20px;
	border-top: dashed 1px #007bff;
}
</style>
</head>


<!--
Display exam editing form.
-->
<div>
	<form action="" method="post" id="edit_exam_form">
		<h3>Editing exam:
			<?php
				# Display exam name.
				echo $exam;
				#print_r($xml);
			?>
		</h3>

		<br>

		<h5>Exam Description</h5>
		<div id="feature">
			<textarea id="txtdesc" name="description" style="width:100%; height:100px" wrap="hard" form="edit_exam_form"><?php echo $xml->description; ?></textarea>
		</div>

		<br>

		<?php
			# Iterate through each exercise in the exam.
			#print_r($xml);
			foreach ($xml->exercise as $x):
		?>
			<div id="exercise">
				<h5>
					<?php
						# Display the name of the current exercise.
						echo $x->exercisename;
					?>
				</h5>
				<?php
					# Store the exercise in the form of an associative array.
					$array = json_decode(json_encode((array) $x), TRUE);
				?>

				<div id="feature">
					<br>
					<input name="<?= $x->exercisename . 'numq'; ?>" value="<?= $x->numq ?>" min="0" step="1" type="number">
					Number of questions
				</div>

				<div id="feature">
					<br>
					<input name="<?= $x->exercisename . 'weight'; ?>" value="<?= $x->weight ?>" min="0" step="1" type="number">
					Weight
				</div>

			</div>
		<?php endforeach; ?>

		<br>
		<input type="submit" value="Save" name="submit" class="btn btn-primary">
	</form>
</div>
