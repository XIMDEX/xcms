/**
 *  \details &copy; 2011  Open Ximdex Evolution SL [http://www.ximdex.org]
 *
 *  Ximdex a Semantic Content Management System (CMS)
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as published
 *  by the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  See the Affero GNU General Public License for more details.
 *  You should have received a copy of the Affero GNU General Public License
 *  version 3 along with Ximdex (see LICENSE file).
 *
 *  If not, visit http://gnu.org/licenses/agpl-3.0.html.
 *
 *  @author Ximdex DevTeam <dev@ximdex.com>
 *  @version $Revision$
 */

			 /**
    * definimos las variables que almacenaran los componentes de la fecha actual
    */
    ahora          = new Date();
    ahoraDay    = ahora.getDate();
    ahoraMonth = ahora.getMonth();
    ahoraYear   = ahora.getYear();

    /**
 * Nestcape Navigator 4x cuenta el anyo a partir de 1900, por lo que es necesario
 * sumarle esa cantidad para obtener el anyo actual adecuadamente
 **/
 if (ahoraYear < 2000) 
        ahoraYear += 1900;

    /**
 * funcion para saber cuantos dias tiene cada mes
 */
    function cuantosDias(mes, anyo)
    {
        var cuantosDias = 31;
        if (mes == "Abril" || mes == "Junio" || mes == "Septiembre" || mes == "Noviembre")
      cuantosDias = 30;
        if (mes == "Febrero" && (anyo/4) != Math.floor(anyo/4))
      cuantosDias = 28;
        if (mes == "Febrero" && (anyo/4) == Math.floor(anyo/4))
      cuantosDias = 29;
        return cuantosDias;
    }

//esta funcion es como la anterior, a diferencia del parametro mes
//en este caso, el mes tiene formato numérico y no literal
	function cuantosDias_num(mes, anyo)
    {
        var cuantosDias = 31;
        if (mes == 4 || mes == 6 || mes == 9 || mes == 11)
      cuantosDias = 30;
        if (mes == 2 && (anyo/4) != Math.floor(anyo/4))
      cuantosDias = 28;
        if (mes == 2 && (anyo/4) == Math.floor(anyo/4))
      cuantosDias = 29;
        return cuantosDias;
    }

    /**
 * una vez que sabemos cuantos dias tiene cada mes
 * asignamos dinamicamente este numero al combo de los dias dependiendo 
 * del mes que aparezca en el combo de los meses
 */
    function asignaDias(combo)
    {
	 var nombre=combo.name;
	 var finnombre= nombre.substring(nombre.length-5,nombre.length);
	 if(finnombre=="Desde")
	 {
        comboDias = document.getElementById('seleccionaDiaDesde');
        comboMeses = document.getElementById('seleccionaMesDesde');
        comboAnyos = document.getElementById('seleccionaAnyoDesde');
     }
	 else
	 {
        comboDias = document.getElementById('seleccionaDiaHasta');
        comboMeses = document.getElementById('seleccionaMesHasta');
        comboAnyos = document.getElementById('seleccionaAnyoHasta');  
	 }
        Month = comboMeses[comboMeses.selectedIndex].text;
        Year = comboAnyos[comboAnyos.selectedIndex].text;

        //dias que tiene que haber en el nuevo combo, sin contar "--"
        diasEnMes = cuantosDias(Month, Year);

		//dias actuales, sin contar "--"
        diasAhora = comboDias.length-1; //-1 por la opción "--"

		//eliminacion de los dias sobrantes del combo anterior
        if (diasAhora > diasEnMes)
        {
            for (i=0; i<(diasAhora-diasEnMes); i++)
            {
                comboDias.options[comboDias.options.length - 1] = null
            }
        }
        if (diasEnMes > diasAhora)
        {
            for (i=0; i<(diasEnMes-diasAhora); i++)
            {
                //sumaOpcion = new Option(comboDias.options.length + 1);
				sumaOpcion = new Option(comboDias.options.length); //no se añade 1 al haber opción "--"
                comboDias.options[comboDias.options.length]=sumaOpcion;
            }
        }
        if (comboDias.selectedIndex < 0) 
          comboDias.selectedIndex = 0;
     }

    /**
 * ahora selecionamos en los combos los valores correspondientes 
 * a la fecha actual del sistema
 */
    function ponDia()
    {
 		/*combos de fecha "desde" */
	    comboDias = document.getElementById('seleccionaDiaDesde');
        comboMeses = document.getElementById('seleccionaMesDesde');
        comboAnyos = document.getElementById('seleccionaAnyoDesde');
        comboAnyos[0].selected = true;
		
        //comboMeses[ahoraMonth].selected = true;
        comboMeses[0].selected = true;
        //asignaDias(comboDias);

        //comboDias[ahoraDay-1].selected = true;
		comboDias[0].selected = true;
		/*combos de fecha "hasta" */
	    comboDias = document.getElementById('seleccionaDiaHasta');
        comboMeses = document.getElementById('seleccionaMesHasta');
        comboAnyos = document.getElementById('seleccionaAnyoHasta');  
	
	    comboAnyos[0].selected = true;
		
		//comboMeses[ahoraMonth].selected = true;
        comboMeses[0].selected = true;   
        //asignaDias(comboDias);

        //comboDias[ahoraDay-1].selected = true;
		//comboDias[ahoraDay-1].selected = true;
		comboDias[0].selected = true;
    }

    /**
 * esta funcion crea dinamicamente el combo de los anyos, empezando
 * por el actual y acabando por el actual+masAnyos
 */
    function rellenaAnyos(masAnyos)
    {
        cadena = "";

        for (i=0; i<masAnyos; i++)
        {
            cadena += "<option>";
            cadena += ahoraYear + i;
        }
        return cadena;
    }

	/* esta funcion crea dinamicamente el combo de los anyos, desde Anyo hasta el actual*/
	function rellenaAnyosDesde(Anyo)
	{
	 cadena = "";
	 for (i=ahoraYear; i>=Anyo; i--)
	 {
	 	cadena +="<option>"+i+"</option>";
	 }
	 return cadena;
	}

/*var ie  = document.all;
if(ie) {
		window.onload=ponDia();
	} else {
		window.onload = ponDia;
	}*/
	

function recopilar_filtros_fecha()
{	
	var fechadesde="";
	var fechahasta="";
	var xmlfiltro="";
	var nosel_1=0; //numero de campos no cumplimentados para fecha1
	var nosel_2=0; //numero de campos no cumplimentados para fecha2

	cajabusqueda=document.getElementById("cajabusqueda");
	dia1=document.getElementById("seleccionaDiaDesde");
	dia2=document.getElementById("seleccionaDiaHasta");
	mes1=document.getElementById("seleccionaMesDesde");
	mes2=document.getElementById("seleccionaMesHasta");
	anyo1=document.getElementById("seleccionaAnyoDesde");
	anyo2=document.getElementById("seleccionaAnyoHasta");
	dia_1=dia1[dia1.selectedIndex].text;
	dia_2=dia2[dia2.selectedIndex].text;
	mes_1=mes1.selectedIndex-1;
	mes_2=mes2.selectedIndex-1;
	anyo_1=anyo1[anyo1.selectedIndex].text;
	anyo_2=anyo2[anyo2.selectedIndex].text;
 
   	//numero de campos no cupmlimentados en fecha 1
	if (dia1.selectedIndex == 0) nosel_1++;
	if	(mes1.selectedIndex == 0) nosel_1++;
	if	(anyo1.selectedIndex == 0) nosel_1++;

    //numero de campos no cupmlimentados en fecha 2
    if (dia2.selectedIndex == 0) nosel_2++;
	if (mes2.selectedIndex == 0) nosel_2++;
	if (anyo2.selectedIndex == 0) nosel_2++;
 
	if( (nosel_1 == 0 ) && (nosel_2 == 0 ) )
	{
	  //todos los campos estan cumplimentados	
	  //antes de escribir la fecha, hay que comprobar que fecha1 < fecha2
 
	  if(timeStamp(anyo_1,mes_1,dia_1)>timeStamp(anyo_2,mes_2,dia_2))
	  {
	   alert("la fecha de inicio es mayor que la fecha de fin");  
	   return -1;
	  }
	  else
	  {	
	   	fechadesde="<filtro campo='FechaInicio' valor='"+timeStamp(anyo_1,mes_1,dia_1)+"' />";
		fechahasta="<filtro campo='FechaFin' valor='"+timeStamp(anyo_2,mes_2,dia_2)+"' />";;	
	  }
	 }
	 else //algun campo no se ha cumplimentado
	 {
	  //si se ha rellenado al menos un campo, pero no ninguno
	  if(  (nosel_1 <3) && (nosel_1 > 0) )
	  {
	   alert ("debe cumplimentar todos los campos de fecha 'desde'");
	   return -1;
	  }
	  else if(nosel_1==0)
	  {
	   fechadesde="<filtro campo='FechaInicio' valor='"+timeStamp(anyo_1,mes_1,dia_1)+"' />";
	  }
  
	  if(  (nosel_2 <3) && (nosel_2 > 0) )
	  {
	   alert ("debe cumplimentar todos los campos de fecha 'hasta'");
	   return -1;
	  } 
	  else if(nosel_2==0)
	  { 
	   fechahasta="<filtro campo='FechaFin' valor='"+timeStamp(anyo_2,mes_2,dia_2)+"' />";;
	  } 
	 }	 
		
	return(fechadesde+fechahasta);
}
			
function num2cad(numero)
{
	 var cad="";
	 if (numero<10)
	 {
	 	cad="0"+numero;
	 }
	 else
	 {
	   cad=numero;
	 }
	 return cad;
}
			
			
function timeStamp(yr,mes,dia)
{
	//devuelve fecha local divivida por mil (sg)
	var fecha= new Date(yr,mes,dia);
	var	x = parseInt(fecha/1000);
	return x;
}
