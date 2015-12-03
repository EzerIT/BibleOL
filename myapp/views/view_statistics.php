<?php
  function stripSortIndex(string $s) {
      return (strlen($s)>0 && $s[0]==='#')
          ? substr(strchr($s, ' '),1)
          : $s;
  }
?>

<div class="statistics">
<?php $first = true; ?>
<?php foreach ($data as $d): ?>
  <?php if ($first) $first = false; else echo "<hr>\n"; ?>

  <h1><?= $this->lang->line('exercise_file_colon') ?> <?= substr(strrchr($d->pathname,'/'),1) ?></h1>  

  <table class="type2 table table-striped">
    <tr>
      <th><?= $this->lang->line('number_abbrev') ?></th>
      <th><?= $this->lang->line('time') ?></th>
      <th><?= $this->lang->line('duration') ?></th>
      <th><?= $this->lang->line('sec_per_correct') ?></th>
      <th><?= $this->lang->line('correct') ?></th>
      <th><?= $this->lang->line('wrong') ?></th>
    </tr>

    <?php foreach ($d->quizzes as $quiz): ?>
      <tr>
        <td><?= $quiz->id ?></td>
        <td><?= $quiz->Time ?></td>
        <td><?= sprintf("%2d:%02d",$quiz->Duration/60,$quiz->Duration%60) ?></td>
        <td><?= $quiz->Correct==0 ? "-" : sprintf("%.2f",$quiz->Duration/$quiz->Correct) ?></td>
        <td><?= $quiz->Correct ?></td>
        <td><?= $quiz->Wrong ?></td>
      </tr>
    <?php endforeach; ?>
  </table>

  <p></p>

  <table class="type3 table table-striped">
    <tr>
      <th><?= $this->lang->line('number_abbrev') ?></th>
      <th><?= $this->lang->line('feature') ?></th>
      <th><?= $this->lang->line('correct_answer') ?></th>
      <th><?= $this->lang->line('mistakes') ?></th>
    </tr>

    <?php foreach ($d->req_features as $rf): ?>
      <tr>
        <td><?= $rf->id ?></td>
        <td><?= $d->l10n->emdrosobject->{$d->qoname}->{$rf->name} ?></td>

        <?php
           if (isset($d->obj2feat->{$d->qoname}->{$rf->name}))
               $featureType = $d->obj2feat->{$d->qoname}->{$rf->name};
           else
               $featureType = 'string'; // This feature name is presumably 'visual'

           if (isset($d->l10n->emdrostype->$featureType->{$rf->value}))
               $featureValue = $d->l10n->emdrostype->$featureType->{$rf->value};
           else
               $featureValue = $rf->value;
        ?>

        <td><?= stripSortIndex($featureValue) ?></td>
        <td><?= $rf->cnt ?></td>
      </tr>
    <?php endforeach; ?>
  </table>
 
<?php endforeach; ?>
</div>
