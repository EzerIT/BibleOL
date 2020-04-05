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
	$valid_start = FALSE;
	$valid_end = FALSE;
	$dates_found = 0;
	# Iterate through the different form elements
	# that are submitted.
	foreach ($_POST as $key => $value){
		# Check if the value is in the HTML datetime-local format.
		if (preg_match($datetime_format, $value)){
			if (!($dates_found)){
				if (new DateTime('NOW') < new DateTime($value)){
					$dates_found = $value;
					$valid_start = TRUE;
				}
			}
			elseif (strtotime($dates_found)){
				if (new DateTime($dates_found) < new DateTime($value)){
					$valid_end = TRUE;
				}
			}
			# Change the value from datetime-local format to SQL datetime format.
			$value = str_replace("T", " ", $value) . ":00";
		}
		# Append the value of the current form element
		# to the $feature_values array.
		$feature_values[] = $value;
	}



	# Set new plan_start
	$xml->plan_start = array_shift($feature_values);
	# Set new plan_end
	$xml->plan_end = array_shift($feature_values);

	print_r($xml->plan_start);
	echo '<script>alert("Stop!");</script>';
	echo $xml->plan_end;
	echo '<script>alert("Stop!");</script>';

	if ($valid_start && $valid_end){
		# Set new max_time
		$xml->time = array_shift($feature_values);

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
			<textarea id="txtdesc" style="width:100%; height:100px" wrap="hard" form="edit_exam_form">
				Coming soon.
				<?php //echo $xml->description, PHP_EOL; ?>
			</textarea>
		</div>

		<div id="feature">
			<?php
			 	# Get current planned end.
				$p_s = $xml->plan_start;
				# Put planned end in datetime-local format.
				$p_start = substr($p_s, 0, 10) . "T" . substr($p_s, 11, 5);
			?>
			<input name="plan_start" value="<?php echo $p_start; ?>" type="datetime-local">
			<?php
				echo $this->lang->line('planned_start');
			?>
		</div>

		<br>

		<div id="feature">
			<?php
			 	# Get current planned end.
				$p_e = $xml->plan_end;
				# Put planned end in datetime-local format.
				$p_end = substr($p_e, 0, 10) . "T" . substr($p_e, 11, 5);
			?>
			<input name="plan_end" value="<?php echo $p_end; ?>" type="datetime-local">
			<?php
				echo $this->lang->line('planned_end');
			?>
		</div>

		<br>

		<div id="feature">
			<input name="exam_time" value="<?php echo $xml->time; ?>" type="number" min="0">
			<?php
				echo $this->lang->line('time');
			?>
		</div>


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
				<?php
				 	# Iterate through the exercise features.
					#print_r($array);
					foreach ($array as $key => $value):
				?>
					<?php
						# Check that the current feature is not exercisename.
						if($key != "exercisename"):
							# Set the name of the input field to [exercisename][featurename].
							# For example, if the current exercise is testex
							# and the current feature is numq the field name
							# would be testexnumq.
					?>
						<div id="feature">
							<?php
								echo "<br>";
							?>
							<input name="<?php echo $x->exercisename . $key; ?>" value="<?php echo $value; ?>" min="0" step="1" type="number">
							<?php echo $key; ?>
						</div>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>
		<?php endforeach; ?>

		<br>
		<input type="submit" value="Save" name="submit" class="btn btn-primary">
	</form>
</div>
