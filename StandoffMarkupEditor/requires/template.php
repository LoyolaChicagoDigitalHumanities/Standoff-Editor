<?php
 

class template
{
   
    function load_template($filename)
    {
       @$handle = fopen($filename,"r");
       if (!$handle){
       echo "Unable to load " . $filename;
       Exit;
       }
       @$temp = fread($handle, filesize($filename));
       return($temp);
    }
    
    
    
    function dynamic_rows($row_name, $row_value, $load_template)
    {
       $list = '';
       $first_list = substr($load_template, 0, strpos($load_template, '<%BEGIN_LOOP%>'));
       $str = substr($load_template, strpos($load_template, '<%BEGIN_LOOP%>') +14);
       $loop_list = substr($str, 0, strpos($str, '<%END_LOOP%>'));
       $end_list = substr($load_template, strpos($load_template, '<%END_LOOP%>') +12);
       for ($a = 0; $a < count($row_value); $a++)
       {
          $tcontent = $loop_list;
          for ($i=0; $i < count($row_name); $i++)
          $tcontent = str_replace($row_name[$i],$row_value[$a][$i],$tcontent);
          $list .= $tcontent;
       }
       return $first_list.$list.$end_list;
    }
    
    
    
     
    function replace_static($static_name, $static_value, $load_template)
    {
       $tcontent = $load_template;
       for ($i=0; $i < count($static_name); $i++)
       $tcontent = str_replace($static_name[$i],$static_value[$i],$tcontent);
       return $tcontent;
    }
    
}

?>