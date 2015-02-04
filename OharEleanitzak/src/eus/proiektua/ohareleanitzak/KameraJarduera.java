 
/*
 * 
  *	 OharEleanitzak: Android QR reader that enables visualisation of multilingual content with a WordPress site and plugin
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

package eus.proiektua.ohareleanitzak;

import java.util.Locale;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

import net.sourceforge.zbar.Config;
import net.sourceforge.zbar.Image;
import net.sourceforge.zbar.ImageScanner;
import net.sourceforge.zbar.Symbol;
import net.sourceforge.zbar.SymbolSet;
import android.app.Activity;
import android.app.AlertDialog;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.content.SharedPreferences;
import android.content.res.Configuration;
import android.hardware.Camera;
import android.hardware.Camera.AutoFocusCallback;
import android.hardware.Camera.PreviewCallback;
import android.hardware.Camera.Size;
import android.net.Uri;
import android.os.Bundle;
import android.os.Handler;
import android.preference.PreferenceManager;
import android.util.Log;
import android.view.Menu;
import android.view.MenuInflater;
import android.view.MenuItem;
import android.widget.FrameLayout;
import android.widget.TextView;

/**
 * Klase hau, aplikazioaren jarduera nagusia dena, QR kodeak eskaneatzeko
 * erabiliko da
 * 
 */

public class KameraJarduera extends Activity {

	private Camera mCamera;
	private KameraAurrebista mPreview;
	private Handler autoFocusHandler;
	private FrameLayout preview;
	private NegozioLogika negLog;

	TextView scanText;

	ImageScanner scanner;

	private boolean previewing = true;
	private boolean hasieraketa = true;

	private String testua = "";

	private static Context testuingurua;

	public final static String ARG_URL = "url";

	static {
		System.loadLibrary("iconv");
	}

	/**
	 * Jarduera sortzen denean exekutatuko den kodea. Hemen behin bakarrik
	 * exekutatu behar diren gauza funtsezkoak egongo dira, ezarpenen
	 * kargatzea(edota hasieratzea) eta interfazearen orientazioa ezartzea
	 */
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		testuingurua = this;
		/*
		 * Aplikazioa lehen aldiz exekutatu izaten ari den kontrolatzeko kodea.
		 * Kode honen bidez lehen exekuzioan erabiltzaileari aplikazioaren
		 * hizkuntza hautatzeko aukera emango zaio
		 */
		SharedPreferences ezarpenak = PreferenceManager
				.getDefaultSharedPreferences(this);
		// lehenExekuzioa ezarpenak baliorik daukan ikusteko. True da defektuzko
		// balioa eta kasu horretan aplikazioa
		// instalatu berria izan da
		boolean lehenExekuzioa = ezarpenak.getBoolean("lehenExekuzioa", true);
		if (lehenExekuzioa) // Lehen exekuzioa bada
		{
			// Ezarpenak eraldatzeko editorearen instantzia lortu
			SharedPreferences.Editor editor = ezarpenak.edit();
			// lehenExekuzioari false balioa eman, hemendik aurrera kode zatia
			// berriro exekuta ez dadin
			editor.putBoolean("lehenExekuzioa", false);
			// Aldaketa gorde
			editor.commit();
			// Hizkuntz hautatzailea deitu
			hizkuntzHautatzaileaDeitu();
		}

		// Ezarpenak kargatzeko
		PreferenceManager.setDefaultValues(this, R.xml.ezarpenak, false);

		/*
		 * Erabiltzaileak aplikazioarentzat aukeratutako hizkuntza kargatu.
		 * Jardueraren interfazea kargatu aurretik egiten da hizkuntza
		 * egokiarekin hasten dela ziurtatzeko
		 */
		hizkuntzaEguneratu(this);

		// Jardueraren interfazea kargatu
		setContentView(R.layout.main);

		// Datu basearekin komunikazioa gauzatzeaz arduratzen den klasearen
		// hasieraketa
		negLog = new NegozioLogika(this);
	}

	/**
	 * Jarduera bigarren plano batera pasatzean egin beharrekoa. Tartean,
	 * kamerarekiko kontrola askatu.
	 * 
	 */
	@Override
	protected void onPause() {
		super.onPause();
		kameraAskatu();
		preview.removeView(mPreview);
		mPreview = null;
	}

	/**
	 * Funtzio hau hainbat egoera ezberdinetan exekutatuko da. Esate baterako,
	 * aplikazioa sortzean eta aplikazioa pausa(onPause) edota geldialdi
	 * egoeratik(onStop) datorrenean. Geldialdi edota pausa egoeratik gatozenean
	 * kamararen kontrola berreskuratu beharko da, QR kodeak irakurtzen
	 * jarraitzeko. Azken bi kasu hauetan kode gutxiago exekutatu beharko da,
	 * aplikazioa sortzen den momentuan baino.
	 */
	@Override
	protected void onResume() {
		super.onResume();

		/*
		 * Aplikazioa sortzen den momentua baldin bada beheko kodea exekutatuko
		 * da. Batik bat, aldagaiak hasieratzeko erabiltzen da.
		 */
		if (hasieraketa) {
			autoFocusHandler = new Handler();
			mCamera = kamerarenInstantziaLortu();

			scanText = (TextView) findViewById(R.id.scanText);

			scanText.setText(R.string.eskaneatzen);

			/* Instance barcode scanner */
			scanner = new ImageScanner();
			scanner.setConfig(0, Config.X_DENSITY, 3);
			scanner.setConfig(0, Config.Y_DENSITY, 3);

			mPreview = new KameraAurrebista(this, mCamera, previewCb,
					autoFocusCB);
			preview = (FrameLayout) findViewById(R.id.cameraPreview);
			preview.addView(mPreview);
			// Hemendik aurrera ez da berriro hasieraketari dagokion kodea
			// exekutatuko
			hasieraketa = false;
		} else// Hasieraketa ez bada, hau da, onPause edo onStop-etik bagatoz
		{
			// Kasu honetan ere hizkuntzaEguneratu funtzioari deituko zaio,
			// hizkuntz egokia kargatzen dela ziurtatzeko
			hizkuntzaEguneratu(this);
			kameraBirhasieratu();
			previewing = true;
		}
	}

	/**
	 * Android aplikazio baten bizitza-zikloko jarduerei modu artifizialean ez
	 * deitzeko sortutako funtzioa. Pausa egoeratik datorrenean kamera
	 * birhasieratzeaz arduratzen da
	 */
	public void kameraBirhasieratu() {
		mCamera = kamerarenInstantziaLortu();

		scanText.setText(R.string.eskaneatzen);

		mPreview = new KameraAurrebista(this, mCamera, previewCb, autoFocusCB);
		preview = (FrameLayout) findViewById(R.id.cameraPreview);
		preview.addView(mPreview);
	}

	/**
	 * Funtzio hau aplikazioaren manifestuan definitutako ezarpenetan
	 * aldaketarik gertatzean exekutatuko da. Manifestuan aplikazioaren
	 * lokala(hizkuntza definitzen duen objektua) edota orientazio aldaketa
	 * zehazten dira tratatu beharreko gertaera gisa, kasu horietan jarduera
	 * berhasieratu egiten da eta. Ondorioz, hizkuntza egokia ezartzeaz
	 * arduratzen da funtzioa.
	 * */
	@Override
	public void onConfigurationChanged(Configuration newConfig) {
		super.onConfigurationChanged(newConfig);
		hizkuntzaEguneratu(this);
	}

	/**
	 * Erabiltzaileak menua ireki nahi duenean exekutatuko da
	 * 
	 * @param menu
	 *            Menu motako objektu bat. Bertan kokatuko da gure menua
	 * @return true itzuliko da ondo joan bada. False itzultzekotan menua ez da
	 *         erakutsiko
	 */
	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		super.onCreateOptionsMenu(menu);
		MenuInflater inflater = getMenuInflater();
		// Menua sortu aurretik hizkuntza ondo ezarri
		hizkuntzaEguneratu(this);
		// ezarpenmenua.xml fitxategiko edukiarekin menua sortu
		inflater.inflate(R.menu.menu_nagusia, menu);
		return true;
	}

	/**
	 * Funtzio hau menua erakutsi aurretik exekutatzen da beti eta honen bidez,
	 * hizkuntzaren aldaketa bat gertatu berri denean, aukeren menuko izenburuak
	 * eguneratzen dira, hizkuntz egokian egon daitezen.
	 */
	@Override
	public boolean onPrepareOptionsMenu(Menu menu) {

		for (int i = 0; i < menu.size(); i++) { // Menuan dagoen elementu
												// bakoitzeko
			// Uneko elementua lortu
			MenuItem elementua = menu.getItem(i);
			switch (elementua.getItemId()) { // Uneko elementuaren
												// identitatearen araberako
												// trataera
			case R.id.konfigurazioa:
				// Ezarpenetaz arduratzen zen aukeraren izenburua eguneratu
				elementua.setTitle(R.string.action_settings);
				break;
			case R.id.irakurritakoKodeak:
				// Historiaz arduratzen zen aukeraren izenburua eguneratu
				elementua.setTitle(R.string.historia);
				break;
			case R.id.laguntza:
				elementua.setTitle(R.string.laguntza);
				break;
			}
		}
		// Menua erakutsia izan dadin, true balioa itzuli
		return true;
	}

	/**
	 * Erabiltzaileak menuko objektu bat hautatzean exekutatuko da
	 * 
	 * @param item
	 *            MenuItem motako objektu bat, zapaldu berri den aukera
	 *            adierazten duena
	 * @return boolean
	 */
	@Override
	public boolean onOptionsItemSelected(MenuItem item) {
		// Aukeratutakoa objektuaren arabera, kodea exekutatu. Momentuz, aukera
		// bakarra dago
		switch (item.getItemId()) {
		case R.id.konfigurazioa:
			ezarpenakDeitu();
			break;
		case R.id.irakurritakoKodeak:
			irakurketaHistoriaDeitu();
			break;
		case R.id.laguntza:
			laguntzaDeitu();
			break;
		}
		return true;
	}

	/**
	 * Funtzio hau arduratzen da aplikazioa hizkuntza egokian kargatzeaz.
	 * Erabiltzaileak hautatutako hizkuntza eta mugikorrean ezarrita dagoena
	 * berdinak badira ez da aldaketarik egin behar. Ezberdinak badira,
	 * ezarpenak aldatu behar dira, erabiltzaileak hautatutako hizkuntza kargatu
	 * dadin.
	 * 
	 * @param context
	 *            Jardueraren testuingurua
	 */
	public static void hizkuntzaEguneratu(Context context) {
		// Erabiltzailearen ezarpenak aldagai batean kargatu
		SharedPreferences ezarpenZerrenda = PreferenceManager
				.getDefaultSharedPreferences(context);
		try {
			// Aplikazioan lehenetsita dagoen hizkuntza lortu(hasiera batean
			// mugikorraren hizkuntza izaten da)
			String mugikorrekoHizkuntza = Locale.getDefault().getLanguage();
			String hizkuntza;
			if (Hizkuntzak.badauka(mugikorrekoHizkuntza)) { // Mugikorreko
															// hizkuntza
															// aplikazioa
															// eskuragarri
															// dagoen hizkuntzen
															// artean badago
				// Erabiltzaileak hautatu duen hizkuntza lortu. Ezarpena hutsik
				// balego, mugikorreko hizkuntza itzuliko du
				hizkuntza = ezarpenZerrenda.getString("hautatutakoHizkuntza",
						mugikorrekoHizkuntza);
			} else { // Aplikazioa mugikorreko hizkuntzan eskuragarri ez badago
				// Erabiltzaileak hautatu duen hizkuntza lortu. Ezarpena hutsik
				// balego, ingelera itzuliko du, duen nazioarteko garrantzia
				// dela eta
				hizkuntza = ezarpenZerrenda.getString("hautatutakoHizkuntza",
						"en");
			}
			if (!hizkuntza.equals(mugikorrekoHizkuntza)) { // Erabiltzaileak
															// hautatutako
															// hizkuntza
															// aplikazioaren
															// berdina
				// ez bada(berdina balitz, ondo kargatuko litzateke hizkuntza
				// horretan, besterik egin beharrik gabe

				// Hautatutako hizkuntzarekin Locale motako objektu bat sortu
				Locale locale = new Locale(hizkuntza);
				// Sortu berri dugun Locale-a balio lehenetsi gisa jarri
				Locale.setDefault(locale);
				// Ezarpenak kargatu, locale gisa sortu berri dugun locale-a
				// jarri eta eguneratu
				Configuration config = new Configuration();
				config.locale = locale;
				context.getResources().updateConfiguration(config, null);
			}
		} catch (ClassCastException e) {
			Log.e("AlbaolApp-KameraJarduera(hizkuntzaEguneratu)",
					e.getLocalizedMessage());
		}

	}

	/**
	 * Kameraren instantzia bat lortzeko
	 * 
	 * @return Camera motako objektu bat
	 */
	public static Camera kamerarenInstantziaLortu() {
		Camera c = null;
		try {
			c = Camera.open();
		} catch (Exception e) {
			dialogSortu(R.string.instantziaGaizki);
			Log.e("AlbaolApp-KameraJarduera(kamerarenInstantziaLortu)",
					e.getLocalizedMessage());
		}
		return c;
	}

	/**
	 * Kameraren kontrola askatzeko
	 * 
	 */
	private void kameraAskatu() {
		if (mCamera != null) {
			previewing = false;
			// Beheko lerro honek ez du funtzionatzen
			// mCamera.stopPreview();
			mCamera.setPreviewCallback(null);
			// Beheko lerroa nik gehitua
			mPreview.getHolder().removeCallback(mPreview);
			mCamera.lock();
			mCamera.release();
			mCamera = null;
		}
	}

	private Runnable doAutoFocus = new Runnable() {
		// Runnable interfazearen inplementazioa
		@Override
		public void run() {
			if (previewing)
				mCamera.autoFocus(autoFocusCB);
		}
	};

	PreviewCallback previewCb = new PreviewCallback() {
		// PreviewCallback interfazearen inplementazioa

		// Kodearen irakurketa gauzatzeaz arduratuko dena
		@Override
		public void onPreviewFrame(byte[] data, Camera camera) {
			Camera.Parameters parameters = camera.getParameters();
			Size size = parameters.getPreviewSize();

			// Irudiaren tamaina eta irakurritakoa ezarri
			Image barcode = new Image(size.width, size.height, "Y800");
			barcode.setData(data);

			// Egiaztatu zerbait irakurri egin dela
			int result = scanner.scanImage(barcode);

			// Irudia irakurri bada
			if (result != 0 && previewing ) {

				previewing = false;
				/*
				 * previewing = false; mCamera.setPreviewCallback(null);
				 * mCamera.stopPreview();
				 */

				SymbolSet syms = scanner.getResults();
				scanText.setText("");
				// Irakurritako simboloak aldagai batean gorde
				for (Symbol sym : syms) {

					testua = sym.getData();
				}

				// Irakurritako kodeak barnean url bat al duen egiaztatu behar
				// dugu. Horretarako adierazpen erregularra
				// erabiliko da
				Pattern p = Pattern
						.compile("^((https?|ftp)://|(www|ftp)\\.)?[a-z0-9-]+(\\.[a-z0-9-]+)+([/?].*)?$");
				// Irakurritako kodearen edukia adierazpen erregularrarekin bat
				// datorren begiratu
				Matcher m = p.matcher(testua);// replace with string to compare
				Activity jarduera = KameraJarduera.this;
				if (!m.find()) {// Adierazpen erregularrarekin bat ez badator

					AlertDialog.Builder testuaErakutsi = new AlertDialog.Builder(
							jarduera);

					testuaErakutsi.setPositiveButton(R.string.urlOnartu,
							new DialogInterface.OnClickListener() {

								@Override
								public void onClick(DialogInterface dialog,
										int which) {

									// Abisua itxi, kamara askatu eta
									// onResume metodoari deitu (berriro
									// kamara martxan jartzeko)
									dialog.cancel();
									previewing = true;
									scanText.setText(R.string.eskaneatzen);
									// kameraAskatu();
									// kameraBirhasieratu();
								}
							});

					testuaErakutsi.create();
					// Abisuari izenburua eta mezua gehitu eta
					// erabiltzaileari erakutsi
					testuaErakutsi.setTitle(R.string.urlEzegokiaIzena);
					String oharra = getResources().getString(
							R.string.urlEzegokia);
					testuaErakutsi.setMessage(oharra);
					testuaErakutsi.show();
				} else {// Kodearen edukia adierazpen erregularrarekin bat
						// badator

					// Ezarpenak kargatu
					SharedPreferences ezarpenZerrenda = PreferenceManager
							.getDefaultSharedPreferences(jarduera);
					try {
						// Aplikazioa ezarrita dagoen hizkuntza eskuratu
						String mugikorrekoHizkuntza = Locale.getDefault()
								.getLanguage();
						String hizkuntza;
						if (Hizkuntzak.badauka(mugikorrekoHizkuntza)) { // Aplikazioa
																		// Hautatutako
																		// hizkuntzan
																		// eskuragarri
																		// badago
							// Erabiltzaileak hautatutako hizkuntza lortu.
							// Hutsik badago, mugikorreko hizkuntza itzuliko du
							hizkuntza = ezarpenZerrenda.getString(
									"hautatutakoHizkuntza",
									mugikorrekoHizkuntza);
						} else { // Aplikazioa ez dago mugikorraren hizkuntzan
									// eskuragarri
							// Erabiltzaileak hautatutako hizkuntza lortu.
							// Hutsik badago, ingelera itzuliko du
							hizkuntza = ezarpenZerrenda.getString(
									"hautatutakoHizkuntza", "en");
						}

						int indizea;

						/*
						 * Irakurlea gai da web helbideak dituen edozein QR kode
						 * irakurri eta irekitzeko. Hala ere, bi mota daude,
						 * batetik, webgune normalei dagozkien kodeak, zuzenean
						 * atzituko direnak, eta bestetik, gure webgunearenak,
						 * aplikazioaren hizkuntzaren arabera tratatuak izan
						 * beharko direnak.
						 */

						/*
						 * Webgunearen helbidearen bukaeran kokatzeko. indexOf
						 * eragiketa "ohareleanitzak.esy.es/" karaktere katearen
						 * lehen posizioan kokatuko da. Azken posizioan
						 * kokatzeko +22 egiten da. Karaktere katea topatu ez
						 * badu -1 bat itzuliko du
						 */
						indizea = testua.indexOf("ohareleanitzak.esy.es/") + 22;
						// -1+22=21 Hortaz, karaktere katea ez da topatu eta
						// helbidea ez dagokio gure webguneari.
						// Kasu honetan ez dugu ezer egin beharko oraingoz.
						if (indizea != 21) {
							// Url-a gure webguneari badagokio
							// Lortu url-aren erroa(indizea aldagaiak adierazten
							// duen posizioa baino lehenago dagoena)
							String erroa = testua.substring(0, indizea);
							// Lortu url-aren luzapena(indizea aldagaiak
							// adierazten
							// duen posizioaren ondoren dagoena)
							String luzapena = testua.substring(indizea);
							// Erroa eta luzapena batu, tartean hizkuntzaren
							// bereizgarri gisa erabiltzen den bi letretako
							// kodea eta / bat jarriz
							testua = erroa + hizkuntza + "/" + luzapena;
						}

						String keyEzarpenak = "berbideratzeaAutomatikokiOnartu";
						// berbideratzeaAutomatikokiOnartu ezarpenaren
						// balioa
						// eskuratu
						Boolean berbideratzeaAutomatikoki = ezarpenZerrenda
								.getBoolean(keyEzarpenak, false);

						// kameraAskatu();
						
						//previewing = false;

						// Bere balioa true bada, ez da URL-aren
						// helbideratzearen
						// konfirmaziorik egin beharko
						if (berbideratzeaAutomatikoki) {

							// previewing = false;
							mCamera.setPreviewCallback(null);
							mCamera.stopPreview();

							// Atzitutako kodeen historian irakurri berri
							// den
							// kodea gordetzeko NegozioLogika klaseari egin
							// beharrako deia
							negLog.kodeaGehitu(testua);
							helbideaIreki(testua);
							kameraAskatu();
							// previewing = true;
						} else {
							// Abisu bat sortzeko kodea. Hemen,
							// erabiltzaileari
							// galdetuko zaio ea URL-ra joan nahi duen
							AlertDialog.Builder testuaErakutsi = new AlertDialog.Builder(
									jarduera);

							// Erabiltzaileak onartzen badu
							testuaErakutsi.setPositiveButton(
									R.string.urlOnartu,
									new DialogInterface.OnClickListener() {

										@Override
										public void onClick(
												DialogInterface dialog,
												int which) {

											// previewing = false;
											mCamera.setPreviewCallback(null);
											mCamera.stopPreview();

											// Atzitutako kodeen historian
											// irakurri berri den
											// kodea gordetzeko
											// NegozioLogika
											// klaseari egin beharrako deia
											negLog.kodeaGehitu(testua);
											helbideaIreki(testua);
											kameraAskatu();
											// previewing = true;
										}
									});

							// Erabiltzaileak onartzen ez badu
							testuaErakutsi.setNegativeButton(
									R.string.urlEzeztatu,
									new DialogInterface.OnClickListener() {
										@Override
										public void onClick(
												DialogInterface dialog,
												int which) {
											// Abisua itxi, kamara askatu
											// eta
											// onResume metodoari
											// deitu(berriro
											// kamara martxan jartzeko)
											dialog.cancel();
											scanText.setText(R.string.eskaneatzen);

											previewing = true;

											// kameraAskatu();
											// kameraBirhasieratu();
										}
									});
							testuaErakutsi.create();
							// Abisuari izenburua eta mezua gehitu eta
							// erabiltzaileari erakutsi
							testuaErakutsi.setTitle(R.string.urlOharIzena);
							String oharra = getResources().getString(
									R.string.urlOharra); // NotFoundException
															// salbuespena ere
															// altxa liteke
							testuaErakutsi.setMessage(oharra + " " + testua);
							testuaErakutsi.show();
						}
					} catch (ClassCastException e) {
						dialogSortu(R.string.qrHizkuntzaEzartzeanErrorea);
						Log.e("AlbaolApp-KameraJarduera(onPreviewFrame)",
								e.getLocalizedMessage());
					}
				}
			}
		}
	};
	
	/**
	 * Funtzio hau salbuespenak gertatzen direnean dialog-ak sortzeko erabiliko da
	 * @param testua
	 */
	public static void dialogSortu(int testua) {
		AlertDialog.Builder testuaErakutsi = new AlertDialog.Builder(
				testuingurua);
		testuaErakutsi
				.setPositiveButton(R.string.urlOnartu,
						new DialogInterface.OnClickListener() {

							@Override
							public void onClick(
									DialogInterface dialog,
									int which) {

								// Abisua itxi eta jarduera
								// amaitu
								dialog.cancel();
							}
						}).create().setTitle(R.string.adi);
		testuaErakutsi
				.setMessage(testua);
		testuaErakutsi.show();
	}

	/**
	 * Ezarpenen orria irekitzen duen funtzioa
	 */
	public void ezarpenakDeitu() {
		Intent i = new Intent(this, Ezarpenak.class);
		startActivity(i);
	}

	/**
	 * Erabiltzaileak atzitu dituen kodeen historia erakusten duen jarduerari
	 * deitzen dion funtzioa
	 */
	public void irakurketaHistoriaDeitu() {
		Intent i = new Intent(this, IrakurketaHistoria.class);
		startActivity(i);
	}

	/**
	 * Erabiltzaileri aplikazioaren hizkuntza hautatzeko aukera ematen dion
	 * jarduerari deitzen dio funtzioa
	 */
	public void hizkuntzHautatzaileaDeitu() {
		Intent i = new Intent(this, HizkuntzHautatzailea.class);
		startActivity(i);
	}

	/**
	 * Laguntza erakusten duen jarduera hasieratzen duen funtzioa
	 */
	public void laguntzaDeitu() {
		Intent i = new Intent(this, LaguntzaOrokorra.class);
		startActivity(i);
	}

	/**
	 * IrakurketaHistoria klasean metodo honen kopia bat dago
	 * 
	 * @param testua
	 */
	public void helbideaIreki(String testua) {
		if (-1 == testua.indexOf("oharrak.albaola.com/")) { // URL helbidea
															// gurea ez bada,
															// arakatzailearekin
															// ireki
			// Saiakera bat sortu,
			// arakatzaileari
			// deitu eta QR kodeak zekarren
			// url-a
			// atzitu ahal izateko
			Intent intent = new Intent(Intent.ACTION_VIEW, Uri.parse(testua));
			startActivity(intent);
		} else { // Gurea bada, WebView-a hasieratzen duen NireWebArakatzailea
					// eragiketari deitu, aldagai gisa url-a emanda
			Intent intent = new Intent(testuingurua, NireWebArakatzailea.class);
			intent.putExtra("url", testua);
			startActivity(intent);
		}
	}

	/*
	 * public static void erroreMezuaErakutsi(int testuId, String errorea) {
	 * AlertDialog.Builder testuaErakutsi = new AlertDialog.Builder(
	 * testuingurua); String oharra =
	 * testuingurua.getResources().getString(testuId); testuaErakutsi
	 * .setPositiveButton(R.string.urlOnartu, new
	 * DialogInterface.OnClickListener() {
	 * 
	 * @Override public void onClick(DialogInterface dialog, int which) {
	 * 
	 * // Abisua itxi, kamara askatu eta // onResume metodoari deitu (berriro //
	 * kamara martxan jartzeko) dialog.cancel(); }
	 * }).create().setTitle(R.string.adi); testuaErakutsi.setMessage(oharra +
	 * errorea ); Log.e("Albaolapp", oharra + errorea); testuaErakutsi.show(); }
	 */

	/**
	 * Fokatze automatiko jarraia egiteko
	 */
	AutoFocusCallback autoFocusCB = new AutoFocusCallback() {
		@Override
		public void onAutoFocus(boolean success, Camera camera) {
			autoFocusHandler.postDelayed(doAutoFocus, 1000);
		}
	};

}