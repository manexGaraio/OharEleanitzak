 
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

import android.app.Activity;
import android.app.AlertDialog;
import android.content.DialogInterface;
import android.content.Intent;
import android.net.Uri;
import android.os.Bundle;
import android.view.LayoutInflater;
import android.view.Menu;
import android.view.MenuInflater;
import android.view.MenuItem;
import android.view.View;
import android.view.ViewGroup.LayoutParams;
import android.widget.ExpandableListView;
import android.widget.ExpandableListView.OnChildClickListener;

/**
 * Klase honen bidez erabiltzaileak irakurritako Kodeen historia erakutsiko da
 *
 */
public class IrakurketaHistoria extends Activity{
	
    private HistoriaMoldagailua moldagailua;
	private ExpandableListView zerrendaHedagarria;

	/**
	 * Eragiketa hau klase hau hasieratzen denean exekutatuko da eta interfazea eta aukeren menua kargatzeaz arduratuko da.
	 * 
	 */
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		 //Historia erakusten dituen interfazea kargatu aurretik, hizkuntz egokia ezarri
		KameraJarduera.hizkuntzaEguneratu(this);
		//irakurritako_kodeak.xml fitxategia ezarri interfaze bezala
		setContentView(R.layout.irakurritako_kodeak);
		zerrendaHedagarria = (ExpandableListView) findViewById(R.id.list);
	}
	
	/**
	 * Eragiketa honen bidez, jarduerak fokua lortzen duen bakoitzean(hasieratu berria delako edota alplikazio 
	 * pilan bere gainean zegoena itxi delako) exekutatuko da eta irakurketa historiaren balioa erakusteaz 
	 * arduratzen da.
	 */
	@Override
	protected void onResume()
	{
		super.onResume();
		moldagailua = new HistoriaMoldagailua(IrakurketaHistoria.this);
		zerrendaHedagarria.setAdapter(moldagailua);
		//Goiburuaren barruko objektu bat zapaltzen denean erantzuteko eragiketa
		zerrendaHedagarria.setOnChildClickListener(new OnChildClickListener() {
 
            @Override
			public boolean onChildClick(ExpandableListView gurasoa, View bista,
                    int goiburuIndizea, int semeIndizea, long id) 
            {
            	//Goiburu indizea eta elementuaren indizea emanda, lortu klikatua izan den elementua
            	String klikatua= moldagailua.getChild(goiburuIndizea,semeIndizea);
            	//Datu basean eguneratu azken atzipena
            	moldagailua.kodeaGehitu(klikatua);
            	helbideaIreki(klikatua);
            	//Web arakatzailea ireki erabiltzaileak zapaldu duen kodea atzitzeko.
            	/*Intent i = new Intent(Intent.ACTION_VIEW, Uri.parse(klikatua));
            	startActivity(i);*/
                return true;
            }
        });
		//moldagailua hutsik badago, beste bista bat kargatu behar da, historia hutsik dagoela adierazten duena
        if(moldagailua.getGroupCount()==0)
        {
	        LayoutInflater inflater = getLayoutInflater();
	        View hutsikDago = inflater.inflate(R.layout.zerrenda_hutsik, null);
	        addContentView(hutsikDago, new LayoutParams(LayoutParams.MATCH_PARENT, LayoutParams.MATCH_PARENT));
        }
	}	
	

	/**
	 * Erabiltzaileak menua ireki nahi duenean exekutatuko da
	 * 
	 * @param menu Menu motako objektu bat. Bertan kokatuko da gure menua
	 * @return true itzuliko da ondo joan bada. False itzultzekotan menua ez da erakutsiko
	 */
	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		super.onCreateOptionsMenu(menu);
		MenuInflater inflater = getMenuInflater();
		// historia_menua.xml fitxategiko edukiarekin menua sortu
		inflater.inflate(R.menu.historia_menua, menu);
		return true;
	}

	/**
	 * Erabiltzaileak menuko objektu bat hautatzean exekutatuko da
	 * 
	 * @param item MenuItem motako objektu bat, zapaldu berri den aukera adierazten duena         
	 * @return boolean
	 */
	@Override
	public boolean onOptionsItemSelected(MenuItem item) {
		// Aukeratutakoa objektuaren arabera, kodea exekutatu. Momentuz, aukera
		// bakarra dago
		switch (item.getItemId()) {
		case R.id.historiaGarbitu:
			Activity jarduera = IrakurketaHistoria.this;
			// Abisu bat sortzeko kodea. Hemen, erabiltzaileari
			// galdetuko zaio ea URL-ra joan nahi duen
			AlertDialog.Builder testuaErakutsi = new AlertDialog.Builder(jarduera);

			// Erabiltzaileak onartzen badu
			testuaErakutsi.setPositiveButton(R.string.urlOnartu,
					new DialogInterface.OnClickListener() {

						@Override
						public void onClick(DialogInterface dialog, int which) {
							
							//Datu basean dauden kode guztiak ezabatu eta moldagailua garbitzeko deia egin
							boolean arrakasta = moldagailua.garbitu();
							zerrendaHedagarria.setAdapter(moldagailua);
							if (true == arrakasta) { //Datu basea arrakastaz garbitu bada
								//Historia hutsik dagoela adierazten duen bista kargatu
								LayoutInflater inflater = getLayoutInflater();
						        View hutsikDago = inflater.inflate(R.layout.zerrenda_hutsik, null);
						        addContentView(hutsikDago, new LayoutParams(LayoutParams.MATCH_PARENT, LayoutParams.MATCH_PARENT));
							}
						}
					});

			// Erabiltzaileak onartzen ez badu
			testuaErakutsi.setNegativeButton(R.string.urlEzeztatu,
					new DialogInterface.OnClickListener() {
						@Override
						public void onClick(DialogInterface dialog, int which) {
							dialog.cancel();
						}
					});
			testuaErakutsi.create();
			// Abisuari izenburua eta mezua gehitu eta
			// erabiltzaileari erakutsi
			testuaErakutsi.setTitle(R.string.urlOharIzena);
			String oharra = getResources().getString(R.string.historiaEzabatuOharra);
			testuaErakutsi.setMessage(oharra);
			testuaErakutsi.show();
			break;
		case R.id.laguntza:
			historiarenLaguntzariDeitu();
			break;
		}
		return true;
	}

	/**
	 * KameraJarduera klasean dagoen metodoaren kopia
	 * @param testua
	 */
	public void helbideaIreki(String testua) {
		if (-1 == testua
				.indexOf("oharrak.albaola.com/")) {
			// Saiakera bat sortu,
			// arakatzaileari
			// deitu eta QR kodeak zekarren
			// url-a
			// atzitu ahal izateko
			Intent intent = new Intent(
					Intent.ACTION_VIEW, Uri
							.parse(testua));
			startActivity(intent);
		} else {
			Intent intent = new Intent(
					this,
					NireWebArakatzailea.class);
			intent.putExtra("url", testua);
			startActivity(intent);
		}
	}
	
	public void historiarenLaguntzariDeitu() {
		Intent intent = (new Intent(this, LaguntzaAzpiatala.class));
		intent.putExtra(LaguntzaOrokorra.ARG_TEXT_ID, R.id.historiaLaguntzaBotoia);
		intent.putExtra(LaguntzaOrokorra.ARG_ZUZENEAN, true);
		startActivity(intent);
	}
	
}
