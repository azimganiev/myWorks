<?php
if(!class_exists('COMMUNAL'))include 'COMMUNAL.inc';
class COMMUNAL_bank extends COMMUNAL{
	
	var $bank_admin=array('AddController'=>'�������� ����������'
						,'EditController'=>'�������� ����������'
						);//���� ������
	
    var $Header=array(
	'employerType'=>'��� ������������'
	,'pass'=>'ID ��� ����� � �������'
	,'name'=>'������������'
	,'fio'=>'���'
	,'BankTime'=>'���������� ����'
	,'CalendarTime'=>'����������� ����'
	,'_author'=>'���������'
	);//�������

	function __construct(){
		parent::__construct();
	}
	
	  function AddController(){//���������� ����������
	$e=explode('_',$this->Viewer['officeId']);//������ ������ � ������������ ��� ����� ����� �������
	$query=&mysqlquery("select * from `COMMUNAL`.`office` where `id` like '%".$e[1]."%'");//���� ������ �� ������ �� ������ ������� ������� ���������� � ���������� �������
	for($i=0; $i<count($query);$i++){//������� ������ ��� �������(����� ����� � ����� ������)
	    $row[$i] = $query[$i]['name'];
	    $this->oid[$i]=$query[$i]['id'];
	}
	switch($this->InputDat[2]){
	    case 'add'://������� ������ � ����� ����������
		$query="insert into `COMMUNAL`.`systemEmployer` (`officeId`,`fio`,`pass`,`login`,`workModule`,`employer`)
			values('".$this->oid[$this->InputDat[3]]."','".$this->InputDat[4]."','".$this->InputDat[5]."','".$this->InputDat[6]."','bank','controller')";
		$text.=$this->getMysqlResult($query,__LINE__.__FILE__);
	    break;
	    default://������ �������� ��� ��������� ����������
		$h[1]['bank']=$this->setSelectable($row);//�������� ������ ��� ������� 
		$h[1]['name']='';
		$h[1]['pass']='';
		$h[1]['login']='';
		$s='bank,name,pass,login';	
		$Header=array('bank'=>'���� ����','name'=>'�.�.�. �������','pass'=>'������','login'=>'�����');//�������������� ���������
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
    function EditController(){//�������������� ���������� � �����������
	$e=explode('_',$this->Viewer['officeId']);//������ ������ � ������������ ��� ����� ����� �������
	$query=&mysqlquery("select * from `COMMUNAL`.`office` where `id` like '%".$e[1]."%'");//���� ������ �� ������ �� ������ ������� ������� ���������� � ���������� �������
	
	$user =&mysqlquery("select * from `COMMUNAL`.`systemEmployer`  where `officeId` like '%".$e[1]."%'and `employer`='controller'");
	
	    for($i=0; $i<count($query);$i++){//������� ������ ��� �������(����� ����� � ����� ������)
		$row[$i] = $query[$i]['name'];
		$this->oid[$i]=$query[$i]['id'];
		$office[]=$query[$i];
	    }
	
    for($i=0; $i<count($user);$i++){//������� ������ �����������
		$this->kas[$i] = $user[$i];	
		$this->kass[$i]=$user[$i]['fio'];
	    }

	    switch($this->InputDat[2]){
		case 'add':
			$query="update `COMMUNAL`.`systemEmployer` set `officeId`='".$this->InputDat[3]."',`fio`='".$this->InputDat[4]."',`pass`='".$this->InputDat[5]."',`login`='".$this->InputDat[6]."' where `id`='".$this->InputDat[7]."'";
		    $text.=$this->getMysqlResult($query,__LINE__.__FILE__);
		break;
		case 'showlink'://�������� ����� � ������� � ���������� �������� ������� ��������� ����� ����������� �� ������� �� ������
			$h[1]['bank']=$this->setSelectable($office,'id','name',$user[$this->InputDat[3]]['officeId']);
			$h[1]['login']=$user[$this->InputDat[3]]['login'];
			$h[1]['name']=$user[$this->InputDat[3]]['fio'];
			$h[1]['pass']=$user[$this->InputDat[3]]['pass'];
			$h[1]['uid']=$user[$this->InputDat[3]]['id'];
			$s='bank,name,pass,login,uid';
			$Header=array('bank'=>'���� ����','name'=>'�.�.�. �������','pass'=>'������','login'=>'�����');
			$a=RemapsArray($h,$s,$Header);
			$a['buttonRowSubmit']=1;
			$a['d']=array('bank'=>'select','name'=>'input','pass'=>'input','login'=>'input','uid'=>'hidden');
			$a['b']=array('bank','name','pass','login','uid');
			$a['buttonAction']=$this->NextCases('add');
			  $text=HTML::TableRowsData($a);
		break;
		default:
		    $h[1]['name']=$this->setSelectable($this->kass);//(�������� ������ �������������)������������ ����� ������� ���� ���������� � ������� ���� �������������
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