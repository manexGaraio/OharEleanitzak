 
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

import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.Date;
import java.util.Locale;

/**
 * Klase honen bidez irakurri diren kodeak adierazten dituzten objektuak definitzen dira
 *
 */
public class Kodea 
{
	//Klasearen atributuak
	
	//Irakurritako kodearen helbidea gordetzen du
	private String helbidea;
	//Irakurritako kode horren azkenAtzipen data gordetzen du
	private Date azkenAtzipena;
	
	//Eraikitzailea
	public Kodea(String helbidea, String azkenAtzipena)
	{
		this.helbidea=helbidea;
		this.azkenAtzipena=dataFormatuzAldatu(azkenAtzipena);
	}

	//Eragiketak
	
	/**
	 * Objektuak gordetzen duen helbidea eskuratzeko eragiketa
	 * @return String objektuaren barneko String-a
	 */
	public String getHelbidea() 
	{
		return helbidea;
	}

	/**
	 * /**
	 * Objektuak gordetzen duen helbidea aldatzeko eragiketa
	 * @param helbidea String bat
	 */
	public void setHelbidea(String helbidea) 
	{
		this.helbidea = helbidea;
	}

	/**
	 * Objektuak gordetzen duen helbidea irakurri zen azken aldia eskuratzeko eragiketa
	 * @return Date objektuaren azken irakurtzea
	 */
	public Date getAzkenAtzipena() 
	{
		return azkenAtzipena;
	}

	/**
	 * Objektuak gordetzen duen helbidea irakurri zen azken aldia aldatzeko eragiketa
	 * @param azkenAtzipena String gisa
	 */
	public void setAzkenAtzipena(String azkenAtzipena) 
	{
		this.azkenAtzipena=dataFormatuzAldatu(azkenAtzipena);
	}
	
	/**
	 * Kodea motako objektuaren azken atzipen dataren formatua eraldatzeko eragiketa.
	 * @param dataString data String bezala jasotzen du
	 * @return {@link java.util.Date} bezala itzultzen du
	 */
	private Date dataFormatuzAldatu(String dataString)
	{
		try
		{
			//Lortu nahi dugun data formatua definitu
			SimpleDateFormat dataFormatua=new SimpleDateFormat("yyyy-MM-dd",Locale.ENGLISH);
			//dataString aldagaiaren edukia hartuta, Date objektura bihurtu eta itzulo
			return dataFormatua.parse(dataString);
		}
		catch (ParseException e) {
			System.out.println("Errore bat gertatu da datari formatua aldatzean" + e.getStackTrace());
			return null;
		}
	}

}
