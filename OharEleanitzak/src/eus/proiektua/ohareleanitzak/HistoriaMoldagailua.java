 
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

import java.util.ArrayList;
import java.util.Calendar;
import java.util.Iterator;
import java.util.Vector;
import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.BaseExpandableListAdapter;
import android.widget.TextView;

/**
 * 
 * Klase hau IrakurketaHistoria klasearen interfazeari portaera emateko erabiltzen da. Inplementatuta dituen eragiketa
 * gehienak {@link android.widget.BaseExpandableListAdapter} klasearen eragiketa abstraktuen inplementazioa dira
 *
 */
public class HistoriaMoldagailua extends BaseExpandableListAdapter
{
	
	private Context testuingurua;
	/*Historiaren elementuak(Goiburu motako Objektuz osatua.Era berean Goiburuak String motako objektuz osatuta daude
	*gordetzeko erabiltzen den aldagaia*/
	private ArrayList<Goiburua> multzoak;
	private NegozioLogika negLog;
	
	//Eraikitzailea
	public HistoriaMoldagailua(Context testuingurua) 
	{
		this.testuingurua = testuingurua;
		multzoak=new ArrayList<Goiburua>();
		//Datu basearekiko harremana egiteaz arduratuko den objektuaren hasieratzea
		negLog=new NegozioLogika(testuingurua);
		//Datu basean dauden kodeen atzipena eta horiek multzoak aldagaian antolatzeaz arduratzen den funtzioari deia
		kodeakDatarenAraberaAntolatu();
	}
	
	/*
	 * BaseExpandableListAdapter klasearen eragiketa abstraktua. GoiburuIndizea-n dagoen goiburuaren semeIndizea-garren
	 * elementua itzultzen da
	 */
	@Override
	public String getChild(int goiburuIndizea, int semeIndizea) 
	{
		ArrayList<String> elementuZerrenda = multzoak.get(goiburuIndizea).getElementuak();
		return elementuZerrenda.get(semeIndizea);
	}

	/*
	 * BaseExpandableListAdapter klasearen eragiketa abstraktua. Semearen identifikadorea itzultzeko erabiltzen den 
	 * eragiketa.Kasu honetan, indizea bera da identifikadorea
	 */
	@Override
	public long getChildId(int goiburuIndizea, int semeIndizea) 
	{
		return semeIndizea;
	}

	/*
	 * BaseExpandableListAdapter klasearen eragiketa abstraktua. Hautatu den elementua adierazten duen bista itzultzen
	 * duen eragiketa
	 */
	@Override
	public View getChildView(int goiburuIndizea, int semeIndizea, boolean azkenSemea, View bista,
			ViewGroup gurasoa) 
	{
		String child = getChild(goiburuIndizea, semeIndizea);
		if (bista == null) {
			LayoutInflater infalInflater = (LayoutInflater) testuingurua.getSystemService(Context.LAYOUT_INFLATER_SERVICE);
			bista = infalInflater.inflate(R.layout.zerrenda_elementua, null);
		}
		TextView helbideTextView=(TextView) bista.findViewById(R.id.helbidea);
		helbideTextView.setText(child);
		return bista;
	}

	/*
	 * BaseExpandableListAdapter klasearen eragiketa abstraktua. Hautatu den Goiburuaren elementu kopurua itzultzen du
	 */
	@Override
	public int getChildrenCount(int goiburuIndizea) 
	{
		ArrayList<String> elementuZerrenda = multzoak.get(goiburuIndizea).getElementuak();

		return elementuZerrenda.size();

	}

	/*
	 * BaseExpandableListAdapter klasearen eragiketa abstraktua. goiburuIndizea indizean dagoen Gobiurua itzultzen du
	 */
	@Override
	public Goiburua getGroup(int goiburuIndizea) 
	{
		return multzoak.get(goiburuIndizea);
	}

	/*
	 * BaseExpandableListAdapter klasearen eragiketa abstraktua. Dagoen Goiburu kopurua itzultzen du
	 */
	@Override
	public int getGroupCount() 
	{
		return multzoak.size();
	}
	
	/*
	 * BaseExpandableListAdapter klasearen eragiketa abstraktua. goiburuIndizea posizioan dagoen Goiburuaren
	 * identifikadorea itzultzen du. Kasu honetan, goiburuIndizea bera da identifikadorea
	 */
	@Override
	public long getGroupId(int goiburuIndizea) 
	{
		return goiburuIndizea;
	}

	/*
	 * BaseExpandableListAdapter klasearen eragiketa abstraktua. Hautatu den goiburua adierazten duen bista itzultzen
	 * duen eragiketa
	 */
	@Override
	public View getGroupView(int goiburuIndizea, boolean azkenSemea, View bista,
			ViewGroup gurasoa) 
	{
		Goiburua goiburua = getGroup(goiburuIndizea);
		if (bista == null) {
			LayoutInflater inf = (LayoutInflater) testuingurua.getSystemService(Context.LAYOUT_INFLATER_SERVICE);
			bista = inf.inflate(R.layout.goiburua, null);
		}
		TextView textView = (TextView) bista.findViewById(R.id.multzoa);
		textView.setText(goiburua.getIzena());
		return bista;
	}
	
	/*
	 * BaseExpandableListAdapter klasearen eragiketa abstraktua. Identifikadore egonkorrak dituen itzultzen duen
	 *  eragiketa
	 */
	@Override
	public boolean hasStableIds() 
	{
		return true;
	}
	
	/*
	 * BaseExpandableListAdapter klasearen eragiketa abstraktua. Elementu semeak, String motako
	 * objektuak alegia, hautatu daitezkeen itzultzen duen eragiketa. True da erantzuna, String motakoa
	 * objektuek portaera izango dute eta sakatuak direnean
	 */
	@Override
	public boolean isChildSelectable(int arg0, int arg1) 
	{
		return true;
	}
	
	/**
	 * Datu basea ezabatu eta moldagailua garbitzen dituen eragiketa
	 */
	public boolean garbitu()
	{
		boolean arrakasta = negLog.irakurritakoKodeakGarbitu();
		if (true == arrakasta ) { //Datu-basea arrakastaz garbitu bada
			this.multzoak =new ArrayList<Goiburua>();
		} 
		return arrakasta;
	}
	
	/**
	 * Eragiketa honek datu basean dauden kodeak Goiburu-etan antolatzen ditu, elementu bakoitza atzitua izan den azken
	 * aldiaren arabera
	 */
	public void kodeakDatarenAraberaAntolatu()
	{
		multzoak=new ArrayList<Goiburua>();
		//Datu basetik kodeak hartu
		Vector<Kodea> kodeak=negLog.irakurritakoKodeakItzuli();
		//Momentua adierazten duen Calendar motako objektua sortu
		Calendar gaur=Calendar.getInstance();
		Calendar lag=Calendar.getInstance();
		Kodea kode;
		long egunEzberdintasuna;
		boolean[] goiburuak=new boolean[5];
		for(int i=0;i<5;i++)
		{
			goiburuak[i]=false;
		}
		Goiburua goiburua=new Goiburua();
		String helbidea;
		Iterator<Kodea> it=kodeak.iterator();
		while(it.hasNext())
		{
			kode=it.next();
			//lag aldagaian uneko kodearen azkenAtzipena balioaren unea ezarri
			lag.setTime(kode.getAzkenAtzipena());
			//Oraingo unea eta uneko kodearen azkenAtzipenaren arteko egun ezberdintasuna kalkulatu
			egunEzberdintasuna=(gaur.getTimeInMillis()-lag.getTimeInMillis())/(24*60*60*1000);
			if(egunEzberdintasuna<1)//Gaur irakurri bada azkenengo aldiz
			{
				if(!goiburuak[0])//Gaur goiburua ezarri ez bada
				{
					goiburuak[0]=true;
					//Goiburu berri bat sortu
					goiburua=new Goiburua();
					//Goiburu horretan gaur testu balioa jarri
					goiburua.setIzena(testuingurua.getString(R.string.gaur));
				}		
				//zerrendaElementua aldagaian uneko kodearen helbidea ezarri
				helbidea=kode.getHelbidea();
				//zerrendaElementua goiburuari gehitu
				goiburua.elementuaGehitu(helbidea);
				//hurrengo kodera pasa, gainontzeko kodea exekutatu gabe
				continue;
			}
			if(egunEzberdintasuna<2)//Atzo irakurri bazen azkenengo aldiz
			{
				if(!goiburuak[1])//Atzo goiburua ezarri ez bada
				{
					if(goiburuak[0])//Gaur goiburua ezarria izan bada, goiburuak badauka elementuren bat bere barnean
					{
						//goiburuaren balioa gorde
						multzoak.add(goiburua);
					}
					goiburuak[1]=true;
					//Goiburu berri bat sortu
					goiburua=new Goiburua();
					//Goiburu horretan atzo testu balioa jarri
					goiburua.setIzena(testuingurua.getString(R.string.atzo));
				}
				//zerrendaElementua aldagaian uneko kodearen helbidea ezarri
				helbidea=kode.getHelbidea();
				//zerrendaElementua goiburuari gehitu
				goiburua.elementuaGehitu(helbidea);
				//hurrengo kodera pasa, gainontzeko kodea exekutatu gabe
				continue;
			}
			if(egunEzberdintasuna<7)//Azken astean irakurri bazen azkenengo aldiz
			{
				if(!goiburuak[2])//Azken astea goiburua ezarri ez bada
				{
					if(goiburuak[0] || goiburuak[1])//Gaur edota atzo goiburua ezarriak izan badira, goiburuak badauka elementuren bat bere barnean
					{
						//goiburuaren balioa gorde
						multzoak.add(goiburua);
					}
					goiburuak[2]=true;
					//Goiburu berri bat sortu
					goiburua=new Goiburua();
					//Goiburu horretan azkenAstea testu balioa jarri
					goiburua.setIzena(testuingurua.getString(R.string.azkenAstea));
				}
				//zerrendaElementua aldagaian uneko kodearen helbidea ezarri
				helbidea=kode.getHelbidea();
				//zerrendaElementua goiburuari gehitu
				goiburua.elementuaGehitu(helbidea);
				//hurrengo kodera pasa, gainontzeko kodea exekutatu gabe
				continue;
			}
			if(egunEzberdintasuna<31)//Azken hilabetean irakurri bazen azkenengo aldiz
			{
				if(!goiburuak[3])//Azken Hilabetea goiburua ezarri ez bada
				{
					if(goiburuak[0] || goiburuak[1] || goiburuak[2])//Gaur, atzo edota azkenAstea goiburua ezarriak izan badira, goiburuak badauka elementuren bat bere barnean
					{
						//goiburuaren balioa gorde
						multzoak.add(goiburua);
					}
					goiburuak[3]=true;
					//Goiburu berri bat sortu
					goiburua=new Goiburua();
					//Goiburu horretan azkenHilabetea testu balioa jarri
					goiburua.setIzena(testuingurua.getString(R.string.azkenHilabetea));
				}
				//zerrendaElementua aldagaian uneko kodearen helbidea ezarri
				helbidea=kode.getHelbidea();
				//zerrendaElementua goiburuari gehitu
				goiburua.elementuaGehitu(helbidea);
				//hurrengo kodera pasa, gainontzeko kodea exekutatu gabe
				continue;
			}
			if(egunEzberdintasuna>=31)//Azken hilabetea baino lehenago irakurri bazen azkenengo aldiz
			{
				if(!goiburuak[4])//Zaharragoak goiburua ezarri ez bada
				{
					if(goiburuak[0] || goiburuak[1] || goiburuak[2] || goiburuak[3])//Gaur, atzo, azkenAstea edota azkenHilabetea goiburua ezarriak izan badira, goiburuak badauka elementuren bat bere barnean
					{
						//goiburuaren balioa gorde
						multzoak.add(goiburua);
					}
					goiburuak[4]=true;
					//Goiburu berri bat sortu
					goiburua=new Goiburua();
					//Goiburu horretan zaharragoak testu balioa jarri
					goiburua.setIzena(testuingurua.getString(R.string.zaharragoak));
				}
				//zerrendaElementua aldagaian uneko kodearen helbidea ezarri
				helbidea=kode.getHelbidea();
				//zerrendaElementua goiburuari gehitu
				goiburua.elementuaGehitu(helbidea);
				//hurrengo kodera pasa, gainontzeko kodea exekutatu gabe
				continue;
			}
		}
		if(goiburuak[0] || goiburuak[1] || goiburuak[2] || goiburuak[3] || goiburuak[4])//Gaur, atzo, azkenAstea, azkenHilabetea edota Zaharragoak goiburua ezarriak izan badira, goiburuak badauka elementuren bat bere barnean
		{
			//goiburuaren balioa gorde
			multzoak.add(goiburua);
		}
	}
	
	/**
	 * Eragiketa honek IrakurritakoKodeak klaseko kodeaGehitu eragiketari deitzen dio, NegozioLogika bitartekari bezala erabiliz
	 * @param uri Datu basean gehitu edo eguneratu behar den kodearen helbidea
	 * @see eus.proiektua.ohareleanitzak.IrakurritakoKodeak#kodeaGehitu(String)
	 */
	public void kodeaGehitu(String uri)
	{
		negLog.kodeaGehitu(uri);
	}
	
}
