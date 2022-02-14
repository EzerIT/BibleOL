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
	<form action="/exams/save_exam" method="post" id="edit_exam_form">
		<h3>Editing exam:
			<?php
				# Display exam name.
				echo str_replace("%2B", "+", $xml->examname);
				#print_r($xml);
			?>
		</h3>

		<input name="id" value="<?= $exam ?>" type="hidden">

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
						echo str_replace("%2B", "+", $x->exercisename);
					?>
				</h5>
				<?php
					# Store the exercise in the form of an associative array.
					$array = json_decode(json_encode((array) $x), TRUE);
				?>

				<div id="feature">
					<br>
					<input name="<?= $x->exercisename . 'numq'; ?>" value="<?= $x->numq ?>" min="1" step="1" type="number">
					Number of questions
				</div>

				<div id="feature">
					<br>
					<input name="<?= $x->exercisename . 'weight'; ?>" value="<?= $x->weight ?>" min="1" step="1" type="number">
					Weight
				</div>

			</div>
		<?php endforeach; ?>

		<br>
		<input type="submit" value="Save" name="submit" class="btn btn-primary">
	</form>
</div>
