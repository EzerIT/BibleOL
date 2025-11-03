<?php
    // Load helper for grades
    // $this->load->helper('calc_grades_helper');
    //
    // load the language file for the translation of features
    // english is loaded as the default language if no language is set
    $language = $this->session->userdata('language') ?? 'en';
    $data = json_decode(file_get_contents('db/property_files/ETCBC4.' . $language . '.prop.pretty.json'), true);

?>
<p><a href="javascript:history.back()" title="<?= $this->lang->line('back_hooverover_txt') ?>"><?= $this->lang->line('go_back') ?></a></p>
<div class="table-responsive" id="table1" style="display:block">
  <h2><?= $this->lang->line('exercise_detail') ?></h2>
  <table class="type2 table table-striped autowidth">
    <caption><?= $this->lang->line('exercise_detail') ?></caption>
    <tr>
      <th><?= $this->lang->line('qest_numbr') ?></th>
      <th><?= $this->lang->line('feat_location') ?></th>
      <th><?= $this->lang->line('txt') ?></th>
      <th><?= $this->lang->line('question_object') ?></th>
      <!-- <th><?= $this->lang->line('hint') ?></th> -->
      <th class="text-center"><?= $this->lang->line('feat_requst') ?></th>
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
      <td class="text-center"><?= $ra->qono . "." . $ra->subqono ?></td>
      <td class="text-center"><?= $ra->location ?></td>
      <td class="text-center"><?= $ra->txt ?></td>
      <!-- <td class="text-center"><?= $ra->disp_value ?></td>
      <td class="text-center"><?= $ra->disp_type ?></td> -->
      <td class="text-center">
          <?= explode(',',$ra->disp_value)[array_search("hint", explode(',',$ra->disp_type))] ?>
      </td>
      <td class="text-center"><?= $ra->name  ?></td>
      <td class="text-center"><?= $ra->correct  ?></td>
      <td class="text-center"><?= $ra->value  ?></td>
      <td class="text-center"><?= $ra->answer  ?></td>
    </tr>

    <?php endforeach; ?>
  </table>
