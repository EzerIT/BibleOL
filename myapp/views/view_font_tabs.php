<div id="font_tabs">
  <ul>
     <?php foreach ($alphabets as $alph): ?>
       <li><a href="#tab_<?= $alph ?>"><?= $alph ?></a></li>
     <?php endforeach; ?>
  </ul>
  <?php foreach ($alphabets as $alph): ?>
      <?= $html[$alph] ?>
  <?php endforeach; ?>
</div>
 <script>
  $(function() {
    $("#font_tabs").tabs();
  });
  </script>