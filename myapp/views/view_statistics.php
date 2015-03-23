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

  <h1>Exercise file: <?= substr(strrchr($d->pathname,'/'),1) ?></h1>  

  <table class="striped">
    <tr><th>No.</th><th>Time</th><th>Duration</th><th>Seconds per correct</th><th>Correct</th><th>Wrong</th></tr>

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

  <table class="striped">
    <tr><th>No.</th><th>Feature</th><th>Correct answer</th><th>Mistakes</th></tr>

    <?php foreach ($d->req_features as $rf): ?>
      <tr>
        <td><?= $rf->id ?></td>
        <td><?= $d->localization->emdrosobject->{$d->qoname}->{$rf->name} ?></td>

        <?php
           if (isset($d->obj2feat->{$d->qoname}->{$rf->name}))
               $featureType = $d->obj2feat->{$d->qoname}->{$rf->name};
           else
               $featureType = 'string'; // This feature name is presumably 'visual'

           if (isset($d->localization->emdrostype->$featureType->{$rf->value}))
               $featureValue = $d->localization->emdrostype->$featureType->{$rf->value};
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
