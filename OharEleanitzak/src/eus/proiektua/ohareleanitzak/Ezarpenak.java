 
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


import android.content.Context;
import android.content.Intent;
import android.os.Bundle;
import android.preference.Preference;
import android.preference.Preference.OnPreferenceChangeListener;
import android.preference.PreferenceActivity;
import android.view.Menu;
import android.view.MenuInflater;
import android.view.MenuItem;

/**
 * Klase hau ezarpenak erakutsi zein aldatzeko erabiliko da
 *
 */
public class Ezarpenak extends PreferenceActivity {

	private Context testuingurua;
	
	@Override
	protected void onCreate(Bundle savedInstanceState) {

		super.onCreate(savedInstanceState);
		testuingurua = this;

		// Ezarpenak erakusten dituen interfazea kargatu aurretik, hizkuntz
		// egokia ezarri
		KameraJarduera.hizkuntzaEguneratu(this);

		/*
		 * Ezarpenak kargatu. Deprekatutako funtzioa. Aplikazioaren APIaren
		 * bertsio minimoa 9 da eta deprekazioaren ordezkariak 11 edo gehiagoko
		 * APIa behar du, beraz, horrela geratuko da.
		 */
		addPreferencesFromResource(R.xml.ezarpenak);

		// hautatutakoHizkuntza ezarpenari entzule bat gehitu, hizkuntzaz aldatzean jarduera berhasieratu dezan
		getPreferenceScreen().findPreference("hautatutakoHizkuntza").
		setOnPreferenceChangeListener(new OnPreferenceChangeListener() {
			@Override
			public boolean onPreferenceChange(Preference preference, Object newValue) {
				// Jarduera berria hasieratu
				Intent i = new Intent(testuingurua, Ezarpenak.class);
				startActivity(i);
				// Jarduera zaharra ezabatu (egingo ez bagenu, jarduera berria jarduera zaharraren gainean legoke pilan)
				finish();
				return true;
			}
		});
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
		inflater.inflate(R.menu.ezarpenak_menua, menu);
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
		case R.id.laguntza:
			ezarpenakLaguntzariDeitu();
			break;
		}
		return true;
	}
	
	/**
	 * LaguntzaAzpiatala klaseari deitzen dion funtzioa, Ezarpenak jardueraren laguntza irekitzeko erabiltzen dena
	 */
	public void ezarpenakLaguntzariDeitu() {
		Intent intent = (new Intent(this, LaguntzaAzpiatala.class));
		// Aldagai bezala Ezarpenen laguntza hasieratzen duen botoiaren id-a emango zaio eta zuzenean boolearrari true balioa
		//  emango zaio, jardueren pila kudeatzean lagungarria izango dena
		intent.putExtra(LaguntzaOrokorra.ARG_TEXT_ID, R.id.ezarpenakLaguntzaBotoia);
		intent.putExtra(LaguntzaOrokorra.ARG_ZUZENEAN, true);
		startActivity(intent);
	}
}