<?php
 
	/*
	*	Plugin Name: Ohar Itzultzailea
	*	Description: Ohareleanitzak.esy.es webgunean eduki eleanitza automatikoki sortzeko erabiltzen den WordPress-erako gehigarria. Horretaz gain, webguneko bidalketen QR kodea eskuratzeko aukera ere ematen du-
	*	Version: 1.0
	*	Author: Manex Garaio Mendizabal
	*	Text Domain: ohar-itzultzailea
	*/
	
	 
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
	*	Fitxategi honen kodearekin eduki editorean botoi berri bat gehitzen da gehigarria ireki ahal izateko, botoi berri bat sortzen da bidalketa bakoitzaren QR kodea
	*	lortu ahal izateko eta horretaz gain gehigarriak behar dituen funtzioak ere kargatzen dira.
	*/
	
	define( 'OI_PLUGIN_KOKAPENA', plugin_dir_path( __FILE__ ) );
	
	define( 'GT_GAKOA' , '' );
	
	define( 'VR_GAKOA', '' );
	
	require_once ( OI_PLUGIN_KOKAPENA . 'functions.php' );
	
	
	//
	//	Gehigarriaren hasieraketa
	//
	
	
	
	
	/*
	*	Funtzio honen bidez gehigarria hizkuntza ezberdinetan egotea ahalbidetzen da. Funtzio honek gehigarriaren karaktere-kateen bertsio ezberdinak kargatzen ditu, 
	*	webgunearen hizkuntzaren arabera.	
	*/
	function oi_itzulpenakKargatu()
	{
		$plugin_dir = basename( dirname(__FILE__) );
		$emaitza = load_plugin_textdomain( 'ohar-itzultzailea', false, $plugin_dir.'/hizkuntzak/' );
	}
	
	//Lerro honekin gehigarriak kargatzen direnean oi_itzulpenakKargatu funtzioari deituko zaio, itzulpenak kargatu ditzan
	add_action( 'plugins_loaded', 'oi_itzulpenakKargatu' );
	
	/*
	*	Funtzio honek Ohar Itzultzailea gehigarriak behar dituen 'script'ak kargatzen ditu, estilo orriarekin batera. Beharrezkoak diren 'script'ak 
	*	kargatzeko Wordpress-en wp_enqueue_script funtzioa erabiltzen da, derrigorrezko parametro bezala 'script'ari emango zaion izena eta kokapena izanik. 
	*	wp_enqueue_style funtzioak berdin funtzionatzen du, baina estilo orriak kargatzeko erabiltzen da.
	*/
	function oi_funtzioakKargatu()
	{
		//JQuery eta Json liburutegiak kargatzeko
		wp_enqueue_script( "jqueryMin", plugins_url( '/ohar-itzultzailea/liburutegiak/jquery.min.js' ) );
		wp_enqueue_script( "jqueryJsonp", plugins_url( '/ohar-itzultzailea/liburutegiak/jquery.jsonp-2.4.0.min.js' ) );		
		
		//Nik garatutako funtzioak kargatzeko lerroa. Derrigorrezko balioez gain jSFuntzioak scriptak dauzkan dependentziak zehazten dira
		wp_enqueue_script( "nire-funtzioak", plugins_url( '/ohar-itzultzailea/jsFuntzioak.php' ), array('jqueryMin','jqueryJsonp') );
		//Gehigarriarentzat garatutako estilo-orria kargatzeko
		wp_enqueue_style( "nire-estiloa", plugins_url( '/ohar-itzultzailea/estilo-orria.css' ) );
		
		wp_enqueue_script( 'admin_url', get_template_directory_uri() . '/js/myajax.js', array( 'jquery', 'wp-ajax-response' ) );
		//wp_enqueue_script( "ttsEskuratzailea", plugins_url( '/ohar-itzultzailea/TTSEskuratzailea.php' ) );
	}
	
	//Lerro honen bidez zehazten da oi_funtzioakKargatu funtzioari deitu behar zaiola orrialdea hasieratzen denean(init gertaera)
	add_action( 'init', 'oi_funtzioakKargatu' );
	
	
	
	
	//
	//	Editorean gehitu beharreko botoiak
	//
	
	
	
	
	/*
	*	Funtzio honen bidez eduki editoreak dituen aukerei aukera berri bat gehitzen zaio, Ohar Itzultzailea gehigarriaren funtzionalitateak irekiko dituena
	*/
	function oi_itzultzaileBotoiaGehitu() {
		global $pagenow;
		if ( $pagenow === 'post.php' ) {
			global $q_config;
			
			/*Itzulpenak egiten dituen orrialdea fitxa berri batean irekitzen da. Hurrengo bi lerroekin leihoaren izena(botoia zapaltzen den bakoitzean fitxa berri bat 
			*	ireki ordez lehen sortutakoa erabil dezan) eta ireki beharreko helbidea zehazten dira
			*/
			$urla = '"'.get_bloginfo("wpurl").'/wp-content/plugins/ohar-itzultzailea/itzulpen-orria.php"';
			$izena = '"'.__("Ohar Itzultzailea","ohar-itzultzailea").'"';
			
			$visualMartxan = get_option( 'oi_visualMartxan' );
			if ( 'bai' === $visualMartxan ) {
				//Honen bidez editorean gehituko den botoi berria definitzen da, itzultzailea ezgaituta dagoela adierazten duena
				echo "<img id = 'itzultzaileEzgaitua' 
					src = '".get_bloginfo("wpurl")."/wp-content/plugins/ohar-itzultzailea/irudiak/itzuli-botoia-ezgaitua.png'
					class = 'thickbox'
					title = '".__('Ohar Itzultzailea ezin da ireki, editoreko "Visual" aukera hautatuta dagoelako. Hautatu "Text" aukera eta eguneratu orrialdea aldaketak eragina izan dezan.','ohar-itzultzailea')."'
					alt = '".__('Itzultzailea eskuragarri ez dagoela irudikatzen duen irudia','ohar-itzultzailea')."'
					onclick ='itzultzaileaEzgaituta()'
					style='cursor: pointer;'/>";
			} else {
				//Honen bidez editorean gehituko den botoi berria definitzen da, itzultzailea abiarazteko erabiliko dena
				echo "<img id = 'itzultzaileBotoia' 
					src = '".get_bloginfo("wpurl")."/wp-content/plugins/ohar-itzultzailea/irudiak/itzuli-botoia.png'
					class = 'thickbox'
					title = '".__('Ohar Itzultzailea irekitzeko','ohar-itzultzailea')."'
					alt = '".__('Itzulpena abian jartzeko erabiltzen den botoia irudikatzen duen irudia','ohar-itzultzailea')."'
					onclick = 'leihoaIreki($urla,$izena)' style='cursor: pointer;'/>";
			}
		}
	}
	
	/*
	*	oi_itzultzaileBotoiaGehitu funtzioari deituko dio media_buttons gertaeran (gertaera honetan multimediarekin lotutako botoia gehitzen dira, dagokien eremuan).
	*	0 balioak funtzio honek beste batzuen gainean duen lehentasuna zehazten du. Balioak botoiak izango duen kokapena ere baldintzatzen du.
	*/
	add_action( 'media_buttons', 'oi_itzultzaileBotoiaGehitu', 0 );
	

	function oi_itzultzaileAutomatikoBotoiaGehitu() {
		global $pagenow;
		if ( $pagenow === 'post.php' ) {
			global $q_config;
			global $post;
			$id = $post->ID;
			
			echo "<img id = 'itzultzaileAutomatikoBotoia' 
				src = '".get_bloginfo("wpurl")."/wp-content/plugins/ohar-itzultzailea/irudiak/itzuli-botoi-automatikoa.png'
				class = 'thickbox'
				title = '".__('Itzulpen guztiak batera modu automatikoan egiteko','ohar-itzultzailea')."'
				alt = '".__('Itzulpen automatikoak abian jartzeko erabiltzen den botoia irudikatzen duen irudia','ohar-itzultzailea')."'
				onclick = 'itzulpenAutomatikoakEgin(" . $id . ")' style='cursor: pointer;'/>";
		}
	}
	
	/*
	*	oi_itzultzaileAutomatikoBotoiaGehitu funtzioari deituko dio media_buttons gertaeran (gertaera honetan multimediarekin lotutako botoia gehitzen dira, dagokien eremuan).
	*	1 balioak funtzio honek beste batzuen gainean duen lehentasuna zehazten du. Balioak botoiak izango duen kokapena ere baldintzatzen du.
	*/
	add_action( 'media_buttons', 'oi_itzultzaileAutomatikoBotoiaGehitu', 1 );
	
	/*
	*	Funtzio honek multimediaren botoiak dauden tokian beste botoi bat gehituko du, testuaren audioa sortzeko erabiliko dena
	*/
	function oi_audioSortzaileBotoiaGehitu() {
		global $pagenow;
		if ( $pagenow === 'post.php' ) {
			global $q_config;
			global $post;
			$id = $post->ID;
			
			//Honen bidez editorean gehituko den botoi berria definitzen da, audioa sortzeko erabiliko dena
			echo "<img id = 'audioSortzaileBotoia' 
				src = '".get_bloginfo("wpurl")."/wp-content/plugins/ohar-itzultzailea/irudiak/audio-sortzaile-botoia.png'
				class = 'thickbox'
				title = '".__('Testuaren audioa sortzeko','ohar-itzultzailea')."'
				alt = '".__('Testuaren audioa sortzea martxan jartzeko erabiltzen den irudia.','ohar-itzultzailea')."'
				onclick = 'audioaSortu(" . $id . ");' style='cursor: pointer;'/>";
		}
	}	
	
	/*
	*	oi_audioSortzaileBotoiaGehitu funtzioari deituko dio media_buttons gertaeran (gertaera honetan multimediarekin lotutako botoia gehitzen dira, dagokien eremuan).
	*	30 balioak funtzio honek beste batzuen gainean duen lehentasuna zehazten du. Balioak botoiak izango duen kokapena ere baldintzatzen du.
	*/
	add_action( 'media_buttons', 'oi_audioSortzaileBotoiaGehitu', 40);
	
	
	
	
	
	//
	//	AJAX deiak
	//
	
	
	
	
	
	/*
	*	Funtzio honek editorean Visual aukera hautatuta dagoen kudeatzeko erabiltzen den AJAX deiari erantzuten dio
	*/
	function oi_visualKudeatzailea() {
		if ( isset( $_POST['visualMartxan'] ) ) { // visualMartxan aldagaiak balioa badu
			$visualMartxan = sanitize_text_field( $_POST['visualMartxan'] );
			if ( 'bai' === $visualMartxan || 'ez' === $visualMartxan ) { // $visualMartxan aldagaiak balio egokia badu, datu basean gorde
				$arrakasta = update_option( 'oi_visualMartxan', $visualMartxan );
			} else {
				return;
			}
		}
	}
	
	// Gertaera honen bidez visualKudeatzailea ekintzari AJAX deia egitean oi_visualKudeatzailea funtzioa exekutatuko da
	add_action( 'wp_ajax_visualKudeatzailea', 'oi_visualKudeatzailea' );
	
	/*
	*	Funtzio honen bidez Google Translateren list aukerari deia egingo zaio. AJAX deiaren parametro bakarra hizkuntz izenak izan nahi ditugun
	*	hizkuntzaren kodea jasoko du.
	*/
	function oi_itzulpenBikoteakLortu() {
		if ( isset( $_POST['hizkuntza'] ) ) { 
			$hizkuntza = sanitize_text_field( $_POST['hizkuntza'] );
			// HTTP deiarentzat behar den helbidea. Key derrigorrezkoa da APIan. Target jartzen da hizkuntz kodea jasotzeaz gain izena lortzeko, 
			// izen horretan
			$helbidea = 'https://www.googleapis.com/language/translate/v2/languages?target=' . $hizkuntza . '&key=' . GT_GAKOA;
			
			// Google Translate zerbitzuari egingo zaion deia zehaztu: helbidea, erantzuna inprimatu ordez aldagai batean gordetzea
			$ch = curl_init( $helbidea );
			if ( false === $ch) {
				wp_send_json( __( 'ERROREA: Ezin izan da Google Translateri egin beharreko deia ireki, hizkuntzak zerrendatzea eskatzean.', 'ohar-itzultzailea' ) );
				return;
			}
			// Honen bidez zehazten da erantzuna aldagai moduan gordeko dela, alegia, ez du inprimatuko
			$arrakasta = curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
			if ( false === $arrakasta ) {
				wp_send_json( __( 'ERROREA: Ezin izan da Google Translateri egin beharreko deiaren konfigurazioa ezarri, hizkuntzak zerrendatzea eskatzean.', 'ohar-itzultzailea' ) );
				return;
			}
			// Deiaren exekuzioa, erantzuna JSON formatuan izango da
			$emaitzaGordina = curl_exec( $ch );
			// Egindako deiaren HTTP kodea lortu, dena ondo joan al den ikusteko
			$erantzunKodea = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
			// Curl konexioa itxi
			curl_close( $ch );
			// JSON erantzuna bektore asoziatibo batera pasatu
			$emaitza = json_decode( $emaitzaGordina, true );
			if ( 200 === $erantzunKodea ) { // Dena ondo joan bada
				$hizkuntzak = array();
				// Jasotako hizkuntz bakoitza bektore batean sartu, erantzun gisa emango dena
				foreach ( $emaitza['data']['languages'] as $emaitz ) {
					$hizkuntza = array(
								'hizkuntzKodea' => $emaitz['language'],
								'hizkuntzIzena' => $emaitz['name'],
							); 
					$hizkuntzak[] = $hizkuntza;
				}
				wp_send_json( $hizkuntzak );
			} else { //Errorea gertatu da. Kasu horretan, errorearen kodea itzuli, Google Translatetik jasotako mezuarekin batera
				wp_send_json( sprintf( __( '%s kodedun errorea: %s.', 'ohar-itzultzailea' ), $erantzunKodea, $emaitza['error']['errors'][0]['message']  ) );
				return;
			}
		} else {
			// Ez dira behar diren aldagaiak pasatu
			wp_send_json( __( 'oi_itzulpenaEgin funtzioak ez ditu behar dituen parametroak jaso.', 'ohar-itzultzailea' ) );
		}
	}
	
	// Gertaera honen bidez bikoteakLortu ekintzari AJAX deia egitean oi_itzulpenBikoteakLortu funtzioa exekutatuko da 
	add_action( 'wp_ajax_bikoteakLortu', 'oi_itzulpenBikoteakLortu' );
	
	/*
	*	Funtzio honen bidez Google Translateren translate aukerari deia egingo zaio. AJAX deiaren parametroak jatorri eta helburu hizkuntza eta
	*	itzuli beharreko testua dira
	*/
	function oi_itzulpenaEgin() {
		if ( isset( $_POST['jatorriHizkuntza'] ) && isset( $_POST['helburuHizkuntza'] ) && isset( $_POST['testua'] ) ) {
			$jatorriHizkuntza = sanitize_text_field( $_POST['jatorriHizkuntza'] );
			$helburuHizkuntza = sanitize_text_field( $_POST['helburuHizkuntza'] );
			$testua = sanitize_text_field( $_POST['testua'] );
			$erroreErantzuna = '';
			$erantzuna = oi_gTItzulpenaEgin( $testua, $helburuHizkuntza, $jatorriHizkuntza, $erroreErantzuna );
			if ( true === is_string($erantzuna) ) {
				wp_send_json( $erantzuna );
			} else {
				$errorea = array(
					'erroreMezua' => $erroreErantzuna,
				); 
				wp_send_json( $errorea );
				return;
			}
		} else {
			// Ez dira behar diren aldagaiak pasatu
			$errorea = array(
				'erroreMezua' => __( 'oi_itzulpenaEgin funtzioak ez ditu behar dituen parametroak jaso.', 'ohar-itzultzailea' ),
			); 
			wp_send_json( $errorea );
			return;
		}
	}
	
	// Gertaera honen bidez testuarenItzulpenaEgin ekintzari AJAX deia egitean oi_itzulpenaEgin funtzioa exekutatuko da 
	add_action( 'wp_ajax_testuarenItzulpenaEgin', 'oi_itzulpenaEgin' );
	
	/*
	*	Funtzio honek itzulpenak egiten ditu Google Translate erabilita, baina interfazea ireki ordez ingelesetik beste hizkuntza guztietara
	*	(eu, es eta fr kenduta) modu automatikoan itzuliko du
	*/
	function oi_itzulpenAutomatikoaEgin() {
		if ( isset( $_POST['jatorriTestua'] ) && isset( $_POST['helburuHizkuntzak'] ) && isset( $_POST['jatorriIzenburua'] ) ) { // Bidalketaren ingelesezko testu eta izenburua eta helburu hizkuntzak jaso
			// $jatorriTestua =  sanitize_text_field( $_POST['jatorriTestua'] );
			$jatorriTestua = $_POST['jatorriTestua'];
			$jatorriIzenburua = sanitize_text_field( $_POST['jatorriIzenburua'] );
			
			// Bektore hau deiaren erantzuna gordetzeko erabiltzen da
			$testuItzulpenak = array();
			foreach ( $_POST['helburuHizkuntzak'] as $helburuHizkuntza ) { // Jaso dugun hizkuntza ezberdin bakoitzeko iterazioa egin
				$erroreErantzuna = '';
				$unekoItzulpena = array(
						'hizkuntza' => $helburuHizkuntza,
					);
				// Ingelesezko testua Google Translate bidez uneko hizkuntzara itzultzeko deia egin
				$erantzunaTestua = oi_gTItzulpenaEgin( $jatorriTestua, sanitize_text_field( $helburuHizkuntza ), 'en', $erroreErantzuna );
				if ( true === is_string( $erantzunaTestua ) ) { // Erantzuna String bat bada, itzulpena ondo egin da
					$unekoItzulpena['testua'] = $erantzunaTestua;
				} else {
					$unekoItzulpena['erroreMezua'] = $erroreErantzuna;
				}
				$erroreErantzuna = '';
				// Ingelesezko izenburua Google Translate bidez uneko hizkuntzara itzultzeko deia egin
				$erantzunaIzenburua = oi_gTItzulpenaEgin( $jatorriIzenburua, sanitize_text_field( $helburuHizkuntza ), 'en', $erroreErantzuna );
				if ( true === is_string( $erantzunaIzenburua ) ) { // Erantzuna String bat bada, itzulpena ondo egin da
					$unekoItzulpena['izenburua'] = $erantzunaIzenburua;
				} else {
					if ( '' !== $unekoItzulpena['erroreMezua'] ) {
						$unekoItzulpena['erroreMezua'] = $unekoItzulpena['erroreMezua'] . " \n " . $erroreErantzuna;
					} else {
						$unekoItzulpena['erroreMezua'] = $erroreErantzuna;
					}
				}
				$testuItzulpenak[] = $unekoItzulpena;
			}
		}
		// Erantzuna bidali, JSON formatuan
		wp_send_json( $testuItzulpenak );
	}
	
	// Gertaera honen bidez testuarenItzulpenAutomatikoaEgin ekintzari AJAX deia egitean oi_itzulpenAutomatikoaEgin funtzioa exekutatuko da 
	add_action( 'wp_ajax_testuarenItzulpenAutomatikoaEgin', 'oi_itzulpenAutomatikoaEgin' );
	
	/*
	*	Funtzio honek audioa sortzeko erabiltzen den AJAX deiari erantzuten dio, functions.php klasea erabiliz
	*/
	function oi_testuarenAudioaSortu() {
		// Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
		require_once( ABSPATH . '/wp-admin/includes/image.php' );
		if ( isset( $_POST['testuak'] ) && isset( $_POST['id'] ) ) { // Bidalketaren id-a eta testuak jaso baditu
			$testuak =  $_POST['testuak'];
			// The ID of the post this attachment is for.
			$parent_post_id = intval( $_POST['id'] );
			// Get the path to the upload directory.
			$wp_upload_dir = wp_upload_dir();
			
			// Bektore hau deiaren erantzuna gordetzeko erabiltzen da
			$audioFitxategiak = array();
			foreach ( $testuak as $testuLag ) { // Jaso dugun testu ezberdin bakoitzeko (hizkuntz bakoitzeko bat) iterazioa egin
				$erantsiMP3 = true;
				$erantsiOGG = true;
				// JSON formatuan dago eskaera, dekodifikatu PHP bidez tratatu ahal izateko
				$testua = json_decode( stripslashes( $testuLag ) , true );
				// $filename should be the path to a file in the upload directory.
				$filename = $parent_post_id . "-" . $testua['hizkuntzKodea'];
				
				// Testuaren audioa sortzen duen functions.php fitxategiko funtzioari deia
				$emaitza = oi_convertTextToAudio( $testua['testua'], $testua['hizkuntzKodea'], 
					$wp_upload_dir['path'] . "/audioak/" . $filename );
					
				if ( true === is_int( $emaitza ) ) { // Zenbakia ez bada, VoiceRSS APIak errrorea itzuli du
					/*
					*	Itzultzen dituen kodeak:
					*		0: ondo joan da
					*		1: Testua hutsik zegoen
					*		2: Ezin izan da curl paraleloa ireki
					*		3: Ezin izan da MP3 fitxategia jaitsi
					*		4: Ezin izan da MP3 fitxategia idazteko ireki
					*		5: Ezin izan da OGG fitxategia jaitsi
					*		6: Ezin izan da OGG fitxategia idazteko ireki
					*		7: Hizkuntzak ez dauka audio aukerarik
					*/
					switch( $emaitza ) {
						case 0:
							// Check the type of file. We'll use this as the 'post_mime_type'.
							$filetypeMP3 = wp_check_filetype( basename( $filename . '.mp3' ), null );
							$filetypeOGG = wp_check_filetype( basename( $filename . '.ogg' ), null );
							
							// Bidalketari erantsi zaizkion fitxategiak lortu
							$fitxategiErantsiak = get_attached_media( 'audio', $parent_post_id );
							foreach( $fitxategiErantsiak as $fitxategiErantsi ) { //Fitxategi erantsi bakoitzarekin konparatu uneko fitxategia
								//if ( $parent_post_id . "-" . $testua['hizkuntzKodea'] === $fitxategiErantsi->post_title ) { 
								if ( $wp_upload_dir['url'] . '/audioak/' . basename( $filename . '.mp3' ) === $fitxategiErantsi->guid ) {
									// Izen bereko fitxategi bat badago lehendik ere erantsita zegoen, ez dugu berriro erantsi behar
									$erantsiMP3 = false;
								}
								if ( $wp_upload_dir['url'] . '/audioak/' . basename( $filename . '.ogg' ) === $fitxategiErantsi->guid ) {
									// Izen bereko fitxategi bat badago lehendik ere erantsita zegoen, ez dugu berriro erantsi behar
									$erantsiOGG = false;
								}
							}
							
							if ( true === $erantsiMP3 ) { // Uneko fitxategia erantsi behar badugu
								// Prepare an array of post data for the attachment.
								$attachment = array(
									'guid'           => $wp_upload_dir['url'] . '/audioak/' . basename( $filename . '.mp3' ), 
									'post_mime_type' => $filetypeMP3['type'],
									'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $filename . '.mp3' ) ),
									'post_content'   => '',
									'post_status'    => 'inherit'
								);

								// Insert the attachment.
								$attach_id = wp_insert_attachment( $attachment, $filename . '.mp3', $parent_post_id );				

								// Generate the metadata for the attachment, and update the database record.
								$attach_data = wp_generate_attachment_metadata( $attach_id, $filename . '.mp3' );
								wp_update_attachment_metadata( $attach_id, $attach_data );
								$erantsiMP3 = false;
							}
							
							if ( true === $erantsiOGG ) { // Uneko fitxategia erantsi behar badugu
								// Prepare an array of post data for the attachment.
								$attachment = array(
									'guid'           => $wp_upload_dir['url'] . '/audioak/' . basename( $filename . '.ogg' ), 
									'post_mime_type' => $filetypeOGG['type'],
									'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $filename . '.ogg' ) ),
									'post_content'   => '',
									'post_status'    => 'inherit'
								);

								// Insert the attachment.
								$attach_id = wp_insert_attachment( $attachment, $filename . '.ogg', $parent_post_id );						

								// Generate the metadata for the attachment, and update the database record.
								$attach_data = wp_generate_attachment_metadata( $attach_id, $filename . '.ogg' );
								wp_update_attachment_metadata( $attach_id, $attach_data );
								$erantsiOGG = false;
							}
							
							if ( false === $erantsiMP3 || false === $erantsiOGG) {
								// Audioa sortu denez, hizkuntz kodea eta helbidea eman, editorean txertatuak izan daitezen
								$fitxategia = array(
									'hizkuntza' => $testua['hizkuntzKodea'],
								);
								/*
								'mp3' => $wp_upload_dir['url'] . "/" . $filename . '.mp3',
									'ogg' => $wp_upload_dir['url'] . "/" . $filename . '.ogg',
								*/
								if ( false === $erantsiMP3 ) {
									$fitxategia['mp3'] = $wp_upload_dir['url'] . "/audioak/" . $filename . '.mp3';
								}
								if ( false === $erantsiOGG ) {
									$fitxategia['ogg'] = $wp_upload_dir['url'] . "/audioak/" . $filename . '.ogg';
								}
								$audioFitxategiak[] = $fitxategia; 
							}
							break;
						case 1:
							break;
						case 2:
							$fitxategia = array(
								'hizkuntza' => $testua['hizkuntzKodea'],
								'erroreMezua' => __( 'Ezin izan dira audio fitxategiak sortzeko egin beharreko deiak egin.', 'ohar-itzultzailea' ),
							);
							$audioFitxategiak[] = $fitxategia;
							break;
						case 3:
							$fitxategia = array(
								'hizkuntza' => $testua['hizkuntzKodea'],
								'erroreMezua' => __( 'Ezin izan da MP3 fitxategia jaitsi.', 'ohar-itzultzailea' ),
							);
							$audioFitxategiak[] = $fitxategia;
							break;
						case 4:
							$fitxategia = array(
								'hizkuntza' => $testua['hizkuntzKodea'],
								'erroreMezua' => __( 'MP3 fitxategia ezin izan da idazteko ireki.', 'ohar-itzultzailea' ),
							);
							$audioFitxategiak[] = $fitxategia;
							break;
						case 5:
							$fitxategia = array(
								'hizkuntza' => $testua['hizkuntzKodea'],
								'erroreMezua' => __( 'Ezin izan da OGG fitxategia jaitsi.', 'ohar-itzultzailea' ),
							);
							$audioFitxategiak[] = $fitxategia;
							break;
						case 6:
							$fitxategia = array(
								'hizkuntza' => $testua['hizkuntzKodea'],
								'erroreMezua' => __( 'OGG fitxategia ezin izan da idazteko ireki.', 'ohar-itzultzailea' ),
							);
							$audioFitxategiak[] = $fitxategia;
							break;
						case 7:
							break;
					}
				} else { // VoiceRSS API-ak itzulitako errorea itzuli
					$fitxategia = array(
						'hizkuntza' => $testua['hizkuntzKodea'],
						'erroreMezua' => $emaitza,
					);
					$audioFitxategiak[] = $fitxategia;
				}
			}
		}
		// Erantzuna bidali, JSON formatuan
		wp_send_json( $audioFitxategiak );
	}
	
	// Gertaera honen bidez audioaSortu ekintzari AJAX deia egitean oi_testuarenAudioaSortu funtzioa exekutatuko da
	add_action( 'wp_ajax_audioaSortu', 'oi_testuarenAudioaSortu' );
	
	/*
	*	Funtzio honek bidalketa/orrialde bat ezabatua denean berari lotutako eranskin guztiak ezabatuko ditu
	*/
	function delete_post_children($post_id) {
		global $wpdb;

		$ids = $wpdb->get_col("SELECT ID FROM {$wpdb->posts} WHERE post_parent = $post_id AND post_type = 'attachment'");

		foreach ( $ids as $id ) {
			wp_delete_attachment($id);
		}
	}
	
	// Bidalketa/Orrialde bat ezabatu aurretik delete_post_children eragiketa exekutatuko da
	add_action('before_delete_post', 'delete_post_children');
	
	
	//
	//	Bidalketa eta orrialde bakoitzaren QR kodea lortzeko funtzioak
	//
	
	/*
	*	Funtzio honek bidalketak kudeatzen diren orrian zutabe berri bat gehitzen du, bidalketa bakoitzaren QR kodea kargatzeko balioko duena. Dagoeneko definituta dauden
	*	zutabeak jasotzen ditu parametro bezala
	*/
	function oi_zutabeaGehitu( $columns )
	{
		//Zutabeak gordetzeko erabiliko den bektorea hasieratu
		$zutabe_bektore = array();
		/*Gure zutabe berriaren kokapena zehaztu baino lehen, dagoeneko gurearen aurretik egongo diren zutabeak definituta al dauden ikusiko dugu, eta izen bereko
		zutabe huts bat sortuko dugu gure bektorean*/
		if( isset( $columns['cb'] ) ){
			$zutabe_bektore['cb'] = '';
		}
		if( isset( $columns['title'] ) ){
			$zutabe_bektore['title'] = '';
		}
		if( isset( $columns['author'] ) ){
			$zutabe_bektore['author'] = '';
		}
		if( isset( $columns['categories'] ) ){
			$zutabe_bektore['categories'] = '';
		}
		if( isset( $columns['tags'] ) ){
			$zutabe_bektore['tags'] = '';
		}
		if( isset( $columns['language'] ) ){
			$zutabe_bektore['language'] = '';
		}
		//Gure zutabe berria gehituko diogu gure bektoreari, bektoreko indize izena eta zutabearen izena emanez
		$zutabe_bektore['QRKodea'] = __( 'QR Kodea', 'ohar-itzultzailea' );
		/*
			Gure bektorea eta dagoeneko definituta dauden zutabeen bektorea batuko ditugu array_merge eragiketaren bidez, gure bektorearen bukaeran parametro gisa 
			jasotako bektorea lotuz. Bi bektoreek izen bereko zutabe bat baldin badute,	eragiketaren hasieran parametro gisa jasotako balioek gainidatziko dituzte guk 
			definitutako balio hutsak.
		*/
		return array_merge( $zutabe_bektore, $columns );
	}
	
	//Bidalketak maneiatzen diren orrian zutabeak kargatzean, oi_zutabeaGehitu eragiketari deituko dio, zutabe berria gehitzeko
	add_filter( 'manage_posts_columns',	'oi_zutabeaGehitu' );
	
	/*
	*	Funtzio honek orrialdeak kudeatzen diren orrian zutabe berri bat gehitzen du, orrialde bakoitzaren QR kodea kargatzeko balioko duena. Dagoeneko definituta dauden
	*	zutabeak jasotzen ditu parametro bezala
	*/
	function oi_orrialdeariZutabeaGehitu( $columns )
	{
		//Zutabeak gordetzeko erabiliko den bektorea hasieratu
		$zutabe_bektore = array();
		/*Gure zutabe berriaren kokapena zehaztu baino lehen, dagoeneko gurearen aurretik egongo diren zutabeak definituta al dauden ikusiko dugu, eta izen bereko
		zutabe huts bat sortuko dugu gure bektorean*/
		if( isset( $columns['cb'] ) )	$zutabe_bektore['cb'] = '';
		if( isset( $columns['title'] ) )	$zutabe_bektore['title'] = '';
		if( isset( $columns['author'] ) )	$zutabe_bektore['author'] = '';
		if( isset( $columns['language'] ) )	$zutabe_bektore['language'] = '';
		//Gure zutabe berria gehituko diogu gure bektoreari, bektoreko indize izena eta zutabearen izena emanez
		$zutabe_bektore['QRKodea'] = __('QR Kodea','ohar-itzultzailea');
		/*
			Gure bektorea eta dagoeneko definituta dauden zutabeen bektorea batuko ditugu array_merge eragiketaren bidez, gure bektorearen bukaeran parametro gisa 
			jasotako bektorea lotuz. Bi bektoreek izen bereko zutabe bat baldin badute,	eragiketaren hasieran parametro gisa jasotako balioek gainidatziko dituzte guk 
			definitutako balio hutsak.
		*/
		return array_merge( $zutabe_bektore, $columns );
	}
	
	//Orrialdeak maneiatzen diren orrian zutabeak kargatzean, oi_zutabeaGehitu eragiketari deituko dio, zutabe berria gehitzeko
	add_filter( 'manage_pages_columns',	'oi_orrialdeariZutabeaGehitu' );
	
	/*
	*	Funtzio honek sortu berri dugun zutabearen errenkada bakoitzean botoi bat gehituko du, errenkada bakoitzak adierazten duen bidalketa edota orrialdearen QR kodea 
	*	jaisteko balioko duena
	*/
	function oi_kodearenBotoiaSortu($zutabea){
		//Uneko bidalketaren balioak lortu
		global $post;
		global $q_config;
		
		if ( $zutabea === 'QRKodea' ) {	//Uneko zutabearen izena QRKodea bada, bertan gehitu botoi berria		
			$id = $post->ID;
			if ( get_post_status( $id ) === "publish" ) { //Bidalketa/Orrialdea 'publish' egoeran badago
				/*Frantsesean, katalanean eta ingeleran erabiltzen den ' karaktereak arazoak ematen ditu. Horregatik, bidalketaren izenburua lortu eta karaktere hori dagokion
				HTML entitatearekin ordeztuko dugu*/
				$izenburuOsoa = str_replace( "'", "&#39;", $post->post_title );
				//Bidalketaren izenburuan lehenetsitako hizkuntzaren bertsioa eskuratu
				$hasiera = strpos( $izenburuOsoa, "<!--:".$q_config['default_language']."-->" ) + 10;
				$luzera = strpos( $izenburuOsoa, "<!--:-->", $hasiera ) - $hasiera;
				$lehenetsitakoIzenburua = substr( $izenburuOsoa, $hasiera, $luzera );
				//Bidalketaren QR kodea zutabean agertuko den irudia definitu, bere QR kodea deskargatzeko esteka bat gordeko duena
				echo "<a href='".get_bloginfo("wpurl")."/wp-content/uploads/kodeak/QR-".$id.".png' download='QR-".$lehenetsitakoIzenburua.".png'>
							<img src='".get_bloginfo("wpurl")."/wp-content/plugins/ohar-itzultzailea/irudiak/qr-botoia.png'
							alt='".__('Bidalketaren edota orrialdearen QR kodea eskuratzea hasieratzeko erabiltzen den botoia','ohar-itzultzailea')."'
							title='".__('Bidalketa edota orrialdearen QR kodea eskuratzeko','ohar-itzultzailea')."'>
					</a>";
			} else { //Bidalketa/Orrialdea argitaratu gabe badago
				echo "<img src = '".get_bloginfo("wpurl")."/wp-content/plugins/ohar-itzultzailea/irudiak/qr-botoia_ezgaitua.png' 
							alt = '".__('Bidalketa edota orrialdea argitaratuta ez dagoenean agertzen den botoia','ohar-itzultzailea')."' 
							title= '".__('Bidalketa/Orrialdea argitaratuta egon behar da QR kode bat izan dezan.','ohar-itzultzailea')."' 
							onClick = 'alert(".__('"Bidalketa/Orrialdea argitaratuta egon behar da QR kode bat izan dezan."','ohar-itzultzailea').");' 
							style='cursor: pointer;' />";
			}
		}
	}
	
	//Bidalketak maneiatzen diren orrian errenkadak kargatzean, oi_kodearenBotoiaSortu eragiketari deituko dio, zutabe egokian botoi berria gehitzeko
	add_action( 'manage_posts_custom_column', 'oi_kodearenBotoiaSortu' );
	
	//Orrialdeak maneiatzen diren orrian errenkadak kargatzean, oi_kodearenBotoiaSortu eragiketari deituko dio, zutabe egokian botoi berria gehitzeko
	add_action( 'manage_pages_custom_column', 'oi_kodearenBotoiaSortu' );
	
	/*
	*	Funtzio honen bidez QR kode bat sortuko da bidalketa/orrialde bat argitaratzen den bakoitzean. Horretarako, QRickit zerbitzua erabiliko da
	*/
	function oi_qrKodeaSortu( $new_status, $old_status, $post ) {
		if ( 'publish' === $new_status && 'publish' !== $old_status ){	//Oraintxe argitaratu bada
			//jasotako elementuaren id-a lortu
			$id = $post->ID;
			
			//QR Code Generator
			//$helbidea="http://api.qrserver.com/v1/create-qr-code/?data=".urlencode( get_permalink( $id ) )."&size=400x400";
			
			//QRickit
			$helbidea = "http://qrickit.com/api/qr?d=".urlencode( get_permalink( $id ) )."&bgdcolor=F0F0F0&qrsize=400";
			
			//Irudi bat sortu, QRickit zerbitzutik jasotako irudia erabiliz
			$img = imagecreatefrompng( $helbidea );
			if ( TRUE == $img ) {//Ondo joan bada

				/*$wp_upload_dir = wp_upload_dir();
				$fitxategiIzena = "QR-".$id.".png";
				$path = $wp_upload_dir['path']."/kodeak/" . $fitxategiIzena;
				//Irudia gorde, emandako helbidean
				$emaitza = imagepng( $img, $path, 0, NULL );
				if ( true !== $emaitza ) {//Irudia ondo gorde ez bada
					$my_post = array(
					'ID' => $id,
					'post_status' => 'draft',
					);
					//Bidalketa/Orrialdea zirriborro bezala gordeko dugu, bere QR kodea ondo sortu ez delako
					wp_update_post( $my_post );
					//QR Kodea ondo sortu ahal izan ez dela adierazten duen aldagaia
					set_transient( 'oi_ezinKodeaSortu', TRUE, 15);
				} else {

					$filetype = wp_check_filetype( basename( $fitxategiIzena ), null );
								
					// Bidalketari erantsi zaizkion fitxategiak lortu
					$fitxategiErantsiak = get_attached_media( 'image', $id );
					foreach( $fitxategiErantsiak as $fitxategiErantsi ) { //Fitxategi erantsi bakoitzarekin konparatu uneko fitxategia
						//if ( $parent_post_id . "-" . $testua['hizkuntzKodea'] === $fitxategiErantsi->post_title ) { 
						if ( $wp_upload_dir['url'] . '/kodeak/' . basename( $fitxategiIzena ) === $fitxategiErantsi->guid ) {
							// Izen bereko fitxategi bat badago lehendik ere erantsita zegoen, ez dugu berriro erantsi behar
							$erantsi = false;
						}
					}
					
					if ( true === $erantsi ) { // Uneko fitxategia erantsi behar badugu
						// Prepare an array of post data for the attachment.
						$attachment = array(
							'guid'           => $wp_upload_dir['url'] . '/kodeak/' . basename( $fitxategiIzena ), 
							'post_mime_type' => $filetype['type'],
							'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $fitxategiIzena ) ),
							'post_content'   => '',
							'post_status'    => 'inherit'
						);

						// Insert the attachment.
						$attach_id = wp_insert_attachment( $attachment, $fitxategiIzena, $id );				

						// Generate the metadata for the attachment, and update the database record.
						$attach_data = wp_generate_attachment_metadata( $attach_id, $fitxategiIzena );
						wp_update_attachment_metadata( $attach_id, $attach_data );
					}
								
				}*/


				$wp_upload_dir = wp_upload_dir();
				$path = $wp_upload_dir['path']."/kodeak/QR-".$post->ID.".png";
				//Irudia gorde, emandako helbidean
				$emaitza = imagepng( $img, $path, 0, NULL );
				if ( true !== $emaitza ) {//Irudia ondo gorde ez bada
					$my_post = array(
					'ID' => $id,
					'post_status' => 'draft',
					);
					//Bidalketa/Orrialdea zirriborro bezala gordeko dugu, bere QR kodea ondo sortu ez delako
					wp_update_post( $my_post );
					//QR Kodea ondo sortu ahal izan ez dela adierazten duen aldagaia
					set_transient( 'oi_ezinKodeaSortu', TRUE, 15);
				}
			} else {
				//errorea gertatu da
				$my_post = array(
				'ID' => $id,
				'post_status' => 'draft',
				);
				//Bidalketa/Orrialdea zirriborro bezala gordeko dugu, bere QR kodea ondo sortu ez delako
				wp_update_post( $my_post );
				//QR Kodea ondo sortu ahal izan ez dela adierazten duen aldagaia
				set_transient( 'oi_ezinKodeaSortu', TRUE, 15);
			}
		} else {//Erabiltzen den espazioa txikitzeko argitaratuta egon diren eta beste egoera batera pasatu diren Bidalketen eta Orrialdeen QR kodeak
				//zerbitzaritik ezabatu egingo dira
			if ( 'publish' === $old_status && 'publish' !== $new_status ) {
				//identifikadorea lortu
				$id = $post->ID;
				$wp_upload_dir = wp_upload_dir();
				$helbidea = $wp_upload_dir['path']."/kodeak/QR-".$post->ID.".png";
				if( file_exists( $helbidea ) ) {
					//QR kodea ezabatzeko
					unlink( $helbidea );
				}
			}
		}
	}
	// Bidalketa edota orrialde baten egoera (zirriborroa, argitaratua...) aldatzen den bakoitzean exekutatuko den funtzioa
	add_action( 'transition_post_status', 'oi_qrKodeaSortu', 10, 3 );
	
	/*
	*	Funtzio hau Kudeatzaileari oharrak erakusteko erabiliko da
	*/
	function oi_erabiltzaileariOharrak( ) {
		//Bidalketa/Orrialdearen QR Kodea sortzean arazorik gertatu den adierazten duen transient-a atzitu
		$ezinKodeaSortu = get_transient( 'oi_ezinKodeaSortu' );
		if ( TRUE === $ezinKodeaSortu ) { // Kodea sortzean arazorik gertatu bada, ohar bat erakutsi
			?>
			<div class="update-nag">
				<p><?php echo __( 'Edukiaren QR kodea gordetzean arazo bat gertatu da eta zirriborro bezala gordeko da. Eman berriro argitaratzeari QR kodea ondo sortzen saiatzeko.', 'ohar-itzultzailea' ); ?></p>
			</div>
			<?php
			// Transient-a ezabatu, hemendik aurrera berriro ager ez dadin
			delete_transient( 'oi_ezinKodeaSortu' );
		}
		global $pagenow;
		if ( $pagenow === 'post.php' ) { // Editorea bagaude
			//Visual aukera martxan al dagoen adierazten duen datu-baseko aukera kargatu
			$visualMartxan = get_option( 'oi_visualMartxan' );
			if ( 'bai' === $visualMartxan ) { //Visual martxan badago, abisu mezua sortu
				?>
				<div class="error">
					<p><?php echo __( 'Editorean "Visual" aukera martxan dago eta horren ondorioz itzultzaileak ez ditu itzulpenak ondo gordeko. Itzulpenen bat egiteko asmoa baduzu, sakatu "Text" aukera eta eguneratu orrialdea aldaketak eragina izan dezan.', 'ohar-itzultzailea' ); ?></p>
				</div>
				<?php
			}
		}
	}
	
	// oi_erabiltzaileariOharrak funtzioa admin_notices gertaerari lotzen zaio, Kudeatzaileari oharrak erakusteko erabiltzen dena
	add_action( 'admin_notices', 'oi_erabiltzaileariOharrak');
