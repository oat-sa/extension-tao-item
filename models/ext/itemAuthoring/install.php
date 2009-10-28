			
			<?php
			/*
	
   
   
    

    
    
    
    

    
    
    

*/
			include($_SERVER['DOCUMENT_ROOT'] . "/middleware/functions.php");
			$output.="<br><br>
			<div class=\"AuthBloc\"><table border=0 cellpadding=3 cellspacing=3><tr></tr><tr><td colspan=3><b>Add ItemModel</b></td>
			
			<FORM method=post>
			<tr><td>Module Name
				</td><td><input name=modname size=15></td></tr>
			<tr><td>Login
				</td><td><input name=modlogin size=15></td></tr>
			<tr><td>Module Pass
				</td><td><input name=modpass size=15></td></tr>
			<tr><td>FileName
				</td><td><input name=filename size=15></td><td><i>New item model downloaded file (ex. : Campus.php) must be present in your www/middleware/ItemModels subdirectoy</td></tr>
			<tr><td>
			<INPUT type=submit name=operation value=Add&nbsp;ItemModel></td></tr>
			
			</FORM>
			
			</table>
			</div>";

			if (!(isset($_POST["operation"])))
			{
				echo $output;
			}
			else
			{
				if (strpos($_POST["filename"],"authoring"))
				{
					$itemmodelName=substr($_POST["filename"],0,strpos($_POST["filename"],"authoring"));
					$nameofiletowrite = $_POST["filename"];
				}
				else
				{
					$itemmodelName=substr($_POST["filename"],0,strlen($_POST["filename"])-4);
					$nameofiletowrite = substr($_POST["filename"],0,strlen($_POST["filename"])-4)."authoring.php";
				}
				
				echo "Nom réél du modèle : ".$itemmodelName."<br>";
				echo "Nom du fichier a ecrire : ".$nameofiletowrite."<br>";
				
				if (is_file($nameofiletowrite))
					{
						echo "Such a model already exists on server... updating related Module";
					}
					else
					{
				if (is_file($_POST["filename"]))
						{
				$handle = fopen($_POST["filename"],"rb");
				$temp2 = fread($handle,100000000);
				fclose($handle);
				$handle = fopen($nameofiletowrite,"wb");
				$temp2 = fwrite($handle,$temp2);
				fclose($handle);
						}
				else {echo "File not found !";die();}
					}

				$auth = calltoKernel('authenticate',array(array($_POST["modlogin"]),array($_POST["modpass"]),array("1"),array($_POST["modname"])));
				
				if ($auth["pSession"][0]=="Authentication failed")
				{echo "BAD LOGIN"; die();}
				$session = $auth["pSession"];
				
				$deflg = calltoKernel('getModuleDeflg',array($session));
				$deflg = $deflg["pDescription"];
				
				$idInstance = calltoKernel('setInstance',array($session,array($deflg), array($itemmodelName),array($itemmodelName),array(7)));
				
				$xx = calltoKernel('setPropertyValuesforInstance',array($session,array($idInstance["pOKorKO"]),array(5),array("XX"),array($itemmodelName.".swf")));
				
			
			}
			?>