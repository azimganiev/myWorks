<?php 
 class skeleton{
     private static $conninfo=array();
     public $err_log=array();
     private static $queries=array();
     
    static public function setconninfo($filename=''){
       $handle = fopen($filename, "r");//открываем файл для чтения
        while (!feof($handle)) {//читаем по одной строчки из файла  с информацией для подключения,парсим считанную строчку и заносим информацию в ассоциативный массив
            $buffer = fgets($handle, 4096);
            $inf=  explode(':', $buffer);
			self::$conninfo[$inf[0]]=trim($inf[1]);
        }
        fclose($handle); //закрываем файл
	}
    static public function setconn(){
        $c=self::getconninfo();//берем иформацию для подключения к БД
         $link = mysql_connect($c['host'],$c['login'],$c['pass']);//создаем подключение
           if (!$link){$this->err_log[]=('Could not connect: ' . mysql_error()); $this->showerror(); }//проверяем установлено ли соединение
        return $link;
    }
    static public function setqueries($filename){
       if (!file_exists($filename)){$this->err_log[]="file ".$filename." doesn't exist";$this->showerror();}//проверяем наличие файла с запросами sql
          $inf= file_get_contents($filename);//читаем содержимое файла
          self::$queries=explode(";", $inf);//парся считанное заносим его в массив запросов  
    }
    static public function getqueries(){//метод для получения массива запросов
     return self::$queries;       
    }
    static public function getconninfo(){//метод для получения информации для подключения к БД
        return self::$conninfo;
    }
	public function showerror(){//метод вытаскивающий информацию об ошибках на экран
        foreach (self::$err_log as $key => $value) {
            echo $value;
        }   
        exit();
    }
    static public function mysqlquery($query,$conn){//доработка к стандартной функции php mysql_query для более легкого обращения с информацией полученной в результате запроса
         $res=mysql_query($query,$conn);
		if($res){$a=array();$i=0;
			while ($row = mysql_fetch_assoc($res)) {
             $i++; 
				foreach ($row as $key => $value) {
                  $a[$i][$key]=$value;  
                }
			}
		}else $a[1]=" ".mysql_error();
      return $a;
    }
     
    static public function start(){
         self::setconninfo('conn_info');
         self::setconn();
         self::setqueries('data23.sql');
    }
}


	if(!isset($_POST['start'])){// определяем, установлено ли поле старт  
		skeleton::start();
			if(empty(skeleton::$err_log))//если лог ошибок пуст вытаскиваем кнопку для запуска 
				echo'<html>
						<form method="post" action="processinghtml.php"><input type="submit" value="запуск" name="start"/></form>
					</html>';
	}else{
       skeleton::setconninfo('conn_info');//устанавливаем информацию для подключения
       $link= skeleton::setconn();//получаем ссылку на подключение к БД
       skeleton::setqueries('data23.sql');//устанавливаем запросы
       $q=skeleton::getqueries();//запрашиваем массив запосов
            for($i=0;$i<=count($q);$i++){//выполняем запросы считанные из файла
                if(!(trim($q[$i])=='')) $res=mysql_query($q[$i].";",$link);   
            }
       $result0=skeleton::mysqlquery("SELECT * from `data2`.`country`",$link);//вытаскиваем список стран
       $result1=skeleton::mysqlquery("SELECT * from `data2`.`city`",$link);//вытаскиваем список городов
       $result2=skeleton::mysqlquery("SELECT * from `data2`.`market`",$link);//вытаскиваем список магазинов
           
       $pgarr="var country =[];";
        for($i=1;$i<=count($result0);$i++){//готовим данные о странах для Javascripta
            $a=$result0[$i];
            $ent="country[".$a['id']."]";
            $pgarr.=$ent." = '".$a['name']."'; ";
        }
        $pgarr.="var city =[];";
        for($j=1;$j<=count($result1);$j++){//готовим данные о городах для Javascripta
            $b=$result1[$j];
            $ent2="city[".$b['id']."]";
            $pgarr.=$ent2." = {inf:{c_id:".$b['c_id'].",nm:'".$b['name']."'}}; ";
        }   
        $pgarr.="var market =[];";
        for($k=1;$k<=count($result2);$k++){//готовим данные о магазинах для Javascripta
            $c=$result2[$k];
            $ent3="market[".$c['id']."]";
			$pgarr.=$ent3." = {inf:{c_id:".$c['c_id'].",nm:'".$c['name']."'}}; ";
        } 
       
		$scr="function sorter(index){
				if(index.indexOf('@')==-1){//определяем есть ли @ в переданном параметре
					if(index!=0) refill(index,1);else start();//если параметр не равен 0 перезаполняем соответствующие выпадающие списки в противном случае нужно показать все записи 
				}else{ var temp = new Array();//если @ есть обрабатывем параметр и устанавливаем значение в селекте 
					temp = index.split('@');
					document.getElementById('countr').selectedIndex=temp[1];
					refill(temp[0],2);//перезаполняем список магазинов
				} 
			 }	
			function initt(item){//запуск заполнения выпадающих списков
				item.disabled='disabled';
				start();
            }";
		$page='<script type="text/javascript">'.iconv("UTF-8","windows-1251",$pgarr).$scr.'
			function refill(index,sel){//функция для перезаполнения списка с городами и магазинами
				tx2="";
				switch(sel){
					case 1://города
						b=true;
						for(i=1;i<city.length;i++)
							if(city[i].inf.c_id==index){
								if(b){q=i;b=false;}//нужно для того что бы выставить выбранным первый магазин находящийся в выбранном городе
								tx2+="<option value="+i+"@"+city[i].inf.c_id+">"+city[i].inf.nm+"</option>";   
							}
						document.getElementById("city").innerHTML=tx2;
						refill(q,2);
					break;
					case 2:for(i=1;i<market.length;i++)//магазины
						if(market[i].inf.c_id==index)
						tx2+="<option value="+i+"@"+market[i].inf.c_id+">"+market[i].inf.nm+"</option>";   
						document.getElementById("market").innerHTML=tx2;
					break;
				} 
			}
			function start(){//заполняем списки данными
				tx="<option value=0 selected>all</option>";
					tx1=tx;tx2=tx;  
					for(i=1;i<country.length;i++){tx+="<option value="+i+">"+country[i]+"</option>";}
					for(i=1;i<city.length;i++){tx1+="<option value="+i+"@"+city[i].inf.c_id+">"+city[i].inf.nm+"</option>";}
					for(i=1;i<market.length;i++){tx2+="<option value="+i+"@"+market[i].inf.c_id+">"+market[i].inf.nm+"</option>";}
						document.getElementById("countr").innerHTML=tx;
						document.getElementById("city").innerHTML=tx1;
						document.getElementById("market").innerHTML=tx2;
			}           
          
        </script>
		<select id="countr" onChange="javascript:sorter(this.value);"></select>
		<select id="city" onChange="javascript:sorter(this.value);" ></select>
		<select id="market"></select> 
		<input type="button" value="load" onclick="javascript:initt(this);">';
		echo $page;
	}//else
?>