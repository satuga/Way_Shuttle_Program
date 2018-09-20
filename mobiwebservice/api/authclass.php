<?php 
class Authorization
{
    function chk_authorization($authheader)
    {
        
      if($authheader['Apikey']!='9d5b4c67dadefbaa3908e116bded36b0' || $authheader['Secretkey']!='6648acc5e97f7549957f7250a3d0a195')
      {
        $content= "<response><result>113</result><message>Authentication Fail</message></response>";
        echo $content;
        exit;
      } 
  
    }

function getallheaders()
    {
           $headers = '';
       foreach ($_SERVER as $name => $value)
       {
           if (substr($name, 0, 5) == 'HTTP_')
           {
               $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
           }
       }
       return $headers;
    }
	}


?>