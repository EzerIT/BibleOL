<?php
    // Load helper for grades
    // $this->load->helper('calc_grades_helper');

    // echo " <p>$userid</P>";
    // echo " <p>$quizzid</P>";
    //
    // print_r($resall);
?>
<div class="table-responsive" id="table1" style="display:block">
  <h2><?= $this->lang->line('exercise_detail') ?></h2>
  <table class="type2 table table-striped autowidth">
    <caption><?= $this->lang->line('grds_exam_by_student_caption') ?></caption>
    <tr>
      <th><?= $this->lang->line('feat_location') ?></th>
      <th class="text-center"><?= $this->lang->line('right_wrong') ?></th>
      <th class="text-center"><?= $this->lang->line('correct_anser') ?></th>
      <th class="text-center"><?= $this->lang->line('answer_by_stud') ?></th>
      <th></th>
    </tr>
    <?php $hiddenStyles = array();
    foreach ($resall as $ra): ?>
      <?PHP // Just in case of us showing multiple exercises on thefuture
      ?>
    <tr class="<?php echo 'headerDet';  ?>">
      <td class="text-center"><?= $ra->location ?></td>
      <td class="text-center"><?= $ra->correct  ?></td>
      <td class="text-center"><?= $ra->value  ?></td>
      <td class="text-center"><?= $ra->answer  ?></td>
    </tr>

    <?php endforeach; ?>
  </table>
