 
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
import android.os.Bundle;

/**
 * Klase hau jarduerak ez diren Klaseetatik Dialog motako abisuak erakusteko erabiliko da
 * */
public class DialogSortzailea extends Activity {

	public final static String ARG_MEZUA = "mezua";
	private String testua;
	
	@Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        // Lortu deiarekin batera eman den aldagaiaren balioa(abisuak erakutsiko duen testua)
        Intent i = getIntent();
        testua = i.getStringExtra(ARG_MEZUA);
        displayAlert();
    }

	/**
	 * Funtzio honek alerta mezua erakutsiko da
	 * */
    private void displayAlert()
    {
    	AlertDialog.Builder testuaErakutsi = new AlertDialog.Builder(
				this);
		testuaErakutsi
				.setPositiveButton(R.string.urlOnartu,
						new DialogInterface.OnClickListener() {

							@Override
							public void onClick(DialogInterface dialog,
									int which) {

								// Abisua itxi eta jarduera amaitu
								dialog.cancel();
								finish();
							}
						}).create().setTitle(R.string.adi);
		testuaErakutsi.setMessage(testua);
		testuaErakutsi.show();
    }
}