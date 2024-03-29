<?php
   function add_stat(string $file) {
       $split_file = explode('?', $file);
       $file_stat = stat($split_file[0]); // We need this to modify the href attribute on $file. Otherwise
                                          // Internet Explorer may not reload the file, even if it has changed.

       if (isset($split_file[1])) // $file had query string
           $query = $split_file[1] . "&amp;stat=$file_stat[9]";
       else
           $query = "stat=$file_stat[9]";

       return "$split_file[0]?$query";
   }


   function make_css(string $file) {
       if (strncmp($file, 'https://', strlen('https://'))==0 ||
           strncmp($file, 'http://', strlen('http://'))==0) // Remote stylesheet
           echo "<link type=\"text/css\" href=\"$file\" rel=\"stylesheet\" />\n";
       else // Local stylesheet
           echo "<link type=\"text/css\" href=\"", site_url(add_stat($file)), "\" rel=\"stylesheet\" />\n";
   }

   function make_js(string $file) {
       echo "<script src=\"", site_url(add_stat($file)), "\"></script>\n";
    }
?>
<!DOCTYPE html>
<html lang="<?= $this->language ?>">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?= $title ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lobster">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.2.0/css/all.css" integrity="sha384-hWVjflwFxL6sNzntih27bfxkr27PmbbK/iSvJ+a4+0owXq79v+lsFkW54bOGbiDQ" crossorigin="anonymous">
    <!-- <script src="https://kit.fontawesome.com/1e0c94ff04.js" crossorigin="anonymous"></script>                    -->

    <?php
       make_css('bootstrap_local/css/bootstrap.min.css');

       if (!isset($in_help) || !$in_help)
           make_css('jquery-ui-1.13.2.custom/jquery-ui.min.css');
    
       make_css('styles/fonts.css');
       if ($this->language=='zh-Hans' || $this->language=='zh-Hant')
           make_css('styles/ol_zh.css');
       else
           make_css('styles/ol.css');
       

       if (!isset($css_list))
           $css_list=array();

       foreach ($css_list as $css)
           make_css($css);

       make_js('js/jquery-3.7.0.min.js');

       if (!isset($in_help) || !$in_help) {
           make_js('bootstrap_local/js/bootstrap.min.js');
           make_js('jquery-ui-1.13.2.custom/jquery-ui.min.js');
       }
       else {
           // Enable bootstrap tooltips on help pages
           
           make_js('bootstrap_local/js/bootstrap.bundle.min.js'); // contains popper
           echo "<script>\n";
           echo "  $(function () {\n";
           echo "    $('[data-toggle=\"tooltip\"]').tooltip().on('click',function(){return false;});\n";
           echo "})\n";
           echo "</script>\n";
       }
    
       if (!isset($js_list))
           $js_list=array();

       foreach ($js_list as $js)
       make_js($js);
