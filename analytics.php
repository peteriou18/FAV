<?php
#Appel des patterns issu de BBclone
require_once ( 'config.php' );
require_once ( 'patterns.php' );


#S'il existe un referer le script s'arrete car les robots n'ont pas de referer. 
if( empty( $_SERVER["HTTP_REFERER"] ) )
{
	$check = 1;
}

#Si le visiteur renvoi un OS connu il ne s'agit pas d'un robot.
foreach($os as $pattern => $o)
{
	if ( preg_match( '#'.$pattern.'#msi' , $_SERVER["HTTP_USER_AGENT"] ) == 1 )
	{
		$check = 0;
	}
}

if ( $check == 1 )
{
	#On verifie de quel bot il s'agit puis on l'insert dans GA.
	foreach( $bots as $pattern => $bot )
	{	
		if ( preg_match( '#'.$pattern.'#i' , $_SERVER['HTTP_USER_AGENT'] ) == 1 )
		{
			$botname = preg_replace ( "/\\s{1,}/i" , '-' , $bot );		//Bot Name
			$uri = $_SERVER["REQUEST_URI"];								//Resquested URI by Crawler
			
			
			$var_utmn = rand( 1000000000,9999999999 );							//random request number
			$var_utmdt = '';//urlencode( wp_title() );							//Nom de la page visitée
			$var_server = gethostbyaddr( $_SERVER['REMOTE_ADDR'] ); 			//server url => pour le crawler Remote host, nom qualifié de la machine cliente
			$var_utmp = $uri;													//Page vue par le visiteur
			$var_random = rand( 1000000000,2000000000 ); 						//number under 2147483647
			$var_now = time(); 													//today
			
			$urchinUrl .= 'http://www.google-analytics.com/__utm.gif?';
			$urchinUrl .= 'utmwv=1';
			$urchinUrl .= '&utmn='.$var_utmn;				//Nb au hasard
			$urchinUrl .= '&utmsr=-';						//Resolution ecran
			$urchinUrl .= '&utmsc=-';						//Qualite ecran
			$urchinUrl .= '&utmul=-';						//Langue du navigateur
			$urchinUrl .= '&utmje=0';						//Java enabled
			$urchinUrl .= '&utmfl=-';						//Flash version
			$urchinUrl .= '&utmdt='.$var_utmdt;				//Nom de la page visitée
			$urchinUrl .= '&utmhn='.$var_utmhn;				//Nom du site Web
			$urchinUrl .= '&utmr=-';						//pas de referer
			$urchinUrl .= '&utmp='.$var_utmp;				//Page Vue par le visiteur
			//$urchinUrl .= '&utme=-';						//Nombre???(Objet*Action*Label) => 5(Robots*Bot Name*Pathname)
			$urchinUrl .= '&utmac='.$var_utmac;				//Numero de compte analytics
			$urchinUrl .= '&utmcc=__utma%3D'.$var_cookie.'.'.$var_random.'.'.$var_now.'.'.$var_now.'.'.$var_now.'.1%3B%2B__utmb%3D'.$var_cookie.'%3B%2B__utmc%3D'.$var_cookie.'%3B%2B__utmz%3D'.$var_cookie.'.'.$var_now.'.1.1.utmccn%3D(organic)%7Cutmcsr%3D'.$botname.'%7Cutmctr%3D'.$uri.'%7Cutmcmd%3Dorganic%3B%2B__utmv%3D'.$var_cookie.'.Robot%20hostname%3A%20'.$var_server.'%3B';										
	
	
			#Injection de la page dans Google Analytics
			$cu = curl_init();
			curl_setopt($cu, CURLOPT_HEADER, 1);
			curl_setopt($cu, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($cu, CURLOPT_URL, $urchinUrl);
			$code = curl_exec($cu);
			curl_close($cu);
			break;
		}
	}
}
?>