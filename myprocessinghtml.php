<?php 
 class skeleton{
     private static $conninfo=array();
     public $err_log=array();
     private static $queries=array();
     
    static public function setconninfo($filename=''){
       $handle = fopen($filename, "r");//��������� ���� ��� ������
        while (!feof($handle)) {//������ �� ����� ������� �� �����  � ����������� ��� �����������,������ ��������� ������� � ������� ���������� � ������������� ������
            $buffer = fgets($handle, 4096);
            $inf=  explode(':', $buffer);
			self::$conninfo[$inf[0]]=trim($inf[1]);
        }
        fclose($handle); //��������� ����
	}
    static public function setconn(){
        $c=self::getconninfo();//����� ��������� ��� ����������� � ��
         $link = mysql_connect($c['host'],$c['login'],$c['pass']);//������� �����������
           if (!$link){$this->err_log[]=('Could not connect: ' . mysql_error()); $this->showerror(); }//��������� ����������� �� ����������
        return $link;
    }
    static public function setqueries($filename){
       if (!file_exists($filename)){$this->err_log[]="file ".$filename." doesn't exist";$this->showerror();}//��������� ������� ����� � ��������� sql
          $inf= file_get_contents($filename);//������ ���������� �����
          self::$queries=explode(";", $inf);//����� ��������� ������� ��� � ������ ��������  
    }
    static public function getqueries(){//����� ��� ��������� ������� ��������
     return self::$queries;       
    }
    static public function getconninfo(){//����� ��� ��������� ���������� ��� ����������� � ��
        return self::$conninfo;
    }
	public function showerror(){//����� ������������� ���������� �� ������� �� �����
        foreach (self::$err_log as $key => $value) {
            echo $value;
        }   
        exit();
    }
    static public function mysqlquery($query,$conn){//��������� � ����������� ������� php mysql_query ��� ����� ������� ��������� � ����������� ���������� � ���������� �������
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


	if(!isset($_POST['start'])){// ����������, ����������� �� ���� �����  
		skeleton::start();
			if(empty(skeleton::$err_log))//���� ��� ������ ���� ����������� ������ ��� ������� 
				echo'<html>
						<form method="post" action="processinghtml.php"><input type="submit" value="������" name="start"/></form>
					</html>';
	}else{
       skeleton::setconninfo('conn_info');//������������� ���������� ��� �����������
       $link= skeleton::setconn();//�������� ������ �� ����������� � ��
       skeleton::setqueries('data23.sql');//������������� �������
       $q=skeleton::getqueries();//����������� ������ �������
            for($i=0;$i<=count($q);$i++){//��������� ������� ��������� �� �����
                if(!(trim($q[$i])=='')) $res=mysql_query($q[$i].";",$link);   
            }
       $result0=skeleton::mysqlquery("SELECT * from `data2`.`country`",$link);//����������� ������ �����
       $result1=skeleton::mysqlquery("SELECT * from `data2`.`city`",$link);//����������� ������ �������
       $result2=skeleton::mysqlquery("SELECT * from `data2`.`market`",$link);//����������� ������ ���������
           
       $pgarr="var country =[];";
        for($i=1;$i<=count($result0);$i++){//������� ������ � ������� ��� Javascripta
            $a=$result0[$i];
            $ent="country[".$a['id']."]";
            $pgarr.=$ent." = '".$a['name']."'; ";
        }
        $pgarr.="var city =[];";
        for($j=1;$j<=count($result1);$j++){//������� ������ � ������� ��� Javascripta
            $b=$result1[$j];
            $ent2="city[".$b['id']."]";
            $pgarr.=$ent2." = {inf:{c_id:".$b['c_id'].",nm:'".$b['name']."'}}; ";
        }   
        $pgarr.="var market =[];";
        for($k=1;$k<=count($result2);$k++){//������� ������ � ��������� ��� Javascripta
            $c=$result2[$k];
            $ent3="market[".$c['id']."]";
			$pgarr.=$ent3." = {inf:{c_id:".$c['c_id'].",nm:'".$c['name']."'}}; ";
        } 
       
		$scr="function sorter(index){
				if(index.indexOf('@')==-1){//���������� ���� �� @ � ���������� ���������
					if(index!=0) refill(index,1);else start();//���� �������� �� ����� 0 ������������� ��������������� ���������� ������ � ��������� ������ ����� �������� ��� ������ 
				}else{ var temp = new Array();//���� @ ���� ����������� �������� � ������������� �������� � ������� 
					temp = index.split('@');
					document.getElementById('countr').selectedIndex=temp[1];
					refill(temp[0],2);//������������� ������ ���������
				} 
			 }	
			function initt(item){//������ ���������� ���������� �������
				item.disabled='disabled';
				start();
            }";
		$page='<script type="text/javascript">'.iconv("UTF-8","windows-1251",$pgarr).$scr.'
			function refill(index,sel){//������� ��� �������������� ������ � �������� � ����������
				tx2="";
				switch(sel){
					case 1://������
						b=true;
						for(i=1;i<city.length;i++)
							if(city[i].inf.c_id==index){
								if(b){q=i;b=false;}//����� ��� ���� ��� �� ��������� ��������� ������ ������� ����������� � ��������� ������
								tx2+="<option value="+i+"@"+city[i].inf.c_id+">"+city[i].inf.nm+"</option>";   
							}
						document.getElementById("city").innerHTML=tx2;
						refill(q,2);
					break;
					case 2:for(i=1;i<market.length;i++)//��������
						if(market[i].inf.c_id==index)
						tx2+="<option value="+i+"@"+market[i].inf.c_id+">"+market[i].inf.nm+"</option>";   
						document.getElementById("market").innerHTML=tx2;
					break;
				} 
			}
			function start(){//��������� ������ �������
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