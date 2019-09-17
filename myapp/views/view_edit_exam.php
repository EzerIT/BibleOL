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
if(isset($_POST["submit"])) {
	# Iterate through the different form elements
	# that are submitted.
	foreach ($_POST as $key => $value){
		# Check if the value is in the HTML datetime-local format.
		if (preg_match($datetime_format, $value)){
			# Change the value from datetime-local format to SQL datetime format.
			$value = str_replace("T", " ", $value) . ":00";
		}
		# Append the value of the current form element
		# to the $feature_values array.
		$feature_values[] = $value;

	}

	# Iterate through the exercises in the exam.
	foreach ($xml->exercise as $x){
		# Store the exercise in the form of an associative array.
		$array = json_decode(json_encode((array) $x), TRUE);
		# Iterate through the features of the exercise.
		foreach ($array as $key => $value){
			# If the current feature is not exercisename
			if($key != "exercisename"){
				# then remove the first feature value in the array
				# and store it.
				$removed = array_shift($feature_values);
				# Assign the stored feature value to the current feature.
				$x->$key = $removed;
			}
		}
	}

	# Rewrite the config file with the updated version.
	$xml->asXML($full_name . "/config.xml");

	redirect("/exams");
}

?>


<head>
<style>

#exercise {
	margin-top: 20px;
	border-bottom: solid 1px black;
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
			?>
		</h3>
		<br>
		<h5>Exam Description</h5>
		<textarea id="txtdesc" style="width:100%; height:100px" wrap="hard" form="edit_exam_form">
			Coming soon.
			<?php //echo $xml->description, PHP_EOL; ?>
		</textarea>
		<?php
			# Iterate through each exercise in the exam.
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
				<?php
				 	# Iterate through the exercise features.
					foreach ($array as $key => $value):
				?>
					<?php
						# Check if current feature is plan_start or plan_end.
						if($key == "plan_start" or $key == "plan_end"):
					?>
						<div id="feature">
							<input name="
								<?php
									# Set the name of the input field to [exercisename][featurename].
									# For example, if the current exercise is testex
									# and the current feature is numq the field name
									# would be testexnumq.
									echo $x->exercisename . $key;
								?>
							" value="<?php echo substr($value, 0, 10) . "T" . substr($value, 11, 5); ?>" type="datetime-local">
							<?php
								# Display the name of the current feature.
								echo $key
							?>
						</div>
					<?php
						# Check that the current feature is not exercisename.
						elseif($key != "exercisename"):
					?>
						<div id="feature">
							<input name="
								<?php
									# Set the name of the input field to [exercisename][featurename].
									# For example, if the current exercise is testex
									# and the current feature is numq the field name
									# would be testexnumq.
									echo $x->exercisename . $key;
								?>
							" value="<?php echo $value; ?>" min="0" step="1" type="number">
							<?php
								# Display the name of the current feature.
								echo $key;
							?>
						</div>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>
		<?php endforeach; ?>
		
		<input type="submit" value="Save" name="submit" class="btn btn-primary">
	</form>
</div>
