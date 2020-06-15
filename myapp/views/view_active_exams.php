<!--
This page is the home page for exams.
It has a list of all the created exams and
professors can edit exams or choose to create
a new exam. In each case a the professor is
redirected to the appropriate page.
-->

<style>
/*
Styling for the page.
This is used to fine tune individual CSS
style characteristics for individual elements
on the page.
*/
.editButton{
  justify-content: center;
  cursor: pointer;

  background-color: white;
  color: #007bff;

  padding: 8px;
  border-radius: 8px;
}
</style>


<h1><?= sprintf($this->lang->line('number_of_exams'), $exam_count) ?></h1>
<h2><?= sprintf($this->lang->line('exams_per_page'), $exams_per_page) ?></h2>
<nav>
    <ul class="pagination">
        <?php for ($p=0; $p<$page_count; ++$p): ?>
            <li class="<?= $p==$offset ? 'active' : '' ?> page-item">
                <a class="page-link" href="<?= site_url("exams?offset=$p&orderby=$orderby&$sortorder") ?>">
                    <?= $p+1 ?>
                </a>
            </li>
        <?php endfor; ?>
    </ul>
</nav>

<?php
    function make_exam_header($me, $label, $field, $sortorder, $orderby){
      if ($orderby===$field) {
          $link_sortorder = $sortorder == 'desc' ? 'asc' : 'desc';
          $arrow = ' <span class="fas fa-caret-' . ($sortorder=='desc' ? 'down' : 'up') . '" aria-hidden="true">';
      }
      else {
        $link_sortorder = 'asc';

        // FIX ARROW
        // NOT SORTING
        $arrow = '';
      }

      return '<th style="white-space"><a href="' . site_url("exams?offset=0&orderby=$field&$link_sortorder") . '">' . $me->lang->line($label) . $arrow . "</a></th>\n";
    }
?>


<div class="table-responsive">
<table class="type2 table table-striped">
    <tr>
        <?= make_exam_header($this, 'exam_name', 'examname', $sortorder, $orderby) ?>
        <?= make_exam_header($this, 'owner', 'ownerid', $sortorder, $orderby) ?>
        <th><?= $this->lang->line('user_operations') ?></th>
    </tr>
    <?php foreach ($allexams as $exam): ?>
        <tr>
            <td class="leftalign"><?= $exam->exam_name ?></td>
            <td class="leftalign"><?= $exam->ownerid ?></td>
            <td class="leftalign">
                <a class="badge badge-primary" href="/exams/edit_exam?exam=<?= $exam->exam_name ?>"><?= $this->lang->line('edit_exam') ?></a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
</div>


<!--
This button redirects to the exam creation page.
-->
<div>
  <a class="btn btn-primary" href="/exams/new_exam" onclick="create_new_exam()"><?= $this->lang->line('create_new_exam_button') ?></a>
</div>
