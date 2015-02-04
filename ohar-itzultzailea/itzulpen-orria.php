<?php

	/*
	 * 
	  *	 OharItzultzailea: WordPress plugin that carries out machine translations (Google Translate), TTS (VoiceRSS) and QR code creation (QRickit) for enabling the creation of multilingual content.
	  *
	  *  Copyright (C) 2015  Manex Garaio Mendizabal
	  *
	  *  This program is free software: you can redistribute it and/or modify
	  *  it under the terms of the GNU General Public License as published by
	  *  the Free Software Foundation, either version 3 of the License, or
	  *  (at your option) any later version.
	  *
	  *  This program is distributed in the hope that it will be useful,
	  *  but WITHOUT ANY WARRANTY; without even the implied warranty of
	  *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	  *  GNU General Public License for more details.
	  *
	  *  You should have received a copy of the GNU General Public License
	  *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
	  *  
	  *  You may contact the author by e-mail at the following address: manex.garaio@gmail.com
	  **/


	//Orriaren egitura definitzen hasi aurretik Wordpress webgunearen wp-config.php konfigurazio fitxategia kargatzen da.
	define(ABSPATH,'../../../');
	require_once( ABSPATH . 'wp-config.php' );
	
?>
<!DOCTYPE html>
<html id="goiburua">
  <head>
  	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php echo get_option('blog_charset'); ?>" />
	<title><?php bloginfo('name') ?> &rsaquo; Ohar Itzultzailea</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <?php
		//Wordpress-en beharrezko estilo orri eta scriptak kargatzeko
		wp_enqueue_style( 'wp-admin' );
		do_action('admin_print_styles');
		do_action('admin_print_scripts');
	?>
  </head>
  <body onload="javascript:itzultzaileaHasieratu()">
	<h1 id="izenburua"><?php echo __('Google Translate itzultzailea','ohar-itzultzailea');?></h1>
    <form id="formulategia" action="#">
		<table id="taula">
			<tr>
				<td></td>
				<td>
					<table id="radioBotoiMultzoa">
						<tr>
							<td id="testuRadioZutabea">
								<input id="testuRadio" type="radio" name="itzuliBeharrekoa" value="testua" checked onclick="hizkuntzarenTestuaErakutsi()"/><label id="testuRadioEtiketa" for="testuRadio"><?php echo __('Testua itzuli','ohar-itzultzailea');?></label>
							</td>
							<td id="izenburuRadioZutabea">
								<input id="izenburuRadio" type="radio" name="itzuliBeharrekoa" value="izenburua" onclick="hizkuntzarenTestuaErakutsi()"/><label id="izenburuRadioEtiketa" for="izenburuRadio"><?php echo __('Izenburua itzuli','ohar-itzultzailea');?></label>
							</td>
						</tr>
					</table>
				</td>
				<td></td>
			</tr>
			<tr>
				<td class="zutabea">
					<select id="jatorriTestuHizkuntza" class="hautatzailea">
					
					</select>
				</td>
				<td class="zutabea">
					<textarea id="jatorriTestua" class="testuKutxa" cols="70" rows="3" onkeyup="tamainaMoldatu('jatorria')"></textarea>
				</td>
				<td class="zutabea" rowspan="2">
					<input id="itzuliBotoia" class="botoia" type="button" value="<?php echo __('Itzuli','ohar-itzultzailea');?>" onclick="javascript:itzuli()" />
				</td>
			</tr>
			<tr>
				<td class="zutabea">
					<select id="helburuTestuHizkuntza" class="hautatzailea">
					
					</select>
				</td>
				<td class="zutabea">
					<textarea id="helburuTestua" class="irakurtzekoTestuKutxa" cols="70" rows="3" disabled></textarea>
				</td>
			</tr>
			<tr>
				<td class="zutabea">
					<input id="itxiBotoia" class="botoia" type="button" value="<?php echo __('Leihoa itxi','ohar-itzultzailea');?>" onClick="leihoaItxi('itxi')"/>
				</td>
				<td class="zutabea">
					<input id="gordeBotoia" class="ezgaitua" type="button" value="<?php echo __('Itzulpena gorde','ohar-itzultzailea');?>" onclick="itzulpenaGorde()" disabled />
				</td>
				<td class="zutabea">
					<input id="gordeEtaItxiBotoia" class="ezgaitua" type="button" value="<?php echo __('Itzulpena gorde eta leihoa itxi','ohar-itzultzailea');?>" onClick="leihoaItxi('gorde')" disabled/>
				</td>
			</tr>
		</table>
	</form>	
	<div id="aitortza">
		<h4><?php echo __('Itzulpenetarako erabilitako web-zerbitzua:','ohar-itzultzailea');?></h4>
		<a href="http://translate.google.com" title="<?php echo __('Google Translate','ohar-itzultzailea');?>">
			<img src='<?php echo get_bloginfo("wpurl")."/wp-content/plugins/ohar-itzultzailea/irudiak/google-translate-logoa.png";?>' 
			alt="<?php echo __('Google Translateren logoa erakusten duen irudia','ohar-itzultzailea');?>"/>
		</a>
	</div>
  </body>
</html>