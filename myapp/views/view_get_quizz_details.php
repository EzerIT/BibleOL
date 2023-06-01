<?php
    // Load helper for grades
    // $this->load->helper('calc_grades_helper');

    // echo " <p>$userid</P>";
    // echo " <p>$quizzid</P>";
    //
    // print_r($resall);
    // load the language file for the translation of features
    // english is loaded as the default language if no language is set
    $language = $this->session->userdata('language') ?? 'en';
    $data = json_decode(file_get_contents('db/property_files/ETCBC4.' . $language . '.prop.pretty.json'), true);
// print_r($data);
// print_r($data['emdrosobject']['word']);
// var_dump($data->emdrosobject->word->g_voc_lex_utf8_variant);

// echo ($data['emdrosobject']['word']['g_voc_lex_utf8_variant'])

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
      <!-- <th><?= $this->lang->line('object_type') ?></th> -->
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
      <td class="text-center"><?= $ra->qono ?></td>
      <td class="text-center"><?= $ra->location ?></td>
      <td class="text-center"><?= $ra->txt ?></td>
      <!-- <td class="text-center"><?= $ra->disp_value ?></td>
      <td class="text-center"><?= $ra->disp_type ?></td> -->
      <td class="text-center"><table border="2px">
        <tr>
        <?php
        $header   = explode(",", $ra->disp_type) ;
        $cell_val = explode(",", $ra->disp_value) ;
        $table_row="";
        foreach ($header as $header_index => $header_value) {
          echo "<td class=\"text-center\">" . ($header_value=='item_number'?$this->lang->line('item_number'):($header_value=='visual'?$this->lang->line('visual'):$data['emdrosobject']['word'][$header_value]))  . "</td>";
          $table_row = $table_row  . '  <td class="text-center">' . $cell_val[$header_index] .'</td>';
        }
        echo "<tr>$table_row</tr>";
        ?>
      </tr>
    </table></td>
      <td class="text-center"><?= $ra->correct  ?></td>
      <td class="text-center"><?= $ra->value  ?></td>
      <td class="text-center"><?= $ra->answer  ?></td>
    </tr>

    <?php endforeach; ?>
  </table>
