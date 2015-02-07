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

	/*
	*	Fitxategi honetan itzulpenak egiteko erabiltzen diren Javascript funtzioak definitzen dira. PHP fitxategi baten barnean daude sartuta, zerbitzariko hainbat
	*	datu eta eragiketa erabiltzen dituzte eta.
	*/
	
	//Wordpress-en definituta dauden hainbat eragiketa erabili ahal izateko Wordpress webgunearen wp-config.php konfigurazio fitxategia kargatzen da.
	define(ABSPATH,'../../../');
	require_once( ABSPATH . 'wp-config.php' );
	
	
	echo "<!-- \n";
	
	//Itzulpenak egiteko erabiliko den zerbitzariaren helbidea
	echo "var api_url = 'http://apy.projectjj.com'; \n";
	
	//Zerbitzariari egiten zaizkion deiak murriztearren erabilgarri dauden hizkuntz-bikoteak eskuratzeko deia egitean, bikoteak gorde egiten dira.
	echo "var hizkuntzaBikoteak = []; \n";
	
	echo "var azkenItzulpena; \n";
	
	echo "var editoreaEraldatuDa = false; \n";
	
	
		//
	//	
	//	Editorean dagoen itzultzailea abiarazteko botoiaren portaera definitzen dituzten eragiketak
	//
		//
	
	/*
	*	Wordpress bidalketen editorea kargatzen denean itzultzailea abiarazten duen botoiaren portaera zehazten duen kode zatia
	*/
	echo '
		//jQuery $ baten bidez erabili ordez jQuery aurrizkiaren bidez erabiltzeko
		$.noConflict();
		jQuery(window).load(function() {
			var kokapena = window.location.href;
			if( kokapena.search("post.php") != -1 ) {	//Bidalketen editorean gaudenean exekutatu beharreko kodea
				itzultzailearenPortaeraEzarri();
			}
		})' . " \n";
		
		
	/*
	*	Funtzio honek bidalketaren editorean gertatzen diren fitxa aldaketak kontrolatzeko azpiegitura jartzen du. Izan ere, erabiltzailea fitxen artean mugitu bada testuen
	*	itzulpen prozesua ezin da ondo gauzatu, izenburuena bai. Hori dela eta, fitxa aldaketa gertatzen denean deitu beharreko funtzioa zehazten da, gero itzultzailea 
	*	testuak itzultzen saiatu ez dadin. Testuak itzultzeko bidalketa eguneratu beharko du, eta itzultzailea ireki aurretik fitxa aldaketatik egin gabe.
	*/
	echo "function itzultzailearenPortaeraEzarri() {";
		foreach ($q_config['enabled_languages'] as $language) {	//Webgunea ezarrita dagoen hizkuntz bakoitzeko
			echo "addEvent( document.getElementById( 'qtrans_select_" . $language . "'), 'click', editoreEraldatua );";
		}
		echo "addEvent( document.getElementById( 'content-tmce' ), 'click', visualMartxan );
		addEvent( document.getElementById( 'content-html' ), 'click', textMartxan ); ";
	echo "} \n";	
	
	/*
	*	Funtzio honek boolear bati balioa aldatuko dio, erabiltzailea editoreko fitxen artean mugitzean.
	*/
	echo "function editoreEraldatua()
	{
		editoreaEraldatuDa = true;
	} \n";

	
	echo "function visualMartxan() {";
		echo "visualAukerarenKudeaketa( 'bai' );
		editoreaEraldatuDa = true;
	} \n";
	
	echo "function textMartxan() {";
		echo "visualAukerarenKudeaketa( 'ez' );
		editoreaEraldatuDa = true;
	} \n";
	
	echo "function sortuXmlHttpRequestObjektua() {
		// aldagai lokal honek XMLHttpRequest objektuaren errefentzia gordeko du
		var xmlHttp;
		// Internet Explorer arakatzailean exekutatzeko
		if(window.ActiveXObject) {
			try { 
				xmlHttp = new ActiveXObject('Microsoft.XMLHTTP'); 
			}
			catch (e) { 
				xmlHttp = false; 
			}
		}
		// beste arakatzaileetarako
		else {
			try { 
				xmlHttp = new XMLHttpRequest(); 
			}
			catch (e) { 
				xmlHttp = false; 
			}
		}
		// itzuli sortutako objektua edo, bestela, erakutsi errore-mezua
		if (!xmlHttp) {
			alert('XMLHttpRequest sortzean errorea gertatu da.');
		} else {
			return xmlHttp;
		}
	} \n";
	
	echo "function visualAukerarenKudeaketa( martxan ) {
		if ( 'bai'!== martxan && 'ez' !== martxan ) {
			martxan = 'ez';
		}
		var xmlHttp = jQuery.ajax({
			type : 'POST',
			url : ajaxurl,
			async : true,
			data : { 'action' : 'visualKudeatzailea',
					'visualMartxan' : martxan },
			timeout : 1000,
		});
	} \n";
	
	/*
	*	Funtzio honekin HTML objektuei gertaeren araberako entzuleak gehitzen zaizkie. Portaera eraldatu nahi zaion elementua, gertaera eta gertaerari erantzuteko funtzioa
	*	pasatzen zaizkio parametro gisa.
	*/
	echo "function addEvent(elementua, gertaera, funtzioa) {
		if(elementua.addEventListener) {	//addEventListener aukera duten nabigatzaileak(Gehienek daukate)
			elementua.addEventListener( gertaera, funtzioa, false );
			return true;
		} else if ( elementua.attachEvent ) {	//addEventListener aukera ez duten eta attachEvent aukera dute nabigatzaileak(Internet Explorer)
			var arrakasta = elementua.attachEvent( 'on'+gertaera, funtzioa );
			return arrakasta;
		} else { //Nabigatzaile zaharrak
			elementua['on'+gertaera] = funtzioa;
			return true;
		}
	}	 \n";
			
		//
	//
	//		ITZULTZAILEAREN ERAGIKETAK
	//
		//
			
	
			
	/*
	*	Funtzio hau itzultzailean abiarazten denean bakarrik exekutatuko da eta erabilgarri dauden hizkuntz bikoteak eta webgunea ezarrita dagoen hizkuntzak lortzeaz
	*	arduratzen da.
	*/
	echo "function itzultzaileaHasieratu() {
		//Webgunea ezarrita dagoen hizkuntza array-a lortu
		var hizkuntzak = webgunearenHizkuntzakItzuli();
		if ( window.opener.editoreaEraldatuDa ) {	//Erabiltzailea editoreko fitxen artean mugitu bada
			//Izenburuak itzuliko direla adierazten duen radio botoia markatu
			document.getElementById( 'izenburuRadio' ).checked = true;
			//Testuak itzuliko direla adierazten duen radio botoiari portaera moldatu
			document.getElementById( 'testuRadio' ).onclick = testuaEzinDaItzuli;
		}
		//Erabilgarri dauden hizkuntza bikoteak lortu
		bikoteakLortu( hizkuntzak );
	} \n";
	
	/*
	*	Funtzio honek erabiltzaileari jakinarazten dio bidalketaren editorearen fitxen artean mugitzeagatik itzultzaileak eragina jasango duela eta testua itzuli nahi 
	*	badu, bidalketa eguneratzeko esango dio. Egoera honetan izenburuak itzuli daitezke
	*/		
	echo "function testuaEzinDaItzuli()
	{
		alert('".__("Oraintxe bertan ezin da bidalketaren testua itzuli, editoreko fitxen artean mugitzeak eragina duelako itzultzailean. Testua itzuli nahi izatekotan, eguneratu artikulua (Publish atalaren barruko \'Update\' botoia sakatuz) eta klikatu itzultzailearen botoiari zuzenean. Izenburuak itzuli nahi izatekotan zuzenean egin dezakezu, bidalketa eguneratu beharrik gabe.",'ohar-itzultzailea')."');
		document.getElementById('izenburuRadio').checked=true;
	} \n";
	
	echo "function itzultzaileaEzgaituta() {
		alert('".__('Ohar Itzultzailea ezin da ireki, editoreko "Visual" aukera hautatuta dagoelako. Hautatu "Text" aukera eta eguneratu orrialdea aldaketak eragina izan dezan.','ohar-itzultzailea')."');
	} \n";
	
	
	//
	//	Editoretik informazioa lortzeaz arduratzen diren eragiketak
	//
	
	/*
	*	Funtzio honekin webgunean ezarrita dauden hizkuntzak lortzen dira.
	*/
	echo "function webgunearenHizkuntzakItzuli() {";
			//qtranslatek erabiltzen duen $q_config objektua kargatu, ezarrita dauden hizkuntzak lortzeko erabiliko duguna
			global $q_config;
			//Webgunea ezarrita dagoen hizkuntzak gordetzeko array-a
			echo 'var hizkuntzak = new Array();';
			$i = 0;
			//$q_config objektuaren bitartez ezarrita dauden hizkuntzak(enabled_languages) lortu, eta loop bat egin
			foreach ( $q_config['enabled_languages'] as $language )	{
				//hizkuntzak array-an gorde uneko hizkuntza
				echo "hizkuntzak[$i] = '$language';";
				$i++;
			}
		//hizkuntzen array-a itzuli
		echo "return hizkuntzak;
	} \n";
	
	/*
	*	Funtzio honen bidez jatorri hizkuntzaren hautatzailean hautatuta dagoen hizkuntzaren testua lortzen da. Bi kasu ezberdinetan exekutatu daiteke, izenburuak edo
	*	bidalketaren testuak erakustean. Hori kontrolatzeko interfazean radioButton gisako bi botoi daude, erabiltzaileak ikusi nahi duena erabaki dezan
	*/
	echo "function hizkuntzarenTestuaErakutsi() {
		if ( document.getElementById( 'izenburuRadio' ).checked ) {	// Erabiltzaileak izenburuak itzultzea erabaki badu
			// Javascript eta DOM eredua erabiliz izenburuaren testua eskuratu
			var izenburukoTestua = window.opener.document.getElementById( 'title' ).value;
			testuaItzultzaileanTxertatu( izenburukoTestua );
			// Testu kutxan dagoen testu kopuruaren arabera dinamikoki tamaina handitu edo txikitzen duen funtzioari deitu
			tamainaMoldatu( 'jatorria' );
		} else { // Erabiltzaileak bidalketaren testua itzultzea erabaki badu
			//Javascript eta DOM eredua erabiliz editoreko testua eskuratu
			var editorekoTestua = window.opener.document.getElementById( 'content' ).value;
			// alert( editorekoTestua );
			testuaItzultzaileanTxertatu( editorekoTestua );
			// Testu kutxan dagoen testu kopuruaren arabera dinamikoki tamaina handitu edo txikitzen duen funtzioari deitu
			tamainaMoldatu( 'jatorria' );
		}
	} \n";
	
	/*
	*	Funtzio honek jasotako testua hizkuntzaren arabera banatuko du, jatorri hautatzailean dagoen hizkuntzaren testua jatorri testu kutxan erakusteko.
	*	Editorean berez hizkuntza guztiak batera gordetzen dira, gero dinamikoki hizkuntz bakoitzeko fitxa bat gehitzen den arren. Edukia hizkuntza ezberdinetan 
	*	bereizteko formatu berezi batekin gordetzen da editorean. 
	*	Adibidez: <!--:eu-->Hau post baten euskarazko edukia da.<!--:-->   Ikusten den bezala hasiera eta bukaerako mugatzaileak daude hizkuntzaren testuaren
	*	inguruan. <!--:'bi_letrako_hizkuntza_kodea'-->'artikuluaren_edukia'<!--:--> egitura dauka. Funtzio honek egitura hori kontutan hartzen du behar den hizkuntzaren 
	*	testua eskuratzeko.
	*/
	echo "function testuaItzultzaileanTxertatu( itzultzailerakoTestua ) {
		var hasieraIndex, bukaeraIndex;
		//Jatorri hizkuntzaren hautatzailearen instantzia lortu
		var jatorriHautatzailea = document.getElementById( 'jatorriTestuHizkuntza' );
		//Bertan hautatuta dagoen aukeraren indizea eskuratu
		var hautatutakoIndizea = jatorriHautatzailea.selectedIndex;
		//Indize horretan dagoen hizkuntzaren kodea(3 letrakoa) lortu
		var hizkuntza =  jatorriHautatzailea.getElementsByTagName( 'option' )[hautatutakoIndizea].value;
		//Jatorrizko testua erakusten den testu-kutxaren instantzia lortu
		var jatorriTestuKutxa = document.getElementById( 'jatorriTestua' );
		//izenburuan dagoen testuan hautatzailean dagoen hizkuntzari dagokion testuaren hasierako mugatzailea bilatu
		var lag = itzultzailerakoTestua.split( '<!--:' + hizkuntza + '-->' );
		if ( lag[1] ) {	//Mugatzailea izenburuan aurkitu bada
			//Bukaerako mugatzailea bilatu
			var testua = lag[1].split('<!--:-->');
			if ( jatorriTestuKutxa.value != '' ) {	//jatorrizko testuaren testu kutxa hutsik ez badago, bere edukia ezabatu
				jatorriTestuKutxa.value = '';
			}
			if ( -1 !== testua[0].indexOf('[audio mp3' ) ) { // Dagoeneko audio etiketa bat badago, kendu, itzultzailea pasa ez dadin
				hasieraIndex = testua[0].indexOf( '[audio mp3' );
				bukaeraIndex = testua[0].lastIndexOf( '[/audio]' ) + 8;
				testua[0] = testua[0].substr( 0, hasieraIndex ).concat( testua[0].substr( bukaeraIndex, testua[0].length ) );
				jatorriTestuKutxa.value = testua[0].trim();
			} else {
				//Testuaren balio berria esleitu
				jatorriTestuKutxa.value = testua[0];
			}
		} else {	//Mugatzailea izenburuan aurkitu ez bada
			if ( jatorriTestuKutxa.value != '' ) { //jatorrizko testuaren testu kutxa hutsik ez badago, bere edukia ezabatu
				jatorriTestuKutxa.value = '';
			}
		}
	} \n";
	
	
	//
	//	Google Translate web-zerbitzuarekin komunikatu eta jasotako datuak tratatzeko funtzioak
	//
	
	
	/*
	*	Funtzio honek Google Translate web-zerbitzuaren listPairs eragiketari deitzen dio, erabilgarri dauden hizkuntz bikoteak lortzen dituena. Webgunea ezarrita dagoen
	*	hizkuntzak ere jasotzen ditu
	*/
	echo "function bikoteakLortu(hizkuntzak) {
		// Kurtsorea itxaroten jarri deia burutu bitartean
		document.body.style.cursor = 'wait';
		// Google Translateri deia egiten dion funtzioari deitzeko AJAX agindua
		var xmlHttp = jQuery.ajax({
			type : 'POST',
			url : '" . admin_url('admin-ajax.php') . "',
			async : true,
			datatype : 'xml',
			data : { 'action' : 'bikoteakLortu',
					'hizkuntza' : '" . $q_config['default_language'] . "' },
			timeout : 15000,
			success : function ( data, status, jqXHR ) {
				if ( 'success' === status ) { // Jasotako erantzunaren egoera arrakastazkoa bada
					if ( typeof data[0].hizkuntzKodea != 'undefined' ) { // Erantzuneko lehen posizioaren hizkuntzKodea hutsik badago, errorea egon da
						var jatorriHautatzailea = document.getElementById( 'jatorriTestuHizkuntza' );
						// Jasotako hizkuntz bakoitzeko iterazioa
						for ( var i = 0, j = data.length; i < j; i++ ) {
							// Webgunean ezarrita dauden hizkuntz bakoitzeko iterazioa
							for ( var k = 0, m = hizkuntzak.length; k < m; k++ ) {
								if ( hizkuntzak[k] === data[i].hizkuntzKodea ) { // Uneko hizkuntza webgunean badago, hautatzailera gehitu
									aukeraBerria=document.createElement( 'option' );
									aukeraBerria.setAttribute( 'value', data[i].hizkuntzKodea );
									//Honen bidez, erabiltzaileak hizkuntza bat hautatzean helbur hautatzailearen balioak eguneratuko dira
									helburuFuntzioa = 'helburuHautatzaileariBalioakEman()';
									aukeraBerria.setAttribute( 'onclick', helburuFuntzioa );
									if( window.opener.qtrans_get_active_language() === data[i].hizkuntzKodea ) {	//Hizkuntza unean editorean jarrita dagoena bada, hautatzailean aukeratua bezala ager dadin
										aukeraBerria.setAttribute( 'selected', 'true' );
									}
									testuNodoa = document.createTextNode( data[i].hizkuntzIzena );
									jatorriHautatzailea.appendChild( aukeraBerria ).appendChild( testuNodoa );
									
									// hizkuntzaren kodea eta izena webgunean gorde, hurrengo batean prest edukitzeko
									var hizkuntzObjektua = { 'hizkuntzIzena' : data[i].hizkuntzIzena,
										'hizkuntzKodea' : data[i].hizkuntzKodea
									};
									hizkuntzaBikoteak.push( hizkuntzObjektua );
									break;
								}
							}
						}
						// Helburu hautatzaileko hizkuntzak kargatu
						helburuHautatzaileariBalioakEman();
					} else { // Erabiltzaileari errorea erakutsi
						alert( data );
					}
				} else { // Jasotako erantzunaren estatusa egokia ez bada, errore mezu bat erakutsi
					alert( '" . __( 'Erroreren bat gertatu da eskaeraren erantzuna jasotzean.', 'ohar-itzultzailea' ) . "' );
				}
			},
			error: function (xhr, status, error) {
				switch( status ) { // Eskaera egitean erroreren bat gertatzen bada mezu bat erakutsi, errore mota bakoitzerako ezberdina
					case 'timeout':
						alert( '" . __( 'Eskaera iraungitu egin da, martxan zeraman denbora luzea dela eta.', 'ohar-itzultzailea' ) . "' );
						break;
					case 'abort':
						alert( '" . __( 'Eskaera abortatu egin da.', 'ohar-itzultzailea' ) . "' );
						break;
					case 'parseError':
						alert( '" . __( 'Eskaeraren parametroak eskuratzean akats bat gertatu da.', 'ohar-itzultzailea' ) . "' );
						break;
					case 'error':
						alert( '" . __( 'Eskaerak errore bat itzuli du: ', 'ohar-itzultzailea' ) . "' + error );
						break;
				}
			},
		});
	} \n";
	
	/*
	*	Funtzio honek helburu hizkuntzaren hautatzaileari balioak emango dizkio, jatorri hizkuntzaren hautatzailean aukeratuta dagoen hizkuntzaren araberakoak.
	*	Helburu hautatzailean egoteko beharrezkoa da hizkuntza hori webgunean ezarrita egotea
	*/
	echo "function helburuHautatzaileariBalioakEman()
	{
		if ( 'wait' === document.body.style.cursor ) { // Kurtsorea itxaroten badago, egoera lehenetsira itzuli
			document.body.style.cursor = 'auto';
		}
		//Jatorri hizkuntzaren hautatzailearen instantzia lortu
		var jatorriHautatzailea = document.getElementById( 'jatorriTestuHizkuntza' );
		//Bertan hautatuta dagoen aukeraren indizea eskuratu
		var hautatutakoIndizea = jatorriHautatzailea.selectedIndex;
		//Indize horretan dagoen hizkuntzaren kodea(3 letrakoa) lortu
		var hautatutakoHizkuntza = jatorriHautatzailea.getElementsByTagName( 'option' )[hautatutakoIndizea].value;
		/*Funtzio hau deitzen den bakoitzean jatorri hautatzailean zerbait gertatu da(hasieraketa edota hizkuntza aldaketa). Beraz, unean hautatuta dagoen hizkuntzaren
			testua erakutsi beharko da, dei honen bidez gauzatzen dena. */
		hizkuntzarenTestuaErakutsi();
		
		//helburu hizkuntzaren hautatzailearen instantzia lortu
		var helburuHautatzailea = document.getElementById( 'helburuTestuHizkuntza' );
		//Elementurik duen bitartean lehenengo elementua ezabatu
		while ( helburuHautatzailea.firstChild ) {
			helburuHautatzailea.removeChild( helburuHautatzailea.firstChild );
		}
		
		var aukeraBerria, testuNodoa, helburuFuntzioa;	
		//Gorde den helburu hizkuntz bakoitzeko iterazioa
		for(var j=0, k=hizkuntzaBikoteak.length; j < k; j++) {
			if( hizkuntzaBikoteak[j].hizkuntzKodea !== hautatutakoHizkuntza ) {	//uneko helburu hizkuntza webguneko hizkuntzarekin bat badator, hautatzailean gorde
				/*Helburu hautatzailearentzako aukera berri bat gorde, balio bezala hizkuntzaren 2 letretako kodea duelarik eta erakutsiko den testu bezala 
					hizkuntzaren izena*/
				aukeraBerria=document.createElement('option');
				//Honen bidez, erabiltzaileak hizkuntza bat hautatzean helbur hautatzailearen balioak eguneratuko dira
				helburuFuntzioa = 'itzuli()';
				aukeraBerria.setAttribute( 'onclick', helburuFuntzioa );
				aukeraBerria.setAttribute( 'value', hizkuntzaBikoteak[j].hizkuntzKodea );
				testuNodoa=document.createTextNode( hizkuntzaBikoteak[j].hizkuntzIzena );
				helburuHautatzailea.appendChild( aukeraBerria ).appendChild( testuNodoa );
			}
		}
	} \n";
	
	/*
	*	Funtzio honekin jatorrizko testua itzuliko da, jatorri hizkuntzaren hautatzailean dagoen hizkuntzatik helburu hizkuntzaren hautatzailean dagoen hizkuntzara.
	*	Funtzio hau helburu hizkuntzaren hautatzaileak balioren bat duenean soilik exekutatu ahalko da, beste egoeretan funtzio hau abiarazten duen botoia ezgaituta 
	*	dago eta.
	*/
	echo "function itzuli()
	{
		//Jatorri hizkuntzaren testua eta hautatuta dauden jatorri eta helburu hizkuntzak lortzeko
		var testua = document.getElementById( 'jatorriTestua' ).value;
		var jatorriHautatzailea = document.getElementById( 'jatorriTestuHizkuntza' );
		var jatorriIndizea = document.getElementById( 'jatorriTestuHizkuntza' ).selectedIndex;
		var jatorriHizkuntza = jatorriHautatzailea.getElementsByTagName( 'option' )[jatorriIndizea].value;
		var helburuHautatzailea = document.getElementById( 'helburuTestuHizkuntza' );
		var helburuIndizea = document.getElementById( 'helburuTestuHizkuntza' ).selectedIndex;
		var helburuHizkuntza = helburuHautatzailea.getElementsByTagName( 'option' )[helburuIndizea].value;
		
		// Google Translateri deia egiten dion funtzioari deitzeko AJAX agindua
		var xmlHttp = jQuery.ajax({
			type : 'POST',
			url : '" . admin_url('admin-ajax.php') . "',
			async : true,
			datatype : 'xml',
			data : { 'action' : 'testuarenItzulpenaEgin',
					'jatorriHizkuntza' : jatorriHizkuntza,
					'helburuHizkuntza' : helburuHizkuntza,
					'testua' : testua,
			},
			timeout : 15000,
			success : function ( data, status, jqXHR ) {
				if ( 'success' === status ) { // Jasotako erantzunaren egoera arrakastazkoa bada
					if ( typeof data.erroreMezua == 'undefined' ) { // erroreMezua undefined bada, itzulpena arrakastaz burutu da
						//Helburu hizkuntzaren testu kutxaren instantzia lortu
						var helburuTestua = document.getElementById( 'helburuTestua' );
						//Testu kutxaren aurreko edukiak garbitu
						if( helburuTestua.firstChild ) {
							helburuTestua.removeChild( helburuTestua.firstChild );
						}
						/*innerHTML-ren bidez testu kutxari itzulpena esleituko diogu. Horrela eginda, tildeak eta beste zenbait karaktere ondo ikusiko direla ziurtatzen da
						(testu nodoen bitartez egitekotan hori ez da horrela gertatzen)*/
						helburuTestua.innerHTML = data;
						/*Itzulpena eginda dagoenez, itzulpena gorde eta itzulpena gordetzeaz gain leihoa ixtea arduratzen diren botoiak gaituko dira, klase berriz 
							bat emateaz gain(itxura berri bat izan dezaten)*/
						var gordeBotoia = document.getElementById( 'gordeBotoia' );
						gordeBotoia.disabled = false;
						gordeBotoia.className = 'botoia';
						var gordeEtaItxiBotoia = document.getElementById( 'gordeEtaItxiBotoia' );
						gordeEtaItxiBotoia.disabled = false;
						gordeEtaItxiBotoia.className = 'botoia';
						//Testu kutxan dagoen testu kopuruaren arabera dinamikoki tamaina handitu edo txikitzen duen funtzioari deitu
						tamainaMoldatu( 'helburua' );
						if ( document.getElementById( 'izenburuRadio' ).checked ) {	//Egin den itzulpena izenburuarena bada
							//Kontrol lanetarako balio duen azkenItzulpena aldagaian azken itzulpenaren helburu hizkuntzaren 3 letretako kodea gorde eta izenburua itzuli dela
							//adierazi. Bien tartean | banatzailea
							azkenItzulpena = helburuHizkuntza + '|izenburua';
						} else {	//Egin den itzulpena testuarena bada
							//Kontrol lanetarako balio duen azkenItzulpena aldagaian azken itzulpenaren helburu hizkuntzaren 3 letretako kodea gorde eta bidalketaren testua
							//itzuli dela adierazi. Bien tartean | banatzailea 
							azkenItzulpena = helburuHizkuntza + '|testua';
						}
					} else { // Erabiltzaileari sortutako errorearen berri eman
						alert( data.erroreMezua );
					}
				} else { // Jasotako erantzunaren estatusa egokia ez bada, errore mezu bat erakutsi
					alert( '" . __( 'Erroreren bat gertatu da eskaeraren erantzuna jasotzean.', 'ohar-itzultzailea' ) . "' );
				}
			},
			error: function (xhr, status, error) {
				switch( status ) { // Eskaera egitean erroreren bat gertatzen bada mezu bat erakutsi, errore mota bakoitzerako ezberdina
					case 'timeout':
						alert( '" . __( 'Eskaera iraungitu egin da, martxan zeraman denbora luzea dela eta.', 'ohar-itzultzailea' ) . "' );
						break;
					case 'abort':
						alert( '" . __( 'Eskaera abortatu egin da.', 'ohar-itzultzailea' ) . "' );
						break;
					case 'parseError':
						alert( '" . __( 'Eskaeraren parametroak eskuratzean akats bat gertatu da.', 'ohar-itzultzailea' ) . "' );
						break;
					case 'error':
						alert( '" . __( 'Eskaerak errore bat itzuli du: ', 'ohar-itzultzailea' ) . "' + error );
						break;
				}
			},
		});
	} \n";
	
	/*
	*	Funtzio honekin egindako itzulpena gordetzen da editorean. Normalean, itzulpena gorde ondoren Publish atalean dagoen Update botoia sakatu behar da, itzulpen 
	*	berria editorean ikusi ahal izateko.
	*/
	echo "function itzulpenaGorde()
	{
		//Azken itzulpenari buruzko informazioa gordetzen duen aldagaian | banatzailea bilatu. Sortuko den bektorearen 0. posizioan 3 letrako hizkuntz kodea dago eta 
		//1.posizioan, berriz, itzuli den testu-mota
		var azkenItzulpenarenInformazioa = azkenItzulpena.split('|');
		//Egin den azken itzulpenaren bi letretako hizkuntz kodea lortu
		var biLetraKodea = azkenItzulpenarenInformazioa[0];
		if ( azkenItzulpenarenInformazioa[1] === 'izenburua' ) {	//Itzulitako azken testua izenburua bazen
			//Wordpress bidalketaren izenburua lortu, azken itzulpenaren hizkuntzan
			var izenburukoTestua = window.opener.document.getElementById( 'qtrans_title_' + biLetraKodea ).value;
			if ( izenburukoTestua != '' ) {	//Izenburua hutsik ez badago
				//Konfirmazio mezu bat sortu, erabiltzaileari jakinarazia lehendik ere badagoela hizkuntza horretan izenburua, eta ea gainidatzi nahi al duen
				var erantzuna = confirm('".__("Izenburuetan badago dagoeneko \'' + biLetraKodea + '\' hizkuntzarako izenburua. Izenburu hori ordeztu nahi al duzu?","ohar-itzultzailea")."');
				if ( erantzuna === false ) {	//Ezetz erantzuten badu
					return
				}
			}
			//Itzulpenaren testua lortu
			var izenburua = document.getElementById( 'helburuTestua' ).value;
			
			//Azken itzulpenaren izenburua gordetzen den tokian balio berria gorde
			window.opener.document.getElementById( 'qtrans_title_' + biLetraKodea ).value = izenburua;
			//QTranslate gehigarriaren funtzioari deitu, izenburuan gertatutako aldaketak gorde ditzan
			window.opener.qtrans_integrate_title();
			//Erabiltzaileari abisu bat eman prozesua ondo gauzatu dela esanez
			alert('".__("Izenburuaren itzulpena ondo bidali da. Eguneratu artikulua (Publish atalaren barruko \'Update\' botoia sakatuz) ondo gordetzen dela ziurtatzeko.","ohar-itzultzailea")."');
		} else {	//Itzulitako azken testua bidalketaren testua bazen
			//Editorean dagoen testua eskuratu
			var editorekoTestua = window.opener.document.getElementById( 'content' ).value;
			//Testuan azken itzulpenaren hizkuntzan testurik dagoen begiratzen da, hizkuntza horren hasierako mugatzailea bilatuz
			var lag = editorekoTestua.split( '<!--:' + biLetraKodea + '-->' );
			if ( lag[1] ) {	//Hizkuntza horretan testua lehendik badago
				//Konfirmazio mezu bat sortu, erabiltzaileari jakinarazia lehendik ere badagoela hizkuntza horretan testua, eta ea gainidatzi nahi al duen
				var erantzuna = confirm( '".__("Testu editorean badago dagoeneko \'' + biLetraKodea + '\' hizkuntzarako testua. Testu hori ordeztu nahi al duzu?","ohar-itzultzailea")."' );
				if ( erantzuna === true ) {	//Baietz erantzuten badu
					//Itzulpenaren testua lortu
					var helburuTestua = document.getElementById( 'helburuTestua' ).value;
					//Testuari hasiera eta bukaerako mugatzaileak gehitu
					var hizkuntzaMugatzailedunTestua = '<!--:' + biLetraKodea + '-->';
					if ( -1 !== lag[1].indexOf('[audio mp3' ) ) { // Dagoeneko audio etiketa bat badago, kendu, itzultzailea pasa ez dadin
						hasieraIndex = lag[1].indexOf( '[audio mp3' );
						bukaeraIndex = lag[1].lastIndexOf( '[/audio]' ) + 8;
						var audioEtiketa = lag[1].substr( hasieraIndex, bukaeraIndex );
						hizkuntzaMugatzailedunTestua = hizkuntzaMugatzailedunTestua.concat( audioEtiketa, '\\n', helburuTestua, '<!--:-->' );
					} else {
						hizkuntzaMugatzailedunTestua = hizkuntzaMugatzailedunTestua.concat( helburuTestua, '<!--:-->' );
					}
					//Hizkuntza horretan dagoen testu zaharraren bukaerako mugatzailearen bukaera bilatu
					var indizea = lag[1].indexOf( '<!--:-->' ) + 8;
					//Testu zaharra kenduta editorean dagoen testuari itzulpenaren testua lotu
					editorekoTestua = lag[0].concat( hizkuntzaMugatzailedunTestua, lag[1].substring( indizea ) );
				} else {	//Ezetz esaten badu, funtziotik atera
					return;
				}
			} else {	//Hizkuntza horretan testua lehendik ez badago
				//Itzulpenaren testua lortu
				var testua = document.getElementById( 'helburuTestua' ).value;
				//Testuari hasiera eta bukaerako mugatzaileak gehitu
				var lag = '&lt;!--:' + biLetraKodea + '--&gt;';
				lag = lag.concat( testua, '&lt;!--:--&gt;' );
				//Mugatzailedun itzulpena editoreko testuari gehitu
				editorekoTestua=editorekoTestua.concat( lag );
			}
			
			//Editoreko testuaren balioa eguneratu
			window.opener.document.getElementById( 'content' ).innerHTML = editorekoTestua;
			//Erabiltzaileari abisu bat eman prozesua ondo gauzatu dela esanez
			alert( '".__("Itzulpena ondo bidali da editorera. Eguneratu artikulua (Publish atalaren barruko \'Update\' botoia sakatuz) ondo gordetzen dela ziurtatzeko. Gogoratu audioa sortu edota eguneratzeaz, hala nahi izatekotan. Eguneratzean agertzen ez bada, ziurtatu editoreko \'Text\' aukeran zaudela eta saiatu berriro.","ohar-itzultzailea")."' );
		}
	} \n";
	
	
	/*
	*	Funtzio honek testu kutxen tamaina moldatzen du, barruan duten testuaren arabera, Funtzio hau tekla bat zapaltzen den bakoitzean, editoreko testua erakustean
	*	edota itzulpena erakustean deitzen da. Parametro gisa, moldatu behar den testu kutxaren izen deskriptiboa ematen da. Moldaketak muga bat dauka, bestela, testu
	*	asko dagoenean testu kutxa handiegia izango litzateke eta, erabiltzailearentzat deserosoa litzatekena.
	*/
	echo "function tamainaMoldatu(testuKutxa)
	{
		switch (testuKutxa)
		{
			case 'jatorria':	//Jatorri testu kutxa moldatu behar bada
				//Jatorri testu kutxaren instantzia lortu
				var jatorriTestuKutxa=document.getElementById('jatorriTestua');
				//Testu kutxaren altuera balio lehenetsira ezarri(beharrezkoa da aldaketak eragina izateko)
				jatorriTestuKutxa.style.height = 'auto';
				if(jatorriTestuKutxa.scrollHeight<294)	//Testu kutxa ezarritako muga(15 bat lerro) baino baxuagoa bada
				{
					//Testu kutxaren altuera handitu, duen altueraren arabera
					jatorriTestuKutxa.style.height = jatorriTestuKutxa.scrollHeight + 10 + 'px';
				}
				else	//Ezarritako muga(15 bat lerro) baino handiagoa bada
				{
					//Testu kutxari 304 pixeletako altuera ezarri
					jatorriTestuKutxa.style.height = 304+ 'px';
				}
				break;
			case 'helburua':	//Helburu testu kutxa moldatu behar bada
				//Helburu testu kutxaren instantzia lortu
				var helburuTestuKutxa=document.getElementById('helburuTestua');
				//Testu kutxaren altuera balio lehenetsira ezarri(beharrezkoa da aldaketak eragina izateko)
				helburuTestuKutxa.style.height = 'auto';
				if(helburuTestuKutxa.scrollHeight<294)	//Testu kutxa ezarritako muga(15 bat lerro) baino baxuagoa bada
				{
					//Testu kutxaren altuera handitu, duen altueraren arabera
					helburuTestuKutxa.style.height = helburuTestuKutxa.scrollHeight + 10 + 'px';
				}
				else	//Ezarritako muga(15 bat lerro) baino handiagoa bada
				{
					//Testu kutxari 304 pixeletako altuera ezarri
					helburuTestuKutxa.style.height = 304+ 'px';
				}
				break;
		}
	} \n";
	
	
	/*
	*	Funtzio honek bidalketa/orrialdearen testu eta izenburuak itzultzen ditu, modu automatiko batean, interfazerik gabe. Jatorri hizkuntza gisa,
	*	ingelesa hartzen da eta hizkuntz guztietara itzuliko du euskara eta gaztelaniara izan ezik, hizkuntz horietan gure testu propioak
	*	izango ditugu eta
	*/
	echo "function itzulpenAutomatikoakEgin( idZenbakia ) {
		var hasieraIndex, bukaeraIndex, lag, lag2, ingelesezkoTestua, ingelesezkoIzenburua,
			editorekoTestua = document.getElementById('content').value,
			hizkuntzak = [];
			
		// Deia martxan dagoen bitartean kurtsorea itxaroten jarri
		document.body.style.cursor = 'wait';
		
		// Ingelesezko testuaren etiketa bilatu
		lag = editorekoTestua.split('<!--:en-->');
		if ( lag[1] ) { // Etiketa topatu bada, itzulpenak egin daitezke
			lag2 = lag[1].split('<!--:-->');
			ingelesezkoTestua = lag2[0];
			if ( -1 !== ingelesezkoTestua.indexOf('[audio mp3' ) ) { // Dagoeneko audio etiketa bat badago, kendu, Google Translatek itzuli ez dezan
				hasieraIndex = ingelesezkoTestua.indexOf( '[audio mp3' );
				bukaeraIndex = ingelesezkoTestua.lastIndexOf( '[/audio]' ) + 8;
				ingelesezkoTestua = ingelesezkoTestua.substr( 0, hasieraIndex ).concat( ingelesezkoTestua.substr( bukaeraIndex, ingelesezkoTestua.length ) );
			}
			if ( -1 !== ingelesezkoTestua.indexOf('<a href' ) ) { // Dagoeneko <a> etiketa bat badago, kendu, Google Translatek itzuli ez dezan
				hasieraIndex = ingelesezkoTestua.indexOf( '<a href' );
				bukaeraIndex = ingelesezkoTestua.lastIndexOf( '/></a>' ) + 6;
				ingelesezkoTestua = ingelesezkoTestua.substr( 0, hasieraIndex ).concat( ingelesezkoTestua.substr( bukaeraIndex, ingelesezkoTestua.length ) );
			}
			ingelesezkoIzenburua = document.getElementById( 'qtrans_title_en' ).value.charAt(0) + document.getElementById( 'qtrans_title_en' ).value.slice(1).toLowerCase();
		} else {
			// Mezua erakutsi ingelesa hutsik dagoela esanez
			alert( '" . __( "Ezin izan dira itzulpenak egin, ingelesezko testua hutsik dagoelako", "ohar-itzultzailea" ) . "' );
			return;
		}";
		
		// Iterazio hauen bidez itzuli behar diren hizkuntzak zehaztu
		foreach ( $q_config['enabled_languages'] as $language ) { //Webgunea ezarrita dagoen hizkuntz bakoitzeko
			echo "if ( 'es' !== '" . $language . "' && 'en' !== '" . $language . "' && 'eu' !== '" . $language . "' ) {
				// Master hizkuntza ( eu, en, es) bat ez bada, bektorean gorde
				hizkuntzak.push( '" . $language . "' );
			}";
		}	
			
		// Itzulpena egiteko erabiltzen den AJAX deia
		echo "var xmlHttp = jQuery.ajax({
			type : 'POST',
			url : ajaxurl,
			async : true,
			datatype : 'xml',
			data : { 'action' : 'testuarenItzulpenAutomatikoaEgin',
					'helburuHizkuntzak[]' : hizkuntzak,
					'jatorriTestua' : ingelesezkoTestua,
					'jatorriIzenburua' : ingelesezkoIzenburua,
				},
			timeout : 30000,
			success : function ( data, status, jqXHR ) {
				if ( 'success' === status ) { // Jasotako erantzunaren egoera arrakastazkoa bada
					var hizkuntza, audioEtiketa,  testuSplitHasiera, testuSplitBukaera, testuZaharra, testuBerria, hasieraIndex, bukaeraIndex,						erabiltzaileariMezua = '',
						itzulpenArrakastatsurik = false,
						gTAtribuzioa = '<a href = \"http://translate.google.com\" title = \"Google Translate\"><img src = \"http://oharrak.albaola.com/wp-content/plugins/ohar-itzultzailea/irudiak/google-translate-logoa.png\" class = \"aligncenter\" alt = \"Google Translate logo\" /></a>',
						testua = document.getElementById('content').value;
						
					// Bukle honetan hizkuntz bakoitzaren arabera lan egingo da, bere itzulpenak prozesatuz 
					for ( var k = 0, m = data.length; k < m; k++) {
						hizkuntza = data[k].hizkuntza;
						if ( typeof data[k].testua != 'undefined') { // Definituta badago, Bidalketa/orrialdearen testua itzuli da
							itzulpenArrakastatsurik = true;
							testuSplitHasiera = testua.split( '<!--:' + hizkuntza + '-->' );
							if ( testuSplitHasiera[1] ) { // Editoreko testuan uneko hizkuntzaren testua topatu
								testuSplitBukaera = testuSplitHasiera[1].split( '<!--:-->' );
								testuZaharra = testuSplitBukaera[0];
								audioEtiketa = '';
								if ( -1 !== testuZaharra.indexOf( '[audio mp3' ) ) { // Dagoeneko audio etiketa bat badago, kendu
									hasieraIndex = testuZaharra.indexOf( '[audio mp3' );
									bukaeraIndex = testuZaharra.lastIndexOf( '[/audio]' ) + 8;
									audioEtiketa = testuZaharra.substr( hasieraIndex, bukaeraIndex ).trim();
								}
								testuEtiketarenHasiera = testua.indexOf( '<!--:' + hizkuntza + '-->' );
								testuEtiketarenBukaera = testua.indexOf( '<!--:-->', testuEtiketarenHasiera ) + 8;
								testuBerria = '<!--:' + hizkuntza + '-->';
								if ( '' === audioEtiketa  ) { // Audio etiketarik ez badago, aldagai hori ez erabili hizkuntzaren testua osatzeko
									testuBerria = testuBerria.concat( data[k].testua, gTAtribuzioa, '<!--:-->' );
									// alert(testuBerria);
								} else {
									testuBerria = testuBerria.concat( audioEtiketa, '\\n', data[k].testua, gTAtribuzioa, '<!--:-->' );
									// alert(testuBerria);
								}
								testua = testua.substr( 0, testuEtiketarenHasiera ).concat( testuBerria, testua.substr( testuEtiketarenBukaera, testua.length ));
							} else { // Hizkuntza ez dago lehendik, zuzenean testuaren bukaerara gehitu
								testuBerria = '<!--:' + hizkuntza + '-->';
								testuBerria = testuBerria.concat( data[k].testua, gTAtribuzioa, '<!--:-->' );
								testua = testua.concat( testuBerria );
							}
						}
						if ( typeof data[k].izenburua != 'undefined' ) { // Definituta badago, Bidalketa/orrialdearen izenburua itzuli da
							itzulpenArrakastatsurik = true;
							document.getElementById( 'qtrans_title_' + hizkuntza ).value = data[k].izenburua.toUpperCase();
							qtrans_integrate_title();
						} 
						if ( typeof data[k].erroreMezua != 'undefined') {
							// Errorea gertatu denez erabiltzaileari errore mezu bat erakutsik zaio, hizkuntza eta gertatu den errorea adieraziz
							erabiltzaileariMezua += '" . __( 'Errorea gertatu da itzulpena egitean. Hizkuntza: ', 'ohar-itzultzailea' ) . "' + data[k].hizkuntza + '. " . __( 'Errore mezua: ', 'ohar-itzultzailea' ) . "' + data[k].erroreMezua + '\\n \\n';
						}
					}
					testua = testua.replace( /<\/ strong>/gi, '</strong>' );
					// testua = testua.replace( '</ STRONG>', '</strong>' );
					
					// Editoreko testua eguneratu
					document.getElementById( 'content' ).value = testua;
				
					if ( '' === erabiltzaileariMezua ) { // Hutsik badago, ez da errorerik egon
						erabiltzaileariMezua = '" . __( 'Itzulpenak ondo burutu dira. Mesedez, eguneratu Bidalketa/Orrialdea (Publish atalaren barruko \\"Update\\" botoia sakatuz) aldaketak ondo gordetzen direla ziurtatzeko.', 'ohar-itzultzailea' ) . "';
					} else {
						if ( true === itzulpenArrakastatsurik ) { // Errorerik egon bada baina itzulpen batenbat arrakastaz egin bada, bukaerara gehitu
							erabiltzaileariMezua += '" . __( 'Gainontzeko itzulpenak arrakastaz burutu dira. Errorea itzuli duten hizkuntzak itzultzeko, erabili interfazedun itzultzailea. Gogoratu ondo burututako itzulpenak gordetzeko bidalketa/orrialdea eguneratu behar duzula.', 'ohar-itzultzailea' ) . "';
						}
					}
					// Erabiltzailerari mezua erakutsi
					alert( erabiltzaileariMezua );
				} else { // Jasotako erantzunaren estatusa egokia ez bada, errore mezu bat erakutsi
					alert( '" . __( 'Erroreren bat gertatu da eskaeraren erantzuna jasotzean.', 'ohar-itzultzailea' ) . "' );
				}
				
				// Kurtsorea defektuz egoerara pasatu
				document.body.style.cursor = 'auto';
			},
			error: function (xhr, status, error) {
				switch( status ) { // Eskaera egitean erroreren bat gertatzen bada mezu bat erakutsi, errore mota bakoitzerako ezberdina
					case 'timeout':
						alert( '" . __( 'Eskaera iraungitu egin da, martxan zeraman denbora luzea dela eta.', 'ohar-itzultzailea' ) . "' );
						break;
					case 'abort':
						alert( '" . __( 'Eskaera abortatu egin da.', 'ohar-itzultzailea' ) . "' );
						break;
					case 'parseError':
						alert( '" . __( 'Eskaeraren parametroak eskuratzean akats bat gertatu da.', 'ohar-itzultzailea' ) . "' );
						break;
					case 'error':
						alert( '" . __( 'Eskaerak errore bat itzuli du: ', 'ohar-itzultzailea' ) . "' + error );
						break;
				}
			},
		});
	} \n";
	
	//
	//	Itzultzailearen leihoa ireki eta ixteko eragiketak
	//
	
	/*
	*	Funtzio honen bidez itzultzailearen leihoa irekitzen da
	*/
	echo "function leihoaIreki(urla,izena)
	{
		window.open(urla,izena);
	} \n";
	
	/*
	*	Funtzio honek itzultzailearen liehoa ixten du. Bi egoeratan gerta daiteke: erabiltzaileak leihoa itxi nahi izatea edota itzulpena gordetzeaz gain leiho itxi 
	*	nahi izatea. Bi kasuen artean ezberdintzeko karaktere kate bat jasotzen da parametro bezala
	*/
	echo "function leihoaItxi(ekintza)
	{
		switch(ekintza)
		{
			case 'gorde':	//Erabiltzaileak itzulpena gorde eta leihoa itxi nahi ditu
				itzulpenaGorde();
				//break- jarrita ez dagoenez hurrengo kasura pasako da
			case 'itxi':	//Erabiltzaileak leihoa itxi nahi badu
				window.close();
				break;
		}
	} \n";
	
	
	//
	// Bidalketa/Orrialdearen testuaren audio lortzeko
	//
	
	
	
	/*
	*	Funtzio hau testuaren audio etiketak kentzeko erabiltzen da, etiketa horiek audio edo a ez badira. Izan ere, eragiketa honek etiketak kentzen
	*	ditu, baina ez barruko edukia. Helburua da VoiceRSS-ek strong, em motako etiketak ez ahoskatzea, baina bai barruko testua. Audio eta a
	*	etiketen kasuan ez gaude interesatuta etiketa horien barruko edukian.
	*/
	echo "function etiketakKendu( etiketadunTestua ) {
		var bukaeraIndex, 
			testuGarbia = '',
			lag = etiketadunTestua.split('<');
		if ( lag.length > 1 ) { // < karakterea topatu baldin bada
			// Karakterearen aurretik zegoen testua gorde, ahoskatu beharrekoa da eta
			testuGarbia = lag[0];
			for ( var i = 0, j = lag.length; i < j; i++ ) { // Topatu den '<' karaktere bakoitzeko iterazioa
				if ( -1 !== lag[i].indexOf('>' ) ) { // '>' karakterea topatu bada, beste '<' karaktere bat topatu aurretik 
													//		(hori lag bektorearen hurrengo posizioan legoke)
					// Etiketaren bukaera bilatu								
					bukaeraIndex = lag[i].indexOf( '>' ) + 1;
					// testuGarbia aldagaian etiketaren barruko edukia erantsi
					testuGarbia += lag[i].substr( bukaeraIndex );
				}
			}
			// Etiketarik gabeko testua itzuli
			return testuGarbia;
		} else { // Etiketarik ez duenez, jasotako testu bera itzuli
			return etiketadunTestua;
		}
	} \n";
	
	/*
	*	Funtzio hau bidalketa/orrialdearen testuaren audioa sortzeko egin beharreko AJAX eskaera burutzeaz arduratzen da, baita erantzun hori 
	*	prozesatu ere.
	*/
	echo "function audioaSortu( idZenbakia ) {
		var hasieraIndex, bukaeraIndex,
			lag = document.getElementById('content').value.split('<!--:'),
			testuak = [];
			
		// Deia martxan dagoen bitartean kurtsorea itxaroten jarri
		document.body.style.cursor = 'wait';
		
		// Bukle honek editoreko hizkuntzaren testua hartu, hizkuntzaren arabera sakabanatu eta testutik audioaren etiketak kentzen ditu, 
		// 	AJAX eskaera egiteko parametro gisa ibiliko den testua lortzeko
		for ( var i = 0, j = lag.length; i < j; i++ ) {
			if ( 0 === lag[i].search( '[a-z]{2}-->' ) ) { // Hizkuntz berri baten testuaren hasiera da, testua eta hizkuntza lortu
				if ( -1 !== lag[i].indexOf('[audio mp3' ) ) { // Dagoeneko audio etiketa bat badago, kendu, Google Translatek ahoska ez dezan
					hasieraIndex = lag[i].indexOf( '[audio mp3' );
					bukaeraIndex = lag[i].lastIndexOf( '[/audio]' ) + 8;
					lag[i] = lag[i].substr( 0, hasieraIndex ).concat( lag[i].substr( bukaeraIndex, lag[i].length ) );
				}
				if ( -1 !== lag[i].indexOf('<a href' ) ) { // Dagoeneko Google atribuzio etiketa bat badago, kendu, Google Translatek ahoska ez dezan
					hasieraIndex = lag[i].indexOf( '<a href' );
					bukaeraIndex = lag[i].lastIndexOf( '/></a>' ) + 6;
					lag[i] = lag[i].substr( 0, hasieraIndex ).concat( lag[i].substr( bukaeraIndex, lag[i].length ) );
				}
				// Uneko hizkuntzaren kodea eta testua aldagai batean gorde, gero testuak izeneko bektorearen bukaeran txertatzeko
				var hizkuntzBerria = {
					hizkuntzKodea : lag[i].substring( 0, 2 ),
					testua : etiketakKendu( lag[i].substring( 5 ) ),
				};
				// alert( etiketakKendu( lag[i].substring( 5 ) ) );
				testuak.push( JSON.stringify( hizkuntzBerria ) );
			} else { // Hizkuntz baten testuaren amaiera da, hurrengo iteraziora pasatu
				continue;
			}
		}
		// Iraungitze denbora kendu zaio, bestela audioa sortzean iraungitu egiten da eta. Berriz jarri behar bada->   timeout : 60000,
		// Audioa sortzeko erabiltzen den AJAX deia
		var xmlHttp = jQuery.ajax({
			type : 'POST',
			url : ajaxurl,
			async : true,
			datatype : 'xml',
			data : { 'action' : 'audioaSortu',
					'testuak[]' : testuak,
					'id' : idZenbakia },
			success : function ( data, status, jqXHR ) {
				if ( 'success' === status ) { // Jasotako erantzunaren egoera arrakastazkoa bada
					var hizkuntza, testuSplitHasiera, testuSplitBukaera, testuZaharra, testuBerria, audioaLehendik, hasieraIndex, bukaeraIndex, testuLag,
						erabiltzaileariMezua = '',
						audioaArrakastaz = false,
						audioBerria = [],
						audioEguneratua = [],
						testua = document.getElementById('content').value;
						
					// Bukle honetan hizkuntz bakoitzaren arabera lan egingo da. Gainera, hizkuntzaren batean errorerik gertatu bada, erabiltzaileari emango zaion mezua prestatuko da
					for ( var k = 0, m = data.length; k < m; k++) {
						if ( typeof data[k].erroreMezua == 'undefined') { // Definituta badago, hizkuntz honi audioa sortu/eguneratu zaio
							hizkuntza = data[k].hizkuntza;
							testuSplitHasiera = testua.split('<!--:'+hizkuntza+'-->');
							if ( testuSplitHasiera[1] ) { // Editoreko testuan uneko hizkuntzaren testua topatu
								testuSplitBukaera = testuSplitHasiera[1].split('<!--:-->');
								testuZaharra = testuSplitBukaera[0];
								testuLag = testuZaharra;
								if ( -1 !== testuZaharra.indexOf('[audio mp3' ) ) { // Dagoeneko audio etiketa bat badago, kendu
									hasieraIndex = testuZaharra.indexOf( '[audio mp3' );
									bukaeraIndex = testuZaharra.lastIndexOf( '[/audio]' ) + 8;
									testuLag = testuZaharra.substr( 0, hasieraIndex ).concat( testuZaharra.substr( bukaeraIndex, testuZaharra.length ) ).trim();
								}
								if ( typeof data[k].mp3 != 'undefined' && typeof data[k].ogg != 'undefined' ) { // Definituta badago, audioa sortu berri zaio
									testuBerria = '[audio mp3=\"' + data[k].mp3 + '\" ogg=\"' + data[k].ogg + '\" preload=\"auto\"][/audio]' + '\\n' + testuLag;
								} else if ( typeof data[k].mp3 != 'undefined' ) {
									testuBerria = '[audio mp3=\"' + data[k].mp3 + '\" preload=\"auto\"][/audio]' + '\\n' + testuLag;
								} else if ( typeof data[k].ogg != 'undefined' ) {
									testuBerria = '[audio ogg=\"' + data[k].ogg + '\" preload=\"auto\"][/audio]' + '\\n' + testuLag;
								}
								testua = testua.replace( testuZaharra, testuBerria );
								audioaArrakastaz = true;
							}
						} else {
							// Errorea gertatu denez erabiltzaileari errore mezu bat erakutsik zaio, hizkuntza eta gertatu den errorea adieraziz
							erabiltzaileariMezua += '" . __( 'Errorea gertatu da audioa sortzean. Hizkuntza: ', 'ohar-itzultzailea' ) . "' + data[k].hizkuntza + '. " . __( 'Errore mezua: ', 'ohar-itzultzailea' ) . "' + data[k].erroreMezua + '\\n \\n';
						}
					}
					
					// Editoreko testua eguneratu
					document.getElementById('content').value = testua;
					if ( true === audioaArrakastaz && '' === erabiltzaileariMezua ) {
						erabiltzaileariMezua = '" . __( 'Audioa arrakastaz sortu da. ', 'ohar-itzultzailea' ) . __('Eguneratu Bidalketa/Orrialdea (Publish atalaren barruko \\"Update\\" botoia sakatuz) aldaketak ondo gordetzen direla ziurtatzeko.', 'ohar-itzultzailea' ) . "';
					} else if ( '' !== erabiltzaileariMezua ) {
						erabiltzaileariMezua += '" . __( 'Gainontzeko hizkuntzatan audioa arrakastaz sortu da. ', 'ohar-itzultzailea' ) . __('Eguneratu Bidalketa/Orrialdea (Publish atalaren barruko \\"Update\\" botoia sakatuz) aldaketak ondo gordetzen direla ziurtatzeko.', 'ohar-itzultzailea' ) . "';
					}
					// Erabiltzailerari mezua erakutsi
					alert( erabiltzaileariMezua );
				} else { // Jasotako erantzunaren estatusa egokia ez bada, errore mezu bat erakutsi
					alert( '" . __( 'Erroreren bat gertatu da eskaeraren erantzuna jasotzean.', 'ohar-itzultzailea' ) . "' );
				}
				
				// Kurtsorea defektuz egoerara pasatu
				document.body.style.cursor = 'auto';
			},
			error: function (xhr, status, error) {
				switch( status ) { // Eskaera egitean erroreren bat gertatzen bada mezu bat erakutsi, errore mota bakoitzerako ezberdina
					case 'timeout':
						alert( '" . __( 'Eskaera iraungitu egin da, martxan zeraman denbora luzea dela eta.', 'ohar-itzultzailea' ) . "' );
						break;
					case 'abort':
						alert( '" . __( 'Eskaera abortatu egin da.', 'ohar-itzultzailea' ) . "' );
						break;
					case 'parseError':
						alert( '" . __( 'Eskaeraren parametroak eskuratzean akats bat gertatu da.', 'ohar-itzultzailea' ) . "' );
						break;
					case 'error':
						alert( '" . __( 'Eskaerak errore bat itzuli du: ', 'ohar-itzultzailea' ) . "' + error );
						break;
				}
				document.body.style.cursor = 'auto';
			},
		});
	} \n";
	
	echo "//-->";
