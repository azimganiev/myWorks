<?php
if(!class_exists('COMMUNAL'))include 'COMMUNAL.inc';
class COMMUNAL_bank extends COMMUNAL{
	
	var $bank_admin=array('AddController'=>'Добавить контролера'
						,'EditController'=>'Изменить контролера'
						);//меню админа
	
    var $Header=array(
	'employerType'=>'Тип деятельности'
	,'pass'=>'ID для входа в систему'
	,'name'=>'наименование'
	,'fio'=>'ФИО'
	,'BankTime'=>'Банковский день'
	,'CalendarTime'=>'Календарный день'
	,'_author'=>'Сотрудник'
	);//надписи

	function __construct(){
		parent::__construct();
	}
	
	  function AddController(){//добавление контролера
	$e=explode('_',$this->Viewer['officeId']);//парсим данные о пользователе нам нужен номер филиала
	$query=&mysqlquery("select * from `COMMUNAL`.`office` where `id` like '%".$e[1]."%'");//даем ссылку на данные об офисах данного филиала полученные в результате запроса
	for($i=0; $i<count($query);$i++){//готовим данные для селекта(офисы банка в одном городе)
	    $row[$i] = $query[$i]['name'];
	    $this->oid[$i]=$query[$i]['id'];
	}
	switch($this->InputDat[2]){
	    case 'add'://заносим данные о новом контролере
		$query="insert into `COMMUNAL`.`systemEmployer` (`officeId`,`fio`,`pass`,`login`,`workModule`,`employer`)
			values('".$this->oid[$this->InputDat[3]]."','".$this->InputDat[4]."','".$this->InputDat[5]."','".$this->InputDat[6]."','bank','controller')";
		$text.=$this->getMysqlResult($query,__LINE__.__FILE__);
	    break;
	    default://строим формочку для занесения информации
		$h[1]['bank']=$this->setSelectable($row);//передаем данные для селекта 
		$h[1]['name']='';
		$h[1]['pass']='';
		$h[1]['login']='';
		$s='bank,name,pass,login';	
		$Header=array('bank'=>'Мини Банк','name'=>'Ф.И.О. кассира','pass'=>'пароль','login'=>'логин');//дополнительные заголовки
		$a=RemapsArray($h,$s,$Header);
		$a['buttonRowSubmit']=1;
		$a['d']=array('bank'=>'select','name'=>'input','pass'=>'input','login'=>'input');
		$a['b']=array('bank','name','pass','login');
		$a['buttonAction']=$this->NextCases('add');
		    //archiv(array(__FILE__.__LINE__,$a),31);
		$text=HTML::TableRowsData($a);
	}
	return $text;
    }
    function EditController(){//редактирование информации о контроллере
	$e=explode('_',$this->Viewer['officeId']);//парсим данные о пользователе нам нужен номер филиала
	$query=&mysqlquery("select * from `COMMUNAL`.`office` where `id` like '%".$e[1]."%'");//даем ссылку на данные об офисах данного филиала полученные в результате запроса
	
	$user =&mysqlquery("select * from `COMMUNAL`.`systemEmployer`  where `officeId` like '%".$e[1]."%'and `employer`='controller'");
	
	    for($i=0; $i<count($query);$i++){//готовим данные для селекта(офисы банка в одном городе)
		$row[$i] = $query[$i]['name'];
		$this->oid[$i]=$query[$i]['id'];
		$office[]=$query[$i];
	    }
	
    for($i=0; $i<count($user);$i++){//создаем список контролеров
		$this->kas[$i] = $user[$i];	
		$this->kass[$i]=$user[$i]['fio'];
	    }

	    switch($this->InputDat[2]){
		case 'add':
			$query="update `COMMUNAL`.`systemEmployer` set `officeId`='".$this->InputDat[3]."',`fio`='".$this->InputDat[4]."',`pass`='".$this->InputDat[5]."',`login`='".$this->InputDat[6]."' where `id`='".$this->InputDat[7]."'";
		    $text.=$this->getMysqlResult($query,__LINE__.__FILE__);
		break;
		case 'showlink'://строится форма с данными о контролере которого выбрали изменения потом сохраняются по нажатию на кнопку
			$h[1]['bank']=$this->setSelectable($office,'id','name',$user[$this->InputDat[3]]['officeId']);
			$h[1]['login']=$user[$this->InputDat[3]]['login'];
			$h[1]['name']=$user[$this->InputDat[3]]['fio'];
			$h[1]['pass']=$user[$this->InputDat[3]]['pass'];
			$h[1]['uid']=$user[$this->InputDat[3]]['id'];
			$s='bank,name,pass,login,uid';
			$Header=array('bank'=>'Мини Банк','name'=>'Ф.И.О. кассира','pass'=>'пароль','login'=>'логин');
			$a=RemapsArray($h,$s,$Header);
			$a['buttonRowSubmit']=1;
			$a['d']=array('bank'=>'select','name'=>'input','pass'=>'input','login'=>'input','uid'=>'hidden');
			$a['b']=array('bank','name','pass','login','uid');
			$a['buttonAction']=$this->NextCases('add');
			  $text=HTML::TableRowsData($a);
		break;
		default:
		    $h[1]['name']=$this->setSelectable($this->kass);//(передаем список пользователей)пользователь потом выберет того информацию о котором надо редактировать
		    $Header=array('name'=>'FIO');
		    $s='name';
		    $a=RemapsArray($h,$s,$Header);
		    $a['d']=array('name'=>'select');
		    $a['b']=array('name');
		    $a['buttonRowSubmit']=1;
		    $a['buttonAction']=$this->NextCases('showlink');
		$text=HTML::TableRowsData($a);
	    }
	return $text;
    }
    
	
	
	}
?>