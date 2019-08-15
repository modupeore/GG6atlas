<?php
   include("config.php");
   session_start();
   $database = "transcriptatlas";
   $phpscript = "login.php";
   if($_SERVER["REQUEST_METHOD"] == "POST") {
      // username and password sent from form 
      $myusername = mysqli_real_escape_string($db_conn,$_POST['username']);
      $mypassword = mysqli_real_escape_string($db_conn,$_POST['password']); 
      $sql = "SELECT firstname,active FROM user WHERE username = '$myusername' and password = '$mypassword'";
$result = mysqli_query($db_conn,$sql);
      $row = mysqli_fetch_array($result,MYSQLI_ASSOC);
      $count = mysqli_num_rows($result);
      // If result matched $myusername and $mypassword, table row must be 1 row
      if($count == 1) {
         if ($row['active'] == 1) {
				header("location: index.php");
			} else {
				$error = "Please wait for activation from Admin\n";
			}
			$_SESSION['login_user'] = $myusername;
			$_SESSION['firstname'] = $row['firstname'];
      }else {
         $error = "Your Login Name or Password is invalid".'<input type = "button" value = " Register " onclick="window.location.href=\'http://raven.anr.udel.edu/~modupe/atlas/register.php\'"/>';
     }
   }
?>
<html>
   
   <head>
      <title>Login Page</title>
      <link rel="icon" type="image/ico" href="images/atlas_ico.png"/>
      <style type = "text/css">
         body {
            width: 100%;
	    font-family:Arial, Helvetica, sans-serif;
            font-size:14px;
         }

        div.file {
           position: absolute;
           top:0;
    	   bottom: 0;
    	   left: 0;
    	   right: 0;
	   margin: auto;
        }
         
         label {
            font-weight:bold;
            width:100px;
            font-size:14px;
         }
         
         .box {
            border:#666666 solid 1px;
         }
         footer {
	    left:80%;
            clear: both;
            position: fixed;
	    bottom:0;
            z-index: 10;
            height: 3em;
            margin-top: -3em;
         }
      </style>
      
   </head>
   
   <body bgcolor = "#FFFFF6">
                      <table>
                                <tr>
                                        <td width=30px></td>
                                        <td align="center">
                                                <a href="http://raven.anr.udel.edu/~modupe/atlas"><img src=".images/atlas_main.png" alt="Transcriptome Atlas" ></a>
                                        </td>
                                </tr>
                        </table>        
      <div align = "center">
         <div class="file" style = "width:300px; height:145px; background-color: #FFF; border: solid 1px #306269; ">
            <div style = "background-color:#306269; color:#FFFFFF; padding:3px;"><b>Login Details</b></div>
                                
            <div style = "margin:30px">
               
               <form action = "" method = "post">
                  <label>UserName  </label><input type = "text" name = "username" class = "box"/><br /><br />
                  <label>Password  </label><input type = "password" name = "password" class = "box" /><br/><br />
                  <input type = "submit" value = " Login "/> 
		  <input type = "button" value = " Register " onclick="window.location.href='http://raven.anr.udel.edu/~modupe/atlas/register.php'"/>
                  <br />
               </form>
               
               <div style = "font-size:11px; color:#cc0000; margin-top:10px"><?php echo $error; ?></div>
                                        
            </div>
         </div>
      </div>
<footer align="right"><p align="right" ><font size="1">- Created by Modupe Adetunji at the University of Delaware - </font></p></footer>
</body>
</html>

