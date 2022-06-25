<?php
/*
	Thx, Peekobot! 
	https://github.com/Peekobot/peekobot
	https://twitter.com/magicroundabout/
	
	This is an addon for the Peekobot. It can be used as a crawler to search a folder and make it available for the Peekobot as a clickable menu.

	Background: I have recipes on my homepage in the folder /recipes/. The recipes are available as text files. 

	folder: /recipes/
	category_recipename.txt

	Example 1:	baking_moms_delicious_cake.txt
	Example 2:	beverages_gingerdrink.txt
	Example 3:	pasta_pasta_casserole.txt

	////////// Inside a text file //////////
	Mom's Delicious Cake

	Ingredients:
	1 l milk
	[...]
	[...]
	[...]
	/////////////////////////////////////////////

	Line 1 is the headline. It will be converted by the crawler as <h2>headline</h2>.

	Have fun with the crawler!

	License of this script: Public Domain
*/															

//exit; //uncomment it to prevent using this file.
$folder = "rezepte"; // WITHOUT a slash. It's added automatically

//The intro node is: $folder_intro
$exitnode = "frage_beenden"; // This is the exit node, where the user has the chance to continue or proceed (to be guided to the main overview, if desired)

$text['intro'] = "Ich liebe es zu kochen 😊 Ich habe sicherlich ein paar interessante Rezepte für dich. Ein paar Rezepte sind vegetarisch, ein paar Rezepte sind vegan. Aber alle sind supereasy zu machen und superlecker.";

$text['item_choose_pre']="Kategorie ";
$text['item_choose'] = ". Welches Rezept möchtest du gerne haben?";

$text['overview_previous'] ="Gib mir bitte nochmal die <b>";
$text['overview_after'] ="-Rezepte-Übersicht</b>.";
$text['overview_main'] ="Gib mir bitte nochmal die <b>Gesamt-Übersicht</b> über die Rezepte.";

$text['category_choose'] ="Aus welcher Kategorie möchtest du ein Rezept haben?";

$text['abort'] = "Nee, lieber doch kein Rezept..";		
$text['abort_enough'] = "Och, an Rezepten genügt mir das fürs Erste.";		
$text['abort_outro'] = "Ok, gerne. Bald kommen noch viel mehr Rezepte hinzu. Frag mich demnächst gerne wieder nach Rezepten 😊";		
														
$text['done'] = date("d.m.Y, H:i:s").": Die Datei <b>".$folder.".js</b> wurde erfolgreich geschrieben.";
	
///////////////// No edit neccessary below this line 	/////////////////
$files = array_slice(scandir($folder."/"),2);

$output [] ="
 const ".$folder."_abort =    {
                text: '".$text['abort']."',
                next: '".$folder."_abort',
            }
			";
			
$output []  = "
 chat.".$folder."_abort = {
        text: '".$text['abort_outro']."',
		next: '".$exitnode."'
    }";
	
$output[] = "
const ".$folder."_abort_enough =    {
                text: '".$text['abort_enough']."',
                next: '".$folder."_abort',
            }
			";

$output[] = "chat.".$folder."_intro = {
        text: '".$text['intro']."',
		next: '".$folder."'
    }
	";

$output[] = "
	const ".$folder."_categories = [";

$category_get_old = "";
$counter=0;
for($a=0;$a<count($files);$a++)
	{
		$base64name = (base64_encode($files[$a]));
		$base64name  = str_replace("=","",$base64name);
		$headline = trim(file($folder."/".$files[$a])[0]);
		$category_get =  substr($files[$a],0,strpos($files[$a],"_"));
		if($category_get !== $category_get_old)
		{
	
$output[] = "
	{
		text: '".$category_get."',
		next: '".$folder."_category_".(strtolower($category_get))."'
    }";
	if($a<count($files)-1){$output[]=",";}
	
	$category_get_old = $category_get;
		}	
	}

$output[] = "
]
";

$counter1 = 0;
$counter2 = 0;
$category_get_old = "";
for($a=0;$a<count($files);$a++)
	{
		$base64name = (base64_encode($files[$a]));
		$base64name  = str_replace("=","",$base64name);
		$headline = trim(file($folder."/".$files[$a])[0]);
		$counter2++;
		$itemname = substr($files[$a],strpos($files[$a],"_")+1);
		$itemname = 	substr($itemname,0,strrpos($itemname,"."));
		$dateiname = (substr($files[$a],0,strrpos($files[$a],".")));
		$dateiname = str_replace("=","",$dateiname);
	
		$category_get =  substr($files[$a],0,strpos($files[$a],"_"));
		if($category_get !== $category_get_old)
		{
		$counter1++;
		if($counter1>1){$output[] = "]
		}
		";}
		$counter2 = 0;	
		$output[]="
		chat.".$folder."_category_".(strtolower($category_get))." = {
        text: '".$text['item_choose_pre'].$category_get.$text['item_choose']."',
		options: [
		";
		
		$category_get_old = $category_get;
	}
	
	if($counter2 !== 0){$output[] = ",";}
	$headline	= str_replace(	
								array("'"),
								array("&apos;"),
								$headline);
	$output[] ="
	            {
                text: '".$headline."',
                next: '".$base64name."'
            }
	";
}

$output[] = "]}";

$category_get_old = "";
$counter=0;
for($a=0;$a<count($files);$a++)
	{
		$category_get =  substr($files[$a],0,strpos($files[$a],"_"));
		if($category_get !== $category_get_old)
		{
			$counter++;
			if($counter>1){$output[]="
		
			]
		
			";}
			$output[] = "
			const ".$folder."_".(strtolower($category_get))."_options = [";					
			$output[]="
		{
                text: '".$text['overview_previous'].$category_get.$text['overview_after']."',
                next: '".$folder."_category_".(strtolower($category_get))."',
		},
		{
                text: '".$text['overview_main']."',
                next: '".$folder."',
        },
			".$folder."_abort_enough
		";	
		$category_get_old = $category_get;
		}
	}
		
$output[]="
			]
	";

for($a=0;$a<count($files);$a++)
	{
		$category_get =  substr($files[$a],0,strpos($files[$a],"_"));
		$base64name = (base64_encode($files[$a]));
		$base64name  = str_replace("=","",$base64name);
		$output[] = "chat.".$base64name." = {
			";
		$content = file_get_contents($folder."/".$files[$a]);
		
		$content = str_replace(
											array(
														"\r\n",
														"'",
														),
											array(
														"<br>",
														"&apos;",
											),
											$content);
		$output[]  = "text: '".$content."',
		";
		$output[] = "options: ".$folder."_".strtolower($category_get)."_options
		}";
		
		if($a<count($files)-1){
		$output[] = "
		,
		";
		}

	}

$output[] = "
 chat.".$folder." = {
        text: '".$text['category_choose']."',
		options: ".$folder."_categories, ".$folder."_abort
    }";

$output=implode("",$output);
file_put_contents($folder.".js",$output);

echo $text['done'];
	
exit;	
?>