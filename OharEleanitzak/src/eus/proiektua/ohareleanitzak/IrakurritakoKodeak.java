 
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

import java.util.Vector;

import android.content.Context;
import android.content.Intent;
import android.database.Cursor;
import android.database.SQLException;
import android.database.sqlite.SQLiteDatabase;
import android.database.sqlite.SQLiteOpenHelper;
import android.util.Log;

/**
 * Klase honen bidez erabiltzaileak atzitu dituen kodeen historia maneiatuko da,
 * datu base batean gordetako datuak erabiliz
 */
public class IrakurritakoKodeak extends SQLiteOpenHelper {
	private Context testuingurua;

	// Eraikitzailea
	public IrakurritakoKodeak(Context context) {
		// Bere gurasoaren(SQLiteOpenHelper) eraikitzaileari deituko dio, Kodeak
		// deituriko datu basea sortzeko esanez
		super(context, "Kodeak", null, 1);
		testuingurua = context;
	}

	/**
	 * Eragiketa honek aplikazioaren lehen exekuzioan datubasearen taula sortuko
	 * du
	 * 
	 * @param db
	 *            SQLiteDatabase motako objektu bat. Bertan sortuko da taula.
	 * 
	 */
	@Override
	public void onCreate(SQLiteDatabase db) {
		try {
			/*
			 * Datu basean Kodeak izeneko taula sortzeko (helbidea eta
			 * AzkenAtzipena aldagaiekin)SQL agindua
			 */
			String sqlAgindua = "CREATE TABLE Kodeak("
					+ "helbidea VARCHAR(255),"
					+ "azkenAtzipena DATETIME NOT NULL,"
					+ "PRIMARY KEY(helbidea) );";
			// Agindu horren exekuzioa
			db.execSQL(sqlAgindua);
		} catch (SQLException e) {
			// Hutsik: Dialog bat sortuta ere aplikazioak 'crash' egiten du, beraz, ez legoke modurik erabiltzaileari
			//  jakinarazteko
			Log.e("AlbaolApp-IrakurritakoKodeak(onCreate)", e.getLocalizedMessage());
		}

	}

	/**
	 * Funtzionalitaterik gabeko funtzioa. Datu basearen bertsio berri bat
	 * sortzekotan exekutatu behar da(Oraingoz, datu basearen bertsio berdina
	 * erabili da garapen prozesuan zehar
	 * */
	@Override
	public void onUpgrade(SQLiteDatabase db, int oldVersion, int newVersion) {
		// HUTSIK
	}

	/**
	 * Eragiketa honek historian erabiltzaileak irakurri berri duen kodea
	 * gehituko du, lehendik ez badago. Egotekotan, azkenAtzipena eguneratuko
	 * du.
	 * 
	 * @param uri
	 *            Irakurri den kodeak barnean duen helbidea(hizkuntzaren arabera
	 *            moldatua)
	 */
	public void kodeaGehitu(String uri) {
		// Datu basea irekitzeko
		SQLiteDatabase db = getReadableDatabase();
		// Erabiltzaileak uri testu-kateak adierazten duen kodea lehendik
		// irakurri al duen jakiteko kontsulta
		String sql = "SELECT helbidea FROM Kodeak WHERE helbidea='" + uri
				+ "';";
		// Kontsulta exekutatu eta emaitza kurtsorean sartu, dauzkan balioak
		// ikusi ahal izateko
		Cursor kurtsorea = db.rawQuery(sql, null);
		// Kodeak taula hutsik ez badago elementu hori lehendik dago taulan
		if (kurtsorea.getCount() > 0) {
			try {
				// azkenAtzipena balioa momentuko data eta denborara
				// eguneratzeko SQL agindua
				sql = "UPDATE Kodeak SET azkenAtzipena=datetime() WHERE helbidea='"
						+ uri + "';";
				// Aginduaren exekuzioa
				db.execSQL(sql);
			} catch (SQLException e) {
				// Hutsik: Arazo bat dela eta datu-basea eguneratzea lortuko ez bagenu, ez litzateke hain kritikoa, ez
				// dugu zertan erabiltzaileari abisatu
				Log.e("AlbaolApp-IrakurritakoKodeak(kodeaGehitu)", e.getLocalizedMessage());
			}
		} else// Taula hutsik dago, hau da, erabiltzaileak ez du iraganean kode
				// hori irakurri
		{
			try {
				// Kodearen uri-a datu basean sartu, atzipen data eta denbora
				// bezala oraina jarriz
				sql = "INSERT INTO Kodeak VALUES('" + uri + "',datetime());";
				db.execSQL(sql);
			} catch (SQLException e) {
				// Hutsik: Arazo bat dela eta datu-basean tupla sartzea lortuko ez bagenu, ez litzateke hain kritikoa, 
				// ez dugu zertan erabiltzaileari abisatu
				Log.e("AlbaolApp-IrakurritakoKodeak(kodeaGehitu)", e.getLocalizedMessage());
			}
		}
		// kurtsorea itxi
		kurtsorea.close();
		// Datu basea itxi
		db.close();
	}

	/**
	 * Eragiketa honek erabiltzaileak irakurri dituen kodeak itzuliko ditu,
	 * historiatik ezabatuak izan ez badira behintzat
	 * 
	 * @return Vector :{@link com.example.prototipoa.Kodea} motako objektuez
	 *         osatua, datu basean dauden helbideak itzuliko dituena, atzipen
	 *         datarekin batera
	 */
	public Vector<Kodea> irakurritakoKodeakItzuli() {
		Vector<Kodea> kodeak = new Vector<Kodea>();
		// Datu basea irekitzeko
		SQLiteDatabase db = getReadableDatabase();
		// Orain arte irakurritako Kodeak itzuliko ditu, atzipen unearen arabera
		// ordenatuta(berrienetik zaharrenera)
		String sql = "SELECT * FROM Kodeak ORDER BY azkenAtzipena DESC;";
		// Kontsulta exekutatu eta emaitza kurtsorean sartu, dauzkan balioak
		// ikusi ahal izateko
		Cursor kurtsorea = db.rawQuery(sql, null);
		Kodea kode;

		// Kurtsorean atzitu gabeko elementuak dauden bitartean egingo da
		// loop-aren barrukoa
		while (kurtsorea.moveToNext()) {
			kode = new Kodea(kurtsorea.getString(kurtsorea
					.getColumnIndex("helbidea")), kurtsorea.getString(kurtsorea
					.getColumnIndex("azkenAtzipena")));
			kodeak.add(kode);
		}
		// kurtsorea itxi
		kurtsorea.close();
		// Datu basea itxi
		db.close();
		return kodeak;
	}

	/**
	 * Erabiltzaileak irakurri dituen kodeen historia garbituko du eragiketa
	 * honek.
	 */
	public boolean irakurritakoKodeakGarbitu() {
		try {
			// Datu basea irekitzeko
			SQLiteDatabase db = getReadableDatabase();
			// Kodeak taulan dagoen eduki guztia ezabatu
			String sql = "DELETE FROM Kodeak;";
			db.execSQL(sql);
			// Datu basea itxi
			db.close();
			return true;
		} catch (SQLException e) {
			String oharra = testuingurua.getResources().getString(R.string.dbGarbitzeanErrorea);
			Intent intent = new Intent(testuingurua, DialogSortzailea.class);
			intent.putExtra(DialogSortzailea.ARG_MEZUA, oharra + e.getLocalizedMessage());
			testuingurua.startActivity(intent);
			return false;
		}
	}
}